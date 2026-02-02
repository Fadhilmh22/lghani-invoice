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
        // 1. Mulai Query dengan Eager Loading (airline, customer) 
        // DAN hitung jumlah pax melalui relasi 'details' yang difilter (tanpa bagasi)
        $query = Ticket::with(['airline', 'invoice.customer'])
                 ->withCount(['details as total_pax' => function($q) {
                     $q->where('class', '!=', 'BAGASI_ONLY');
                 }]);
    
        // 2. Logika Search (PNR atau Nama Booker)
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            
            $query->where(function($q) use ($search) {
                // Cari berdasarkan PNR (Booking Code)
                $q->where('booking_code', 'like', '%' . $search . '%')
                  // Cari berdasarkan ID Tiket
                  ->orWhere('id', 'like', '%' . $search . '%')
                  // Cari berdasarkan Nama Booker (Customer) di tabel Invoices
                  ->orWhereHas('invoice.customer', function($queryCustomer) use ($search) {
                      $queryCustomer->where('booker', 'like', '%' . $search . '%');
                  });
            });
        }
    
        // 3. Urutkan dari yang terbaru dan Pagination
        $tickets = $query->orderBy('id', 'desc')
                         ->paginate(10)
                         ->appends(request()->all());
    
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
    
            // 1. Bersihkan Inputan Harga
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
    
            // 2. Hitung Rata-rata per Pax
            $pax_paid_per_pax = floor($pax_paid_total / $pax_count);
            $price_per_pax    = floor($price_total / $pax_count); 
            $nta_per_pax      = floor($nta_total / $pax_count);
            $discount_per_pax = floor($discount_total / $pax_count);
            $profit_per_pax   = floor(($price_total - $nta_total) / $pax_count);
    
            // 3. Simpan ke tabel Invoices
            $invoice = new Invoice();
            $invoice->invoiceno = 'LGT' . date('YmdHis');
            $invoice->customer_id = $request->customer_id;
            $invoice->total = $pax_paid_total - $discount_total;
            $invoice->edited = auth()->user()->name ?? 'Admin'; 
            $invoice->status_pembayaran = 'Belum Lunas';
            $invoice->save();
    
            // 4. Simpan ke tabel Tickets
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
            $ticket->free_baggage   = $request->free_baggage ?? 0;
            $ticket->total_publish  = $publish_total;
            $ticket->total_profit   = $price_total - $nta_total;
            $ticket->save();
    
            // 5. Simpan Data Penumpang ke Invoice_detail
            $pax_with_baggage = []; 
            foreach ($request->passengers as $paxData) {
                $new_pax = Invoice_detail::create([
                    'invoice_id'   => $invoice->id,
                    'ticket_id'    => $ticket->id, // <--- TAMBAHKAN INI (Kunci Pemisah)
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
    
            // 6. Simpan Data Bagasi (Jika Ada)
            if ($baggage_total > 0 && count($pax_with_baggage) > 0) {
                $baggage_per_pax = floor($baggage_total / count($pax_with_baggage));
                $kg_per_pax      = $total_kg > 0 ? floor($total_kg / count($pax_with_baggage)) : 0;
    
                foreach ($pax_with_baggage as $pax_id) {
                    Invoice_detail::create([
                        'invoice_id'   => $invoice->id,
                        'ticket_id'    => $ticket->id, // <--- TAMBAHKAN INI JUGA
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
        
        // Ambil penumpang yang sudah terkunci ke ticket_id ini
        $passengers = Invoice_detail::where('ticket_id', $id)
                                    ->where('class', '!=', 'BAGASI_ONLY')
                                    ->get();
    
        // JIKA KOSONG (untuk data lama yang belum punya ticket_id)
        if ($passengers->isEmpty()) {
            $passengers = Invoice_detail::where('invoice_id', $ticket->invoice_id)
                                        ->where('booking_code', $ticket->booking_code)
                                        ->where('class', '!=', 'BAGASI_ONLY')
                                        ->get();
        }
    
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
    
            // 1. Update data Tiket
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
                'free_baggage'  => $request->free_baggage ?? 0,
                'total_publish' => $clean($request->publish_price),
                'total_profit'  => $price_total - $nta_total 
            ]);
    
            // 2. Update Invoice Induk
            $invoice = Invoice::findOrFail($ticket->invoice_id);
            
            // Hitung ulang total invoice berdasarkan gabungan semua tiket di dalamnya
            $totalInvoiceBaru = Ticket::where('invoice_id', $invoice->id)->sum('total_publish');
    
            $invoice->update([
                'customer_id' => $request->customer_id,
                'total'       => $totalInvoiceBaru, // Update total invoice gabungan
                'edited'      => auth()->user()->name ?? 'Admin'
            ]);
    
            // 3. PENTING: LOGIKA ANTI-DUPLICATE (Hapus data lama milik tiket ini saja)
            // Kita hapus berdasarkan ticket_id, ATAU jika ticket_id masih kosong (data lama), 
            // kita hapus berdasarkan PNR lama tiket ini di invoice terkait.
            Invoice_detail::where('ticket_id', $id)
                ->orWhere(function($query) use ($ticket) {
                    $query->where('invoice_id', $ticket->invoice_id)
                          ->where('booking_code', $ticket->getOriginal('booking_code'));
                })->delete();
            
            $pax_with_baggage = []; 
            foreach ($request->passengers as $paxData) {
                $new_pax = Invoice_detail::create([
                    'invoice_id'   => $invoice->id,
                    'ticket_id'    => $ticket->id, // <--- KUNCI PEMISAH
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
    
            // 4. Update Data Bagasi
            if ($baggage_total > 0 && count($pax_with_baggage) > 0) {
                $baggage_per_selected_pax = floor($baggage_total / count($pax_with_baggage));
                $total_kg = (int) $request->baggage_kg; 
                $kg_per_selected_pax = $total_kg > 0 ? floor($total_kg / count($pax_with_baggage)) : 0;
            
                foreach ($pax_with_baggage as $pax_id) {
                    Invoice_detail::create([
                        'invoice_id'   => $invoice->id,
                        'ticket_id'    => $ticket->id, // <--- KUNCI PEMISAH BAGASI
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
        // 1. Ambil data tiket beserta relasi airline dan customer
        $ticket = Ticket::with(['airline', 'invoice.customer'])->findOrFail($id);
        
        // 2. AMBIL PENUMPANG - Kunci Utama: Filter berdasarkan ticket_id
        // Supaya penumpang dari tiket lain (meskipun satu invoice) tidak ikut muncul
        $passengers = Invoice_detail::where('ticket_id', $id)
                                    ->where('class', '!=', 'BAGASI_ONLY')
                                    ->get();
    
        // 3. Logika Cadangan (Fallback)
        // Jika data lama belum ada ticket_id, kita cari berdasarkan PNR (booking_code) di invoice tersebut
        if ($passengers->isEmpty()) {
            $passengers = Invoice_detail::where('invoice_id', $ticket->invoice_id)
                                        ->where('booking_code', $ticket->booking_code)
                                        ->where('class', '!=', 'BAGASI_ONLY')
                                        ->get();
        }
    
        // 4. Ambil data bagasi gratis dari tiket
        $free_baggage = $ticket->free_baggage;
    
        // 5. Generate Nomor Tiket Global (Format: TCKT + Tanggal + ID)
        $ticketNoGlobal = 'TCKT' . $ticket->created_at->format('Ymd') . str_pad($ticket->id, 3, '0', STR_PAD_LEFT);
        
        // 6. Kirim data ke View dan Generate PDF
        $pdf = PDF::loadView('ticket.print', compact('ticket', 'passengers', 'ticketNoGlobal', 'free_baggage'));
        
        return $pdf->setPaper('A4', 'portrait')->stream('E-Ticket-' . $ticket->booking_code . '.pdf');
    }

    public function getPassengers($id)
    {
        $ticket = Ticket::find($id);
        if (!$ticket) {
            return response()->json([]);
        }
    
        // Filter berdasarkan ticket_id agar hanya penumpang di tiket ini yang muncul
        // Dan class != BAGASI_ONLY supaya baris bagasi tidak muncul sebagai nama orang
        $passengers = \App\Models\Invoice_detail::where('ticket_id', $id)
                        ->where('class', '!=', 'BAGASI_ONLY')
                        ->get();
    
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

    public function bulkInvoice(Request $request)
    {
        $ticketIds = $request->ticket_ids;
    
        if (!$ticketIds || count($ticketIds) < 2) {
            return back()->with('error', 'Pilih minimal 2 tiket untuk digabungkan.');
        }
    
        DB::beginTransaction();
        try {
            // 1. Ambil data semua tiket yang dipilih
            $selectedTickets = \App\Models\Ticket::whereIn('id', $ticketIds)->get();
    
            // 2. Tentukan Invoice Induk
            // Kita cari: Apakah ada salah satu tiket yang sudah punya invoice lama?
            // Jika tidak ada, pakai invoice dari tiket pertama yang dipilih.
            $mainTicket = $selectedTickets->first(); 
            $mainInvoiceId = $mainTicket->invoice_id;
    
            foreach ($selectedTickets as $ticket) {
                // Jika tiket yang sedang di-loop punya invoice berbeda dari induk
                if ($ticket->invoice_id != $mainInvoiceId) {
                    $oldInvoiceId = $ticket->invoice_id;
                    
                    // Pindahkan Tiket ke Invoice Induk
                    $ticket->update(['invoice_id' => $mainInvoiceId]);
                    
                    // Pindahkan semua detail (Pax & Bagasi) ke Invoice Induk
                    \App\Models\Invoice_detail::where('invoice_id', $oldInvoiceId)
                        ->update(['invoice_id' => $mainInvoiceId]);
    
                    // Hapus invoice lama yang sudah ditinggalkan agar tidak duplikat di DB
                    \App\Models\Invoice::where('id', $oldInvoiceId)->delete();
                }
            }
    
            // 3. Update Total Harga di Invoice Induk (Akumulasi Semua Tiket)
            $invoiceInduk = \App\Models\Invoice::find($mainInvoiceId);
            $totalHargaSemuaTiket = \App\Models\Ticket::where('invoice_id', $mainInvoiceId)->sum('total_profit'); 
            // Catatan: Gunakan field yang sesuai untuk total tagihan (misal: total_publish atau total_profit)
            
            $invoiceInduk->update([
                'total' => $totalHargaSemuaTiket
            ]);
    
            DB::commit();
            return redirect()->route('ticket.index')->with('success', 'Tiket berhasil digabungkan ke dalam Invoice!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menggabungkan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $ticket = Ticket::findOrFail($id);
            $invoiceId = $ticket->invoice_id;
            $bookingCode = $ticket->booking_code;

            // 1. Hapus detail penumpang berdasarkan PNR/Booking Code
            // Kita pakai booking_code agar tidak menghapus tiket lain dalam 1 invoice
            Invoice_detail::where('invoice_id', $invoiceId)
                        ->where('booking_code', $bookingCode)
                        ->delete();

            // 2. Hapus data utama tiket
            $ticket->delete();

            // 3. Cek apakah di invoice tersebut masih ada tiket lain?
            $remainingTickets = Ticket::where('invoice_id', $invoiceId)->count();

            if ($remainingTickets == 0) {
                // Jika sudah tidak ada tiket sama sekali, hapus invoicenya juga
                Invoice::where('id', $invoiceId)->delete();
            } else {
                // Jika masih ada tiket lain, hitung ulang total invoice
                $newTotal = Invoice_detail::where('invoice_id', $invoiceId)->sum('pax_paid');
                Invoice::where('id', $invoiceId)->update(['total' => $newTotal]);
            }

            DB::commit();
            return redirect()->route('ticket.index')->with('success', 'Tiket dan data penumpang berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }
    
}