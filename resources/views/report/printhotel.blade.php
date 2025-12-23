<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        body {
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
            color: #333;
            text-align: left;
            font-size: 12px;
            margin: 0;
        }
        .container {
            margin-left: -15px;
            padding: 0px;
            width: 1060px;
            height: auto;
            background-color: #fff;
        }
        caption {
            font-size: 28px;
            margin-bottom: 15px;
        }
        td,
        tr,
        th {
            padding: 3px;
            border: 1px solid #333;
        }
        h4,
        p {
            margin: 0px;
        }
        .table-no-border {
            border: none;
        }
        .tr-no-border td {
            border: none;
        }
        .mb-15 {
            margin-bottom: 15px;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .text-orange {
            color: #ff9e40;
        }
        .td-total {
            border-left: 0;
            border-right: 0;
            border-bottom: 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <table style="width:100%" class="table-no-border mb-15">
            <tr class="tr-no-border">
                <td style="width: 25%;">
                    <img src="{{ asset('lghani-fit.png') }}" alt="" height="100px">
                </td>
                <td style="width: 75%;" class="text-right">
                    L Ghani Tour & Travel<br>
                    <span class="text-orange">KOMPLEK PERMATA 2 CIMAHI</span> BLOK M6 NO 2 RT 01 RW 24 KEL. TANI MULYA KEC. NGAMPRAH KAB. BANDUNG BARAT 40552<br>
                    <a href="mailto:Lghani_travel@ymail.com">Lghani_travel@ymail.com</a> <a href="tel:+6285621511280">085621511280</a> / <a href="tel:+6282117211162">082117211162</a>
                </td>
            </tr>
        </table>
        <h3 class="text-center mb-15">Sales Hotel Period <?=$period?></h3>
        <table style="width: 100%;border-collapse: collapse;border: 1px solid #333;">
             <thead>
                <tr>
                    <th class="text-center" style="width: 2%">No</th>
                    <th class="text-center" style="width: 8%">No Invoice</th>
                    <th class="text-center" style="width: 8%">No Voucher</th>
                    <th class="text-center" style="width: 9%">Hotel</th>
                    <th class="text-center" style="width: 9%">Room</th>
                    <th class="text-center" style="width: 4%">Room No</th>
                    <th class="text-center" style="width: 6%">Meal Type</th>
                    <th class="text-center" style="width: 5%">Guest</th>
                    <th class="text-center" style="width: 5%">Check In</th>
                    <th class="text-center" style="width: 5%">Check Out</th>
                    <th class="text-center" style="width: 6%">Price</th>
                    <th class="text-center" style="width: 6%">NTA</th>
                    <th class="text-center" style="width: 6%">Discount</th>
                    <th class="text-center" style="width: 6%">Profit</th>
                    <th class="text-center" style="width: 5%">Date Issued</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $no = 1;
                    $totalPrice = 0;
                    $totalNta = 0;
                    $totalDiscount = 0;
                    $totalProfit = 0;
                @endphp
                @foreach ($invoices as $inv)
                    @php
                       $startdate = strtotime($inv->check_in);
$enddate = strtotime($inv->check_out) -  86400;

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
                        $discount = ($inv->discount);
                        $profit = $total - $nta - $discount;

                        $totalPrice += $total;
                        $totalNta += $nta;
                        $totalDiscount += $discount;
                        $totalProfit += $profit;
                    @endphp

                <tr>
                    <td>{{ $no++ }}</td>
                    <td class="text-center">{{ $inv->invoiceno }}</td>
                    <td class="text-center">{{ $inv->voucher_no }}</td>
                    <td>{{ $inv->hotel_name }}</td>
                    <td>{{ $inv->room_name }}</td>
                    <td class="text-center">{{ $inv->room_no }}</td>
                    <td class="text-center">{{ $inv->meal_type }}</td>
                    <td class="text-center">{{ $inv->adult }} adult,<br>{{ $inv->children }} children</td>
                    <td class="text-center">{{ date("d/m/Y", strtotime($inv->check_in)) }}</td>
                    <td class="text-center">{{ date("d/m/Y", strtotime($inv->check_out)) }}</td>
                    <td class="text-right">{{ number_format($total) }}</td>
                    <td class="text-right">{{ number_format($nta) }}</td>
                    <td class="text-right">{{ number_format($discount) }}</td>
                    <td class="text-right">{{ number_format($profit) }}</td>
                    <td class="text-center">{{ date("d/m/Y", strtotime($inv->created_at)) }}</td>
                </tr>
                @endforeach

                <tr>
                    <td colspan="10" class="text-right"><b>Total</b></td>
                    <td class="text-right"><b>{{ number_format($totalPrice) }}</b></td>
                    <td class="text-right"><b>{{ number_format($totalNta) }}</b></td>
                    <td class="text-right"><b>{{ number_format($totalDiscount) }}</b></td>
                    <td class="text-right"><b>{{ number_format($totalProfit) }}</b></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <table style="width: 100%;" class="table-no-border">
            <tbody>
                <tr class="tr-no-border">
                    <td style="width: 50%"></td>
                    <td class="text-center" style="width: 20%;vertical-align:top;">
                        Created By<br><br><br><br>
                        Indriawati
                    </td>
                    <td style="width: 30px"></td>
                    <td class="text-center" style="width: 20%;vertical-align:top;">
                        Approved By<br><br><br><br>
                        Herry Rochiman
                    </td>
                </tr>
                <tr class="tr-no-border">
                    <td style="width: 50%"></td>
                    <td style="border-top: 1px solid rgba(0,0,0,0.8) !important;">Admin</td>
                    <td style="width: 30px"></td>
                    <td style="border-top: 1px solid rgba(0,0,0,0.8) !important;">Direktur</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>