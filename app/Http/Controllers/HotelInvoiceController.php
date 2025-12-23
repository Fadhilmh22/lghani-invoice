<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Airlines;
use App\Models\Customer;
use App\Models\Invoice_detail;
use App\Models\Hotel_invoice;
use App\Models\Hotel_voucher;
use App\Models\Hotel;
use App\Models\Hotel_rate;
use App\Models\Hotel_voucher_room;
use Illuminate\Support\Facades\DB;
use PDF;

class HotelInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
    
        // Query untuk pencarian
        $invoices = Hotel_invoice::with(['customer' => function ($query) use ($search) {
                $query->where('booker', 'like', '%' . $search . '%');
            }])
            ->whereHas('customer', function ($query) use ($search) {
                $query->where('booker', 'like', '%' . $search . '%');
            })
            ->orderBy('created_at', 'DESC')
            ->paginate(10);
    
        return view('hotelinvoice.index', compact('invoices'));
    }

    public function create()
    {
        $customers = Customer::orderBy('created_at', 'DESC')->get();
        return view('hotelinvoice.create', compact('customers'));
    }
    
    public function ubahStatus(Request $request, $id)
    {
        $invoice = Hotel_invoice::findOrFail($id);

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
    
        return redirect()->route('hotelinvoice.index', [
            'page' => $request->page,
            'search' => $request->search,
        ])->with('success', 'Status pembayaran berhasil diubah.');
    }

    public function save(Request $request)
    {
        $this->validate($request, [
            'customer_id' => 'required|exists:customers,id'
        ]);

        $total = 0;
        $date = \Carbon\Carbon::now()->format('Ymd');
        $invoiceno = Hotel_invoice::selectRaw("COALESCE(MAX(CAST(SUBSTRING(invoiceno, 4) AS integer)), " . $date . "000) AS invoiceno")
                    ->where(DB::raw("SUBSTRING(invoiceno, 4, 8)"), $date)
                    ->get()
                    ->toArray();

        $finalinvoiceno = "LGH" . ($invoiceno[0]['invoiceno'] + 1);

        try {
            $invoice = Hotel_invoice::create([
                'invoiceno' => $finalinvoiceno,
                'customer_id' => $request->customer_id,
                'issued_date' => \Carbon\Carbon::now()->format('Y-m-d'),
                'hotel_due_date' => $request->hotel_due_date,
                'payment_date' => $request->payment_date,
                'office_code' => $request->office_code,
                'discount' => $request->discount,
                'issued_by' => auth()->user()->name,
                'total' => $total,
                'status_pembayaran' => 'Belum Lunas',
                'status' => 1
            ]);

            return redirect(route('hotelinvoice.index'));
            // return redirect(url('/hotel-voucher/new?bid=' . $invoice->id));
        } catch(\Exception $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $customers = Customer::orderBy('created_at', 'DESC')->get();
        $invoice = Hotel_invoice::with(['customer'])->find($id);
        return view('hotelinvoice.edit', compact('invoice', 'customers'));
    }

    public function update(Request $request, $id)
    {
        
        $this->validate($request, [
            'customer_id' => 'required|exists:customers,id'
        ]);

        try {
            
            
            $invoice = [
                
                'customer_id' => $request->customer_id,
                'hotel_due_date' => $request->hotel_due_date,
                'payment_date' => $request->payment_date,
                'office_code' => $request->office_code,
                'discount' => $request->discount
            ];
            // $invoice->status = $request->input('status_pembayaran');

            Hotel_invoice::whereId($id)->update($invoice);
            return redirect(route('hotelinvoice.index'))->with(['success' => 'Data Invoice Telah Diubah']);
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $invoice = Hotel_invoice::find($id);
        $invoice->delete();
        return redirect()->back()->with(['success' => '<strong>' . $invoice->invoiceno . '</strong> telah berhasil dihapus']);
    }

    public function generateInvoice($id)
    {
        $invoice = Hotel_invoice::with(['customer'])->find($id);

        $voucher = Hotel_voucher::where('booking_id', $id)->first();
        $hotel = Hotel::find($voucher->hotel_id);
        $dbVoucher = Hotel_voucher_room::with('hotelguest')->where('hotel_voucher_id', $voucher->id)->get()->toArray();
        
        $voucherRoom = [];
        foreach ($dbVoucher as $v) {
            if( !isset($voucherRoom[ $v['room_id'] ]) ) {
                $voucherRoom[ $v['room_id'] ] = array("count" => 0);
            }
            
            $voucherRoom[ $v['room_id'] ]['count']++;
            $voucherRoom[ $v['room_id'] ]['hotelguest'][] = $v['hotelguest'];
        }
        
        $dbRooms = Hotel_rate::where('hotel_id', $voucher->hotel_id)->orderBy('room_code','ASC')->get();
        $rooms = [];

        foreach ($dbRooms as $key => $value) {
            $rooms[ $value->id ] = $value;
        }
        
        $startdate = strtotime($voucher->check_in);
        $enddate = strtotime($voucher->check_out) - 86400; // kurangi 1 hari di tgl checkout
        
        $i = 0;
        $weekDay = 0;
        $weekEnd = 0;
        
        $currDate = 0;
        while( $enddate > $currDate ) {
        	$currDate = $startdate + ( 86400 * $i );
            $w = date("w", $currDate);
            
            if( $w == 5 || $w == 6 ) {
            	$weekEnd++;
            } else {
            	$weekDay++;
            }
            
            $i++;
        }

        $filename = $invoice->invoiceno . "-" . date("dmyHis");
        // $pdf = PDF::loadView('invoice.print', compact('invoice', 'details'))->setPaper('a4', 'portrait');
        // $pdf = PDF::loadView('hotelinvoice.print')->setPaper('a4', 'portrait');
        // return $pdf->download($filename.'-invoice.pdf');
        // $pdf->render();
        // $pdf->stream("dompdf_out.pdf");
        // $pdf->stream();

        // return view('hotelinvoice.print', compact('invoice', 'voucher', 'hotel', 'voucherRoom', 'rooms', 'weekDay', 'weekEnd'));
        $pdf = PDF::loadView('hotelinvoice.print', compact('invoice', 'voucher', 'hotel', 'voucherRoom', 'rooms', 'weekDay', 'weekEnd'))->setPaper('a4', 'portrait');
        return $pdf->download($filename.'-invoice.pdf');
    }
    
    public function generateInvoicedisc($id)
    {
        $invoice = Hotel_invoice::with(['customer'])->find($id);

        $voucher = Hotel_voucher::where('booking_id', $id)->first();
        $hotel = Hotel::find($voucher->hotel_id);
        $dbVoucher = Hotel_voucher_room::with('hotelguest')->where('hotel_voucher_id', $voucher->id)->get()->toArray();
        
        $voucherRoom = [];
        foreach ($dbVoucher as $v) {
            if( !isset($voucherRoom[ $v['room_id'] ]) ) {
                $voucherRoom[ $v['room_id'] ] = array("count" => 0);
            }
            
            $voucherRoom[ $v['room_id'] ]['count']++;
            $voucherRoom[ $v['room_id'] ]['hotelguest'][] = $v['hotelguest'];
        }
        
        $dbRooms = Hotel_rate::where('hotel_id', $voucher->hotel_id)->orderBy('room_code','ASC')->get();
        $rooms = [];

        foreach ($dbRooms as $key => $value) {
            $rooms[ $value->id ] = $value;
        }
        
        $startdate = strtotime($voucher->check_in);
        $enddate = strtotime($voucher->check_out) - 86400; // kurangi 1 hari di tgl checkout
        
        $i = 0;
        $weekDay = 0;
        $weekEnd = 0;
        
        $currDate = 0;
        while( $enddate > $currDate ) {
        	$currDate = $startdate + ( 86400 * $i );
            $w = date("w", $currDate);
            
            if( $w == 5 || $w == 6 ) {
            	$weekEnd++;
            } else {
            	$weekDay++;
            }
            
            $i++;
        }

        $filename = $invoice->invoiceno . "-" . date("dmyHis");
        // $pdf = PDF::loadView('invoice.print', compact('invoice', 'details'))->setPaper('a4', 'portrait');
        // $pdf = PDF::loadView('hotelinvoice.print')->setPaper('a4', 'portrait');
        // return $pdf->download($filename.'-invoice.pdf');
        // $pdf->render();
        // $pdf->stream("dompdf_out.pdf");
        // $pdf->stream();

        // return view('hotelinvoice.print', compact('invoice', 'voucher', 'hotel', 'voucherRoom', 'rooms', 'weekDay', 'weekEnd'));
        $pdf = PDF::loadView('hotelinvoice.printdisc', compact('invoice', 'voucher', 'hotel', 'voucherRoom', 'rooms', 'weekDay', 'weekEnd'))->setPaper('a4', 'portrait');
        return $pdf->download($filename.'-invoice.pdf');
    }
}
