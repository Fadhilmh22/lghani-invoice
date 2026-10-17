@extends('master')

@section('konten')
<style>
    .invoice-section { background: #fff; border-radius: 10px; padding: 25px; margin-bottom: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    .section-title { font-weight: 600; font-size: 15px; margin-bottom: 15px; border-left: 5px solid #4f46e5; padding-left: 12px; color: #1e293b; }
    .form-group label { font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 5px; }
    .form-control { font-size: 13px; border-radius: 6px; }
    
    /* Styling Input Baggage + Unit (KG/Rp) */
    .ticket-unit-wrapper {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        width: 100%;
    }
    .ticket-unit-input {
        max-width: 110px;
    }
    .ticket-unit-label {
        font-size: 11px;
        color: #64748b;
        white-space: nowrap;
    }
    .journey-card { border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px; margin-bottom: 14px; background: #fff; }
    .journey-card > p { color: #1e293b; font-size: 15px; margin-bottom: 16px; }
    .transit-panel { padding: 14px; margin-top: 10px; border: 1px solid #c7d2fe; border-radius: 8px; background: #f8faff; }
    .transit-heading { font-size: 13px; font-weight: 700; color: #3730a3; margin-bottom: 10px; }
    .transit-toggle { display: block; margin: 8px 0 12px; color: #4f46e5; font-weight: 600; cursor: pointer; }
    .second-transit-toggle { display: block; margin: 12px 0 8px; font-weight: 600; color: #334155; cursor: pointer; }
    .segment-fields { padding: 10px; background: #fff; border: 1px solid #e2e8f0; border-radius: 7px; margin-top: 10px; }

    /* CSS TAMBAHAN UNTUK SELECT2 & SEARCH ICON */
    .select2-container--default .select2-selection--single {
        height: 34px !important;
        border: 1px solid #cbd5e1 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 32px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 32px !important;
    }
    .select2-search--dropdown {
        position: relative;
        padding: 8px !important;
    }
    .select2-search--dropdown::after {
        content: "";
        position: absolute;
        right: 18px;
        top: 18px;
        width: 14px;
        height: 14px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'%3E%3C/path%3E%3C/svg%3E");
        background-size: contain;
        background-repeat: no-repeat;
    }
    .select2-search__field {
        padding: 6px 35px 6px 10px !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 4px !important;
    }
</style>

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

<div class="container-fluid">
    <h3 class="page-header"><i class="fa fa-edit"></i> Edit Issued Ticket: {{ $ticket->booking_code }}</h3>

    <form action="{{ route('ticket.update', $ticket->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="invoice-section">
            <div class="section-title">Informasi Utama</div>
            <div class="row">
                <div class="col-md-3 form-group">
                    <label>Customer / Booker</label>
                    <select name="customer_id" class="form-control select2" required>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ $ticket->invoice->customer_id == $c->id ? 'selected' : '' }}>
                                {{ $c->booker }} ({{ $c->company }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 form-group">
                    <label>Maskapai</label>
                    <select name="airline_id" class="form-control select2" id="airlineSelect" required>
                        @foreach($airlines as $a)
                            <option value="{{ $a->id }}" data-balance="{{ $a->balance }}" {{ $ticket->airline_id == $a->id ? 'selected' : '' }}>
                                {{ $a->airlines_name }}
                            </option>
                        @endforeach
                    </select>
                    <small id="airlineBalanceInfo" class="text-muted" style="display:block; margin-top:4px; font-size:12px;"></small>
                </div>
                <div class="col-md-3 form-group">
                    <label>Kode Booking (PNR)</label>
                    <input type="text" name="pnr" class="form-control" value="{{ $ticket->booking_code }}" required style="text-transform: uppercase;">
                </div>
                <div class="col-md-3 form-group">
                    <label>Class</label>
                    <input type="text" name="class" class="form-control" value="{{ $ticket->class }}">
                </div>
            </div>
        </div>

        <div class="invoice-section">
            <div class="section-title">Detail Penerbangan</div>
            <p class="text-muted" style="font-size:12px; margin:-6px 0 16px;">Isi rute dari bandara asal ke tujuan. Pengaturan transit menampilkan segmen tambahan hingga maksimal dua transit.</p>
            <div class="row">
                <div class="col-md-6 journey-card">
                    <p><strong><i class="fa fa-plane-outbound"></i> Pergi</strong></p>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>No. penerbangan segmen 1</label>
                            <input type="text" name="flight_out" class="form-control" value="{{ $ticket->flight_out }}">
                        </div>

                        @php
                            $partsOut = $ticket->route_out ? explode('-', $ticket->route_out) : [];
                            $depOut = $partsOut[0] ?? '';
                            $arrOut = count($partsOut) ? end($partsOut) : '';
                            $stopOutDepart = $ticket->stop_out_depart_code ?: (isset($partsOut[1]) ? $partsOut[1] : '');
                            $stopOutArrival = $ticket->stop_out_arrival_code ?: (count($partsOut) > 3 ? ($partsOut[2] ?? '') : '');
                            $hasSecondStopOut = !empty($ticket->stop_out_arrival_code) || count($partsOut) > 3;
                            $hasStopOut = !empty($ticket->stop_out_depart_code) || $hasSecondStopOut;
                        @endphp

                        <div class="col-md-6 form-group">
                            <label>Bandara asal</label>
                            <select name="departure_out_code" id="departure_out_code" class="form-control select2" data-type="departure">
                                <option value="">-- Pilih Bandara Keberangkatan --</option>
                                @foreach($airports as $apt)
                                    <option value="{{ $apt->code }}" {{ $depOut == $apt->code ? 'selected' : '' }}>{{ $apt->name }} - {{ $apt->code }}</option>
                                @endforeach
                            </select>

                            <label style="margin-top:8px;">Bandara tujuan</label>
                            <select name="arrival_out_code" id="arrival_out_code" class="form-control select2" data-type="arrival">
                                <option value="">-- Pilih Bandara Kedatangan --</option>
                                @foreach($airports as $apt)
                                    <option value="{{ $apt->code }}" {{ $arrOut == $apt->code ? 'selected' : '' }}>{{ $apt->name }} - {{ $apt->code }}</option>
                                @endforeach
                            </select>

                            <label class="transit-toggle">
                                <input type="checkbox" name="has_stop_out" value="1" id="toggle_stop_out" style="margin-right:6px;" {{ $hasStopOut ? 'checked' : '' }}>
                                Tambahkan transit (maksimal 2)
                            </label>
                            <div id="stop_out_wrapper" class="transit-panel" style="{{ $hasStopOut ? 'display:block;' : 'display:none;' }}">
                                <div class="transit-heading">Transit 1 &mdash; segmen penerbangan kedua</div>
                                <div class="form-group">
                                    <label>Bandara transit pertama</label>
                                    <select name="stop_out_depart_code" id="stop_out_depart_code" class="form-control select2" data-type="stop">
                                        <option value="">-- Pilih Bandara Kedatangan Leg 1 --</option>
                                        @foreach($airports as $apt)
                                            <option value="{{ $apt->code }}" {{ $stopOutDepart == $apt->code ? 'selected' : '' }}>{{ $apt->name }} - {{ $apt->code }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="segment-fields">
                                    <div class="transit-heading">Segmen 2 &mdash; Transit 1 ke tujuan berikutnya</div>
                                    <div class="row">
                                        <div class="col-sm-7 form-group">
                                            <label>Maskapai segmen 2</label>
                                            <select name="stop_airline_out" id="stop_airline_out" class="form-control select2">
                                                <option value="">Sama dengan maskapai utama</option>
                                                @foreach($airlines as $a)
                                                    <option value="{{ $a->airlines_name }}" {{ ($ticket->stop_airline_out ?? '') == $a->airlines_name ? 'selected' : '' }}>{{ $a->airlines_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-sm-5 form-group">
                                            <label>No. penerbangan segmen 2</label>
                                            <input type="text" name="stop_flight_leg2_out" id="stop_flight_leg2_out" class="form-control" placeholder="Contoh: CX-828" value="{{ $ticket->stop_flight_leg2_out ?? '' }}">
                                        </div>
                                    </div>
                                </div>

                                <label class="second-transit-toggle">
                                    <input type="checkbox" name="has_second_stop_out" value="1" id="toggle_second_stop_out" style="margin-right:6px;" {{ $hasSecondStopOut ? 'checked' : '' }}>
                                    Tambahkan transit kedua
                                </label>
                                <div id="second_stop_out_wrapper" style="{{ $hasSecondStopOut ? 'display:block;' : 'display:none;' }}">
                                    <div class="form-group">
                                        <label>Bandara transit kedua</label>
                                        <select name="stop_out_arrival_code" id="stop_out_arrival_code" class="form-control select2" data-type="stop">
                                            <option value="">-- Pilih Bandara Transit Kedua --</option>
                                            @foreach($airports as $apt)
                                                <option value="{{ $apt->code }}" {{ $stopOutArrival == $apt->code ? 'selected' : '' }}>{{ $apt->name }} - {{ $apt->code }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="segment-fields">
                                        <div class="transit-heading">Segmen 3 &mdash; Transit 2 ke tujuan akhir</div>
                                        <div class="row">
                                            <div class="col-sm-7 form-group">
                                                <label>Maskapai segmen 3</label>
                                                <select name="stop_airline_leg3_out" id="stop_airline_leg3_out" class="form-control select2">
                                                    <option value="">Sama dengan maskapai utama</option>
                                                    @foreach($airlines as $a)
                                                        <option value="{{ $a->airlines_name }}" {{ ($ticket->stop_airline_leg3_out ?? '') == $a->airlines_name ? 'selected' : '' }}>{{ $a->airlines_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-sm-5 form-group">
                                                <label>No. penerbangan segmen 3</label>
                                                <input type="text" name="stop_flight_leg3_out" id="stop_flight_leg3_out" class="form-control" placeholder="Contoh: CX-7080" value="{{ $ticket->stop_flight_leg3_out ?? '' }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="route_out" id="route_out" value="{{ $ticket->route_out }}" />
                        </div>

                        <div class="col-md-12" style="padding:0;">
                            <div id="stop_out_wrapper_detail" class="transit-panel" style="{{ $hasStopOut ? 'display:block;' : 'display:none;' }}">
                                <div class="transit-heading">Jadwal transit</div>
                                <div class="row">
                                    <div class="col-sm-6 form-group"><label>Tiba di transit 1</label><input type="text" name="stop_time_out_arrival" id="stop_time_out_arrival" class="form-control" value="{{ $ticket->stop_time_out_arrival ? date('Y-m-d\\TH:i', strtotime($ticket->stop_time_out_arrival)) : ($ticket->stop_time_out ? date('Y-m-d\\TH:i', strtotime($ticket->stop_time_out)) : '') }}"></div>
                                    <div class="col-sm-6 form-group"><label>Berangkat dari transit 1</label><input type="text" name="stop_time_out_depart" id="stop_time_out_depart" class="form-control" value="{{ $ticket->stop_time_out_depart ? date('Y-m-d\\TH:i', strtotime($ticket->stop_time_out_depart)) : ($ticket->stop_time_out ? date('Y-m-d\\TH:i', strtotime($ticket->stop_time_out)) : '') }}"></div>
                                </div>
                                <div id="second_stop_out_times" style="{{ $hasSecondStopOut ? 'display:block;' : 'display:none;' }}">
                                    <div class="row">
                                        <div class="col-sm-6 form-group"><label>Tiba di transit 2</label><input type="text" name="stop_time_out_arrival2" id="stop_time_out_arrival2" class="form-control" value="{{ $ticket->stop_time_out_arrival2 ? date('Y-m-d\\TH:i', strtotime($ticket->stop_time_out_arrival2)) : '' }}"></div>
                                        <div class="col-sm-6 form-group"><label>Berangkat dari transit 2</label><input type="text" name="stop_time_out_depart2" id="stop_time_out_depart2" class="form-control" value="{{ $ticket->stop_time_out_depart2 ? date('Y-m-d\\TH:i', strtotime($ticket->stop_time_out_depart2)) : '' }}"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Berangkat</label>
                            <input type="text" name="dep_out" class="form-control" value="{{ $ticket->dep_time_out ? date('Y-m-d\TH:i', strtotime($ticket->dep_time_out)) : '' }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Tiba</label>
                            <input type="text" name="arr_out" class="form-control" value="{{ $ticket->arr_time_out ? date('Y-m-d\TH:i', strtotime($ticket->arr_time_out)) : '' }}">
                        </div>
                    </div>
                </div>
                <div class="col-md-6 journey-card">
                    <p><strong><i class="fa fa-plane-arrival"></i> Pulang (Opsional)</strong></p>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>No. penerbangan segmen 1</label>
                            <input type="text" name="flight_in" class="form-control" value="{{ $ticket->flight_in }}">
                        </div>

                        @php
                            $partsIn = $ticket->route_in ? explode('-', $ticket->route_in) : [];
                            $depIn = $partsIn[0] ?? '';
                            $arrIn = count($partsIn) ? end($partsIn) : '';
                            $stopInDepart = $ticket->stop_in_depart_code ?: (isset($partsIn[1]) ? $partsIn[1] : '');
                            $stopInArrival = $ticket->stop_in_arrival_code ?: (count($partsIn) > 3 ? ($partsIn[2] ?? '') : '');
                            $hasSecondStopIn = !empty($ticket->stop_in_arrival_code) || count($partsIn) > 3;
                            $hasStopIn = !empty($ticket->stop_in_depart_code) || $hasSecondStopIn;
                        @endphp
                        <div class="col-md-6 form-group">
                            <label>Bandara asal</label>
                            <select name="departure_in_code" id="departure_in_code" class="form-control select2" data-type="departure">
                                <option value="">-- Pilih Bandara Keberangkatan --</option>
                                @foreach($airports as $apt)
                                    <option value="{{ $apt->code }}" {{ $depIn == $apt->code ? 'selected' : '' }}>{{ $apt->name }} - {{ $apt->code }}</option>
                                @endforeach
                            </select>

                            <label style="margin-top:8px;">Bandara tujuan</label>
                            <select name="arrival_in_code" id="arrival_in_code" class="form-control select2" data-type="arrival">
                                <option value="">-- Pilih Bandara Kedatangan --</option>
                                @foreach($airports as $apt)
                                    <option value="{{ $apt->code }}" {{ $arrIn == $apt->code ? 'selected' : '' }}>{{ $apt->name }} - {{ $apt->code }}</option>
                                @endforeach
                            </select>

                            <label class="transit-toggle">
                                <input type="checkbox" name="has_stop_in" value="1" id="toggle_stop_in" style="margin-right:6px;" {{ $hasStopIn ? 'checked' : '' }}>
                                Tambahkan transit (maksimal 2)
                            </label>
                            <div id="stop_in_wrapper" class="transit-panel" style="{{ $hasStopIn ? 'display:block;' : 'display:none;' }}">
                                <div class="transit-heading">Transit 1 &mdash; segmen penerbangan kedua</div>
                                <div class="form-group">
                                    <label>Bandara transit pertama</label>
                                    <select name="stop_in_depart_code" id="stop_in_depart_code" class="form-control select2" data-type="stop">
                                        <option value="">-- Pilih Bandara Kedatangan Leg 1 --</option>
                                        @foreach($airports as $apt)
                                            <option value="{{ $apt->code }}" {{ $stopInDepart == $apt->code ? 'selected' : '' }}>{{ $apt->name }} - {{ $apt->code }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="segment-fields">
                                    <div class="transit-heading">Segmen 2 &mdash; Transit 1 ke tujuan berikutnya</div>
                                    <div class="row">
                                        <div class="col-sm-7 form-group">
                                            <label>Maskapai segmen 2</label>
                                            <select name="stop_airline_in" id="stop_airline_in" class="form-control select2">
                                                <option value="">Sama dengan maskapai utama</option>
                                                @foreach($airlines as $a)
                                                    <option value="{{ $a->airlines_name }}" {{ ($ticket->stop_airline_in ?? '') == $a->airlines_name ? 'selected' : '' }}>{{ $a->airlines_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-sm-5 form-group">
                                            <label>No. penerbangan segmen 2</label>
                                            <input type="text" name="stop_flight_leg2_in" id="stop_flight_leg2_in" class="form-control" placeholder="Contoh: CX-828" value="{{ $ticket->stop_flight_leg2_in ?? '' }}">
                                        </div>
                                    </div>
                                </div>

                                <label class="second-transit-toggle">
                                    <input type="checkbox" name="has_second_stop_in" value="1" id="toggle_second_stop_in" style="margin-right:6px;" {{ $hasSecondStopIn ? 'checked' : '' }}>
                                    Tambahkan transit kedua
                                </label>
                                <div id="second_stop_in_wrapper" style="{{ $hasSecondStopIn ? 'display:block;' : 'display:none;' }}">
                                    <div class="form-group">
                                        <label>Bandara transit kedua</label>
                                        <select name="stop_in_arrival_code" id="stop_in_arrival_code" class="form-control select2" data-type="stop">
                                            <option value="">-- Pilih Bandara Transit Kedua --</option>
                                            @foreach($airports as $apt)
                                                <option value="{{ $apt->code }}" {{ $stopInArrival == $apt->code ? 'selected' : '' }}>{{ $apt->name }} - {{ $apt->code }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="segment-fields">
                                        <div class="transit-heading">Segmen 3 &mdash; Transit 2 ke tujuan akhir</div>
                                        <div class="row">
                                            <div class="col-sm-7 form-group">
                                                <label>Maskapai segmen 3</label>
                                                <select name="stop_airline_leg3_in" id="stop_airline_leg3_in" class="form-control select2">
                                                    <option value="">Sama dengan maskapai utama</option>
                                                    @foreach($airlines as $a)
                                                        <option value="{{ $a->airlines_name }}" {{ ($ticket->stop_airline_leg3_in ?? '') == $a->airlines_name ? 'selected' : '' }}>{{ $a->airlines_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-sm-5 form-group">
                                                <label>No. penerbangan segmen 3</label>
                                                <input type="text" name="stop_flight_leg3_in" id="stop_flight_leg3_in" class="form-control" placeholder="Contoh: CX-7080" value="{{ $ticket->stop_flight_leg3_in ?? '' }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="route_in" id="route_in" value="{{ $ticket->route_in }}" />
                        </div>

                        <div class="col-md-12" style="padding:0;">
                            <div id="stop_in_wrapper_detail" class="transit-panel" style="{{ $hasStopIn ? 'display:block;' : 'display:none;' }}">
                                <div class="transit-heading">Jadwal transit</div>
                                <div class="row">
                                    <div class="col-sm-6 form-group"><label>Tiba di transit 1</label><input type="text" name="stop_time_in_arrival" id="stop_time_in_arrival" class="form-control" value="{{ $ticket->stop_time_in_arrival ? date('Y-m-d\\TH:i', strtotime($ticket->stop_time_in_arrival)) : ($ticket->stop_time_in ? date('Y-m-d\\TH:i', strtotime($ticket->stop_time_in)) : '') }}"></div>
                                    <div class="col-sm-6 form-group"><label>Berangkat dari transit 1</label><input type="text" name="stop_time_in_depart" id="stop_time_in_depart" class="form-control" value="{{ $ticket->stop_time_in_depart ? date('Y-m-d\\TH:i', strtotime($ticket->stop_time_in_depart)) : ($ticket->stop_time_in ? date('Y-m-d\\TH:i', strtotime($ticket->stop_time_in)) : '') }}"></div>
                                </div>
                                <div id="second_stop_in_times" style="{{ $hasSecondStopIn ? 'display:block;' : 'display:none;' }}">
                                    <div class="row">
                                        <div class="col-sm-6 form-group"><label>Tiba di transit 2</label><input type="text" name="stop_time_in_arrival2" id="stop_time_in_arrival2" class="form-control" value="{{ $ticket->stop_time_in_arrival2 ? date('Y-m-d\\TH:i', strtotime($ticket->stop_time_in_arrival2)) : '' }}"></div>
                                        <div class="col-sm-6 form-group"><label>Berangkat dari transit 2</label><input type="text" name="stop_time_in_depart2" id="stop_time_in_depart2" class="form-control" value="{{ $ticket->stop_time_in_depart2 ? date('Y-m-d\\TH:i', strtotime($ticket->stop_time_in_depart2)) : '' }}"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 form-group"><label>Berangkat</label>
                            <input type="text" name="dep_in" class="form-control" value="{{ $ticket->dep_time_in ? date('Y-m-d\TH:i', strtotime($ticket->dep_time_in)) : '' }}">
                        </div>
                        <div class="col-md-6 form-group"><label>Tiba</label>
                            <input type="text" name="arr_in" class="form-control" value="{{ $ticket->arr_time_in ? date('Y-m-d\TH:i', strtotime($ticket->arr_time_in)) : '' }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="invoice-section">
            <div class="section-title">Data Penumpang</div>
            <table class="table table-bordered">
                <thead>
                    <tr class="active">
                        <th width="12%">Title</th>
                        <th>Nama Lengkap</th>
                        <th width="12%">Tipe</th>
                        <th width="20%">No. Tiket</th>
                        <th width="15%" class="text-center">Baggage?</th> <th width="50px"></th>
                    </tr>
                </thead>
                <tbody id="passenger-container">
                    @foreach($passengers as $index => $pax)
                    @php
                        $splitName = explode('. ', $pax->name, 2);
                        $currentTitle = $pax->genre ?? ($splitName[0] ?? 'MR');
                        $realName = isset($splitName[1]) ? $splitName[1] : $pax->name;
                        $currentType = $pax->type ?? 'Adult';
                        $hasBaggage = \DB::table('invoice_details')
                                        ->where('invoice_id', $ticket->invoice_id)
                                        ->where('class', 'BAGASI_ONLY')
                                        ->where('ticket_no', $pax->id)
                                        ->exists();
                    @endphp
                    <tr>
                        <td>
                            <select name="passengers[{{ $index }}][title]" class="form-control">
                                <option value="MR" {{ $currentTitle == 'MR' ? 'selected' : '' }}>MR</option>
                                <option value="MRS" {{ $currentTitle == 'MRS' ? 'selected' : '' }}>MRS</option>
                                <option value="MS" {{ $currentTitle == 'MS' ? 'selected' : '' }}>MS</option>
                                <option value="MSTR" {{ $currentTitle == 'MSTR' ? 'selected' : '' }}>MSTR</option>
                                <option value="MISS" {{ $currentTitle == 'MISS' ? 'selected' : '' }}>MISS</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" name="passengers[{{ $index }}][name]" class="form-control" value="{{ $realName }}" required>
                        </td>
                        <td>
                            <select name="passengers[{{ $index }}][type]" class="form-control">
                                <option value="Adult" {{ $currentType == 'Adult' ? 'selected' : '' }}>Adult</option>
                                <option value="Child" {{ $currentType == 'Child' ? 'selected' : '' }}>Child</option>
                                <option value="Infant" {{ $currentType == 'Infant' ? 'selected' : '' }}>Infant</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" name="passengers[{{ $index }}][ticket_num]" class="form-control" value="{{ $pax->ticket_no }}">
                        </td>
                        <td class="text-center">
                            <input type="checkbox" name="passengers[{{ $index }}][has_baggage]" value="1" {{ $hasBaggage ? 'checked' : '' }} style="width: 20px; height: 20px; cursor: pointer;">
                            <br><small class="text-muted">Munculkan Bagasi</small>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fa fa-times"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <button type="button" class="btn btn-default btn-sm" id="add-passenger"><i class="fa fa-plus"></i> Tambah Penumpang</button>
        </div>

        <div class="invoice-section">
            <div class="row">
                <div class="col-md-6" style="border-right: 1px solid #f1f5f9;">
                    <div class="section-title" style="border-left-color: #6366f1;">Detail Keuangan (Invoice)</div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Pax Paid (Harga Jual Akhir)</label>
                            <input type="number" name="pax_paid" id="pax_paid" class="form-control" value="{{ (int)$passengers->sum('pax_paid') }}" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Price (Harga Dasar di Invoice)</label>
                            <input type="number" name="price" id="price" class="form-control" value="{{ (int)$passengers->sum('price') }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Discount</label>
                            <input type="number" name="discount" id="discount" class="form-control" value="{{ (int)$passengers->sum('discount') }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>NTA (Modal)</label>
                            <input type="number" name="nta_price" id="nta_price" class="form-control" value="{{ (int)$passengers->sum('nta') }}" required>
                        </div>
                        <input type="hidden" name="publish_price" id="publish_price" value="{{ (int)$ticket->total_publish }}">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="section-title" style="border-left-color: #f59e0b;">Rincian Tiket (Airfare PDF)</div>
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>Basic Fare</label>
                            <input type="number" name="basic_fare" id="basic_fare" class="form-control" value="{{ (int)$ticket->basic_fare }}">
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Tax & Surcharge</label>
                            <input type="number" name="total_tax" id="total_tax" class="form-control" value="{{ (int)$ticket->total_tax }}">
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Fee Ticket</label>
                            <input type="number" name="fee_ticket" id="fee_ticket" class="form-control" value="{{ (int)$ticket->fee }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>Free Baggage</label>
                            <div class="ticket-unit-wrapper">
                                <input type="number" name="free_baggage" class="form-control ticket-unit-input" value="{{ $ticket->free_baggage ?? 0 }}">
                                <span class="ticket-unit-label">KG</span>
                            </div>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Add On (Qty)</label>
                            <div class="ticket-unit-wrapper">
                                <input type="number" name="baggage_kg" id="baggage_kg" class="form-control ticket-unit-input" value="{{ $ticket->baggage_kg }}">
                                <span class="ticket-unit-label">KG</span>
                            </div>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Add On Price</label>
                            <div class="ticket-unit-wrapper">
                                <span class="ticket-unit-label">Rp</span>
                                <input type="number" name="baggage_price" id="baggage_price" class="form-control ticket-unit-input" value="{{ (int)$ticket->baggage_price }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="well well-sm" style="margin-top: 5px; background: #fffbeb; border: 1px solid #fde68a; display: flex; justify-content: space-between; align-items: center; padding: 10px 15px;">
                                <span style="color: #92400e; font-weight: 600;">Total Tertera di PDF:</span>
                                @php $totalPDF = $ticket->basic_fare + $ticket->total_tax + $ticket->fee + $ticket->baggage_price; @endphp
                                <strong id="total_ticket_display" style="font-size: 20px; color: #b45309;">Rp {{ number_format($totalPDF, 0, ',', '.') }}</strong>
                            </div>
                            <p class="text-muted" style="font-size: 11px; margin-top: -5px;">*Nilai ini yang akan muncul di print out tiket satuan.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-right" style="margin-top: 20px;">
                <hr>
                <a href="{{ route('ticket.index') }}" class="btn btn-default btn-lg">BATAL</a>
                <button type="submit" class="btn btn-primary btn-lg"><i class="fa fa-save"></i> SIMPAN PERUBAHAN</button>
            </div>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
$(document).ready(function() {
    $('.select2').select2({
        width: '100%',
        placeholder: '-- Pilih Data --'
    });

    $('#airlineSelect').on('change', function(){
        var bal = $(this).find('option:selected').data('balance');
        if(bal !== undefined) {
            $('#airlineBalanceInfo').text('Saldo saat ini: IDR ' + parseInt(bal).toLocaleString('id-ID'));
        } else {
            $('#airlineBalanceInfo').text('');
        }
    });
    $('#airlineSelect').trigger('change');

    function cleanNumber(val) {
        return parseFloat(val.toString().replace(/[^0-9.-]+/g, "")) || 0;
    }

    $('#basic_fare, #total_tax, #fee_ticket, #baggage_price').on('input', function() {
        let basic   = cleanNumber($('#basic_fare').val());
        let tax     = cleanNumber($('#total_tax').val());
        let fee     = cleanNumber($('#fee_ticket').val());
        let baggage = cleanNumber($('#baggage_price').val());
        let totalPDF = basic + tax + fee + baggage;
        $('#total_ticket_display').text('Rp ' + totalPDF.toLocaleString('id-ID'));
        $('#publish_price').val(totalPDF);
    });

    if (window.flatpickr) {
        const dateTimeConfig = {
            enableTime: true,
            dateFormat: 'Y-m-d\\TH:i',
            altInput: true,
            altFormat: 'd-m-Y H:i',
            time_24hr: true
        };
        flatpickr('input[name="dep_out"], input[name="arr_out"], input[name="dep_in"], input[name="arr_in"], input[name^="stop_time_"]', dateTimeConfig);
    }

    let rowIdx = {{ count($passengers) }};
    $('#add-passenger').click(function() {
        let html = `<tr>
        <td><select name="passengers[${rowIdx}][title]" class="form-control"><option>MR</option><option>MRS</option><option>MS</option><option>MSTR</option><option>MISS</option></select></td>
        <td><input type="text" name="passengers[${rowIdx}][name]" class="form-control" required></td>
        <td><select name="passengers[${rowIdx}][type]" class="form-control"><option>Adult</option><option>Child</option><option>Infant</option></select></td>
        <td><input type="text" name="passengers[${rowIdx}][ticket_num]" class="form-control"></td>
        <td class="text-center">
            <input type="checkbox" name="passengers[${rowIdx}][has_baggage]" value="1" style="width: 20px; height: 20px; cursor: pointer;">
            <br><small class="text-muted">Munculkan Bagasi</small>
        </td>
        <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fa fa-times"></i></button></td>
        </tr>`;
        $('#passenger-container').append(html);
        rowIdx++;
    });

    $('#passenger-container').on('click', '.remove-row', function() { $(this).closest('tr').remove(); });

    function buildRoute(prefix) {
        const dep = $(`#departure_${prefix}_code`).val() || '';
        const arr = $(`#arrival_${prefix}_code`).val() || '';
        const stops = [];
        if ($(`#toggle_stop_${prefix}`).is(':checked')) {
            const firstStop = $(`#stop_${prefix}_depart_code`).val();
            if (firstStop) stops.push(firstStop);
            if ($(`#toggle_second_stop_${prefix}`).is(':checked')) {
                const secondStop = $(`#stop_${prefix}_arrival_code`).val();
                if (secondStop) stops.push(secondStop);
            }
        }
        return [dep, ...stops, arr].filter(Boolean).join('-');
    }

    function syncRoutes() {
        $('#route_out').val(buildRoute('out'));
        $('#route_in').val(buildRoute('in'));
    }

    ['out', 'in'].forEach(function(prefix) {
        const $firstToggle = $(`#toggle_stop_${prefix}`);
        const $secondToggle = $(`#toggle_second_stop_${prefix}`);

        function clearFields(selector) {
            $(selector).each(function() {
                $(this).is('select') ? $(this).val('').trigger('change') : $(this).val('');
            });
        }

        $firstToggle.on('change', function() {
            const enabled = this.checked;
            $(`#stop_${prefix}_wrapper, #stop_${prefix}_wrapper_detail`).toggle(enabled);
            if (!enabled) {
                $secondToggle.prop('checked', false);
                $(`#second_stop_${prefix}_wrapper, #second_stop_${prefix}_times`).hide();
                clearFields(`#stop_${prefix}_depart_code, #stop_${prefix}_arrival_code, #stop_airline_${prefix}, #stop_airline_leg3_${prefix}, #stop_flight_leg2_${prefix}, #stop_flight_leg3_${prefix}, input[name="stop_time_${prefix}_arrival"], input[name="stop_time_${prefix}_depart"], input[name="stop_time_${prefix}_arrival2"], input[name="stop_time_${prefix}_depart2"]`);
            }
            syncRoutes();
        });

        $secondToggle.on('change', function() {
            const enabled = this.checked;
            $(`#second_stop_${prefix}_wrapper, #second_stop_${prefix}_times`).toggle(enabled);
            if (!enabled) {
                clearFields(`#stop_${prefix}_arrival_code, #stop_airline_leg3_${prefix}, #stop_flight_leg3_${prefix}, input[name="stop_time_${prefix}_arrival2"], input[name="stop_time_${prefix}_depart2"]`);
            }
            syncRoutes();
        });
    });

    $('#departure_out_code, #arrival_out_code, #stop_out_depart_code, #stop_out_arrival_code, #departure_in_code, #arrival_in_code, #stop_in_depart_code, #stop_in_arrival_code').on('change', syncRoutes);
});
</script>
@endsection
