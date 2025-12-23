<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if (!$startDate || !$endDate) {
            $startDate = today()->format('Y-m-d');
            $endDate = today()->format('Y-m-d');
        }

        $endDate .= ' 23:59:59';

        $startCarbon = Carbon::parse($startDate)->startOfDay();
        $endCarbon = Carbon::parse($endDate)->endOfDay();

        $totalInvoiceInRange = Invoice::whereBetween('created_at', [$startCarbon, $endCarbon])
            ->where('total', '!=', 0)
            ->count();

        $totalPenjualanInRange = Invoice::whereBetween('created_at', [$startCarbon, $endCarbon])
            ->where('total', '!=', 0)
            ->sum('total');

        // previous day (same length shifted by 1 day) comparisons
        $prevStart = $startCarbon->copy()->subDay();
        $prevEnd = $endCarbon->copy()->subDay();

        $totalInvoicePrev = Invoice::whereBetween('created_at', [$prevStart, $prevEnd])
            ->where('total', '!=', 0)
            ->count();

        $totalPenjualanPrev = Invoice::whereBetween('created_at', [$prevStart, $prevEnd])
            ->where('total', '!=', 0)
            ->sum('total');

        // percent change helpers (avoid division by zero)
        $invoiceChangePercent = 0;
        if ($totalInvoicePrev == 0) {
            $invoiceChangePercent = ($totalInvoiceInRange == 0) ? 0 : 100;
        } else {
            $invoiceChangePercent = round((($totalInvoiceInRange - $totalInvoicePrev) / $totalInvoicePrev) * 100, 1);
        }

        $penjualanChangePercent = 0;
        if ($totalPenjualanPrev == 0) {
            $penjualanChangePercent = ($totalPenjualanInRange == 0) ? 0 : 100;
        } else {
            $penjualanChangePercent = round((($totalPenjualanInRange - $totalPenjualanPrev) / $totalPenjualanPrev) * 100, 1);
        }

        // Total sales for current month: sum invoice totals (not pax count)
        $totalPenjualanBulanIni = DB::table('invoices')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->where('total', '!=', 0)
            ->sum('total');

        // REVISI 7: Total Revenue ambil dari Total Profit per Bulan.
        // Gunakan kolom `profit` pada `invoice_details` jika tersedia.
        $totalProfitPerMonth = DB::table('invoice_details')
            ->join('invoices', 'invoice_details.invoice_id', '=', 'invoices.id')
            ->whereYear('invoices.created_at', now()->year)
            ->whereMonth('invoices.created_at', now()->month)
            ->where('invoices.total', '!=', 0)
            ->sum('invoice_details.profit');

        // previous month comparisons
        $prevMonth = Carbon::now()->subMonth();
        $totalPenjualanBulanLalu = DB::table('invoices')
            ->whereYear('created_at', $prevMonth->year)
            ->whereMonth('created_at', $prevMonth->month)
            ->where('total', '!=', 0)
            ->sum('total');

        $totalProfitPrevMonth = DB::table('invoice_details')
            ->join('invoices', 'invoice_details.invoice_id', '=', 'invoices.id')
            ->whereYear('invoices.created_at', $prevMonth->year)
            ->whereMonth('invoices.created_at', $prevMonth->month)
            ->where('invoices.total', '!=', 0)
            ->sum('invoice_details.profit');

        if ($totalPenjualanBulanLalu == 0) {
            $monthSalesChangePercent = ($totalPenjualanBulanIni == 0) ? 0 : 100;
        } else {
            $monthSalesChangePercent = round((($totalPenjualanBulanIni - $totalPenjualanBulanLalu) / $totalPenjualanBulanLalu) * 100, 1);
        }

        if ($totalProfitPrevMonth == 0) {
            $monthProfitChangePercent = ($totalProfitPerMonth == 0) ? 0 : 100;
        } else {
            $monthProfitChangePercent = round((($totalProfitPerMonth - $totalProfitPrevMonth) / $totalProfitPrevMonth) * 100, 1);
        }

        return view('home', compact(
            'totalInvoiceInRange',
            'totalPenjualanInRange',
            'totalPenjualanBulanIni',
            'totalProfitPerMonth',
            'startDate',
            'endDate',
            'totalInvoicePrev',
            'invoiceChangePercent',
            'totalPenjualanPrev',
            'penjualanChangePercent',
            'totalPenjualanBulanLalu',
            'monthSalesChangePercent',
            'totalProfitPrevMonth',
            'monthProfitChangePercent'
        ));
    }
}
