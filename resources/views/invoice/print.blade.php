<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Invoice</title>
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
        margin: 0 auto;
        padding: 2px;
        width: 720px;
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
            vertical-align: top;
            padding: 3px;
            border: none;
/*            width: 185px;*/
        }

    th {
        background-color: #f0f0f0;
    }

    h4,
    p {
        margin: 0px;
    }

    .table-no-border {
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
    </style>
</head>

<body>
    <div class="container">
        <h2>L.Ghani Tour & Travel</h2>
        <div class="text-center mb-15">
            <img src="{{ asset('lghani-fit.png') }}" alt="" height="100px">
        </div>
        
        <div>
        <h3 class="text-center mb-15">INVOICE TICKET AIRLINES</h3>
        </div>
        <table style="width: 100%;" class="table-no-border mb-15">
            <tr class="tr-no-border">
                <td style="width: 65%">
                    <table>
                        <tr>
                            <td>To</td>
                            <td>:</td>
                            <td>{{ $invoice->customer->company }}</td>
                        </tr>
                         @if (!empty($invoice->customer->phone))
                         <tr>
                            <td>Phone</td>
                            <td>:</td>
                            <td>{{ $invoice->customer->phone }}</td>
                         </tr>
                         @endif
                    
                         @if (!empty($invoice->customer->alamat))
                        <tr>
                            <td>Address</td>
                            <td>:</td>
                            <td style="height: 3em; overflow: hidden;">{{ $invoice->customer->alamat }}</td>
                        </tr>
                        @endif
                         <br>
                        <tr>
                            <td>Agen No</td>
                            <td>:</td>
                            <td>BD0132</td>
                        </tr>
                    </table>
                </td>
                <td style="width: 20%"></td>
                <td style="width: 50%">
                    <table>
                        <tr>
                            <td>No Invoice</td>
                            <td>:</td>
                            <td>{{ $invoice->invoiceno }}</td>
                        </tr>
                        <tr>
                            <td>Invoice Date</td>
                            <td>:</td>
                            <td>{{ date("d/m/Y", strtotime($invoice->created_at)) }}</td>
                        </tr>
                            @if ($invoice->customer->payment != 'Cash')
                        <tr>
                            <td>Due Date</td>
                            <td>:</td>
                                <td>{{ date("d/m/Y", strtotime($invoice->created_at . ' + 14 days')) }}</td>
                        </tr>
                            @endif
                        <tr>
                            <td>Booker</td>
                            <td>:</td>
                            <td style="white-space: nowrap;">{{ $invoice->customer->gender }}. {{ $invoice->customer->booker }}</td>
                        </tr>
                        <tr>
                            <td>Payment</td>
                            <td>:</td>
                            <td>{{ $invoice->customer->payment }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        
                <br><br>
        <table style="width: 100%;" class="table-no-border mb-15">
            <thead>
        <tr class="tr-no-border">
            <th style="width: 5%;" class="text-center">No</th>
            <th style="width: 25%;" class="text-center">Name</th>
            <th style="width: 50%;" class="text-center">Details</th>
            <th style="width: 20%;" class="text-center">Amount</th>
        </tr>
    </thead>
            <tbody>
                @php
                $no = 1;
                $totalPaxPaid = 0;
                $totalDiscount = 0;
                @endphp
                @foreach ($details as $detail)
                @php
                $totalPaxPaid += $detail->pax_paid;
                $totalDiscount += $detail->discount;
                @endphp
                <tr class="tr-no-border">
                    <td style="width: 2%">{{ $no++ }}</td>
                    <td style="width: 25%">{{ $detail->name }}, {{ $detail->genre }}</td>
                    <td style="width: 65%">{{ $detail->booking_code }} / {{ $detail->airlines_code }} -
                        {{ $detail->airlines_no }} - {{ $detail->class }} / {{ $detail->ticket_no }} /
                        {{ $detail->route }} / {{ $detail->depart_date }} / {{ $detail->return_date }}</td>
                    <td colspan="4" class="text-right">Rp {{ number_format($detail->pax_paid) }}</td>
                </tr>
                @endforeach

                <tr class="tr-no-border">
                    <td colspan="4" style="border-top: 1px solid rgba(0,0,0,0.6) !important; padding: 0 !important;">
                    </td>
                </tr>

                <tr class="tr-no-border">
                    <td colspan="3" class="text-right">Sub Total</td>
                    <td class="text-right">Rp {{ number_format($totalPaxPaid) }}</td>
                </tr>
                <tr class="tr-no-border">
                    <td colspan="3" class="text-right">Grand Total</td>
                    <td class="text-right">Rp {{ number_format($totalPaxPaid) }}</td>
                </tr>
            </tbody>
        </table>
        <table style="width: 100%;" class="table-no-border">
            <tbody>
                <tr class="tr-no-border">
                <td style="width: 50%">
                    <table style="width: 100%">
                        <tr>
                            <td>BCA CAB. ABD RACHMAN SALEH</td>
                        </tr>
                        <tr>
                            <td colspan="2"><b>515.090.5674</b><span style="margin-left: 2em; vertical-align: top;">AN. INDRIAWATI</span></td>
                        </tr>
                        <tr>
                            <td  style="padding-top: 20px;">MANDIRI CAB. PADJAJARAN</td>
                        </tr>
                        <tr>
                            <td colspan="2"><b>132.00.153.6687.6</b><span style="margin-left: 2em; vertical-align: top;">AN. CV. Langit Ghani</span></td>
                        </tr>
                        <tr>
                            <td  style="padding-top: 20px;">BRI</td>
                        </tr>
                        <tr>
                            <td colspan="2"><b>1070-01-003873-50-3 (002)</b><span style="margin-left: 2em; vertical-align: top;">AN. Herry Rochiman</span></td>
                        </tr>
                    </table>
                </td>
                <td style="width: 10%"></td>
                <td style="width: 40%">
                    <table style="width: 100%">
                        <tr>
                            <td class="text-center" style="width: 20%;vertical-align:top;">
                             Received By
                            @if(auth()->user()->role == 'Owner')
                                @for($i = 0; $i < 9; $i++)
                                    <br>
                                @endfor
                            @else
                                @for($i = 0; $i < 7; $i++)
                                    <br>
                                @endfor
                            @endif
                            {{ $invoice->customer->booker }}
                            </td>
                    <td class="text-center" style="width: 20%;vertical-align:top;">
                       Prepared By
                         @if(auth()->user()->role == 'Owner')
                            <img src="{{ asset('caplogo.png') }}" alt="" height="120px">
                        <br>
                        Indriawati
                        @else
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        Indriawati
                        @endif
                    </td>
                        </tr>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
        <div class="text-center" style="margin-top: 30px;margin-bottom: 15px;">
            <img src="{{ asset('lghani-footer.png') }}" alt="" width="50%">
        </div>
        <div class="text-center">
            <span class="text-orange">KOMPLEK PERMATA 2 CIMAHI</span> BLOK M6 NO 2 RT 01 RW 24 KEL. TANI MULYA KEC.
            NGAMPRAH KAB. BANDUNG BARAT 40552<br>
            <a href="mailto:Lghani_travel@ymail.com">Lghani_travel@ymail.com</a> <a
                href="tel:+6285621511280">085621511280</a> / <a href="tel:+6282117211162">082117211162</a>
        </div>
    </div>
</body>

</html>