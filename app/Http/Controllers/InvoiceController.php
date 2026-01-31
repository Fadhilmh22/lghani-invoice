<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Airlines;
use App\Models\Customer;
use App\Models\Invoice_detail;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PDF;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
    
        // Hapus filter has('detail') agar invoice gabungan baru bisa muncul
        $invoice = Invoice::with(['customer', 'tickets']) // pastikan relasi tickets ada di model
          ->whereHas('customer', function ($query) use ($search) {
              $query->where('booker', 'like', '%' . $search . '%')
              ->orWhere('company', 'like', '%' . $search . '%');
          })
          ->orderBy('created_at', 'DESC')
          ->paginate(10);    
    
        return view('invoice.index', compact('invoice'));
    }

    public function create()
    {
        $customers = Customer::orderBy('booker', 'ASC')->get();
        return view('invoice.create', compact('customers'));
    }
    
    // PERBAIKAN: Menambahkan Request $request dan logika respons JSON untuk AJAX
    public function ubahStatus(Request $request, $id) 
    {
        $invoice = Invoice::findOrFail($id);

        // Periksa status pembayaran saat ini dan ubah sesuai kebutuhan
        if ($invoice->status_pembayaran == 'Belum Lunas') {
            $invoice->status_pembayaran = 'Sudah Lunas';
        } else {
            $invoice->status_pembayaran = 'Belum Lunas';
        }
    
        $invoice->save();
        
        // JIKA INI ADALAH PERMINTAAN AJAX, KEMBALIKAN JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'new_status' => $invoice->status_pembayaran,
                'message' => 'Status pembayaran berhasil diubah menjadi ' . $invoice->status_pembayaran
            ]);
        }

        // Jika bukan AJAX (fallback, meski di Blade sudah dicegah), lakukan redirect
        return redirect()->route('invoice.index');
    }


    public function save(Request $request)
    {
        $this->validate($request, [
            'customer_id' => 'required|exists:customers,id'
        ]);

        $total = 0;
        $profit = 0;

        $date = \Carbon\Carbon::now()->format('Ymd');
        $invoiceno = Invoice::selectRaw("COALESCE(MAX(CAST(SUBSTRING(invoiceno, 4) AS integer)), " . $date . "000) AS invoiceno")
                            ->where(DB::raw("SUBSTRING(invoiceno, 4, 8)"), $date)
                            ->get()
                            ->toArray();

        $finalinvoiceno = "LGT" . ($invoiceno[0]['invoiceno'] + 1);

        try {
            $invoice = Invoice::create([
                'customer_id' => $request->customer_id,
                'invoiceno' => $finalinvoiceno,
                'edited' => auth()->user()->name,
                'status' => 1,
                'status_pembayaran' => 'Belum Lunas',
                'total' => $total,
            ]);
            
            // Logika ini tampaknya salah. Seharusnya 'Sudah Lunas' jika sudah lewat jatuh tempo, 
            // BUKAN jika sekarang > jatuh tempo (mungkin intended logic adalah set default)
            $dueDate = $invoice->created_at->addDays(14);
            if (now() > $dueDate) {
                // Seharusnya mungkin set status LUNAS jika jatuh tempo terlewati
                // Tapi ini ditempatkan setelah invoice dibuat, yang aneh. Dibiarkan sesuai kode user.
                // $invoice->status_pembayaran = 'Sudah Lunas'; 
                // $invoice->save();
            }

            return redirect(route('invoice.edit', ['id' => $invoice->id]));
        } catch(\Exception $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }

public function edit($id)
{
    $invoice = Invoice::with(['customer', 'detail'])->find($id);

    if (!$invoice) {
        return redirect()->route('invoice.index')->with('error', 'Invoice tidak ditemukan');
    }

    $airlines = Airlines::orderBy('airlines_code', 'ASC')->get();
    $comboAirline = [];
    foreach ($airlines as $airline) {
        $comboAirline[$airline->id] = ['airlines_code' => $airline->airlines_code];
    }

    return view('invoice.edit', compact('invoice', 'airlines', 'comboAirline'));
}


    public function update(Request $request, $id)
{
    $this->validate($request, [
        'genre' => 'required|string',
        'name' => 'required|string',
        'booking_code' => 'required|string',
        'airline_id' => 'required|exists:airlines,id',
        'airlines_no' => 'required|string',
        'class' => 'required|string',
        'ticket_no' => 'required|string',
        'route' => 'required|string',
        'depart_date' => 'required|date',
        'return_date' => 'nullable|date|after_or_equal:depart_date',
        'pax_paid' => 'required|integer',
        'price' => 'required|integer',
        'discount' => 'required|integer'
    ]);

    try {
        $invoice = Invoice::find($id);

        $invoice->status = $request->input('status_pembayaran');

        Invoice::whereId($id)->update([
            "total" => $invoice->total + $request->pax_paid,
        ]);

        $profit = ($request->price - $request->discount - $request->nta);

        // Validasi tanggal depart_date dan return_date
        $departDate = date("Y-m-d", strtotime($request->depart_date));
        $returnDate = ($request->return_date) ? date("Y-m-d", strtotime($request->return_date)) : null;

        if ($returnDate && $returnDate < $departDate) {
            return redirect()->back()->with(['error' => 'Tanggal kembali harus setelah tanggal keberangkatan']);
        }

        Invoice_detail::create([
            'invoice_id' => $id,
            'genre' =>  $request->genre,
            'name' => $request->name,
            'booking_code' => $request->booking_code,
            'airline_id' => $request->airline_id,
            'airlines_no' => $request->airlines_no,
            'class' => $request->class,
            'ticket_no' => $request->ticket_no,
            'route' => $request->route,
            'depart_date' => date("Y-m-d", strtotime($request->depart_date)),
            'return_date' => $returnDate,
            'pax_paid' => $request->pax_paid,
            'price' => $request->price,
            'discount' => $request->discount,
            'nta' => $request->nta,
            'profit' => $profit
        ]);

        if ($request->has('redirect')) {
            return redirect()->route('invoice.index')->with(['success' => 'Data Telah disimpan']);
        } else {
            $message = 'Berhasil!';
        }

        return redirect()->route('invoice.edit', ['id' => $id])->with('success', $message);
    } catch (\Exception $e) {
        return redirect()->back()->with('error', $e->getMessage());
    }
}

   public function deleteProduct($id)
{
    $detail = Invoice_detail::find($id);

    $invoice = Invoice::find($detail->invoice_id);
    Invoice::whereId($detail->invoice_id)->update([
        "total" => $invoice->total - $detail->pax_paid,
    ]);
    
    $detail->delete();
    return redirect()->route('invoice.edit', ['id' => $detail->invoice_id])->with(['success' => 'Data telah dihapus']);
}

    public function destroy($id)
    {
        $invoice = Invoice::find($id);
        $invoice->delete();
        return redirect()->back()->with(['success' => 'Invoice telah dihapus']);
    }

    public function generateInvoice($id)
    {
        $invoice = Invoice::with(['customer'])->find($id);

        $field = ["invoice_details.*", "invoices.invoiceno", "invoices.status", "customers.booker", "customers.payment", "airlines.airlines_code", "airlines.airlines_name"];

        $details = DB::table('invoice_details')->select($field)
                    ->join('invoices', 'invoice_details.invoice_id', '=', 'invoices.id')
                    ->join('customers', 'invoices.customer_id', '=', 'customers.id')
                    ->join('airlines', 'invoice_details.airline_id', '=', 'airlines.id')
                    ->where('invoice_details.invoice_id', $id)
                    ->get();

        $filename = $invoice->invoiceno . "-" . date("dmyHis");
        $pdf = PDF::loadView('invoice.print', compact('invoice', 'details'))->setPaper('a4', 'portrait');
        return $pdf->download($filename.'-invoice.pdf');
    }
    
    public function generateInvoicedisc($id)
    {
        set_time_limit(0);
        $invoice = Invoice::with(['customer'])->find($id);

        $field = ["invoice_details.*", "invoices.invoiceno", "invoices.status", "customers.booker", "customers.payment", "airlines.airlines_code", "airlines.airlines_name"];

        $details = DB::table('invoice_details')->select($field)
                    ->join('invoices', 'invoice_details.invoice_id', '=', 'invoices.id')
                    ->join('customers', 'invoices.customer_id', '=', 'customers.id')
                    ->join('airlines', 'invoice_details.airline_id', '=', 'airlines.id')
                    ->where('invoice_details.invoice_id', $id)
                    ->get();

        $filename = $invoice->invoiceno . "-" . date("dmyHis");
        $pdf = PDF::loadView('invoice.printdisc', compact('invoice', 'details'))->setPaper('a4', 'portrait');
        return $pdf->download($filename.'-invoice.pdf');
    }
}