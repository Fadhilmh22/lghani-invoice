@extends('master')

@section('konten')
@php
    $latestInvoices = \App\Models\Invoice::with('customer')
        ->whereHas('detail', function($q) {
            $q->whereNotNull('ticket_no')->where('ticket_no', '!=', '');
        })
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();
    $latestHotelInvoices = \App\Models\Hotel_invoice::with('customer')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

    // Prepare Most Airlines data (monthly & yearly) from invoice/invoice_details
    $monthStart = Carbon::now()->startOfMonth();
    $monthEnd = Carbon::now()->endOfMonth();
    $year = Carbon::now()->year;

    $monthlyRaw = DB::table('invoice_details')
        ->join('invoices','invoice_details.invoice_id','=','invoices.id')
        ->join('airlines','invoice_details.airline_id','=','airlines.id')
        ->select('airlines.airlines_name as label', DB::raw('count(*) as total'))
        ->whereBetween('invoices.created_at', [$monthStart, $monthEnd])
        ->groupBy('airlines.airlines_name')
        ->orderByDesc('total')
        ->limit(6)
        ->get()
        ->toArray();

    $yearlyRaw = DB::table('invoice_details')
        ->join('invoices','invoice_details.invoice_id','=','invoices.id')
        ->join('airlines','invoice_details.airline_id','=','airlines.id')
        ->select('airlines.airlines_name as label', DB::raw('count(*) as total'))
        ->whereYear('invoices.created_at', $year)
        ->groupBy('airlines.airlines_name')
        ->orderByDesc('total')
        ->limit(6)
        ->get()
        ->toArray();

    // Fallback when no data: preserve small sample
    if (empty($monthlyRaw)) {
        $monthlyRaw = [ (object)['label' => 'No Data', 'total' => 1] ];
    }
    if (empty($yearlyRaw)) {
        $yearlyRaw = [ (object)['label' => 'No Data', 'total' => 1] ];
    }

    $airlinesMonthly = array_map(function($r){ return ['label'=>$r->label,'total'=>(int)$r->total]; }, $monthlyRaw);
    $airlinesYearly = array_map(function($r){ return ['label'=>$r->label,'total'=>(int)$r->total]; }, $yearlyRaw);
    $airlinesData = $airlinesMonthly; // default

    $summaryStats = [
        ['label' => 'Project', 'value' => 10],
        ['label' => 'Task', 'value' => 10],
        ['label' => 'Client', 'value' => 10],
        ['label' => 'Revenue', 'value' => 10],
        ['label' => 'Income', 'value' => 10],
    ];

    $chartSeries = [
        'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        'income' => [70, 85, 65, 90, 80, 95],
        'expense' => [40, 55, 45, 60, 50, 65],
    ];

    // Income comparison: Ticket vs Hotel
    // Monthly: last 6 months; Yearly: last 5 years
    $months = [];
    $ticketMonthly = [];
    $hotelMonthly = [];
    for ($i = 5; $i >= 0; $i--) {
        $start = Carbon::now()->subMonths($i)->startOfMonth();
        $end = Carbon::now()->subMonths($i)->endOfMonth();
        $months[] = $start->format('M');

        // ticket income following report logic: (pax_paid - price) + profit where ticket_no exists
        $t = DB::table('invoice_details')
            ->join('invoices','invoice_details.invoice_id','=','invoices.id')
            ->whereNotNull('invoice_details.ticket_no')
            ->whereBetween('invoices.created_at', [$start, $end])
            ->select(DB::raw('SUM((invoice_details.pax_paid - invoice_details.price) + invoice_details.profit) as total'))
            ->value('total');
        $ticketMonthly[] = (float) $t;

        // hotel revenue: compute using the same logic as report/printhotel (weekday/weekend pricing per room)
        $h = 0;
        $hotelRows = DB::table('hotel_voucher_rooms')
            ->join('hotel_rates', 'hotel_voucher_rooms.room_id', '=', 'hotel_rates.id')
            ->join('hotel_vouchers', 'hotel_voucher_rooms.hotel_voucher_id', '=', 'hotel_vouchers.id')
            ->join('hotel_invoices', 'hotel_vouchers.booking_id', '=', 'hotel_invoices.id')
            ->whereBetween('hotel_invoices.created_at', [$start, $end])
            ->select('hotel_voucher_rooms.check_in','hotel_voucher_rooms.check_out','hotel_rates.weekday_price','hotel_rates.weekend_price','hotel_rates.weekday_nta','hotel_rates.weekend_nta','hotel_invoices.discount')
            ->get();

        foreach ($hotelRows as $inv) {
            $startdate = strtotime($inv->check_in);
            $enddate = strtotime($inv->check_out) - 86400;
            $weekDay = 0;
            $weekEnd = 0;
            while ($startdate <= $enddate) {
                $w = date("w", $startdate);
                if ($w == 5 || $w == 6) {
                    $weekEnd++;
                } else {
                    $weekDay++;
                }
                $startdate = strtotime("+1 day", $startdate);
            }
            $total = ($weekDay * $inv->weekday_price) + ($weekEnd * $inv->weekend_price);
            $nta = ($weekDay * $inv->weekday_nta) + ($weekEnd * $inv->weekend_nta);
            $discount = $inv->discount;
            $profit = $total - $nta - $discount;
            $h += $profit;
        }
        $hotelMonthly[] = (float) $h;
    }

    $years = [];
    $ticketYearly = [];
    $hotelYearly = [];
    for ($y = date('Y')-4; $y <= date('Y'); $y++) {
        $years[] = (string)$y;
        $t = DB::table('invoice_details')
            ->join('invoices','invoice_details.invoice_id','=','invoices.id')
            ->whereNotNull('invoice_details.ticket_no')
            ->whereYear('invoices.created_at', $y)
            ->select(DB::raw('SUM((invoice_details.pax_paid - invoice_details.price) + invoice_details.profit) as total'))
            ->value('total');
        $ticketYearly[] = (float) $t;

        // compute yearly hotel income using same per-room logic as report
        $h = 0;
        $hotelRows = DB::table('hotel_voucher_rooms')
            ->join('hotel_rates', 'hotel_voucher_rooms.room_id', '=', 'hotel_rates.id')
            ->join('hotel_vouchers', 'hotel_voucher_rooms.hotel_voucher_id', '=', 'hotel_vouchers.id')
            ->join('hotel_invoices', 'hotel_vouchers.booking_id', '=', 'hotel_invoices.id')
            ->whereYear('hotel_invoices.created_at', $y)
            ->select('hotel_voucher_rooms.check_in','hotel_voucher_rooms.check_out','hotel_rates.weekday_price','hotel_rates.weekend_price','hotel_rates.weekday_nta','hotel_rates.weekend_nta','hotel_invoices.discount')
            ->get();

        foreach ($hotelRows as $inv) {
            $startdate = strtotime($inv->check_in);
            $enddate = strtotime($inv->check_out) - 86400;
            $weekDay = 0;
            $weekEnd = 0;
            while ($startdate <= $enddate) {
                $w = date("w", $startdate);
                if ($w == 5 || $w == 6) {
                    $weekEnd++;
                } else {
                    $weekDay++;
                }
                $startdate = strtotime("+1 day", $startdate);
            }
            $total = ($weekDay * $inv->weekday_price) + ($weekEnd * $inv->weekend_price);
            $nta = ($weekDay * $inv->weekday_nta) + ($weekEnd * $inv->weekend_nta);
            $discount = $inv->discount;
            $profit = $total - $nta - $discount;
            $h += $profit;
        }
        $hotelYearly[] = (float) $h;
    }

        // Totals for UI summary (monthly default)
        $ticketMonthlyTotal = array_sum($ticketMonthly);
        $hotelMonthlyTotal = array_sum($hotelMonthly);
        $ticketYearlyTotal = array_sum($ticketYearly);
        $hotelYearlyTotal = array_sum($hotelYearly);

    // Top customers (monthly & yearly) based on invoices total
    $monthStart = Carbon::now()->startOfMonth();
    $monthEnd = Carbon::now()->endOfMonth();
    $year = Carbon::now()->year;

    $customersMonthlyRaw = DB::table('invoices')
        ->join('customers','invoices.customer_id','=','customers.id')
        ->select(DB::raw("COALESCE(customers.booker, customers.company, 'Unknown') as label"), DB::raw('SUM(invoices.total) as total'))
        ->whereBetween('invoices.created_at', [$monthStart, $monthEnd])
        ->groupBy('customers.id', 'customers.booker', 'customers.company')
        ->orderByDesc('total')
        ->limit(6)
        ->get()
        ->toArray();

    $customersYearlyRaw = DB::table('invoices')
        ->join('customers','invoices.customer_id','=','customers.id')
        ->select(DB::raw("COALESCE(customers.booker, customers.company, 'Unknown') as label"), DB::raw('SUM(invoices.total) as total'))
        ->whereYear('invoices.created_at', $year)
        ->groupBy('customers.id', 'customers.booker', 'customers.company')
        ->orderByDesc('total')
        ->limit(6)
        ->get()
        ->toArray();

    if (empty($customersMonthlyRaw)) { $customersMonthlyRaw = [ (object)['label' => 'Tidak ada data', 'total' => 0] ]; }
    if (empty($customersYearlyRaw)) { $customersYearlyRaw = [ (object)['label' => 'Tidak ada data', 'total' => 0] ]; }

    $customersMonthly = array_map(function($r){ return ['label'=>$r->label,'total'=>(float)$r->total]; }, $customersMonthlyRaw);
    $customersYearly = array_map(function($r){ return ['label'=>$r->label,'total'=>(float)$r->total]; }, $customersYearlyRaw);
    // Debug values: check schema and compute raw sums for troubleshooting
    $hotel_total_sum = (float) DB::table('hotel_invoices')->sum('total');
    $hotel_grand_total_exists = Schema::hasColumn('hotel_invoices','grand_total');
    $hotel_grand_total_sum = $hotel_grand_total_exists ? (float) DB::table('hotel_invoices')->sum('grand_total') : null;
