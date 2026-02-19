<?php

namespace App\Http\Controllers;

use App\Models\Airlines;
use Illuminate\Http\Request;


class AirlinesController extends Controller
{
    public function index(Request $request)
    {
        $additionalData = Airlines::when($request->search, function($query) use ($request) {
            $query->where('airlines_code', 'like', "%{$request->search}%")
                  ->orWhere('airlines_name', 'like', "%{$request->search}%");
        })->orderBy('created_at', 'DESC')->paginate(10);

        return view('airlines.index', compact('additionalData'));
    }

    public function create()
    {
        return view('airlines.create');
    }

    public function save(Request $request)
    {
        // Normalize code to uppercase so database always stores uppercase codes
        $request->merge(['airlines_code' => strtoupper($request->airlines_code)]);

        $this->validate($request, [
            'airlines_code' => 'required|string|max:3',
            'airlines_name' => 'required|string'
        ]);

        try {
            $airlines = Airlines::create([
                'airlines_code' => $request->airlines_code,
                'airlines_name' => $request->airlines_name
            ]);
            return redirect('/airline')->with(['success' => '<strong>' . $airlines->airlines_code . '</strong> Telah disimpan']);
        } catch(\Exception $e) {
            return redirect('/airline/new')->with(['error' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $airlines = Airlines::find($id);
        return view('airlines.edit', compact('airlines'));
    }

    public function update(Request $request, $id)
    {
        $airlines = Airlines::find($id);
        // Ensure code is stored uppercase
        $request->merge(['airlines_code' => strtoupper($request->airlines_code)]);

        $airlines->update([
            'airlines_code' => $request->airlines_code,
            'airlines_name' => $request->airlines_name
        ]);
        return redirect('/airline')->with(['success' => '<strong>' . $airlines->airlines_code . '</strong> Diperbaharui']);
    }

    public function destroy($id)
    {
        $airlines = Airlines::find($id);
        $airlines->delete();
        return redirect('/airline')->with(['success' => '</strong>' . $airlines->airlines_code . '</strong> Dihapus']);
    }
}
