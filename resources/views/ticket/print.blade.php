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
        .flight-table { width: 100%; border: 1px solid #eee; border-top: none; border-collapse: collapse; }
        .flight-table th { background: #fafafa; padding: 5px 10px; text-align: left; font-size: 9px; color: #888; border-bottom: 1px solid #eee; }
        .flight-table td { padding: 10px; vertical-align: top; border-bottom: 1px solid #eee; }
        .city-name { font-size: 12px; font-weight: bold; }
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
                <th width="35%">FLIGHT</th>
                <th width="32.5%">DEPARTING</th>
                <th width="32.5%">ARRIVING</th>
            </tr>
        </thead>
        <tbody>
            @php
                function getAirlineLogo($name) {
                    $name = strtolower($name);
                    if (str_contains($name, 'air asia')) return 'airasia.png';
                    if (str_contains($name, 'batik')) return 'batik.png';
                    if (str_contains($name, 'citilink')) return 'citilink.png';
                    if (str_contains($name, 'garuda')) return 'garuda.png';
                    if (str_contains($name, 'lion')) return 'lion.png';
                    if (str_contains($name, 'nam')) return 'nam.png';
                    if (str_contains($name, 'super air jet')) return 'Super Air Jet.png';
                    if (str_contains($name, 'wings')) return 'wings.png';
                    if (str_contains($name, 'scoot')) return 'scoot.png';
                    if (str_contains($name, 'sriwijaya')) return 'sriwijaya.png';
                    if (str_contains($name, 'transnusa')) return 'transnusa.png';
                    if (str_contains($name, 'trigana air')) return 'triganaair.png';
                    if (str_contains($name, 'xpress air')) return 'xpressair.png';
                    if (str_contains($name, 'qatar airways')) return 'qatar.png';
                    if (str_contains($name, 'pelita air')) return 'pelitaair.png';
                    if (str_contains($name, 'fly jaya')) return 'flyjaya.png';
                    return null;
                }
                $logoOut = getAirlineLogo($ticket->airline->airlines_name);
            @endphp

            <tr>
                <td>
                    <table width="100%" style="border:none;">
                        <tr>
                            @if($logoOut)
                            <td width="50px" style="border:none; padding:0;">
                                <img src="{{ public_path('airlines-logo/' . $logoOut) }}" style="width: 40px; height: auto;">
                            </td>
                            @endif
                            <td style="border:none; padding:0; padding-left: 5px;">
                                <strong>{{ $ticket->airline->airlines_name }}</strong><br>
                                <span style="font-size: 11px; font-weight: bold; color: #1e40af;">
                                    {{ $ticket->airline->airlines_code }} - {{ $ticket->flight_out }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </td>
                <td>
                    <div class="city-name">{{ $ticket->route_out }}</div>
                    {{ \Carbon\Carbon::parse($ticket->dep_time_out)->format('D, d M Y') }}<br>
                    <strong>{{ \Carbon\Carbon::parse($ticket->dep_time_out)->format('H:i') }}</strong>
                </td>
                <td>
                    <div class="city-name">
                        {{ $ticket->route_in ? explode(' - ', $ticket->route_out)[1] ?? $ticket->route_out : $ticket->route_out }}
                    </div>
                    {{ \Carbon\Carbon::parse($ticket->arr_time_out)->format('D, d M Y') }}<br>
                    <strong>{{ \Carbon\Carbon::parse($ticket->arr_time_out)->format('H:i') }}</strong>
                </td>
            </tr>
            
            @if($ticket->route_in)
            <tr>
                <td>
                    <table width="100%" style="border:none;">
                        <tr>
                            @if($logoOut)
                            <td width="50px" style="border:none; padding:0;">
                                <img src="{{ public_path('airlines-logo/' . $logoOut) }}" style="width: 40px; height: auto;">
                            </td>
                            @endif
                            <td style="border:none; padding:0; padding-left: 5px;">
                                <strong>{{ $ticket->airline->airlines_name }}</strong><br>
                                <span style="font-size: 11px; font-weight: bold; color: #1e40af;">
                                    {{ $ticket->airline->airlines_code }} - {{ $ticket->flight_in ?? $ticket->flight_out }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </td>
                <td>
                    <div class="city-name">{{ $ticket->route_in }}</div>
                    {{ \Carbon\Carbon::parse($ticket->dep_time_in)->format('D, d M Y') }}<br>
                    <strong>{{ \Carbon\Carbon::parse($ticket->dep_time_in)->format('H:i') }}</strong>
                </td>
                <td>
                    <div class="city-name">{{ explode(' - ', $ticket->route_in)[1] ?? $ticket->route_in }}</div>
                    {{ \Carbon\Carbon::parse($ticket->arr_time_in)->format('D, d M Y') }}<br>
                    <strong>{{ \Carbon\Carbon::parse($ticket->arr_time_in)->format('H:i') }}</strong>
                </td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="section-header">Passenger(s) Details</div>
    <table class="pax-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="45%">Passenger Name</th>
                <th width="15%">PNR</th>
                <th width="25%">E-Ticket</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            {{-- PERBAIKAN: Menggunakan variabel $passengers agar tidak bercampur dengan tiket lain --}}
            @foreach($passengers as $index => $pax)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    <strong>{{ strtoupper($pax->genre ?? '') }} {{ $pax->name }}</strong> (Adult)
                </td>
                <td style="color:#d97706; font-weight:bold;">{{ $ticket->booking_code }}</td>
                <td>{{ $pax->ticket_no ?? '-' }}</td>
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
                    <tr>
                        <td>Base Fare</td>
                        <td align="right">Rp {{ number_format($ticket->basic_fare, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Booking Fee & Taxes</td>
                        <td align="right">Rp {{ number_format($ticket->total_tax + $ticket->fee, 0, ',', '.') }}</td>
                    </tr>
                    @if($ticket->baggage_price > 0)
                    <tr>
                        <td>Add-on Baggage</td>
                        <td align="right">Rp {{ number_format($ticket->baggage_price, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td>Total Fare</td>
                        <td align="right">Rp {{ number_format($ticket->total_publish, 0, ',', '.') }}</td>
                    </tr>
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
                            <td><span style="color:#888;">Add-On Baggage</span><br><strong>{{ $ticket->baggage_kg }} KG</strong></td>
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
        Check-in begins 4 hours for international and 3 hours for domestic prior to the flight for seat assignment and closes 75 minutes prior to the scheduled departure. <br>
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