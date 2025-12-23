<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hotel;
use App\Models\Hotel_voucher;
use App\Models\Hotel_voucher_room;
use App\Models\Hotel_voucher_guest;
use App\Models\Hotel_rate;
use App\Models\Hotel_invoice;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use App\Models\Invoice;
use PDF;

class HotelVoucherController extends Controller
{
    private $currencys = ["idr" => "IDR"];
    private $dayName = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];

    public function index()
    {
        $hotelVouchers = Hotel_voucher::orderBy('created_at', 'DESC')->paginate(10);
        return view('hotelvoucher.index', compact('hotelVouchers'));
    }

    public function create()
    {
        $hotels = Hotel::orderBy('hotel_name','ASC')->get();
        $customers = Customer::orderBy('booker', 'ASC')->get();
        $currencys = $this->currencys;
        $booking_id = isset($_GET['bid']) && !empty($_GET['bid']) && is_numeric($_GET['bid']) ? $_GET['bid'] : "";
        $invoice = [];

        if( !empty($booking_id) ) {
            $invoice = Hotel_invoice::find($booking_id);
        }

        return view('hotelvoucher.add', compact('hotels', 'currencys', 'booking_id', 'customers', 'invoice'));
    }

    public function save(Request $request)
    {
        $this->validate($request, [
            'currency' => 'required|nullable|string',
            'booker' => 'required|nullable|string',
            'booker_agent' => 'nullable|string',
            'no_booker_agent' => 'nullable|string',
            'nationality' => 'required|nullable|string',
            'attention' => 'nullable|string',
            'hotel_id' => 'required|nullable|integer',
            'check_in' => 'required|date',
            'check_out' => 'required|date',
            'confirm_by' => 'required|nullable|string',
            'remark' => 'nullable|string',
            'rsvn_and_payment_by' => 'required|nullable|string',
            'count_type_room' => 'required|nullable|integer',
        ]);

        $hotelCode = $request->hotel_code;
        $bookingId = $request->booking_id;
        $bookingNo = "";

        $date = \Carbon\Carbon::now()->format('Ymd');

        $finalVoucherNo = "";

        $voucher_no = Hotel_voucher::selectRaw("COALESCE(MAX(CAST(SUBSTRING(voucher_no, " . (strlen($hotelCode) + 1) . ") AS integer)), " . $date . "000) AS voucher_no")
                    ->where(DB::raw("SUBSTRING(voucher_no, " . (strlen($hotelCode) + 1) . ", 8)"), $date)
                    ->get()
                    ->toArray();
        $finalVoucherNo = $hotelCode . ($voucher_no[0]['voucher_no'] + 1);

        try {
            $updateVoucherNo = false;
            if( !empty($bookingId) ) {
                $hotelInvoice = Hotel_invoice::find($bookingId);

                if( !empty($hotelInvoice) ) {
                    $bookingNo = $hotelInvoice->invoiceno;
                    $updateVoucherNo = true;
                }
            }

            if( $updateVoucherNo ) {
                Hotel_invoice::whereId($bookingId)->update([
                    'voucherno' => $finalVoucherNo,
                    'customer_id' => $request->customer_id,
                    'hotel_due_date' => $request->hotel_due_date,
                    'payment_date' => $request->payment_date,
                    'office_code' => $request->office_code,
                ]);
            } else {
                $date = \Carbon\Carbon::now()->format('Ymd');
                $invoiceno = Hotel_invoice::selectRaw("COALESCE(MAX(CAST(SUBSTRING(invoiceno, 4) AS integer)), " . $date . "000) AS invoiceno")
                    ->where(DB::raw("SUBSTRING(invoiceno, 4, 8)"), $date)
                    ->get()
                    ->toArray();

                 $finalinvoiceno = "LGH" . ($invoiceno[0]['invoiceno'] + 1);

                $invoice = Hotel_invoice::create([
                    'invoiceno' => $finalinvoiceno,
                    'voucherno' => $finalVoucherNo,
                    'customer_id' => $request->customer_id,
                    'issued_date' => \Carbon\Carbon::now()->format('Y-m-d'),
                    'hotel_due_date' => $request->hotel_due_date,
                    'payment_date' => $request->payment_date,
                    'office_code' => $request->office_code,
                    'issued_by' => auth()->user()->name,
                    'status' => 1
                ]);

                $bookingId = $invoice->id;
                $bookingNo = $invoice->invoiceno;
            }

            $voucher = Hotel_voucher::create([
                'voucher_no' => $finalVoucherNo,
                'booking_id' => $bookingId,
                'booking_no' => $bookingNo,
                'currency' => $request->currency,
                'booker' => $request->booker,
                'booker_agent' => $request->booker_agent,
                'no_booker_agent' => $request->no_booker_agent,
                'nationality' => $request->nationality,
                'attention' => $request->attention,
                'hotel_id' => $request->hotel_id,
                'check_in' => $request->check_in,
                'check_out' => $request->check_out,
                'confirm_by' => $request->confirm_by,
                'remark' => $request->remark,
                'rsvn_and_payment_by' => $request->rsvn_and_payment_by,
                'count_type_room' => $request->count_type_room,
                'issued_date' => \Carbon\Carbon::now()->format('Y-m-d')
            ]);

            return redirect(route('hotelvoucher.room', ['id' => $voucher->id]));
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $voucher = Hotel_voucher::find($id);
        $hotels = Hotel::orderBy('hotel_code','ASC')->get();
        $customers = Customer::orderBy('created_at', 'DESC')->get();
        $invoice = Hotel_invoice::find($voucher->booking_id);
        $currencys = $this->currencys;
        return view('hotelvoucher.edit', compact('voucher', 'hotels', 'currencys', 'customers', 'invoice'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'currency' => 'required|nullable|string',
            'booker' => 'required|nullable|string',
            'booker_agent' => 'nullable|string',
            'no_booker_agent' => 'nullable|string',
            'nationality' => 'required|nullable|string',
            'attention' => 'nullable|string',
            'hotel_id' => 'required|nullable|integer',
            'check_in' => 'required|date',
            'check_out' => 'required|date',
            'confirm_by' => 'required|nullable|string',
            'remark' => 'nullable|string',
            'rsvn_and_payment_by' => 'required|nullable|string',
            'count_type_room' => 'required|nullable|integer',
        ]);

        $hotelCode = $request->hotel_code;
        $resetRoomAndGues = $request->reset_room_and_guest;

        $date = \Carbon\Carbon::now()->format('Ymd');

        try {
            $voucher = [
                'currency' => $request->currency,
                'booker' => $request->booker,
                'booker_agent' => $request->booker_agent,
                'no_booker_agent' => $request->no_booker_agent,
                'nationality' => $request->nationality,
                'attention' => $request->attention,
                'hotel_id' => $request->hotel_id,
                'check_in' => $request->check_in,
                'check_out' => $request->check_out,
                'confirm_by' => $request->confirm_by,
                'remark' => $request->remark,
                'rsvn_and_payment_by' => $request->rsvn_and_payment_by,
                'count_type_room' => $request->count_type_room
            ];
            
            Hotel_voucher::whereId($id)->update($voucher);

            Hotel_invoice::whereId(Hotel_voucher::find($id)->booking_id)->update([
                'customer_id' => $request->customer_id,
                'hotel_due_date' => $request->hotel_due_date,
                'payment_date' => $request->payment_date,
                'office_code' => $request->office_code,
            ]);
            
            if( $resetRoomAndGues > 0 ) {
                Hotel_voucher_room::where('hotel_voucher_room_id', $id)->delete();
                Hotel_voucher_guest::where('hotel_voucher_room_id', $id)->delete();
        
                return redirect(route('hotelvoucher.room', ['id' => $voucher->id]));
            } else {
                return redirect(route('hotelvoucher.index'));
            }
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }

    public function room($id)
    {

        $voucher = Hotel_voucher::find($id);
        $voucherRoom = Hotel_voucher_room::with('hotelguest')->where('hotel_voucher_id', $id)->get()->toArray();
        $dayName = $this->dayName;
        $roomselected = isset($_GET['roomselected']) ? $_GET['roomselected'] : "";

        $dbRooms = Hotel_rate::where('hotel_id', $voucher->hotel_id)->orderBy('room_code','ASC')->get();
        $rooms = [];

        foreach ($dbRooms as $key => $value) {
            $rooms[ $value->id ] = $value;
        }

        return view('hotelvoucher.room', compact('voucher', 'voucherRoom', 'rooms', 'dayName', 'roomselected'));
    }

    public function updateRoom(Request $request, $id)
    {
        // $this->validate($request, [
        //     'room_id' => 'required|exists:rooms,id',
        //     'room_no' => 'nullable|string',
        //     'meal_type' => 'nullable|string',
        //     'use_allotment' => 'required|string'
        // ]);


        try {
            $message = "";
            $voucher = Hotel_voucher::find($id);

            $voucherRoomid = "";
            $dataRoom = array(
                'hotel_voucher_id' => $id,
                'voucher_no' => $voucher->voucher_no,
                'hotel_id' => $voucher->hotel_id,
                'check_in' => $voucher->check_in,
                'check_out' => $voucher->check_out,
                'remark' => $voucher->remark,
                'booking_status' => 1,

                'room_id' => $request->room_id,
                'room_no' => $request->room_no,
                'meal_type' => $request->meal_type,
                'use_allotment' => $request->use_allotment,
                'no_of_extrabed' => $request->no_of_extrabed
            );

            if( empty($request->hotel_voucher_room_id) ) {
                $hotelVoucherRoom = Hotel_voucher_room::create($dataRoom);
                $voucherRoomid = $hotelVoucherRoom->id;

                $message = 'Data Room Telah Ditambahkan';
            } else {
                Hotel_voucher_room::whereId($request->hotel_voucher_room_id)->update($dataRoom);
                $voucherRoomid = $request->hotel_voucher_room_id;

                $message = 'Data Room Telah Diubah';
            }

            for ($i=0; $i < count($request->guest_type); $i++) { 
                if( $request->guest_type[ $i ] != "" ) {
                    $dataGuest = array(
                        'hotel_voucher_id' => $id,
                        'hotel_voucher_room_id' => $voucherRoomid,
                        'guest_type' => $request->guest_type[ $i ],
                        'guest_gender' => $request->guest_gender[ $i ],
                        'guest_first_name' => $request->guest_first_name[ $i ],
                        'guest_last_name' => $request->guest_last_name[ $i ],
                        'guest_age' => $request->guest_age[ $i ]
                    );

                    if( empty($request->hotel_voucher_guest_id[ $i ]) ) {
                        Hotel_voucher_guest::create($dataGuest);
                    } else {
                        Hotel_voucher_guest::whereId($request->hotel_voucher_guest_id[ $i ])->update($dataGuest);
                    }
                }
            }

            return redirect()->back()->with(['success' => $message]);
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }

    public function deleteProduct($id)
    {
        Hotel_voucher_room::find($id)->delete();
        Hotel_voucher_guest::where('hotel_voucher_room_id', $id)->delete();

        return redirect()->back()->with(['success' => 'Data telah dihapus']);
    }

    public function destroy($id)
    {
        $voucher = Hotel_voucher::find($id);
        $voucher->delete();
        return redirect()->back()->with(['success' => '<strong>' . $voucher->voucher_no . '</strong> telah berhasil dihapus']);
    }

    private function getHotelCombo() {
        $hotels = Hotel::orderBy('hotel_code','ASC')->get();
        $comboHotel = [];
        foreach ($hotels as $hotel) {
            $comboHotel[ $hotel->id ] = ['hotel_code' => $hotel->hotel_code, 'hotel_name' => $hotel->hotel_name];
        }

        return $comboHotel;
    }

    public function generateVoucher($id)
    {
        $invoice = Invoice::with(['customer'])->find($id);
        $voucher = Hotel_voucher::find($id);
        $hotel = Hotel::find($voucher->hotel_id);
        $voucherRoom = Hotel_voucher_room::with('hotelguest')->where('hotel_voucher_id', $id)->get()->toArray();
        $dayName = $this->dayName;
        $roomselected = isset($_GET['roomselected']) ? $_GET['roomselected'] : "";

        $dbRooms = Hotel_rate::where('hotel_id', $voucher->hotel_id)->orderBy('room_code','ASC')->get();
        $rooms = [];

        foreach ($dbRooms as $key => $value) {
            $rooms[ $value->id ] = $value;
        }

        $filename = $voucher->voucher_no . "-" . date("dmyHis");
        $pdf = PDF::loadView('hotelvoucher.print', compact('voucher', 'voucherRoom', 'rooms', 'hotel', 'invoice'))->setPaper('a4', 'portrait');
        return $pdf->download($filename.'-voucher.pdf');
        // return $pdf->stream('invoice.pdf');
        // return view('hotelvoucher.print', compact('voucher', 'voucherRoom', 'rooms', 'hotel'));
    }

    public function generateVoucherByInvoice($id)
    {
        $invoices = Invoice::with(['customer'])->find($id);
        $voucher = Hotel_voucher::where('booking_id', $id)->first();
        $hotel = Hotel::find($voucher->hotel_id);
        $voucherRoom = Hotel_voucher_room::with('hotelguest')->where('hotel_voucher_id', $voucher->id)->get()->toArray();

        $dbRooms = Hotel_rate::where('hotel_id', $voucher->hotel_id)->orderBy('room_code','ASC')->get();
        $rooms = [];
        
        $booker = null;
    if (!empty($invoice->customer->company)) {
        $booker = $invoice->customer->company;
    }

        foreach ($dbRooms as $key => $value) {
            $rooms[ $value->id ] = $value;
        }

        $filename = $voucher->voucher_no . "-" . date("dmyHis");
        $pdf = PDF::loadView('hotelvoucher.print', compact('voucher', 'voucherRoom', 'rooms', 'hotel', 'invoices', 'booker'))->setPaper('a4', 'portrait');
        return $pdf->download($filename.'-voucher.pdf');
        // return $pdf->stream('invoice.pdf');
        // return view('hotelvoucher.print', compact('voucher', 'voucherRoom', 'rooms', 'hotel'));
    }

    public function getRoomDetail(Request $request) {
        $result = false;
        $data = [];

        $id = $request->input('id');

        $dbRoom = Hotel_voucher_room::with('hotelguest')->where('id', $id)->get()->toArray();
        if( !empty($dbRoom) ) {
            $result = true;
            $data = $dbRoom[0];
        }

        return response()->json(array('result' => $result,'data' => $data), 200);
    }
}