@endphp
<div class="dashboard-wrapper">
    
    <style>
    .status-chip { display:inline-block; padding:6px 8px; border-radius:8px; min-width:64px; }
    .status-chip .status-line { display:block; line-height:1; }
    .status-completed { background:#e6fffa; color:#065f46; }
    .status-progress { background:#fff7ed; color:#7c2d12; }
    </style>
    <div class="dashboard-greeting">
        <div>
            <p class="welcome-label">Selamat Datang Kembali</p>
            <h2>{{ Auth::user()->name }}</h2>
            <p class="welcome-sub">Anda login sebagai {{ Auth::user()->role }}</p>
        </div>
        <div class="filter-card">
            <form action="{{ route('home') }}" method="GET" class="filter-form">
                <div class="form-field">
                    <label for="start_date">Start Date</label>
                    <input type="date" id="start_date" name="start_date" value="{{ $startDate }}">
                </div>
                <div class="form-field">
                    <label for="end_date">End Date</label>
                    <input type="date" id="end_date" name="end_date" value="{{ $endDate }}">
                </div>
                <button type="submit" class="filter-btn">
                    <i class="fa fa-search"></i>
                </button>
            </form>
        </div>
    </div>

    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-icon badge-gold">
                <i class="fa fa-receipt"></i>
            </div>
            <div>
                <p class="stat-label">Total Invoices Per Day</p>
                <h3>{{ number_format($totalInvoiceInRange ?? 0) }}</h3>
                <span class="stat-trend {{ (isset($invoiceChangePercent) && $invoiceChangePercent < 0) ? 'down' : 'up' }}">{{ (isset($invoiceChangePercent) && $invoiceChangePercent > 0) ? '+' : '' }}{{ isset($invoiceChangePercent) ? number_format($invoiceChangePercent, 1) : '0.0' }}%</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon badge-green">
                <i class="fa fa-wallet"></i>
            </div>
            <div>
                <p class="stat-label">Total Overall Sales Per Day</p>
                <h3>Rp{{ number_format($totalPenjualanInRange ?? 0) }}</h3>
                <span class="stat-trend {{ (isset($penjualanChangePercent) && $penjualanChangePercent < 0) ? 'down' : 'up' }}">{{ (isset($penjualanChangePercent) && $penjualanChangePercent > 0) ? '+' : '' }}{{ isset($penjualanChangePercent) ? number_format($penjualanChangePercent, 1) : '0.0' }}%</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon badge-blue">
                <i class="fa fa-chart-line"></i>
            </div>
            <div>
                <p class="stat-label">Total Sales Per Month</p>
                <h3>Rp{{ number_format($totalPenjualanBulanIni ?? 0) }}</h3>
                <span class="stat-trend {{ (isset($monthSalesChangePercent) && $monthSalesChangePercent < 0) ? 'down' : 'up' }}">{{ (isset($monthSalesChangePercent) && $monthSalesChangePercent > 0) ? '+' : '' }}{{ isset($monthSalesChangePercent) ? number_format($monthSalesChangePercent, 1) : '0.0' }}%</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon badge-purple">
                <i class="fa fa-coins"></i>
            </div>
            <div>
                <p class="stat-label">Total Revenue Per Month</p>
                <h3>Rp{{ number_format($totalProfitPerMonth ?? 0) }}</h3>
                <span class="stat-trend {{ (isset($monthProfitChangePercent) && $monthProfitChangePercent < 0) ? 'down' : 'up' }}">{{ (isset($monthProfitChangePercent) && $monthProfitChangePercent > 0) ? '+' : '' }}{{ isset($monthProfitChangePercent) ? number_format($monthProfitChangePercent, 1) : '0.0' }}%</span>
            </div>
        </div>
    </div>

    <div class="content-grid">
        @if($latestInvoices->isNotEmpty())
        <div class="card large-card">
            <div class="card-header">
                    <div>
                    <h4>Latest Invoice List</h4>
                    <div class="tabs">
                        <span class="tab active" data-tab="ticketing">Ticketing</span>
                        <span class="tab" data-tab="hotel">Hotel</span>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="invoice-table">
                    <thead>
                        <tr class="ticketing-head">
                            <th>Invoice ID</th>
                            <th>Date</th>
                            <th>Booker</th>
                            <th>Company</th>
                            <th>Phone Number</th>
                            <th>Printed By</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody class="ticketing-body">
                        @forelse ($latestInvoices as $row)
                        @php
                            $statusLabel = $row->status_pembayaran;
                            $statusClass = $statusLabel === 'Sudah Lunas' ? 'status-completed' : 'status-progress';
                            $statusParts = explode(' ', $statusLabel);
                        @endphp
                        <tr>
                            <td><strong>{{ $row->invoiceno }}</strong></td>
                            <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d-m-Y H:i:s') }}</td>
                            <td class="uppercase-text">
                                {{ optional($row->customer)->gender }}. {{ optional($row->customer)->booker }}
                            </td>
                            <td>{{ optional($row->customer)->company }}</td>
                            <td>{{ optional($row->customer)->phone }}</td>
                            <td>{{ $row->edited }}</td>
                            <td>Rp {{ number_format($row->total) }}</td>
                            <td>
                                <span class="status-chip {{ $statusClass }}">
                                    <span class="status-line">{{ $statusParts[0] ?? '' }}</span>
                                    <span class="status-line">{{ $statusParts[1] ?? '' }}</span>
                                </span>
                            </td>
                        </tr>
                        @empty
                        {{-- handled by outer conditional; nothing to show here --}}
                        @endforelse
                    </tbody>

                    <tbody class="hotel-body" style="display:none;">
                        @forelse ($latestHotelInvoices as $hrow)
                        @php
                            $hStatusLabel = $hrow->status_pembayaran ?? '';
                            $hStatusClass = ($hStatusLabel === 'Sudah Lunas') ? 'status-completed' : 'status-progress';
                            $hStatusParts = explode(' ', $hStatusLabel);
                        @endphp
                        <tr>
                            <td><strong>{{ $hrow->invoiceno ?? '-' }}</strong></td>
                            <td>{{ \Carbon\Carbon::parse($hrow->created_at)->format('d-m-Y H:i:s') }}</td>
                            <td class="uppercase-text">{{ optional($hrow->customer)->gender }}. {{ optional($hrow->customer)->booker }}</td>
                            <td>{{ optional($hrow->customer)->company }}</td>
                            <td>{{ optional($hrow->customer)->phone }}</td>
                            <td>{{ $hrow->issued_by ?? ($hrow->edited ?? '-') }}</td>
                            <td>
                                <span class="status-chip {{ $hStatusClass }}">
                                    <span class="status-line">{{ $hStatusParts[0] ?? '' }}</span>
                                    <span class="status-line">{{ $hStatusParts[1] ?? '' }}</span>
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center p-4">Tidak ada data invoice hotel.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <div class="card side-card">
            <div class="card-header" style="position:relative;">
                <h4>Most Airlines</h4>
                <button id="airlineRangeBtn" class="dropdown-btn" type="button">Monthly <i class="fa fa-chevron-down" style="margin-left:8px"></i></button>
                <ul id="airlineRangeMenu" class="airline-range-menu" style="display:none; position:absolute; top:42px; right:18px; background:#fff; list-style:none; padding:6px; border-radius:8px; box-shadow:0 8px 20px rgba(2,6,23,0.08); z-index:60;">
                    <li data-value="monthly" style="padding:6px 12px; cursor:pointer; white-space:nowrap;">Monthly</li>
                    <li data-value="yearly" style="padding:6px 12px; cursor:pointer; white-space:nowrap;">Yearly</li>
                </ul>
            </div>
            <div class="donut-wrapper" style="flex-direction:column; align-items:center;">
                <canvas id="airlinesChart" style="max-width:320px; width:100%;"></canvas>
                <ul class="legend-list" style="display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:12px; justify-items:start; margin-top:12px; width:100%; max-width:480px;">
                    @foreach ($airlinesData as $airline)
                    <li style="display:flex; align-items:center; gap:8px;">
                        <span class="legend-dot" style="width:10px;height:10px;border-radius:50%;"></span>
                        <div style="text-align:left;">
                            <p style="margin:0;font-size:13px;">{{ $airline['label'] }}</p>
                            <small style="color:#6b7280;">{{ $airline['total'] }} tiket</small>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <div class="content-grid">
        <div class="card large-card">
            <div class="card-header" style="position:relative;">
                <h4>Income: Ticket vs Hotel</h4>
                <button id="incomeRangeBtn" class="dropdown-btn" type="button">Monthly <i class="fa fa-chevron-down" style="margin-left:8px"></i></button>
                <ul id="incomeRangeMenu" style="display:none; position:absolute; top:42px; right:18px; background:#fff; list-style:none; padding:6px; border-radius:8px; box-shadow:0 8px 20px rgba(2,6,23,0.08); z-index:60;">
                    <li data-value="monthly" style="padding:6px 12px; cursor:pointer; white-space:nowrap;">Monthly</li>
                    <li data-value="yearly" style="padding:6px 12px; cursor:pointer; white-space:nowrap;">Yearly</li>
                </ul>
            </div>
            <div class="income-stat-grid" style="display:flex; gap:12px; margin-bottom:12px;">
                <div class="stat-card" style="flex:1; display:flex; align-items:center; gap:12px; padding:12px;">
                    <div class="stat-icon badge-blue" style="width:48px;height:48px;border-radius:12px; font-size:20px; display:flex; align-items:center; justify-content:center;">
                        <i class="fa fa-plane"></i>
                    </div>
                    <div style="flex:1;">
                        <div style="font-size:12px;color:#64748b">Ticket Income</div>
                        <div id="ticketTotal" style="font-weight:700; margin-top:6px; font-size:18px;">Rp{{ number_format($ticketMonthlyTotal ?? 0) }}</div>
                        <small style="color:#6b7280">Monthly</small>
                    </div>
                </div>
                <div class="stat-card" style="flex:1; display:flex; align-items:center; gap:12px; padding:12px;">
                    <div class="stat-icon badge-purple" style="width:48px;height:48px;border-radius:12px; font-size:20px; display:flex; align-items:center; justify-content:center;">
                        <i class="fa fa-bed"></i>
                    </div>
                    <div style="flex:1;">
                        <div style="font-size:12px;color:#92400e">Hotel Income</div>
                        <div id="hotelTotal" style="font-weight:700; margin-top:6px; font-size:18px;">Rp{{ number_format($hotelMonthlyTotal ?? 0) }}</div>
                        <small style="color:#6b7280">Monthly</small>
                    </div>
                </div>
            </div>
            <div class="chart-wrapper">
                <canvas id="incomeChart"></canvas>
            </div>
        </div>
        <div class="card side-card summary-card">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <h4>Top Customers</h4>
                <div style="position:relative;">
                    <button id="customerRangeBtn" class="dropdown-btn" type="button">Monthly <i class="fa fa-chevron-down" style="margin-left:8px"></i></button>
                    <ul id="customerRangeMenu" style="display:none; position:absolute; top:40px; right:0; background:#fff; list-style:none; padding:6px; border-radius:8px; box-shadow:0 8px 20px rgba(2,6,23,0.08); z-index:60;">
                        <li data-value="monthly" style="padding:6px 12px; cursor:pointer; white-space:nowrap;">Monthly</li>
                        <li data-value="yearly" style="padding:6px 12px; cursor:pointer; white-space:nowrap;">Yearly</li>
                    </ul>
                </div>
            </div>
            <ul id="customerList" style="margin-top:12px; padding:0; list-style:none;">
                @foreach($customersMonthly as $cust)
                <li style="display:flex; justify-content:space-between; align-items:center; padding:10px 0; border-bottom:1px solid #f1f5f9;">
                    <div style="display:flex; gap:10px; align-items:center;">
                        <div class="summary-icon" style="width:36px;height:36px;border-radius:10px;"><i class="fa fa-user"></i></div>
                        <div>
                            <p style="margin:0">{{ $cust['label'] }}</p>
                            <small style="color:#6b7280">{{ number_format($cust['total'] ?? 0) }} </small>
                        </div>
                    </div>
                    <strong>Rp{{ number_format($cust['total'] ?? 0) }}</strong>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>

<style>
.dashboard-wrapper {
    background: #f3f7ff;
    padding: 25px 30px 60px;
    border-radius: 24px;
    margin-bottom: 40px;
}

.dashboard-greeting {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 20px;
    align-items: center;
    margin-bottom: 30px;
}

.welcome-label {
    color: #8a8fb3;
    font-weight: 500;
    margin-bottom: 5px;
}

.welcome-sub {
    color: #8a8fb3;
    margin-top: 6px;
}

.filter-card {
    background: #fff;
    padding: 16px;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
}

.filter-form {
    display: flex;
    gap: 10px;
    align-items: flex-end;
}

.form-field {
    display: flex;
    flex-direction: column;
    font-size: 12px;
    color: #7b7e9e;
}

.form-field input {
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 8px 12px;
    min-width: 160px;
}

.filter-btn {
    background: linear-gradient(135deg, #4a6cf7, #7c3aed);
    border: none;
    color: #fff;
    border-radius: 12px;
    padding: 10px 16px;
}

.stat-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
    gap: 18px;
    margin-bottom: 25px;
}

.stat-card {
    background: #fff;
    border-radius: 20px;
    padding: 18px;
    display: flex;
    gap: 14px;
    align-items: center;
    box-shadow: 0 15px 40px rgba(15, 23, 42, 0.08);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 20px;
}

.badge-gold { background: linear-gradient(180deg, #fbcf33, #fda085); }
.badge-green { background: linear-gradient(180deg, #2ec7a6, #43e97b); }
.badge-blue { background: linear-gradient(180deg, #4facfe, #00f2fe); }
.badge-purple { background: linear-gradient(180deg, #a18cd1, #fbc2eb); }

.stat-label {
    font-size: 13px;
    color: #98a2b3;
    margin-bottom: 4px;
}

.stat-card h3 {
    margin: 0;
    font-weight: 600;
}

.stat-trend {
    font-size: 12px;
}
.stat-trend.up { color: #16a34a; }
.stat-trend.down { color: #dc2626; }

.content-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
    margin-bottom: 25px;
}

.card {
    background: #fff;
    border-radius: 24px;
    padding: 22px;
    box-shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
}

.card.large-card {
    min-height: 360px;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.tabs {
    display: flex;
    gap: 12px;
    margin-top: 6px;
}

.tab {
    font-size: 12px;
    color: #94a3b8;
    padding-bottom: 4px;
}

.tab.active {
    color: #1d4ed8;
    border-bottom: 2px solid #1d4ed8;
}

.invoice-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0 10px;
}

.invoice-table th {
    font-size: 12px;
    text-transform: uppercase;
    color: #a0aec0;
    font-weight: 600;
    padding-bottom: 10px;
}

.invoice-table td {
    background: #f8fafc;
    padding: 14px 10px;
    border-top: 1px solid #e2e8f0;
    border-bottom: 1px solid #e2e8f0;
}

.tabs .tab { cursor: pointer; }
.tabs .tab.active { background: transparent; }

.invoice-table tr td:first-child {
    border-left: 1px solid #e2e8f0;
    border-top-left-radius: 12px;
    border-bottom-left-radius: 12px;
}

.invoice-table tr td:last-child {
    border-right: 1px solid #e2e8f0;
    border-top-right-radius: 12px;
    border-bottom-right-radius: 12px;
}

.status-chip {
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 12px;
}

.status-completed {
    background: rgba(34, 197, 94, 0.15);
    color: #15803d;
}

.status-progress {
    background: rgba(234, 179, 8, 0.15);
    color: #b45309;
}

.status-rejected {
    background: rgba(248, 113, 113, 0.18);
    color: #b91c1c;
}

.side-card {
    min-height: 360px;
}

.dropdown-btn {
    border: none;
    background: #f1f5f9;
    padding: 6px 14px;
    border-radius: 999px;
    font-size: 12px;
    color: #475569;
}

.donut-wrapper {
    display: flex;
    gap: 16px;
    align-items: center;
}

.donut-wrapper canvas {
    max-width: 180px;
}

.legend-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.legend-list li {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 10px;
}

.legend-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #4f46e5;
}

.chart-wrapper {
    position: relative;
    min-height: 280px;
}

.chart-highlight {
    position: absolute;
    top: 40%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #fff;
    padding: 12px 18px;
    border-radius: 16px;
    box-shadow: 0 15px 40px rgba(15, 23, 42, 0.12);
    text-align: center;
}

.summary-card ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.summary-card li {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #f1f5f9;
}

.summary-icon {
    width: 34px;
    height: 34px;
    border-radius: 12px;
    background: #eef2ff;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6366f1;
    margin-right: 10px;
}

@media (max-width: 992px) {
    .content-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
    // Tab switching for Latest Invoice List (Ticketing / Hotel)
    document.addEventListener('DOMContentLoaded', function() {
        var totalThs = document.querySelectorAll('.ticketing-head th:nth-child(7)');
        document.querySelectorAll('.tabs .tab').forEach(function(tab) {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.tabs .tab').forEach(function(t){ t.classList.remove('active'); });
                tab.classList.add('active');

                var target = tab.getAttribute('data-tab');
                if (target === 'hotel') {
                    document.querySelectorAll('.ticketing-body').forEach(function(el){ el.style.display = 'none'; });
                    document.querySelectorAll('.hotel-body').forEach(function(el){ el.style.display = ''; });
                    totalThs.forEach(function(th){ th.style.display = 'none'; });
                } else {
                    document.querySelectorAll('.hotel-body').forEach(function(el){ el.style.display = 'none'; });
                    document.querySelectorAll('.ticketing-body').forEach(function(el){ el.style.display = ''; });
                    totalThs.forEach(function(th){ th.style.display = ''; });
                }
            });
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const incomeCanvas = document.getElementById('incomeChart');
        const airlineCanvas = document.getElementById('airlinesChart');

        const chartSeries = @json($chartSeries);
        const monthsLabels = @json($months);
        const ticketMonthlyData = @json($ticketMonthly);
        const hotelMonthlyData = @json($hotelMonthly);
        const yearsLabels = @json($years);
        const ticketYearlyData = @json($ticketYearly);
        const hotelYearlyData = @json($hotelYearly);
        const ticketMonthlyTotal = @json($ticketMonthlyTotal);
        const hotelMonthlyTotal = @json($hotelMonthlyTotal);
        const ticketYearlyTotal = @json($ticketYearlyTotal);
        const hotelYearlyTotal = @json($hotelYearlyTotal);
        const airlinesMonthly = @json(array_column($airlinesMonthly, 'label'));
        const airlineTotalsMonthly = @json(array_column($airlinesMonthly, 'total'));
        const airlinesYearly = @json(array_column($airlinesYearly, 'label'));
        const airlineTotalsYearly = @json(array_column($airlinesYearly, 'total'));

        if (incomeCanvas) {
            const incomeCtx = incomeCanvas.getContext('2d');

            // helper to draw rounded rect for indicator box
            function roundRect(ctx, x, y, w, h, r) {
                if (w < 2 * r) r = w / 2;
                if (h < 2 * r) r = h / 2;
                ctx.beginPath();
                ctx.moveTo(x + r, y);
                ctx.arcTo(x + w, y, x + w, y + h, r);
                ctx.arcTo(x + w, y + h, x, y + h, r);
                ctx.arcTo(x, y + h, x, y, r);
                ctx.arcTo(x, y, x + w, y, r);
                ctx.closePath();
                ctx.fill();
                ctx.stroke();
            }

            // plugin draws vertical hover line and indicator box (index indicator)
            const indexPlugin = {
                id: 'indexPlugin',
                afterInit(chart) {
                    chart._activeIndex = chart.data.labels.length - 1;
                },
                afterEvent(chart, args) {
                    const e = args.event;
                    const points = chart.getElementsAtEventForMode(e, 'nearest', {intersect: false}, false);
                    if (points.length) {
                        chart._activeIndex = points[0].index;
                        chart.draw();
                    }
                },
                afterDraw(chart) {
                    const ctx = chart.ctx;
                    const index = (chart._activeIndex != null) ? chart._activeIndex : (chart.data.labels.length - 1);
                    if (!chart.scales.x) return;
                    const x = chart.scales.x.getPixelForValue(index);

                    // vertical line
                    ctx.save();
                    ctx.beginPath();
                    ctx.moveTo(x + 0.5, chart.chartArea.top);
                    ctx.lineTo(x + 0.5, chart.chartArea.bottom);
                    ctx.strokeStyle = 'rgba(99,102,241,0.16)';
                    ctx.lineWidth = 2;
                    ctx.stroke();
                    ctx.restore();

                    // indicator box
                    const ticketVal = chart.data.datasets[0].data[index] || 0;
                    const hotelVal = chart.data.datasets[1].data[index] || 0;
                    const boxWidth = 200;
                    const boxHeight = 56;
                    const boxX = chart.chartArea.right - boxWidth - 12;
                    const boxY = chart.chartArea.top + 12;

                    ctx.save();
                    ctx.fillStyle = 'rgba(255,255,255,0.98)';
                    ctx.strokeStyle = 'rgba(2,6,23,0.06)';
                    ctx.lineWidth = 1;
                    roundRect(ctx, boxX, boxY, boxWidth, boxHeight, 8);

                    ctx.fillStyle = '#111827';
                    ctx.font = '600 13px sans-serif';
                    ctx.fillText('Ticket: Rp ' + Number(ticketVal || 0).toLocaleString(), boxX + 12, boxY + 20);
                    ctx.fillStyle = '#111827';
                    ctx.font = '600 13px sans-serif';
                    ctx.fillText('Hotel:  Rp ' + Number(hotelVal || 0).toLocaleString(), boxX + 12, boxY + 40);
                    ctx.restore();
                }
            };

            const incomeChart = new Chart(incomeCtx, {
                type: 'line',
                data: {
                    labels: monthsLabels,
                    datasets: [
                        {
                            label: 'Ticket Income',
                            data: ticketMonthlyData,
                            borderColor: '#1f78b4',
                            backgroundColor: function(context) {
                                const g = incomeCtx.createLinearGradient(0, 0, 0, 300);
                                g.addColorStop(0, 'rgba(31,120,180,0.18)');
                                g.addColorStop(1, 'rgba(31,120,180,0.02)');
                                return g;
                            },
                            fill: true,
                            tension: 0.4,
                            borderWidth: 3,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#ffffff',
                            pointBorderWidth: 2
                        },
                        {
                            label: 'Hotel Income',
                            data: hotelMonthlyData,
                            borderColor: '#f97316',
                            backgroundColor: function(context) {
                                const g = incomeCtx.createLinearGradient(0, 0, 0, 300);
                                g.addColorStop(0, 'rgba(249,115,22,0.14)');
                                g.addColorStop(1, 'rgba(249,115,22,0.02)');
                                return g;
                            },
                            fill: true,
                            tension: 0.4,
                            borderWidth: 3,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#ffffff',
                            pointBorderWidth: 2
                        }
                    ]
                },
                options: {
                    plugins: { legend: { display: true }, tooltip: { enabled: false } },
                    interaction: { mode: 'index', intersect: false },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#e2e8f0' },
                            ticks: {
                                callback: function(value) {
                                    if (!value) return 'Rp0';
                                    return 'Rp' + Number(value).toLocaleString();
                                }
                            }
                        },
                        x: { grid: { display: false } }
                    }
                },
                plugins: [indexPlugin]
            });

            // income pill menu handlers (unchanged behavior)
            const incomeBtn = document.getElementById('incomeRangeBtn');
            const incomeMenu = document.getElementById('incomeRangeMenu');
            if (incomeBtn && incomeMenu) {
                incomeBtn.addEventListener('click', function(ev){ ev.stopPropagation(); incomeMenu.style.display = (incomeMenu.style.display === 'block') ? 'none' : 'block'; });
                incomeMenu.querySelectorAll('li').forEach(function(li){
                    li.addEventListener('click', function(){
                        const v = this.getAttribute('data-value');
                        incomeBtn.innerHTML = (v === 'yearly' ? 'Yearly <i class="fa fa-chevron-down" style="margin-left:8px"></i>' : 'Monthly <i class="fa fa-chevron-down" style="margin-left:8px"></i>');
                        if (v === 'yearly'){
                            incomeChart.data.labels = yearsLabels;
                            incomeChart.data.datasets[0].data = ticketYearlyData;
                            incomeChart.data.datasets[1].data = hotelYearlyData;
                            const tTot = document.getElementById('ticketTotal');
                            const hTot = document.getElementById('hotelTotal');
                            if (tTot) tTot.textContent = 'Rp' + (ticketYearlyTotal || 0).toLocaleString();
                            if (hTot) hTot.textContent = 'Rp' + (hotelYearlyTotal || 0).toLocaleString();
                        } else {
                            incomeChart.data.labels = monthsLabels;
                            incomeChart.data.datasets[0].data = ticketMonthlyData;
                            incomeChart.data.datasets[1].data = hotelMonthlyData;
                            const tTot = document.getElementById('ticketTotal');
                            const hTot = document.getElementById('hotelTotal');
                            if (tTot) tTot.textContent = 'Rp' + (ticketMonthlyTotal || 0).toLocaleString();
                            if (hTot) hTot.textContent = 'Rp' + (hotelMonthlyTotal || 0).toLocaleString();
                        }
                        incomeChart.update();
                        incomeMenu.style.display = 'none';
                    });
                });
                document.addEventListener('click', function(e){ if (!incomeMenu.contains(e.target) && e.target !== incomeBtn) { incomeMenu.style.display = 'none'; } });
            }
        }

        if (airlineCanvas) {
            const airlinesCtx = airlineCanvas.getContext('2d');
            const airlinesChart = new Chart(airlinesCtx, {
            type: 'doughnut',
            data: {
                labels: airlinesMonthly,
                datasets: [{
                    data: airlineTotalsMonthly,
                    backgroundColor: ['#6366f1', '#f97316', '#22c55e', '#3b82f6', '#ef4444', '#06b6d4'],
                    borderWidth: 0
                }]
            },
            options: {
                cutout: '70%',
                plugins: { legend: { display: false } }
            }
            });

            // Update legend list and chart when range changes
            function renderLegend(labels, totals) {
                const legend = document.querySelector('.legend-list');
                if (!legend) return;
                legend.innerHTML = '';
                for (let i=0;i<labels.length;i++){
                    const li = document.createElement('li');
                    li.innerHTML = `<span class="legend-dot" style="background:${['#6366f1','#f97316','#22c55e','#3b82f6','#ef4444','#06b6d4'][i%6]}"></span><div><p>${labels[i]}</p><small>${totals[i]} tiket</small></div>`;
                    legend.appendChild(li);
                }
            }

            // initial legend
            renderLegend(airlinesMonthly, airlineTotalsMonthly);

            // button + popover menu handlers (restores original pill UI)
            const rangeBtn = document.getElementById('airlineRangeBtn');
            const rangeMenu = document.getElementById('airlineRangeMenu');
            if (rangeBtn && rangeMenu) {
                rangeBtn.addEventListener('click', function(ev){ ev.stopPropagation(); rangeMenu.style.display = (rangeMenu.style.display === 'block') ? 'none' : 'block'; });
                rangeMenu.querySelectorAll('li').forEach(function(li){
                    li.addEventListener('click', function(){
                        const v = this.getAttribute('data-value');
                        rangeBtn.innerHTML = (v === 'yearly' ? 'Yearly <i class="fa fa-chevron-down" style="margin-left:8px"></i>' : 'Monthly <i class="fa fa-chevron-down" style="margin-left:8px"></i>');
                        if (v === 'yearly'){
                            airlinesChart.data.labels = airlinesYearly;
                            airlinesChart.data.datasets[0].data = airlineTotalsYearly;
                            renderLegend(airlinesYearly, airlineTotalsYearly);
                        } else {
                            airlinesChart.data.labels = airlinesMonthly;
                            airlinesChart.data.datasets[0].data = airlineTotalsMonthly;
                            renderLegend(airlinesMonthly, airlineTotalsMonthly);
                        }
                        airlinesChart.update();
                        rangeMenu.style.display = 'none';
                    });
                });
                document.addEventListener('click', function(e){ if (!rangeMenu.contains(e.target) && e.target !== rangeBtn) { rangeMenu.style.display = 'none'; } });
            }
        }

            // Customers monthly/yearly dynamic list
            const customersMonthly = @json($customersMonthly);
            const customersYearly = @json($customersYearly);
            const custBtn = document.getElementById('customerRangeBtn');
            const custMenu = document.getElementById('customerRangeMenu');
            const custList = document.getElementById('customerList');

            function renderCustomers(list) {
                if (!custList) return;
                custList.innerHTML = '';
                list.forEach(function(c){
                    const li = document.createElement('li');
                    li.style = 'display:flex; justify-content:space-between; align-items:center; padding:10px 0; border-bottom:1px solid #f1f5f9;';
                    li.innerHTML = `<div style="display:flex; gap:10px; align-items:center;"><div class="summary-icon" style="width:36px;height:36px;border-radius:10px;"><i class="fa fa-user"></i></div><div><p style="margin:0">${c.label}</p><small style="color:#6b7280">${(c.total||0).toLocaleString()}</small></div></div><strong>Rp${(c.total||0).toLocaleString()}</strong>`;
                    custList.appendChild(li);
                });
            }

            if (custBtn && custMenu) {
                custBtn.addEventListener('click', function(ev){ ev.stopPropagation(); custMenu.style.display = (custMenu.style.display === 'block') ? 'none' : 'block'; });
                custMenu.querySelectorAll('li').forEach(function(li){
                    li.addEventListener('click', function(){
                        const v = this.getAttribute('data-value');
                        custBtn.innerHTML = (v === 'yearly' ? 'Yearly <i class="fa fa-chevron-down" style="margin-left:8px"></i>' : 'Monthly <i class="fa fa-chevron-down" style="margin-left:8px"></i>');
                        if (v === 'yearly') renderCustomers(customersYearly); else renderCustomers(customersMonthly);
                        custMenu.style.display = 'none';
                    });
                });
                document.addEventListener('click', function(e){ if (!custMenu.contains(e.target) && e.target !== custBtn) { custMenu.style.display = 'none'; } });
            }

            // initial customers render
            renderCustomers(customersMonthly);
    });
</script>
@endsection

