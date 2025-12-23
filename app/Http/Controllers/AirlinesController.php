<?php

namespace App\Http\Controllers;

use App\Models\Airlines;
use Illuminate\Http\Request;


class AirlinesController extends Controller
{
    public function index()
    {
        $airlines = Airlines::orderBy('created_at', 'DESC')->get();

        $additionalData = Airlines::orderBy('created_at', 'DESC')->paginate(10);
        return view('airlines.index', compact('airlines', 'additionalData'));
    }

    public function create()
    {
        return view('airlines.create');
    }

    public function save(Request $request)
    {
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
