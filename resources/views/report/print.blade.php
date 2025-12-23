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
        <h3 class="text-center mb-15">Sales Ticket Period <?=$period?></h3>
        <table style="width: 100%;border-collapse: collapse;border: 1px solid #333;">
             <thead>
                <tr>
                    <th class="text-center" style="width: 4%">No</th>
                    <th class="text-center" style="width: 10%">No Invoice</th>
                    <th class="text-center" style="width: 6%">Airlines</th>
                    <th class="text-center" style="width: 6%">Class</th>
                    <th class="text-center" style="width: 6%">No Ticket</th>
                    <th class="text-center" style="width: 8%">Rute</th>
                    <th class="text-center" style="width: 8%">Pax Paid</th>
                    <th class="text-center" style="width: 8%">Price</th>
                    <th class="text-center" style="width: 8%">Discount</th>
                    <th class="text-center" style="width: 8%">NTA</th>
                    <th class="text-center" style="width: 8%">UP</th>
                    <th class="text-center" style="width: 8%">Profit</th>
                    <th class="text-center" style="width: 8%">Total Profit</th>
                    <th class="text-center" style="width: 14%">Date Issued</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $no = 1;
                    $total_pax_paid = 0;
                    $total_up = 0;
                    $total_price = 0;
                    $total_discount = 0;
                    $total_nta = 0;
                    $total_profit = 0;
                    $total_profit1 = 0;
                @endphp
                @foreach ($invoices as $invoice)

                    @php
                        $total_pax_paid += $invoice->pax_paid;
                        $total_price += $invoice->price;
                        $total_discount += $invoice->discount;
                        $total_nta += $invoice->nta;
                        $total_up += ($invoice->pax_paid - $invoice->price);
                        $total_profit += $invoice->profit;
                        $total_profit1 += (($invoice->pax_paid - $invoice->price)+$invoice->profit);
                    @endphp
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $invoice->invoiceno }}</td>
                    <td>{{ $invoice->airlines_code }}</td>
                    <td>{{ $invoice->class }}</td>
                    <td>{{ $invoice->ticket_no }}</td>
                    <td>{{ $invoice->route }}</td>
                    <td class="text-right">{{ number_format($invoice->pax_paid) }}</td>
                    <td class="text-right">{{ number_format($invoice->price) }}</td>
                    <td class="text-right">{{ number_format($invoice->discount) }}</td>
                    <td class="text-right">{{ number_format($invoice->nta) }}</td>
                    <td class="text-right">{{ number_format($invoice->pax_paid - $invoice->price) }}</td>
                    <td class="text-right">{{ number_format($invoice->profit) }}</td>
                    <td class="text-right">{{ number_format(($invoice->pax_paid - $invoice->price)+$invoice->profit) }}</td>
                    <td class="text-right">{{ $invoice->created_at }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <table style="width: 100%;" class="table-no-border mb-15">
            <tbody>
                <tr class="tr-no-border">
                    <td class="text-right" style="width: 4%"></td>
                    <td class="text-right" style="width: 10%"></td>
                    <td class="text-right" style="width: 6%"></td>
                    <td class="text-right" style="width: 6%"></td>
                    <td class="text-right" style="width: 10%"></td>
                    <td class="text-right" style="width: 8%;font-weight: bold;">Total</td>
                    <td class="text-right" style="width: 8%;font-weight: bold;">{{ number_format($total_pax_paid) }}</td>
                    <td class="text-right" style="width: 8%;font-weight: bold;">{{ number_format($total_price) }}</td>
                    <td class="text-right" style="width: 8%;font-weight: bold;">{{ number_format($total_discount) }}</td>
                    <td class="text-right" style="width: 8%;font-weight: bold;">{{ number_format($total_nta) }}</td>
                    <td class="text-right" style="width: 8%;font-weight: bold;">{{ number_format($total_up) }}</td>
                    <td class="text-right" style="width: 8%;font-weight: bold;">{{ number_format($total_profit) }}</td>
                    <td class="text-right" style="width: 8%;font-weight: bold;">{{ number_format($total_profit1) }}</td>
                    <td class="text-right" style="width: 8%"></td>
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
                    <td style="border-top: 1px solid rgba(0,0,0,0.8) !important;">Admin Ticket</td>
                    <td style="width: 30px"></td>
                    <td style="border-top: 1px solid rgba(0,0,0,0.8) !important;">Direktur</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>