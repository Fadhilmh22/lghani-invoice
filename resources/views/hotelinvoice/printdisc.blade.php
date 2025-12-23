<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Invoice</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        html {
            margin: 0;
        }
        body {
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
            color: #333;
            text-align: left;
            font-size: 12px;
            margin: 10;
        }
        table {
            border-collapse: collapse;
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
            background-color: #000;
            color: #FFF;
            text-align: left;
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
        .text-justify {
            text-align: justify;
        }
        .text-orange {
            color: #ff9e40;
        }

        .horizontal-line {
            width: 100%;
            border: 1px solid #000;
        }
        .table-separator {
            width: 8px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <div style="height: 80px;">
            <div style="float: left;">
                <h2 style="margin-bottom: 5px;">L.Ghani Tour & Travel</h2>
                <p>Komp. Permata 2 Cimahi City : Bandung, Zipcode : 40552, Country : Indonesia</p>
                <p>Phone : 022-93438006-07, 082117211162, Fax : 022-6121284</p>
                <p>YM / E-Mail : l.ghani_travel@yahoo.com, in_in_bgt@yahoo.com</p>
            </div>
            <img src="{{ asset('lghani-fit.png') }}" alt="" height="60px" style="float: right;padding-top: 20px;">
        </div>
        <br>
        <div class="horizontal-line"></div>

        <div class="mb-15">
            <h2 class="text-center">INVOICE HOTEL</h2>
        </div>
        <div>
        <table style="width: 100%;" class="table-no-border mb-15">
            <tr>
                <td style="width: 65%">
                    <table>
                        <tr>
                            <td>To</td>
                            <td>:</td>
                            <td>{{ $invoice->customer->company }}</td>
                        </tr>
                         @if (!empty($invoice->customer->alamat))
                        <tr>
                            <td>Address</td>
                            <td>:</td>
                            <td style="height: 3em; overflow: hidden;">{{ $invoice->customer->alamat }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td>Agen No</td>
                            <td>:</td>
                            <td>BD0132</td>
                        </tr>
                    </table>
                </td>
                <td style="width: 50%"></td>
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
                            <td>{{ $invoice->hotel_due_date == "" ? "00/00/0000" : date("d/m/Y", strtotime($invoice->hotel_due_date)) }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td>Payment</td>
                            <td>:</td>
                            <td>{{ $invoice->customer->payment }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        </div>

        <table style="width: 100%;" class="table-no-border">
            <thead>
                <tr>
                    <th width="5%" >No</th>
                    <th width="50%" >Description</th>
                    <th width="30%" >Guest</th>
                    <th width="15%" >Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>{{ $voucher->booking_no }}/{{ $hotel->hotel_name }} {{ date("d/m/Y", strtotime($voucher->check_in)) }} - {{ date("d/m/Y", strtotime($voucher->check_out)) }}</td>
                    <td></td>
                    <td></td>
                </tr>
                @php
                    $total = 0;
                @endphp
                @foreach($voucherRoom as $room_id => $hRoom)
                    @php
                        $totalWeekDay = $rooms[ $room_id ]->weekday_price * $weekDay;
                        $totalWeekEnd = $rooms[ $room_id ]->weekend_price * $weekEnd;
                        $total = $total + $totalWeekDay + $totalWeekEnd;
                    @endphp
                    @if( $weekDay > 0 )
                        <tr>
                            <td></td>
                            <td>{{  $rooms[ $room_id ]->room_name }} Rp {{ $rooms[ $room_id ]->weekday_price }} X {{ $hRoom['count'] }} Room X {{ $weekDay }} Night weekday</td>
                            <td @if ( $weekEnd > 0 ) rowspan='2' @endif>
                                @foreach( $hRoom['hotelguest'] as $arrGuest )
                                    @foreach( $arrGuest as $hGuest )
                                        @if($hGuest['guest_last_name'] != "")
                                            <p>{{ $hGuest['guest_gender'] }}. {{ $hGuest['guest_last_name'] }}, {{ $hGuest['guest_first_name'] }}</p>
                                        @else
                                            <p>{{ $hGuest['guest_gender'] }}. {{ $hGuest['guest_first_name'] }}, {{ $hGuest['guest_first_name'] }}</p>
                                        @endif
                                    @endforeach
                                @endforeach
                            </td>
                            <td class="text-right"><span style="float: left">Rp </span><span style="float: right">{{ number_format($totalWeekDay) }}</span></td>
                        </tr>
                    @endif
                    @if( $weekEnd > 0 )
                        <tr>
                            <td></td>
                            <td>{{  $rooms[ $room_id ]->room_name }} Rp {{ $rooms[ $room_id ]->weekend_price }} X {{ $hRoom['count'] }} Room X {{ $weekEnd }} Night weekend</td>
                            @if( $weekDay == 0 )
                                <td>
                                    @foreach( $hRoom['hotelguest'] as $arrGuest )
                                        @foreach( $arrGuest as $hGuest )
                                            @if($hGuest['guest_last_name'] != "")
                                                <p>{{ $hGuest['guest_gender'] }}. {{ $hGuest['guest_last_name'] }}, {{ $hGuest['guest_first_name'] }}</p>
                                            @else
                                                <p>{{ $hGuest['guest_gender'] }}. {{ $hGuest['guest_first_name'] }}, {{ $hGuest['guest_first_name'] }}</p>
                                            @endif
                                        @endforeach
                                    @endforeach
                                </td>
                            @endif
                            <td class="text-right"><span style="float: left">Rp </span><span style="float: right">{{ number_format($totalWeekEnd) }}</span></td>
                        </tr>
                    @endif
                @endforeach
                <tr>
                    <td></td>
                    <td><b>Booker {{ $voucher['booker'] }}<b></td>
                    <td></td>
                    <td></td>
                </tr>
                
                <tr class="tr-no-border">
                    <td colspan="4" style="border-top: 1px solid rgba(0,0,0,0.6) !important; padding: 0 !important;">
                    </td>
                </tr>
                <tr class="tr-no-border">
                    <td colspan="3" class="text-right">Sub Total</td>
                    <td class="text-right">Rp {{ number_format($total * $hRoom['count']) }}</td>
                </tr>
                <tr class="tr-no-border">
                    <td colspan="3" class="text-right">Discount</td>
                    <td class="text-right">Rp {{ number_format($invoice->discount) }}</td>
                </tr>
                <tr class="tr-no-border">
                    <td colspan="3" class="text-right">Grand Total</td>
                    <td class="text-right">Rp {{ number_format($total * $hRoom['count'] - $invoice->discount) }}</td>
                </tr>
               
            </tbody>
        </table>

        <br>

        <table style="width: 100%;" class="table-no-border">
            <tr>
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
        </table>

        <br>
        <p class="text-center">..........Thank You..........</p>
    </div>
</body>

</html>