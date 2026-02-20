<?php

namespace App\Http\Controllers;

use App\Models\Airport;
use Illuminate\Http\Request;

class AirportController extends Controller
{
    public function index(Request $request) {
    $airports = Airport::when($request->search, function($query) use ($request) {
        $query->where('code', 'like', "%{$request->search}%")
              ->orWhere('name', 'like', "%{$request->search}%")
              ->orWhere('city', 'like', "%{$request->search}%");
    })
    ->latest()
    ->paginate(10);
    return view('airports.index', compact('airports'));
}

public function create() {
    return view('airports.create');
}

public function store(Request $request) {
    // Normalize code to uppercase before validation so uniqueness is checked consistently
    $request->merge(['code' => strtoupper($request->code)]);

    $request->validate([
        'code' => 'required|unique:airports,code|max:5',
        'name' => 'required',
        'city' => 'required'
    ]);
    Airport::create($request->all());
    return redirect()->route('airports.index')->with('success', 'Bandara berhasil ditambahkan');
}

public function edit($id) {
    $airport = Airport::findOrFail($id);
    return view('airports.edit', compact('airport'));
}

public function update(Request $request, $id) {
    // Normalize code to uppercase before validation
    $request->merge(['code' => strtoupper($request->code)]);

    $request->validate([
        'code' => 'required|max:5|unique:airports,code,'.$id,
        'name' => 'required',
        'city' => 'required'
    ]);
    Airport::findOrFail($id)->update($request->all());
    return redirect()->route('airports.index')->with('success', 'Data bandara diperbarui');
}

public function destroy($id) {
    Airport::findOrFail($id)->delete();
    return redirect()->back()->with('success', 'Bandara dihapus');
}
}
