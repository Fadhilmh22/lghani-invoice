<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Booking Details - {{ $ticket->booking_code }}</title>
    <style>
        @page { margin: 15px; }
        body { font-family: Arial, sans-serif; font-size: 10px; color: #333; line-height: 1.3; margin: 0; padding: 10px; }
        
        /* HEADER */
        .header { width: 100%; border-bottom: 1px solid #ddd; padding-bottom: 10px; margin-bottom: 10px; }
        .logo { width: 130px; }
        .booking-title { font-size: 18px; font-weight: bold; color: #000; }
        
        /* SECTION & TABLE */
        .section-header { background: #f5f5f5; padding: 5px 10px; font-weight: bold; border: 1px solid #eee; margin-top: 10px; font-size: 11px; }
        
        .flight-table { width: 100%; border: 1px solid #eee; border-top: none; border-collapse: collapse; table-layout: fixed; }
        .flight-table th { background: #fafafa; padding: 5px 10px; text-align: left; font-size: 9px; color: #888; border-bottom: 1px solid #eee; }
        .flight-table td { padding: 8px 6px; vertical-align: top; border-bottom: 1px solid #eee; }
        .flight-table td table td { padding: 0; vertical-align: top; }

        .status-badge { 
            position: relative; 
            z-index: 2; 
            background: #fff; 
            padding: 0 5px; 
            font-size: 8px; 
            font-weight: bold; 
            color: #94a3b8; 
            white-space: nowrap; 
        }
        
        /* CLASS KOTAK BIRU TRANSIT */
        .transit-pill { 
            background: #eff6ff; 
            border: 1px solid #bfdbfe; 
            color: #1e40af; 
            padding: 2px 6px; 
            border-radius: 10px; 
            text-transform: uppercase;
        }

        .city-name { font-size: 11px; font-weight: bold; display: block; margin-bottom: 2px; }
        
        /* PAX TABLE */
        .pax-table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        .pax-table th { background: #f5f5f5; border: 1px solid #eee; padding: 6px; text-align: left; font-size: 9px; color: #666; }
        .pax-table td { border: 1px solid #eee; padding: 8px 6px; }
        
        /* GRID & PAYMENT */
        .grid-table { width: 100%; margin-top: 15px; border-collapse: collapse; }
        .grid-td { width: 50%; vertical-align: top; padding-right: 10px; }
        .payment-table { width: 100%; border-collapse: collapse; }
        .payment-table td { padding: 4px 0; border-bottom: 1px solid #f0f0f0; }
        .total-row { font-weight: bold; font-size: 12px; border-top: 1.5px solid #333 !important; }
        
        /* FOOTER INFO */
        .important-info { margin-top: 15px; font-size: 8.5px; color: #444; text-align: justify; }
        .important-info strong { font-size: 10px; display: block; margin-bottom: 3px; }
        .not-allowed-box { margin-top: 15px; border: 1px solid #ddd; padding: 10px; }
        .not-allowed-title { color: #d32f2f; font-weight: bold; margin-bottom: 5px; font-size: 10px; }
        .not-allowed-list { font-size: 8.5px; color: #d32f2f; line-height: 1.5; }
        .agency-footer { margin-top: 20px; border-top: 1px solid #eee; padding-top: 10px; font-size: 9px; line-height: 1.4; }
    </style>
</head>
<body>

    <table class="header">
        <tr>
            <td width="55%">
                <div style="font-weight: bold; font-size: 13px;">LGhani T & T</div>
                <img src="{{ public_path('logo-lghani.png') }}" class="logo">
                <div style="font-size: 10px; color: #555;">tour&travel</div>
            </td>
            <td width="45%" align="right">
                <div class="booking-title">Booking Details</div>
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
                    | Time: <strong>{{ $ticket->created_at->format('H:i') }}</strong>
                </div>
                <div style="margin-top: 5px; font-weight: bold;">{{ $ticket->invoice->customer->email ?? 'Ighani_travel@ymail.com' }}</div>
            </td>
        </tr>
    </table>

    <div class="section-header">Flight Details</div>
    <table class="flight-table">
        <thead>
            <tr>
                <th width="34%">FLIGHT</th>
                <th width="33%">DEPARTING</th>
                <th width="33%">ARRIVING</th>
            </tr>
        </thead>
        <tbody>
            @php
                function getAirlineLogo($name) {
                    $name = strtolower($name);
                    $logos = [
                        'air asia' => 'airasia.png',
                        'batik' => 'batik.png',
                        'citilink' => 'citilink.png',
                        'garuda' => 'garuda.png',
                        'lion' => 'lion.png',
                        'super air jet' => 'Super Air Jet.png',
                        'flyjaya' => 'flyjaya.png',
                        'sriwijaya' => 'sriwijaya.png',
                        'scoot' => 'scoot.png',
                        'singapore airlines' => 'singaporeairlines.png',
                        'malaysia airlines' => 'malaysiairlines.png',
                        'thai airways' => 'thaiairways.png',
                        'indonesia' => 'indonesiairlines.png',
                        'cathay' => 'cathaypacific.png',
                        'emirates' => 'emirates.png',
                        'qatar' => 'qatar.png',
                        'etihad' => 'etihad.png'
                    ];
                    foreach ($logos as $key => $val) {
                        if (str_contains($name, $key)) return $val;
                    }
                    return null;
                }
                $logoOut = getAirlineLogo($ticket->airline->airlines_name);

                $rawClass = $passengers->first()->class ?? 'Economy';
                // Parse "Y,A/B,V" format: Y=leg1_out, A=leg2_out, B=leg1_in, V=leg2_in
                if (str_contains($rawClass, '/')) {
                    $parts = explode('/', $rawClass);
                    $classOutArr = explode(',', $parts[0]);
                    $classInArr = explode(',', $parts[1] ?? $parts[0]);
                } else {
                    $classOutArr = explode(',', $rawClass);
                    $classInArr = $classOutArr;
                }
                
                // Function to get correct class for each leg
                function getClassForLeg($classArr, $legIndex) {
                    // Normalize array
                    $arr = array_values(array_filter(array_map('trim', $classArr), function($v) { return $v !== ''; }));
                    if ($legIndex == 0) {
                        // Leg 1: use first element if present
                        return isset($arr[0]) ? $arr[0] : '';
                    } else {
                        // Leg 2 (transit): use second element if exists, otherwise fallback to first
                        return isset($arr[1]) ? $arr[1] : (isset($arr[0]) ? $arr[0] : '');
                    }
                }
            @endphp

            @foreach(['out', 'in'] as $type)
                @php 
                    if($type == 'in' && !$ticket->route_in) continue;
                    $route = ($type == 'out') ? $ticket->route_out : $ticket->route_in;
                    $depT = ($type == 'out') ? $ticket->dep_time_out : $ticket->dep_time_in;
                    $arrT = ($type == 'out') ? $ticket->arr_time_out : $ticket->arr_time_in;
                    $fNo = ($type == 'out') ? $ticket->flight_out : ($ticket->flight_in ?? $ticket->flight_out);

                    $parts = explode('-', $route);
                    $depC = trim($parts[0]);
                    $arrC = trim($parts[count($parts)-1]);
                    $midC = (count($parts) === 3) ? trim($parts[1]) : null;
                    $hasStop = $midC !== null;
                    
                    $stopFlight1 = ($type == 'out') ? ($ticket->stop_flight_leg1_out ?? $ticket->flight_out) : ($ticket->stop_flight_leg1_in ?? $ticket->flight_in);
                    $stopArrival = ($type == 'out') ? $ticket->stop_time_out_arrival ?? $ticket->stop_time_out : $ticket->stop_time_in_arrival ?? $ticket->stop_time_in;
                    $stopDepart  = ($type == 'out') ? $ticket->stop_time_out_depart ?? $ticket->stop_time_out : $ticket->stop_time_in_depart ?? $ticket->stop_time_in;
                    $stopFlight2 = ($type == 'out') ? $ticket->stop_flight_leg2_out : $ticket->stop_flight_leg2_in;
                @endphp
                
                <tr>
                    <td colspan="3" style="background:#fbfdff; padding:6px 10px; font-weight:700; color:#1f2937;">{{ $type == 'out' ? 'Departure' : 'Return' }}</td>
                </tr>
                @if($hasStop)
                    {{-- LEG 1: Departure to Stop --}}
                    <tr>
                        <td>
                            <table width="100%" style="border:none;">
                                <tr>
                                    @if($logoOut)
                                    <td width="40px" style="border:none; padding:0;">
                                        <img src="{{ public_path('airlines-logo/' . $logoOut) }}" style="width: 35px; height: auto;">
                                    </td>
                                    @endif
                                    <td style="border:none; padding:0; padding-left: 5px;">
                                        <strong>{{ $ticket->airline->airlines_name }}</strong><br>
                                        <span style="font-size: 11px; font-weight: bold; color: #1e40af;">{{ $ticket->airline->airlines_code }} - {{ $stopFlight1 ?? $fNo }}</span>
                                        <div style="font-size: 9px; color: #555;">Class: {{ getClassForLeg(($type == 'out') ? $classOutArr : $classInArr, 0) }}</div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td align="left">
                            <div class="city-name">{{ $airports[$depC] ?? $depC }}</div>
                            <div style="font-weight: bold; color: #555;">{{ $depC }}</div>
                            {{ \Carbon\Carbon::parse($depT)->format('D, d M Y') }}<br>
                            <strong>{{ \Carbon\Carbon::parse($depT)->format('H:i') }}</strong>
                        </td>

                        <td align="left">
                            <div class="city-name">{{ $airports[$midC] ?? $midC }}</div>
                            <div style="font-weight: bold; color: #555;">{{ $midC }}</div>
                            @if($stopArrival)
                                {{ \Carbon\Carbon::parse($stopArrival)->format('D, d M Y') }}<br>
                                <strong>{{ \Carbon\Carbon::parse($stopArrival)->format('H:i') }}</strong>
                            @else
                                <span style="color: #999;">-- --</span>
                            @endif
                        </td>
                    </tr>
                    
                    {{-- LEG 2: Stop to Arrival --}}
                    <tr>
                        <td>
                            <table width="100%" style="border:none;">
                                <tr>
                                    @if($logoOut)
                                    <td width="40px" style="border:none; padding:0;">
                                        <img src="{{ public_path('airlines-logo/' . $logoOut) }}" style="width: 35px; height: auto;">
                                    </td>
                                    @endif
                                    <td style="border:none; padding:0; padding-left: 5px;">
                                        <strong>{{ $ticket->airline->airlines_name }}</strong><br>
                                        <span style="font-size: 11px; font-weight: bold; color: #1e40af;">{{ $ticket->airline->airlines_code }} - {{ $stopFlight2 ?? $fNo }}</span>
                                        <div style="font-size: 9px; color: #555;">Class: {{ getClassForLeg(($type == 'out') ? $classOutArr : $classInArr, 1) }}</div>
                                        <div style="margin-top:6px;"><span class="transit-pill">TRANSIT • {{ $midC }}</span></div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td align="left">
                            <div class="city-name">{{ $airports[$midC] ?? $midC }}</div>
                            <div style="font-weight: bold; color: #555;">{{ $midC }}</div>
                            @if($stopDepart)
                                {{ \Carbon\Carbon::parse($stopDepart)->format('D, d M Y') }}<br>
                                <strong>{{ \Carbon\Carbon::parse($stopDepart)->format('H:i') }}</strong>
                            @else
                                <span style="color: #999;">-- --</span>
                            @endif
                        </td>

                        <td align="left">
                            <div class="city-name">{{ $airports[$arrC] ?? $arrC }}</div>
                            <div style="font-weight: bold; color: #555;">{{ $arrC }}</div>
                            {{ \Carbon\Carbon::parse($arrT)->format('D, d M Y') }}<br>
                            <strong>{{ \Carbon\Carbon::parse($arrT)->format('H:i') }}</strong>
                        </td>
                    </tr>
                @else
                    {{-- DIRECT FLIGHT --}}
                    <tr>
                        <td>
                            <table width="100%" style="border:none;">
                                <tr>
                                    @if($logoOut)
                                    <td width="40px" style="border:none; padding:0;">
                                        <img src="{{ public_path('airlines-logo/' . $logoOut) }}" style="width: 35px; height: auto;">
                                    </td>
                                    @endif
                                    <td style="border:none; padding:0; padding-left: 5px;">
                                        <strong>{{ $ticket->airline->airlines_name }}</strong><br>
                                        <span style="font-size: 11px; font-weight: bold; color: #1e40af;">{{ $ticket->airline->airlines_code }} - {{ $fNo }}</span>
                                        <div style="font-size: 9px; color: #555;">Class: {{ getClassForLeg(($type == 'out') ? $classOutArr : $classInArr, 0) }}</div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td align="left">
                            <div class="city-name">{{ $airports[$depC] ?? $depC }}</div>
                            <div style="font-weight: bold; color: #555;">{{ $depC }}</div>
                            {{ \Carbon\Carbon::parse($depT)->format('D, d M Y') }}<br>
                            <strong>{{ \Carbon\Carbon::parse($depT)->format('H:i') }}</strong>
                        </td>

                        <td align="left">
                            <div class="city-name">{{ $airports[$arrC] ?? $arrC }}</div>
                            <div style="font-weight: bold; color: #555;">{{ $arrC }}</div>
                            {{ \Carbon\Carbon::parse($arrT)->format('D, d M Y') }}<br>
                            <strong>{{ \Carbon\Carbon::parse($arrT)->format('H:i') }}</strong>
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <div class="section-header">Passenger(s) Details</div>
    <table class="pax-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="40%">Passenger Name</th>
                <th width="15%">PNR</th>
                <th width="20%">E-Ticket</th>
                <th width="10%">Baggage</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($passengers as $index => $pax)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td><strong>{{ strtoupper($pax->genre ?? '') }} {{ $pax->name }}</strong> ({{ $pax->type ?? 'Adult' }})</td>
                <td style="color:#d97706; font-weight:bold;">{{ $ticket->booking_code }}</td>
                <td>{{ $pax->ticket_no ?? '-' }}</td>
                <td>{{ $pax->baggage_kg ?? ($free_baggage ?? 0) }} KG</td>
                <td style="color:green; font-weight:bold;">Confirmed</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="grid-table">
        <tr>
            <td class="grid-td">
                <div style="font-weight: bold; margin-bottom: 5px; border-bottom: 1px solid #eee;">Payment Details</div>
                <table class="payment-table">
                    <tr><td>Base Fare</td><td align="right">Rp {{ number_format($ticket->basic_fare, 0, ',', '.') }}</td></tr>
                    <tr><td>Total Taxes</td><td align="right">Rp {{ number_format($ticket->total_tax + $ticket->fee, 0, ',', '.') }}</td></tr>
                    @if($ticket->baggage_price > 0)
                    <tr><td>Add-on Baggage</td><td align="right">Rp {{ number_format($ticket->baggage_price, 0, ',', '.') }}</td></tr>
                    @endif
                    <tr class="total-row"><td>Total Fare</td><td align="right">Rp {{ number_format($ticket->total_publish, 0, ',', '.') }}</td></tr>
                </table>
            </td>
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
        </tr>
    </table>

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

    <div class="agency-footer">
        <strong>LGhani T & T</strong><br>
        Komplek Permata 2 Cimahi Blok M6 no2, Kel. Tanimulya, Kec. Ngamprah, Kab. Bandung Barat, 40552.<br>
        <strong>Contact:</strong> 085-62151280 | <strong>Email:</strong> Ighani_travel@ymail.com
    </div>

</body>
</html>