<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hotel;
use App\Models\Hotel_rate;

class RoomController extends Controller
{
   public function index(Request $request)
{
    $search = $request->input('search');

    // Query untuk pencarian
    $comboHotel = $this->getHotelCombo();
    $rooms = Hotel_rate::orderBy('created_at', 'DESC');

    if (!empty($search)) {
        $rooms->whereHas('hotel', function ($query) use ($search) {
            $query->where('hotel_name', 'like', '%' . $search . '%')
                  ->orWhere('room_name', 'like', '%' . $search . '%');
        });
    }

    $rooms = $rooms->paginate(10);

    return view('room.index', ['rooms' => $rooms, 'comboHotel' => $comboHotel]);
}


    public function create()
    {
        $hotels = Hotel::orderBy('hotel_name','ASC')->get();
        return view('room.add', compact('hotels'));
    }

    public function save(Request $request)
    {
        $this->validate($request, [
            'hotel_id' => 'required|exists:hotels,id',
            'room_code' => 'nullable|string',
            'room_name' => 'nullable|string',
            'room_type' => 'nullable|string',
            'bed_type' => 'nullable|string',
            'weekday_price' => 'required|integer',
            'weekday_nta' => 'required|integer',
            'weekend_price' => 'required|integer',
            'weekend_nta' => 'required|integer'
        ]);

        try {
            $room = Hotel_rate::create([
                'hotel_id' => $request->hotel_id,
                'room_code' => $request->room_code,
                'room_name' => $request->room_name,
                'room_type' => $request->room_type,
                'bed_type' => $request->bed_type,
                'weekday_price' => $request->weekday_price,
                'weekday_nta' => $request->weekday_nta,
                'weekend_price' => $request->weekend_price,
                'weekend_nta' => $request->weekend_nta,
            ]);
            return redirect('/room')->with(['success' => '<strong>' . $room->room_name . '</strong>  telah berhasil ditambahkan']);
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $hotels = Hotel::orderBy('hotel_code','ASC')->get();
        $room = Hotel_rate::find($id);
        $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : "";
        return view('room.edit', compact('room', 'hotels', 'redirect'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'hotel_id' => 'required|exists:hotels,id',
            'room_code' => 'nullable|string',
            'room_name' => 'nullable|string',
            'room_type' => 'nullable|string',
            'bed_type' => 'nullable|string',
            'weekday_price' => 'required|integer',
            'weekday_nta' => 'required|integer',
            'weekend_price' => 'required|integer',
            'weekend_nta' => 'required|integer'
        ]);

        try {
            $room = Hotel_rate::find($id);
            $room->update([
                'hotel_id' => $request->hotel_id,
                'room_code' => $request->room_code,
                'room_name' => $request->room_name,
                'room_type' => $request->room_type,
                'bed_type' => $request->bed_type,
                'weekday_price' => $request->weekday_price,
                'weekday_nta' => $request->weekday_nta,
                'weekend_price' => $request->weekend_price,
                'weekend_nta' => $request->weekend_nta,
            ]);

            if( !empty($request->redirect) ) {
                return redirect('/' . $request->redirect);
            }
            return redirect('/room')->with(['success' =>  '<strong>' . $room->room_name . '</strong>  telah berhasil diperbaharui']);
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $room = Hotel_rate::find($id);
        $room->delete();
        return redirect()->back()->with(['success' => '<strong>' . $room->room_name . '</strong> telah berhasil dihapus']);
    }

    private function getHotelCombo() {
        $hotels = Hotel::orderBy('hotel_code','ASC')->get();
        $comboHotel = [];
        foreach ($hotels as $hotel) {
            $comboHotel[ $hotel->id ] = ['hotel_code' => $hotel->hotel_code, 'hotel_name' => $hotel->hotel_name];
        }

        return $comboHotel;
    }

    public function getRoomByHotel(Request $request) {
        $result = false;
        $data = [];

        $id = $request->input('id');

        $data = Hotel_rate::select(['id', 'room_code', 'room_name', 'room_type', 'weekday_price', 'weekday_nta', 'weekend_price', 'weekend_nta'])->where('hotel_id', $id)->get();
        if( !empty($data) ) {
            $result = true;
        }

        return response()->json(array('result' => $result,'data' => $data), 200);
    }

    public function detail(Request $request)
    {
        $result = false;
        $data = [];

        $id = $request->input('id');

        $data = Hotel_rate::find($id);
        if( !empty($data) ) {
            $result = true;
        }
        
        return response()->json(array('result' => $result,'data' => $data), 200);
    }
}
