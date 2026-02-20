<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;       
use App\Models\Invoice_detail; 
use App\Models\Customer;
use App\Models\Airlines;
use App\Models\Airport;
use App\Models\Ticket;
use App\Models\AirlineTopup;
use DB;
use PDF; 

class TicketController extends Controller
{
    public function index(Request $request)
    {
        // 1. Mulai Query dengan Eager Loading (airline, customer) 
        $query = Ticket::with(['airline', 'invoice.customer'])
                 ->withCount(['details as total_pax' => function($q) {
                     $q->where('class', '!=', 'BAGASI_ONLY');
                 }]);
    
        // 2. Logika Search (PNR atau Nama Booker)
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            
            $query->where(function($q) use ($search) {
                $q->where('booking_code', 'like', '%' . $search . '%')
                  ->orWhere('id', 'like', '%' . $search . '%')
                  ->orWhereHas('invoice.customer', function($queryCustomer) use ($search) {
                      $queryCustomer->where('booker', 'like', '%' . $search . '%');
                  });
            });
        }
    
        $tickets = $query->orderBy('id', 'desc')
                         ->paginate(10)
                         ->appends(request()->all());
    
        return view('ticket.index', compact('tickets'));
    }

    public function create()
    {
        $customers = Customer::orderBy('booker', 'ASC')->get();
        $airlines = Airlines::orderBy('airlines_name', 'ASC')->get();
        $airports = Airport::orderBy('name', 'ASC')->get();
        return view('ticket.create', compact('customers', 'airlines', 'airports'));
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
    
            // Simpan ke tabel Invoices
            $invoice = new Invoice();
            $date = \Carbon\Carbon::now()->format('Ymd');
            $seqResult = Invoice::selectRaw("COALESCE(MAX(CAST(SUBSTRING(invoiceno, 12) AS INTEGER)), 0) AS maxseq")
                            ->where(DB::raw("SUBSTRING(invoiceno,4,8)"), $date)
                            ->get()
                            ->toArray();

            $nextSeq = (int) $seqResult[0]['maxseq'] + 1;
            $finalinvoiceno = 'LGT' . $date . str_pad($nextSeq, 3, '0', STR_PAD_LEFT);
            $invoice->invoiceno = $finalinvoiceno;
            $invoice->customer_id = $request->customer_id;
            $invoice->total = $pax_paid_total - $discount_total;
            $invoice->edited = auth()->user()->name ?? 'Admin'; 
            $invoice->status_pembayaran = 'Belum Lunas';
            $invoice->save();
    
            // Simpan ke tabel Tickets
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
            $ticket->stop_flight_leg1_out = $request->flight_out ?? null;
            $ticket->stop_time_out  = $request->stop_time_out ?? null;
            $ticket->stop_time_out_arrival  = $request->stop_time_out_arrival ?? null;
            $ticket->stop_time_out_depart   = $request->stop_time_out_depart ?? null;
            $ticket->stop_flight_leg2_out = $request->stop_flight_leg2_out ?? null;
            $ticket->stop_airline_out = $request->stop_airline_out ?? null;
            $ticket->stop_flight_leg1_in  = $request->flight_in ?? null;
            $ticket->stop_time_in   = $request->stop_time_in ?? null;
            $ticket->stop_time_in_arrival  = $request->stop_time_in_arrival ?? null;
            $ticket->stop_time_in_depart   = $request->stop_time_in_depart ?? null;
            $ticket->stop_flight_leg2_in  = $request->stop_flight_leg2_in ?? null;
            $ticket->stop_airline_in = $request->stop_airline_in ?? null;
            $ticket->class          = $request->class;
            $ticket->basic_fare     = $clean($request->basic_fare);
            $ticket->total_tax      = $clean($request->total_tax);
            $ticket->fee            = $clean($request->fee_ticket);
            $ticket->baggage_kg     = $total_kg;
            $ticket->baggage_price  = $baggage_total;
            $ticket->free_baggage   = $request->free_baggage ?? 0;
            $ticket->total_publish  = $publish_total;
            $ticket->total_profit   = $price_total - $nta_total;
            $ticket->nta_total      = $nta_total;
            $ticket->save();
    
            // Simpan Data Penumpang
            $pax_with_baggage = []; 
            foreach ($request->passengers as $paxData) {
                $new_pax = Invoice_detail::create([
                    'invoice_id'   => $invoice->id,
                    'ticket_id'    => $ticket->id,
                    'airline_id'   => $request->airline_id,
                    'booking_code' => strtoupper($request->pnr),
                    'name'         => strtoupper($paxData['name']),
                    'type'         => $paxData['type'] ?? 'Adult',
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
                        'ticket_id'    => $ticket->id,
                        'airline_id'   => $request->airline_id,
                        'booking_code' => strtoupper($request->pnr),
                        'name'         => 'ADD ON BAGGAGE', 
                        'genre'        => '-',
                        'ticket_no'    => $pax_id,
                        'price'        => $baggage_per_pax, 
                        'pax_paid'     => $baggage_per_pax,
                        'nta'          => $baggage_per_pax,
                        'profit'       => 0,
                        'class'        => 'BAGASI_ONLY', 
                        'route'        => $kg_per_pax . ' KG',
                        'airlines_no'  => $request->flight_out, 
                        'depart_date'  => $request->dep_out,
                        'return_date'  => $request->dep_in,
                    ]);
                }
            }
    
