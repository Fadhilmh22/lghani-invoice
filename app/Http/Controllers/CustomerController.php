<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = Customer::orderBy('created_at', 'DESC')->paginate(10);
        
        $search = $request->input('search');

        // Query untuk pencarian
        $customers = Customer::where(function($query) use ($search) {
                    $query->where('booker', 'like', "%$search%")
                          ->orWhere('company', 'like', "%$search%");
                })
                ->orderBy('created_at', 'DESC')
                ->paginate(10);
        return view('customer.index', ['customers' => $customers]);
    }
    
    public function search(Request $request)
{
    $searchTerm = $request->input('term');

    $results = DB::table('customers')
        ->where('booker', 'LIKE', '%' . $searchTerm . '%')
        ->get(['id', 'booker as text']);

    dd($results); // Debugging: Hentikan eksekusi dan tampilkan hasil
    // return response()->json($results);
}

    // Metode untuk mendapatkan data pelanggan awal untuk Select2
    public function initialData()
    {
        $customers = Customer::select('id', 'booker as text')->get(); // Mengambil kolom id dan booker, serta menamai booker sebagai text

        return response()->json($customers);
    }


    public function create()
    {
        return view('customer.add');
    }

    public function save(Request $request)
    {
        $this->validate($request, [
            'gender' => 'nullable|string',
            'booker' => 'required|string',
            'company' => 'required|string',
            'phone' => 'nullable|max:13',
            'alamat' => 'nullable|string',
            'email' => 'nullable|email|string|unique:customers,email',
            'payment' => 'required|string'

        ]);

        try {
            $customer = Customer::create([
                'gender' => $request->gender,
                'booker' => $request->booker,
                'company' => $request->company,
                'phone' => $request->phone,
                'alamat' => $request->alamat,
                'email' => $request->email,
                'payment' => $request->payment

            ]);
            return redirect('/customer')->with(['success' => '<strong>' . $customer->booker . '</strong>  telah berhasil ditambahkan']);
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $customer = Customer::find($id);
        return view('customer.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'gender' => 'nullable|string',
            'booker' => 'required|string',
            'company' => 'required|string',
            'phone' => 'nullable|max:13',
            'alamat' => 'nullable|string',
            'email' => 'nullable|email|string',
            'payment' => 'required|string'
        ]);

        try {
            $customer = Customer::find($id);
            $customer->update([
                'gender' => $request->gender,
                'booker' => $request->booker,
                'company' => $request->company,
                'phone' => $request->phone,
                'alamat' => $request->alamat,
                'email' => $request->email,
                'payment' => $request->payment
            ]);
            return redirect('/customer')->with(['success' =>  '<strong>' . $customer->booker . '</strong>  telah berhasil diperbaharui']);
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $customer = Customer::find($id);
        $customer->delete();
        return redirect()->back()->with(['success' => '<strong>' . $customer->booker . '</strong> telah berhasil dihapus']);
    }
}
