<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Passenger;

class PassengerController extends Controller
{
    public function index(Request $request)
    {
        $passengers = Passenger::orderBy('created_at', 'DESC')->paginate(10);

        $search = $request->input('search');

        // Query untuk pencarian
        $passengers = Passenger::where('name', 'like', "%$search%")
                            ->orderBy('created_at', 'DESC')
                            ->paginate(10);
        return view('passenger.index', ['passengers' => $passengers]);
    }

    public function create()
    {
        return view('passenger.add');
    }

    public function save(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string',
        'id_card' => 'required|size:16|unique:passengers',
        'date_birth' => 'nullable|date',
        'gff' => 'nullable|string|unique:passengers',
        'phone' => 'nullable|max:13'
    ]);

    if ($validator->fails()) {
        return redirect('/passenger')
            ->with('error', 'Data Gagal Ditambahkan, harap periksa kembali!')
            ->withErrors($validator)
            ->withInput();
    }

    try {
        $passenger = Passenger::create([
            'name' => $request->name,
            'id_card' => $request->id_card,
            'date_birth' => $request->date_birth,
            'gff' => $request->gff,
            'phone' => $request->phone
        ]);

        return redirect('/passenger')->with(['success' => '<strong>' . $passenger->name . '</strong>  telah berhasil ditambahkan']);
    } catch (\Exception $e) {
        return redirect('/passenger')->with(['error' => 'Data Gagal Ditambahkan, harap periksa kembali']);
    }
}

    public function edit($id)
{
    $passenger = Passenger::find($id);

    if (!$passenger) {
        return redirect()->route('passenger.index')->with('error', 'Penumpang tidak ditemukan');
    }

    return view('passenger.edit', compact('passenger'));
}


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'id_card' => 'nullable|max:16',
            'date_birth' => 'nullable|date',
            'gff' => 'nullable|string',
            'phone' => 'nullable|max:13'
        ]);

        try {
            $passenger = Passenger::find($id);
            $passenger->update([
                'name' => $request->name,
                'id_card' => $request->id_card,
                'date_birth' => $request->date_birth,
                'gff' => $request->gff,
                'phone' => $request->phone
            ]);
            return redirect('/passenger')->with(['success' =>  '<strong>' . $passenger->name . '</strong>  telah berhasil diperbaharui']);
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $passenger = Passenger::find($id);
        $passenger->delete();
        return redirect()->back()->with(['success' => '<strong>' . $passenger->name . '</strong> telah berhasil dihapus']);
    }
}