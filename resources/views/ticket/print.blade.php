<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Booking Details - {{ $ticket->booking_code }}</title>
    <style>
        @page { margin: 15px; }
        body { font-family: Arial, sans-serif; font-size: 10px; color: #333; line-height: 1.3; margin: 0; padding: 10px; }
        .header { width: 100%; border-bottom: 1px solid #ddd; padding-bottom: 10px; margin-bottom: 10px; }
        .logo { width: 130px; }
        .booking-title { font-size: 18px; font-weight: bold; color: #000; }
        .section-header { background: #f5f5f5; padding: 5px 10px; font-weight: bold; border: 1px solid #eee; margin-top: 10px; font-size: 11px; }
        .flight-table { width: 100%; border: 1px solid #eee; border-top: none; border-collapse: collapse; table-layout: fixed; }
        .flight-table th { background: #fafafa; padding: 5px 10px; text-align: left; font-size: 9px; color: #888; border-bottom: 1px solid #eee; }
        .flight-table td { padding: 8px 6px; vertical-align: top; }
        .flight-table td table td { padding: 0; vertical-align: top; }
        .transit-pill { background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; padding: 2px 6px; border-radius: 10px; text-transform: uppercase; font-size: 9px; }
        .city-name { font-size: 11px; font-weight: bold; display: block; margin-bottom: 2px; }
        .pax-table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        .pax-table th { background: #f5f5f5; border: 1px solid #eee; padding: 6px; text-align: left; font-size: 9px; color: #666; }
        .pax-table td { border: 1px solid #eee; padding: 8px 6px; }
        .grid-table { width: 100%; margin-top: 15px; border-collapse: collapse; }
        .grid-td { width: 50%; vertical-align: top; padding-right: 10px; }
        .payment-table { width: 100%; border-collapse: collapse; }
        .payment-table td { padding: 4px 0; border-bottom: 1px solid #f0f0f0; }
        .total-row { font-weight: bold; font-size: 12px; border-top: 1.5px solid #333 !important; }
        .important-info { margin-top: 15px; font-size: 8.5px; color: #444; text-align: justify; }
        .important-info strong { font-size: 10px; display: block; margin-bottom: 3px; }
        .not-allowed-box { margin-top: 15px; border: 1px solid #ddd; padding: 10px; }
        .not-allowed-title { color: #d32f2f; font-weight: bold; margin-bottom: 5px; font-size: 10px; }
        .not-allowed-list { font-size: 8.5px; color: #d32f2f; line-height: 1.5; }
        .agency-footer { margin-top: 20px; border-top: 1px solid #eee; padding-top: 10px; font-size: 9px; line-height: 1.4; }
    </style>
</head>
<body>
    @php
        $isKAI = strtolower($ticket->airline->airlines_code ?? '') === 'kai';
        $logoMain = $ticket->airline->logo_path ?? null;
        $stopOutAirline = $ticket->stop_airline_out ? \App\Models\Airlines::where('airlines_name', $ticket->stop_airline_out)->first() : null;
        $stopInAirline = $ticket->stop_airline_in ? \App\Models\Airlines::where('airlines_name', $ticket->stop_airline_in)->first() : null;
        $logoStopOut = $stopOutAirline ? $stopOutAirline->logo_path ?? $logoMain : $logoMain;
        $logoStopIn = $stopInAirline ? $stopInAirline->logo_path ?? $logoMain : $logoMain;
        
        $rawClass = $passengers->first()->class ?? 'Economy';
        $classOutArr = explode(',', str_contains($rawClass, '/') ? explode('/', $rawClass)[0] : $rawClass);
        $classInArr = explode(',', str_contains($rawClass, '/') ? explode('/', $rawClass)[1] ?? explode('/', $rawClass)[0] : $rawClass);
        
        function getClassForLeg($classArr, $legIndex) {
            $arr = array_values(array_filter(array_map('trim', $classArr), fn($v) => $v !== ''));
            return $legIndex == 0 ? ($arr[0] ?? '') : ($arr[1] ?? ($arr[0] ?? ''));
        }

        function airportInfo($code, $airports, $airportCities, $fallbackCity = '') {
            $name = $airports[$code] ?? $code;
            $city = $airportCities[$code] ?? $fallbackCity;
            return $name . ($city ? ' (' . $city . ')' : '');
        }
    @endphp

    <table class="header">
        <tr>
            <td width="55%">
                <div style="font-weight: bold; font-size: 13px;">LGhani T & T</div>
                <img src="{{ public_path('logo-lghani.png') }}" class="logo">
                <div style="font-size: 10px; color: #555;">tour&travel</div>
            </td>
            <td width="45%" align="right">
                <div style="display:flex; align-items:center; justify-content:flex-end; gap:12px;">
                    @if(!empty($isKAI))
                        <img src="{{ public_path('airlines-logo/kai.png') }}" style="height:40px; object-fit:contain;">
                    @endif
                    <div class="booking-title">Booking Details</div>
                </div>
                <div style="margin-top: 5px;">
                    <span style="color: #777;">Ticket ID:</span> 
                    <strong>{{ $ticketNoGlobal }}</strong>
                </div>
                <div>
                    <span style="color: #777;">Booking ID (PNR):</span> 
                    <strong>{{ $ticket->booking_code }}</strong>
                </div>
                <div style="color: #666;">
                    Issued Date: <strong>{{ $ticket->created_at->format('d M Y') }}</strong> 
                </div>
                <div style="margin-top: 5px; font-weight: bold;">{{ $ticket->invoice->customer->email ?? 'Ighani_travel@ymail.com' }}</div>
            </td>
        </tr>
    </table>

    <div class="section-header">Flight Details</div>
    <table class="flight-table">
        <thead>
            <tr>
                <th width="34%">{{ $isKAI ? 'TRAIN' : 'FLIGHT' }}</th>
                <th width="33%">DEPARTING</th>
                <th width="33%">ARRIVING</th>
            </tr>
        </thead>
        <tbody>
            @foreach(['out', 'in'] as $type)
                @php
                    if($type == 'in' && !$ticket->route_in) continue;
                    $route = $type == 'out' ? $ticket->route_out : $ticket->route_in;
                    $depT = $type == 'out' ? $ticket->dep_time_out : $ticket->dep_time_in;
                    $arrT = $type == 'out' ? $ticket->arr_time_out : $ticket->arr_time_in;
                    $fNo = $type == 'out' ? $ticket->flight_out : ($ticket->flight_in ?? $ticket->flight_out);
                    
                    $parts = explode('-', $route);
                    $depC = trim($parts[0]);
                    $arrC = trim(end($parts));
                    $midC = count($parts) > 2 ? trim($parts[1]) : null;
                    $midC2 = count($parts) > 3 ? trim($parts[2]) : null;
                    $hasStop = !is_null($midC);

                    // Ambil Data Kota
                    $depCity = $cities[$depC] ?? '';
                    $arrCity = $cities[$arrC] ?? '';
                    $midCity = $midC ? ($cities[$midC] ?? '') : '';
                    $midCity2 = $midC2 ? ($cities[$midC2] ?? '') : '';

                    $stopFlight1 = $type == 'out' ? ($ticket->stop_flight_leg1_out ?? $fNo) : ($ticket->stop_flight_leg1_in ?? $fNo);
                    $stopFlight2 = $type == 'out' ? $ticket->stop_flight_leg2_out : $ticket->stop_flight_leg2_in;
                    $stopArrival = $type == 'out' ? $ticket->stop_time_out_arrival ?? $ticket->stop_time_out : $ticket->stop_time_in_arrival ?? $ticket->stop_time_in;
                    $stopDepart = $type == 'out' ? $ticket->stop_time_out_depart ?? $ticket->stop_time_out : $ticket->stop_time_in_depart ?? $ticket->stop_time_in;
                    
                    $leg1AirlineName = $ticket->airline->airlines_name;
                    $leg1AirlineCode = $ticket->airline->airlines_code;
                    $stopAirlineName = $type == 'out' ? ($ticket->stop_airline_out ?: $leg1AirlineName) : ($ticket->stop_airline_in ?: $leg1AirlineName);
                    $leg2Airline = \App\Models\Airlines::where('airlines_name', $stopAirlineName)->first();
                    $leg2AirlineCode = $leg2Airline ? $leg2Airline->airlines_code : $leg1AirlineCode;
                    $leg2AirlineName = $leg2Airline ? $leg2Airline->airlines_name : $leg1AirlineName;
                    $logoForLeg1 = $logoMain;
                    $logoForLeg2 = $type == 'out' ? $logoStopOut : $logoStopIn;
                    $classArr = $type == 'out' ? $classOutArr : $classInArr;
                @endphp
                
                <tr><td colspan="3" style="background:#fbfdff; padding:6px 10px; font-weight:700;">{{ $type == 'out' ? 'Departure' : 'Return' }}</td></tr>
                
                @if($hasStop)
                    {{-- Leg 1 --}}
                    <tr>
                        <td style="border-bottom: 1px dashed #eee;">
                            <table width="100%">
                                <tr>
                                    @if($logoForLeg1)<td width="40px"><img src="{{ asset('airlines-logo/' . basename($logoForLeg1)) }}" style="width:35px;"></td>@endif
                                    <td><strong>{{ $leg1AirlineName }}</strong><br>
                                    <span style="font-size:11px; font-weight:bold; color:#1e40af;">{{ $leg1AirlineCode }} - {{ $stopFlight1 }}</span><br>
                                    <small>Class: {{ getClassForLeg($classArr, 0) }}</small>
                                </td>
                                </tr>
                            </table>
                        </td>
                        <td style="border-bottom: 1px dashed #eee;">
                            <div class="city-name">{{ airportInfo($depC, $airports, $airportCities, $depCity) }}</div>
                            {{ $depC }}<br><strong>{{ \Carbon\Carbon::parse($depT)->format('D, d M H:i') }}</strong>
                        </td>
                        <td style="border-bottom: 1px dashed #eee;">
                            <div class="city-name">{{ airportInfo($midC, $airports, $airportCities, $midCity) }}</div>
                            {{ $midC }}<br>@if($stopArrival)<strong>{{ \Carbon\Carbon::parse($stopArrival)->format('D, d M H:i') }}</strong>@endif
                        </td>
                    </tr>
                    {{-- Leg 2 --}}
                    <tr>
                        <td style="border-bottom: 1px solid #eee;">
                            <table width="100%">
                                <tr>
                                    @if($logoForLeg2)<td width="40px"><img src="{{ asset('airlines-logo/' . basename($logoForLeg2)) }}" style="width:35px;"></td>@endif
                                    <td><strong>{{ $leg2AirlineName }}</strong><br>
                                    <span style="font-size:11px; font-weight:bold; color:#1e40af;">{{ $leg2AirlineCode }} - {{ $stopFlight2 ?? $fNo }}</span><br>
                                    <small>Class: {{ getClassForLeg($classArr, 1) }}</small>
                                    <div style="margin-top:4px;"><span class="transit-pill">TRANSIT • {{ $midC }}</span></div>
                                </td>
                                </tr>
                            </table>
                        </td>
                        <td style="border-bottom: 1px solid #eee;">
                            <div class="city-name">{{ airportInfo($midC2 ?? $midC, $airports, $airportCities, $midCity2 ?: $midCity) }}</div>
                            {{ $midC2 ?? $midC }}<br>@if($stopDepart)<strong>{{ \Carbon\Carbon::parse($stopDepart)->format('D, d M H:i') }}</strong>@endif
                        </td>
                        <td style="border-bottom: 1px solid #eee;">
                            <div class="city-name">{{ airportInfo($arrC, $airports, $airportCities, $arrCity) }}</div>
                            {{ $arrC }}<br><strong>{{ \Carbon\Carbon::parse($arrT)->format('D, d M H:i') }}</strong>
                        </td>
                    </tr>
                @else
                    {{-- Direct --}}
                    <tr>
                        <td style="border-bottom: 1px solid #eee;">
                            <table width="100%">
                                <tr>
                                    @if($logoForLeg1)<td width="40px"><img src="{{ asset('airlines-logo/' . basename($logoForLeg1)) }}" style="width:35px;"></td>@endif
                                    <td><strong>{{ $leg1AirlineName }}</strong><br><span style="font-size:11px; font-weight:bold; color:#1e40af;">{{ $leg1AirlineCode }} - {{ $fNo }}</span><br><small>Class: {{ getClassForLeg($classArr, 0) }}</small></td>
                                </tr>
                            </table>
                        </td>
                        <td style="border-bottom: 1px solid #eee;">
                            <div class="city-name">{{ airportInfo($depC, $airports, $airportCities, $depCity) }}</div>
                            {{ $depC }}<br><strong>{{ \Carbon\Carbon::parse($depT)->format('D, d M H:i') }}</strong>
                        </td>
                        <td style="border-bottom: 1px solid #eee;">
                            <div class="city-name">{{ airportInfo($arrC, $airports, $airportCities, $arrCity) }}</div>
                            {{ $arrC }}<br><strong>{{ \Carbon\Carbon::parse($arrT)->format('D, d M H:i') }}</strong>
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <div class="section-header">Passenger(s) Details</div>
    <table class="pax-table">
        <thead>
            <tr><th width="5%">No</th><th width="40%">Name</th><th width="15%">PNR</th><th width="20%">Ticket</th>@if(!$isKAI)<th width="10%">Baggage</th>@endif<th width="10%">Status</th></tr>
        </thead>
        <tbody>
            @foreach($passengers as $index => $pax)
            <tr>
                <td>{{ $index+1 }}</td>
                <td><strong>{{ strtoupper($pax->genre ?? '') }} {{ $pax->name }}</strong> ({{ $pax->type ?? 'Adult' }})</td>
                <td style="color:#d97706; font-weight:bold;"><strong>{{ $ticket->booking_code }}</strong></td>
                <td>{{ $pax->ticket_no ?? '-' }}</td>
                @if(!$isKAI)<td>{{ $pax->baggage_kg ?? ($free_baggage ?? 0) }} KG</td>@endif
                <td style="color:green; font-weight:bold;">Confirmed</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="grid-table">
        <tr>
            <td class="grid-td">
                <div style="font-weight:bold; margin-bottom:5px; border-bottom:1px solid #eee;">Payment Details</div>
                <table class="payment-table">
                    <tr><td>Base Fare</td><td align="right">Rp {{ number_format($ticket->basic_fare) }}</td></tr>
                    <tr><td>Taxes & Fees</td><td align="right">Rp {{ number_format($ticket->total_tax + $ticket->fee) }}</td></tr>
                    @if(!$isKAI && $ticket->baggage_price > 0)
                    <tr><td>Add-on Baggage</td><td align="right">Rp {{ number_format($ticket->baggage_price, 0, ',', '.') }}</td></tr>
                    @endif
                    @php $totalAmount = $ticket->basic_fare + $ticket->total_tax + $ticket->fee + $ticket->baggage_price; @endphp
                    <tr class="total-row"><td>Total Amount</td><td align="right">Rp {{ number_format($totalAmount) }}</td></tr>
                </table>
            </td>
            @if(!$isKAI)
            <td class="grid-td">
                <div style="font-weight: bold; margin-bottom: 5px; border-bottom: 1px solid #eee;">Flight Inclusions</div>
                <div style="border: 1px solid #eee; padding: 10px;">
                    <table width="100%">
                        <tr>
                            <td><span style="color:#888;">Cabin Baggage</span><br><strong>7 Kg</strong></td>
                            <td><span style="color:#888;">Baggage</span><br><strong>{{ $free_baggage ?? 0 }} KG</strong></td>
                            @if($ticket->baggage_kg > 0)
                            <td><span style="color:#888;">Add-On</span><br><strong>{{ $ticket->baggage_kg }} KG</strong></td>
                            @endif
                        </tr>
                    </table>
                </div>
            </td>
            @endif
        </tr>
    </table>

    @if($isKAI)
    <div class="important-info">
        <strong>Important Information</strong>
        <ul style="margin:0; padding-left:18px;">
            <li>Use your e-ticket to print the boarding pass at the station, starting 7×24 hours before departure.</li>
            <li>For boarding, carry an official ID matching the one used during booking.</li>
            <li>Arrive at the station at least 60 minutes prior to departure.</li>
        </ul>
    </div>
    @else
    <div class="important-info">
        <strong>Important Information</strong>
        All Guests, including children and infants, must present valid identification at check-in. <br>
        Check-in begins 4 hours for international and 3 hours for domestic prior to the flight and closes 75 minutes prior to departure.
    </div>

    <div class="not-allowed-box">
        <div class="not-allowed-title">Not allowed! These items are Dangerous Goods and are not permitted:</div>
        <div class="not-allowed-list">
            <strong>Hand Baggage & Check-in Baggage:</strong> Lighters, Matches, Flammable Liquids, Toxic Substances, Corrosives, Explosives, Radioactive Materials.
        </div>
    </div>
    @endif

    <div class="agency-footer">
        <strong>LGhani T & T</strong><br>
        Komplek Permata 2 Cimahi Blok M6 no2, Kel. Tanimulya, Kec. Ngamprah, Kab. Bandung Barat, 40552.<br>
        <strong>Contact:</strong> 085-62151280 | <strong>Email:</strong> Ighani_travel@ymail.com
    </div>

</body>
</html>