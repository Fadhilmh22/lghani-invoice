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
        E-Mail : l.ghani_travel@yahoo.com, lghani_travel@ymail.com</p>
        </div>
        <img src="{{ asset('lghani-fit.png') }}" alt="" height="60px" style="float: right;padding-top: 20px;">
        </div>
        <br>
        <div class="horizontal-line"></div>

        <div class="mb-15">
            <h2 class="text-center">HOTEL VOUCHER</h2>
        </div>

        @if(!empty($voucher['booking_id']))
            <div style="width: 100%; height: 20px;">
                <div style="float: left;font-size: 14px;">
                    <b>BOOKING ID</b> : {{ $voucher['booking_no'] }}
                </div>
                <div style="float: right;font-size: 14px;">
                    <b>BOOKING STATUS</b> : CONFIRMED
                </div>
            </div>
        @endif
        <div class="horizontal-line"></div>

        <table style="width: 100%;" class="table-no-border mb-15">
            <tr>
                <td colspan="6">
                    <h3 style="margin-top:0">{{ $hotel['hotel_name'] }} @if($hotel['region'] != "") ({{ $hotel['region'] }}) @endif</h3>
                    <p>{{ $hotel['address'] }}</p>
                    @if($hotel['phone'] != "")
                        <p>Tel : {{ $hotel['phone'] }}</p>
                    @endif
                </td>
            </tr>
            <tr>
                <td colspan="6"><div class="horizontal-line"></div></td>
            </tr>
            <tr>
                <td width="23%"><b>GUEST / GROUP'S NAME</b></td>
                <td class="table-separator">:</td>
                <td width="27%">{{ $voucher['booker'] }}</td>

                <td width="23%"><b>NATIONALITY</b></td>
                <td class="table-separator">:</td>
                <td width="27%">{{ $voucher['nationality'] }}</td>
            </tr>
            <tr>
                <td colspan="6"><div class="horizontal-line"></div></td>
            </tr>
            <tr>
                <td width="23%"><b>ATTENTION</b></td>
                <td class="table-separator">:</td>
                <td width="27%">{{ $voucher['attention'] }}</td>

                <td width="23%"><b>VOUCHER NO.</b></td>
                <td class="table-separator">:</td>
                <td width="27%">{{ $voucher['voucher_no'] }}</td>
            </tr>
            <tr>
                <td width="23%"><b>ISSUED DATE</b></td>
                <td class="table-separator">:</td>
                <td width="27%">{{ $voucher['issued_date'] }}</td>

                <td width="23%"><b>AGENCY REF NO.</b></td>
                <td class="table-separator">:</td>
                <td width="27%">{{ $voucher['no_booker_agent'] }}</td>
            </tr>
            <tr>
                <td colspan="6"><div class="horizontal-line"></div></td>
            </tr>
        </table>

        <table style="width: 100%;" class="table-no-border">
            <thead>
                <tr>
                    <th>No of Rooms(s)</th>
                    <th>Room Name</th>
                    <th>Meal Type</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $no = 1;
                @endphp
                @foreach ($voucherRoom as $hRoom)
                    <tr class="tr-no-border">
                        <td>{{ $no++ }}</td>
                        <td>{{  $rooms[ $hRoom['room_id'] ]->room_name }} , {{  $rooms[ $hRoom['room_id'] ]->room_type }} , {{  $rooms[ $hRoom['room_id'] ]->bed_type }}</td>
                        <td>{{ $hRoom['meal_type'] }}</td>
                        <td>{{ $hRoom['check_in'] }}</td>
                        <td>{{ $hRoom['check_out'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="horizontal-line"></div>
        <br>

        <table style="width: 100%;" class="table-no-border">
            <thead>
                <tr>
                    <th>Guest Name</th>
                    <th>Child Age(s)</th>
                    <th>No of Extrabed</th>
                    <th>Booking Status</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $no = 1;
                @endphp
                @foreach ($voucherRoom as $hRoom)
                    <tr class="tr-no-border">
                        <td>
                            @foreach ($hRoom['hotelguest'] as $hGuest)
                                @if($hGuest['guest_last_name'] != "")
                                    <p>{{ $hGuest['guest_gender'] }}. {{ $hGuest['guest_last_name'] }}, {{ $hGuest['guest_first_name'] }}</p>
                                @else
                                    <p>{{ $hGuest['guest_gender'] }}. {{ $hGuest['guest_first_name'] }}, {{ $hGuest['guest_first_name'] }}</p>
                                @endif
                            @endforeach
                        </td>
                        <td>
                            @foreach ($hRoom['hotelguest'] as $hGuest)
                                @if($hGuest['guest_age'] != "")
                                    <p>{{ $hGuest['guest_age'] }}</p>
                                @else
                                    <p>NA</p>
                                @endif
                            @endforeach
                        </td>
                        <td>{{ $hRoom['no_of_extrabed'] == 0 ? "-" : $hRoom['no_of_extrabed'] }}</td>
                        <td>CONF</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="horizontal-line"></div>
        <br>
        <div style="background: #d1e2e8; padding: 10px">
            @if( $voucher['remark'] != "" )
                <h4 style="margin-top:0">SPECIAL REQUEST :</h4>
                <p class="text-justify">{{ $voucher['remark'] }}</p>
                <br>
            @endif

            <h4 style="margin-top:0">HOTEL MESSAGE :</h4>
            <p class="text-justify">Special Instructions - Please note that our rates do not include expenses or fees which must be sttled by the guest at the hotel, such as resort fees, city tax, parking, gratuities, and such others.</p>
        </div>
        <br>
        <p class="text-justify">*Bedding requested is subject to availability of hotel.</p>
        <br>
        <h4>Amendment Policy :</h4>
        <p class="text-justify">Amendment is not allowed.</p>
    </div>
</body>

</html>