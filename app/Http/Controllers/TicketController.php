<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;       
use App\Models\Invoice_detail; 
use App\Models\Customer;
use App\Models\Airlines;
use App\Models\Ticket;
use DB;
use PDF; 

class TicketController extends Controller
{
    public function index(Request $request)
    {
        // 1. Mulai Query dengan Eager Loading agar ringan
        $query = Ticket::with(['airline', 'invoice.customer']);
    
        // 2. Logika Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            
            $query->where(function($q) use ($search) {
                // Cari berdasarkan PNR (Booking Code)
                $q->where('booking_code', 'like', '%' . $search . '%')
                  // Cari berdasarkan No Ticket Kustom (jika user hafal angka belakangnya)
                  ->orWhere('id', 'like', '%' . $search . '%')
                  // Cari berdasarkan Nama Booker (Customer) di tabel Invoices
                  ->orWhereHas('invoice.customer', function($queryCustomer) use ($search) {
                      $queryCustomer->where('booker', 'like', '%' . $search . '%');
                  });
            });
        }
    
        // 3. Urutkan dan Pagination (10 data per halaman)
        $tickets = $query->orderBy('id', 'desc')->paginate(10)->appends(request()->all());
    
        return view('ticket.index', compact('tickets'));
    }

    public function create()
    {
        $customers = Customer::orderBy('booker', 'ASC')->get();
        $airlines = Airlines::orderBy('airlines_name', 'ASC')->get();
        return view('ticket.create', compact('customers', 'airlines'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $clean = function($val) {
                return (int) preg_replace('/[^0-9]/', '', $val);
            };
    
            $publish_total  = $clean($request->publish_price);
            $discount_total = $clean($request->discount);
            $nta_total      = $clean($request->nta_price);
            $pax_paid_total = $clean($request->pax_paid); 
            $price_total    = $clean($request->price); 
            $baggage_total  = $clean($request->baggage_price);
            $total_kg       = (int) $request->baggage_kg;
    
            $pax_count = count($request->passengers);
            if ($pax_count == 0) {
                return redirect()->back()->with('error', 'Minimal harus ada 1 penumpang!');
            }
    
            $pax_paid_per_pax = floor($pax_paid_total / $pax_count);
            $price_per_pax    = floor($price_total / $pax_count); 
            $nta_per_pax      = floor($nta_total / $pax_count);
            $discount_per_pax = floor($discount_total / $pax_count);
            $profit_per_pax   = floor(($price_total - $nta_total) / $pax_count);
    
            $invoice = new Invoice();
            $invoice->invoiceno = 'LGT' . date('YmdHis');
            $invoice->customer_id = $request->customer_id;
            $invoice->total = $pax_paid_total - $discount_total;
            $invoice->edited = auth()->user()->name ?? 'Admin'; 
            $invoice->status_pembayaran = 'Belum Lunas';
            $invoice->save();
    
            $ticket = new Ticket();
            $ticket->invoice_id     = $invoice->id;
            $ticket->airline_id     = $request->airline_id;
            $ticket->booking_code   = strtoupper($request->pnr);
            $ticket->flight_out     = $request->flight_out;
            $ticket->flight_in      = $request->flight_in;
            $ticket->route_out      = $request->route_out;
            $ticket->route_in       = $request->route_in;
            $ticket->dep_time_out   = $request->dep_out;
            $ticket->dep_time_in    = $request->dep_in;
            $ticket->arr_time_out   = $request->arr_out;
            $ticket->arr_time_in    = $request->arr_in;
            $ticket->class          = $request->class;
            $ticket->basic_fare     = $clean($request->basic_fare);
            $ticket->total_tax      = $clean($request->total_tax);
            $ticket->fee            = $clean($request->fee_ticket);
            $ticket->baggage_kg     = $total_kg;
            $ticket->baggage_price  = $baggage_total;
            $ticket->free_baggage   = $request->free_baggage ?? 0; // SIMPAN KE DB
            $ticket->total_publish  = $publish_total;
            $ticket->total_profit   = $price_total - $nta_total;
            $ticket->save();
    
            $pax_with_baggage = []; 
            foreach ($request->passengers as $paxData) {
                $new_pax = Invoice_detail::create([
                    'invoice_id'   => $invoice->id,
                    'airline_id'   => $request->airline_id,
                    'booking_code' => strtoupper($request->pnr),
                    'name'         => strtoupper($paxData['name']),
                    'genre'        => $paxData['title'],
                    'ticket_no'    => $paxData['ticket_num'] ?? '-',
                    'price'        => $price_per_pax,    
                    'pax_paid'     => $pax_paid_per_pax, 
                    'nta'          => $nta_per_pax,      
                    'profit'       => $profit_per_pax,   
                    'discount'     => $discount_per_pax,
                    'depart_date'  => $request->dep_out,
                    'return_date'  => $request->dep_in,
                    'airlines_no'  => $request->flight_out,
                    'class'        => $request->class,
                    'route'        => $request->route_out . ($request->route_in ? ' - ' . $request->route_in : ''),
                ]);
    
                if (isset($paxData['has_baggage']) && $paxData['has_baggage'] == "1") {
                    $pax_with_baggage[] = $new_pax->id;
                }
            }
    
            if ($baggage_total > 0 && count($pax_with_baggage) > 0) {
                $baggage_per_pax = floor($baggage_total / count($pax_with_baggage));
                $kg_per_pax      = $total_kg > 0 ? floor($total_kg / count($pax_with_baggage)) : 0;
    
                foreach ($pax_with_baggage as $pax_id) {
                    Invoice_detail::create([
                        'invoice_id'   => $invoice->id,
                        'airline_id'   => $request->airline_id,
                        'booking_code' => strtoupper($request->pnr),
                        'name'         => 'ADD ON BAGGAGE', 
                        'genre'        => '-',
                        'ticket_no'    => $pax_id,
                        'price'        => $baggage_per_pax, 
                        'pax_paid'     => $baggage_per_pax,
                        'nta'          => 0,
                        'profit'       => $baggage_per_pax,
                        'class'        => 'BAGASI_ONLY', 
                        'route'        => $kg_per_pax . ' KG',
                        'airlines_no'  => $request->flight_out, 
                        'depart_date'  => $request->dep_out,
                        'return_date'  => $request->dep_in,
                    ]);
                }
            }
    
            DB::commit();
            return redirect()->route('ticket.index')->with('success', 'Berhasil Disimpan!');
        } catch (\Exception $e) {
            DB::rollback();
            return "Gagal Simpan: " . $e->getMessage();
        }
    }

    public function edit($id)
    {
        $ticket = Ticket::with(['airline', 'invoice.customer'])->findOrFail($id);
        $customers = Customer::orderBy('booker', 'ASC')->get();
        $airlines = Airlines::orderBy('airlines_name', 'ASC')->get();
        
        $passengers = Invoice_detail::where('invoice_id', $ticket->invoice_id)
                                    ->where('class', '!=', 'BAGASI_ONLY')
                                    ->get();
    
        return view('ticket.edit', compact('ticket', 'customers', 'airlines', 'passengers'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $ticket = Ticket::findOrFail($id);
            $clean = function($val) { 
                return (int) preg_replace('/[^0-9]/', '', $val); 
            };
    
            $pax_paid_total = $clean($request->pax_paid); 
            $price_total    = $clean($request->price); 
            $nta_total      = $clean($request->nta_price);
            $discount_total = $clean($request->discount);
            $baggage_total  = $clean($request->baggage_price); 
            
            $pax_count = count($request->passengers);
            if ($pax_count == 0) {
                return redirect()->back()->with('error', 'Minimal harus ada 1 penumpang!');
            }

            $pax_paid_per_pax = floor($pax_paid_total / $pax_count);
            $price_per_pax    = floor($price_total / $pax_count); 
            $nta_per_pax      = floor($nta_total / $pax_count);
            $discount_per_pax = floor($discount_total / $pax_count);
            $profit_per_pax   = floor(($price_total - $nta_total) / $pax_count);
    
            $ticket->update([
                'airline_id'    => $request->airline_id,
                'booking_code'  => strtoupper($request->pnr),
                'basic_fare'    => $clean($request->basic_fare), 
                'total_tax'     => $clean($request->total_tax),
                'fee'           => $clean($request->fee_ticket),
                'baggage_kg'    => $request->baggage_kg,    
                'baggage_price' => $baggage_total, 
                'free_baggage'  => $request->free_baggage ?? 0, // UPDATE KE DB
                'total_publish' => $clean($request->publish_price),
                'total_profit'  => $price_total - $nta_total 
            ]);
    
            $invoice = Invoice::findOrFail($ticket->invoice_id);
            $invoice->update([
                'customer_id' => $request->customer_id,
                'total'       => $pax_paid_total - $discount_total,
                'edited'      => auth()->user()->name ?? 'Admin'
            ]);
    
            Invoice_detail::where('invoice_id', $ticket->invoice_id)->delete();
            
            $pax_with_baggage = []; 
            foreach ($request->passengers as $paxData) {
                $new_pax = Invoice_detail::create([
                    'invoice_id'   => $invoice->id,
                    'airline_id'   => $request->airline_id,
                    'booking_code' => strtoupper($request->pnr),
                    'name'         => strtoupper($paxData['name']),
                    'genre'        => $paxData['title'],
                    'ticket_no'    => $paxData['ticket_num'] ?? '-',
                    'price'        => $price_per_pax,    
                    'pax_paid'     => $pax_paid_per_pax, 
                    'nta'          => $nta_per_pax,      
                    'profit'       => $profit_per_pax,   
                    'discount'     => $discount_per_pax,
                    'depart_date'  => $request->dep_out,
                    'return_date'  => $request->dep_in,
                    'airlines_no'  => $request->flight_out,
                    'class'        => $request->class,
                    'route'        => $request->route_out . ($request->route_in ? ' - ' . $request->route_in : ''),
                ]);
    
                if (isset($paxData['has_baggage']) && $paxData['has_baggage'] == "1") {
                    $pax_with_baggage[] = $new_pax->id;
                }
            }
    
            if ($baggage_total > 0 && count($pax_with_baggage) > 0) {
                $baggage_per_selected_pax = floor($baggage_total / count($pax_with_baggage));
                $total_kg = (int) $request->baggage_kg; 
                $kg_per_selected_pax = $total_kg > 0 ? floor($total_kg / count($pax_with_baggage)) : 0;
            
                foreach ($pax_with_baggage as $pax_id) {
                    Invoice_detail::create([
                        'invoice_id'   => $invoice->id,
                        'airline_id'   => $request->airline_id,
                        'booking_code' => strtoupper($request->pnr),
                        'name'         => 'ADD ON BAGGAGE', 
                        'genre'        => '-',
                        'ticket_no'    => $pax_id, 
                        'price'        => $baggage_per_selected_pax, 
                        'pax_paid'     => $baggage_per_selected_pax,
                        'nta'          => 0,
                        'profit'       => $baggage_per_selected_pax,
                        'class'        => 'BAGASI_ONLY', 
                        'route'        => $kg_per_selected_pax . ' KG',
                        'airlines_no'  => $request->flight_out, 
                        'depart_date'  => $request->dep_out,
                        'return_date'  => $request->dep_in,
                    ]);
                }
            }
    
            DB::commit();
            return redirect()->route('ticket.index')->with('success', 'Berhasil Update!');
        } catch (\Exception $e) {
            DB::rollback();
            return "Gagal Update: " . $e->getMessage();
        }
    }

    public function print($id)
    {
        $ticket = Ticket::with(['airline', 'invoice.customer'])->findOrFail($id);
        
        // AMBIL DARI DATABASE
        $free_baggage = $ticket->free_baggage;
    
        $passengers = Invoice_detail::where('invoice_id', $ticket->invoice_id)
                                    ->where('class', '!=', 'BAGASI_ONLY')
                                    ->get();
        
        $ticketNoGlobal = 'TCKT' . $ticket->created_at->format('Ymd') . str_pad($ticket->id, 3, '0', STR_PAD_LEFT);
        
        $pdf = PDF::loadView('ticket.print', compact('ticket', 'passengers', 'ticketNoGlobal', 'free_baggage'));
        return $pdf->setPaper('A4', 'portrait')->stream('E-Ticket.pdf');
    }

    public function getPassengers($id)
    {
        $ticket = Ticket::find($id);
        if (!$ticket) {
            return response()->json([]);
        }
        $passengers = \App\Models\Invoice_detail::where('invoice_id', $ticket->invoice_id)->get();
        return response()->json($passengers);
    }

    public function printSplit($ticket_id, $passenger_id)
    {
        $ticket = Ticket::with(['airline', 'invoice.customer'])->findOrFail($ticket_id);
        $pax = Invoice_detail::where('id', $passenger_id)->firstOrFail();
        
        // 1. Ambil data bagasi BERBAYAR (Add-on) khusus untuk penumpang ini
        $baggage_pax = Invoice_detail::where('invoice_id', $ticket->invoice_id)
                        ->where('class', 'BAGASI_ONLY')
                        ->where('ticket_no', $passenger_id)
                        ->first();
    
        $baggage_price_pax = $baggage_pax ? $baggage_pax->price : 0;
        $baggage_kg_pax = $baggage_pax ? (int) filter_var($baggage_pax->route, FILTER_SANITIZE_NUMBER_INT) : 0;
    
        // 2. LOGIKA FREE BAGGAGE (Bagasi Gratis)
        // Kita ambil utuh dari database, tidak dibagi-bagi.
        // Variabel ini yang akan dibaca oleh Blade PDF
        $free_baggage = $ticket->free_baggage ?? 0;
    
        // 3. Hitung jatah harga tiket per orang
        $pax_count = Invoice_detail::where('invoice_id', $ticket->invoice_id)
                    ->where('class', '!=', 'BAGASI_ONLY')
                    ->count();
        $pax_count = $pax_count > 0 ? $pax_count : 1;
    
        // Set data ke object ticket untuk kebutuhan tampilan layout PDF
        $ticket->basic_fare = floor($ticket->basic_fare / $pax_count);
        $ticket->baggage_price = $baggage_price_pax;
        $ticket->baggage_kg = $baggage_kg_pax; 
        
        $ticket->total_publish = $pax->pax_paid + $baggage_price_pax;
        $ticket->total_tax = $ticket->total_publish - $ticket->basic_fare - $ticket->baggage_price;
        $ticket->fee = 0; 
    
        $passengers = collect([$pax]);
        $ticketNoGlobal = 'TCKT' . $ticket->created_at->format('Ymd') . str_pad($ticket->id, 3, '0', STR_PAD_LEFT);
    
        // 4. Kirim variabel free_baggage ke view
        $pdf = PDF::loadView('ticket.print', compact('ticket', 'passengers', 'ticketNoGlobal', 'baggage_pax', 'free_baggage'));
        return $pdf->setPaper('A4', 'portrait')->stream('Ticket_' . $pax->name . '.pdf');
    }
}