// --- POTONG SALDO (SISTEM BYPASS / BISA MINUS) ---
if ($nta_total > 0) {
    $selectedAirline = Airlines::find($request->airline_id);
    $lionGroupCodes = ['JT', 'IU', 'IW', 'ID'];

    // Tentukan target saldo (Lion Group lari ke JT)
    if (in_array(strtoupper($selectedAirline->airlines_code), $lionGroupCodes)) {
        $mainAirline = Airlines::where('airlines_code', 'JT')->first() ?? $selectedAirline;
    } else {
        $mainAirline = $selectedAirline;
    }

    if ($mainAirline) {
        $before = $mainAirline->balance;
        $after  = $before - $nta_total; // Saldo akan otomatis minus jika pengurang > saldo awal
        
        $mainAirline->balance = $after;
        $mainAirline->save();

        // Catat riwayat pemotongan
        AirlineTopup::create([
            'airline_id'      => $selectedAirline->id, 
            'amount'          => -$nta_total,
            'before_balance'  => $before,
            'after_balance'   => $after,
            'user_id'         => auth()->id(),
        ]);
    }
}
// --- END POTONG SALDO ---

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
        $airports = Airport::orderBy('name', 'ASC')->get();
        
        $passengers = Invoice_detail::where('ticket_id', $id)
                                    ->where('class', '!=', 'BAGASI_ONLY')
                                    ->get();
    
        if ($passengers->isEmpty()) {
            $passengers = Invoice_detail::where('invoice_id', $ticket->invoice_id)
                                        ->where('booking_code', $ticket->booking_code)
                                        ->where('class', '!=', 'BAGASI_ONLY')
                                        ->get();
        }
    
        return view('ticket.edit', compact('ticket', 'customers', 'airlines', 'airports', 'passengers'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $ticket = Ticket::findOrFail($id);
            $clean = function($val) { return (int) preg_replace('/[^0-9]/', '', $val); };
    
            $oldAirlineId = $ticket->airline_id;
            $oldNtaTotal  = $ticket->nta_total ?? 0;
            
            $publish_total  = $clean($request->publish_price);
            $discount_total = $clean($request->discount);
            $nta_total      = $clean($request->nta_price);
            $pax_paid_total = $clean($request->pax_paid); 
            $price_total    = $clean($request->price); 
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
                'flight_out'    => $request->flight_out,
                'flight_in'     => $request->flight_in,
                'route_out'     => $request->route_out,
                'route_in'      => $request->route_in,
                'dep_time_out'  => $request->dep_out,
                'dep_time_in'   => $request->dep_in,
                'arr_time_out'  => $request->arr_out,
                'arr_time_in'   => $request->arr_in,
                'class'         => $request->class,
                'basic_fare'    => $clean($request->basic_fare), 
                'total_tax'     => $clean($request->total_tax),
                'fee'           => $clean($request->fee_ticket),
                'baggage_kg'    => $request->baggage_kg,    
                'baggage_price' => $baggage_total, 
                'total_publish' => $publish_total,
                'total_profit'  => $price_total - $nta_total,
                'nta_total'     => $nta_total,
            ]);
    
            $invoice = Invoice::findOrFail($ticket->invoice_id);
            $totalInvoiceBaru = Ticket::where('invoice_id', $invoice->id)->sum('total_publish');
            $invoice->update([
                'customer_id' => $request->customer_id,
                'total'       => $totalInvoiceBaru,
                'edited'      => auth()->user()->name ?? 'Admin'
            ]);
    
            Invoice_detail::where('ticket_id', $id)->delete();
            
            $pax_with_baggage = []; 
            foreach ($request->passengers as $paxData) {
                $new_pax = Invoice_detail::create([
                    'invoice_id'   => $invoice->id,
                    'ticket_id'    => $ticket->id,
                    'airline_id'   => $request->airline_id,
                    'booking_code' => strtoupper($request->pnr),
                    'name'         => strtoupper($paxData['name']),
                    'type'         => $paxData['type'] ?? 'Adult',
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
                if (isset($paxData['has_baggage']) && $paxData['has_baggage'] == "1") { $pax_with_baggage[] = $new_pax->id; }
            }
    
            if ($baggage_total > 0 && count($pax_with_baggage) > 0) {
                $baggage_per_pax = floor($baggage_total / count($pax_with_baggage));
                foreach ($pax_with_baggage as $pax_id) {
                    Invoice_detail::create([
                        'invoice_id'   => $invoice->id,
                        'ticket_id'    => $ticket->id,
                        'airline_id'   => $request->airline_id,
                        'booking_code' => strtoupper($request->pnr),
                        'name'         => 'ADD ON BAGGAGE', 
                        'ticket_no'    => $pax_id,
                        'price'        => $baggage_per_pax, 
                        'pax_paid'     => $baggage_per_pax,
                        'nta'          => $baggage_per_pax,
                        'class'        => 'BAGASI_ONLY', 
                    ]);
                }
            }
    
            // LOGIKA ADJUST SALDO LION GROUP SAAT UPDATE
            $lionGroupCodes = ['JT', 'IU', 'IW', 'ID'];
            
            // 1. Kembalikan Saldo Lama
            $oldAir = Airlines::find($oldAirlineId);
            $mainOld = (in_array(strtoupper($oldAir->airlines_code), $lionGroupCodes)) ? Airlines::where('airlines_code', 'JT')->first() : $oldAir;
            if ($mainOld) {
                $mainOld->balance += $oldNtaTotal;
                $mainOld->save();
            }

            // 2. Potong Saldo Baru
            $newAir = Airlines::find($request->airline_id);
            $mainNew = (in_array(strtoupper($newAir->airlines_code), $lionGroupCodes)) ? Airlines::where('airlines_code', 'JT')->first() : $newAir;
            if ($mainNew) {
                $mainNew->balance -= $nta_total;
                $mainNew->save();
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
        $passengers = Invoice_detail::where('ticket_id', $id)->where('class', '!=', 'BAGASI_ONLY')->get();
        if ($passengers->isEmpty()) {
            $passengers = Invoice_detail::where('invoice_id', $ticket->invoice_id)->where('booking_code', $ticket->booking_code)->where('class', '!=', 'BAGASI_ONLY')->get();
        }
        $free_baggage = $ticket->free_baggage;
        $ticketNoGlobal = 'TCKT' . $ticket->created_at->format('Ymd') . str_pad($ticket->id, 3, '0', STR_PAD_LEFT);
        $airports = Airport::pluck('name', 'code');
        
        foreach ($passengers as $p) {
            $b = Invoice_detail::where('invoice_id', $ticket->invoice_id)->where('class', 'BAGASI_ONLY')->where('ticket_no', $p->id)->first();
            $freeKg = (int) ($ticket->free_baggage ?? 0);
            if ($b) {
                $addonKg = $b->route ? (int) filter_var($b->route, FILTER_SANITIZE_NUMBER_INT) : 0;
                $p->baggage_price = $b->price;
                $p->baggage_kg = $freeKg + $addonKg;
            } else {
                $p->baggage_price = 0;
                $p->baggage_kg = $freeKg;
            }
        }
        
        $pdf = PDF::loadView('ticket.print', compact('ticket', 'passengers', 'ticketNoGlobal', 'free_baggage', 'airports'));
        return $pdf->setPaper('A4', 'portrait')->stream('E-Ticket-' . $ticket->booking_code . '.pdf');
    }

    public function getPassengers($id)
    {
        $passengers = \App\Models\Invoice_detail::where('ticket_id', $id)->where('class', '!=', 'BAGASI_ONLY')->get();
        return response()->json($passengers);
    }

    public function printSplit($ticket_id, $passenger_id)
    {
        $ticket = Ticket::with(['airline', 'invoice.customer'])->findOrFail($ticket_id);
        $pax = Invoice_detail::where('id', $passenger_id)->firstOrFail();
        $baggage_pax = Invoice_detail::where('invoice_id', $ticket->invoice_id)->where('class', 'BAGASI_ONLY')->where('ticket_no', $passenger_id)->first();
        $free_baggage = $ticket->free_baggage ?? 0;
    
        $pax_count = Invoice_detail::where('ticket_id', $ticket_id)->where('class', '!=', 'BAGASI_ONLY')->count() ?: 1;

        $ticket->basic_fare = floor($ticket->basic_fare / $pax_count);
        $ticket->total_tax = floor($ticket->total_tax / $pax_count);
        $ticket->fee = floor(($ticket->fee ?? 0) / $pax_count);
        $ticket->total_publish = $ticket->basic_fare + $ticket->total_tax + $ticket->fee + ($baggage_pax ? $baggage_pax->price : 0);

        $passengers = collect([$pax]);
        $ticketNoGlobal = 'TCKT' . $ticket->created_at->format('Ymd') . str_pad($ticket->id, 3, '0', STR_PAD_LEFT);
        $airports = Airport::pluck('name', 'code');
    
        $pdf = PDF::loadView('ticket.print', compact('ticket', 'passengers', 'ticketNoGlobal', 'baggage_pax', 'free_baggage', 'airports'));
        return $pdf->setPaper('A4', 'portrait')->stream('Ticket_' . $pax->name . '.pdf');
    }

    public function bulkInvoice(Request $request)
    {
        $ticketIds = $request->ticket_ids;
        if (!$ticketIds || count($ticketIds) < 2) { return back()->with('error', 'Pilih minimal 2 tiket.'); }
    
        DB::beginTransaction();
        try {
            $selectedTickets = \App\Models\Ticket::whereIn('id', $ticketIds)->get();
            $mainInvoiceId = $selectedTickets->first()->invoice_id;
    
            foreach ($selectedTickets as $ticket) {
                if ($ticket->invoice_id != $mainInvoiceId) {
                    $oldId = $ticket->invoice_id;
                    $ticket->update(['invoice_id' => $mainInvoiceId]);
                    \App\Models\Invoice_detail::where('invoice_id', $oldId)->update(['invoice_id' => $mainInvoiceId]);
                    \App\Models\Invoice::where('id', $oldId)->delete();
                }
            }
            $invoiceInduk = \App\Models\Invoice::find($mainInvoiceId);
            $invoiceInduk->update(['total' => \App\Models\Invoice_detail::where('invoice_id', $mainInvoiceId)->sum('pax_paid')]);
            DB::commit();
            return redirect()->route('ticket.index')->with('success', 'Tiket digabungkan!');
        } catch (\Exception $e) { DB::rollback(); return back()->with('error', $e->getMessage()); }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $ticket = Ticket::findOrFail($id);
            
            // LOGIKA KEMBALIKAN SALDO LION GROUP SAAT HAPUS
            if ($ticket->nta_total > 0) {
                $air = Airlines::find($ticket->airline_id);
                $lionGroupCodes = ['JT', 'IU', 'IW', 'ID'];
                $mainAir = (in_array(strtoupper($air->airlines_code), $lionGroupCodes)) ? Airlines::where('airlines_code', 'JT')->first() : $air;

                if ($mainAir) {
                    $before = $mainAir->balance;
                    $after  = $before + $ticket->nta_total;
                    $mainAir->balance = $after;
                    $mainAir->save();
                    
                    AirlineTopup::create([
                        'airline_id'     => $ticket->airline_id,
                        'amount'         => $ticket->nta_total,
                        'before_balance' => $before,
                        'after_balance'  => $after,
                        'user_id'        => auth()->id(),
                    ]);
                }
            }

            Invoice_detail::where('ticket_id', $id)->delete();
            $invoiceId = $ticket->invoice_id;
            $ticket->delete();

            if (Ticket::where('invoice_id', $invoiceId)->count() == 0) {
                Invoice::where('id', $invoiceId)->delete();
            } else {
                Invoice::where('id', $invoiceId)->update(['total' => Invoice_detail::where('invoice_id', $invoiceId)->sum('pax_paid')]);
            }

            DB::commit();
            return redirect()->route('ticket.index')->with('success', 'Berhasil dihapus!');
        } catch (\Exception $e) { DB::rollback(); return back()->with('error', $e->getMessage()); }
    }
}