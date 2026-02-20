<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Airlines;
use App\Models\AirlineTopup;
use DB;

class TopUpController extends Controller
{
    public function index(Request $request)
    {
        $airlines = Airlines::orderBy('airlines_name')->get();
        
        // Mulai query dengan relasi
        $query = AirlineTopup::with('airline', 'user');

        // Filter Berdasarkan Maskapai
        if ($request->filled('airline_id')) {
            $query->where('airline_id', $request->airline_id);
        }

        // Filter Berdasarkan Tanggal (Dari - Sampai)
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Urutkan yang terbaru dan gunakan Pagination
        $topups = $query->orderBy('created_at', 'desc')->paginate(15);

        // Kirim input request ke view agar form filter tetap terisi saat di-search
        return view('topup.index', compact('airlines', 'topups'))->with($request->all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'airline_id' => 'required|exists:airlines,id',
            'amount'     => 'required', // Hapus validasi 'numeric|min:1' agar bisa minus
        ]);

        DB::transaction(function() use ($request) {
            // Support angka negatif (tanda minus)
            $amount = (int) preg_replace('/[^0-9-]/', '', $request->amount);
            
            $selectedAirline = Airlines::find($request->airline_id);
            $lionGroupCodes = ['JT', 'IU', 'IW', 'ID'];
            
            if (in_array(strtoupper($selectedAirline->airlines_code), $lionGroupCodes)) {
                $mainAirline = Airlines::where('airlines_code', 'JT')->first() ?? $selectedAirline;
            } else {
                $mainAirline = $selectedAirline;
            }

            $before = $mainAirline->balance;
            $after  = $before + $amount;
            
            $mainAirline->balance = $after;
            $mainAirline->save();

            AirlineTopup::create([
                'airline_id'     => $selectedAirline->id,
                'amount'         => $amount,
                'before_balance' => $before,
                'after_balance'  => $after,
                'user_id'        => auth()->id(),
            ]);
        });

        return back()->with('success', 'Update saldo berhasil!');
    }
}