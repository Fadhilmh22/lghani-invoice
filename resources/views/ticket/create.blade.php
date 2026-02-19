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

    /* CSS Select2 dengan Search Icon */
    .select2-container--default .select2-selection--single { height: 34px !important; border: 1px solid #cbd5e1 !important; border-radius: 6px !important; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 32px !important; font-size: 13px; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 32px !important; }
    .select2-search--dropdown { position: relative; padding: 8px !important; }
    .select2-search--dropdown::after {
        content: ""; position: absolute; right: 18px; top: 18px; width: 14px; height: 14px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'%3E%3C/path%3E%3C/svg%3E");
        background-size: contain; background-repeat: no-repeat;
    }
    .select2-search__field { padding: 6px 35px 6px 10px !important; border: 1px solid #e2e8f0 !important; border-radius: 4px !important; }
</style>

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

<div class="container-fluid">
    <h3 class="page-header"><i class="fa fa-plane-departure"></i> Issue New Ticket</h3>

    <form action="{{ route('ticket.store') }}" method="POST">
        @csrf
        
        <div class="invoice-section">
            <div class="section-title">Informasi Utama</div>
            <div class="row">
                <div class="col-md-3 form-group">
                    <label>Customer / Booker</label>
                    <select name="customer_id" class="form-control select2" required>
                        <option value="">-- Cari Booker --</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}">{{ $c->booker }} ({{ $c->company }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 form-group">
                    <label>Maskapai</label>
                    <select name="airline_id" class="form-control select2" required>
                        <option value="">-- Pilih Maskapai --</option>
                        @foreach($airlines as $a)
                            <option value="{{ $a->id }}">{{ $a->airlines_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 form-group">
                    <label>Kode Booking (PNR)</label>
                    <input type="text" name="pnr" class="form-control" required style="text-transform: uppercase;" placeholder="ABCDEF">
                </div>
                <div class="col-md-3 form-group">
                    <label>Class</label>
                    <input type="text" name="class" class="form-control" placeholder="Economy">
                </div>
            </div>
        </div>

        <div class="invoice-section">
            <div class="section-title">Detail Penerbangan</div>
            <div class="row">
                <div class="col-md-6" style="border-right: 1px solid #f1f5f9;">
                    <p><strong><i class="fa fa-plane-outbound"></i> Pergi</strong></p>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>No. Flight</label>
                            <input type="text" name="flight_out" class="form-control" placeholder="GA-123">
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Departure</label>
                            <select name="departure_out_code" id="departure_out_code" class="form-control select2" data-type="departure">
                                <option value="">-- Pilih Bandara Keberangkatan --</option>
                                @foreach($airports as $apt)
                                    <option value="{{ $apt->code }}">{{ $apt->name }} - {{ $apt->code }}</option>
                                @endforeach
                            </select>

                            <label style="margin-top:8px;">Arrivals</label>
                            <select name="arrival_out_code" id="arrival_out_code" class="form-control select2" data-type="arrival">
                                <option value="">-- Pilih Bandara Kedatangan --</option>
                                @foreach($airports as $apt)
                                    <option value="{{ $apt->code }}">{{ $apt->name }} - {{ $apt->code }}</option>
                                @endforeach
                            </select>

                            <div id="stop_out_wrapper" style="margin-top:8px; display:none;">
                                <label style="font-weight:600; font-size:12px;">
                                    <input type="checkbox" id="has_stop_out" style="margin-right:6px;"> Tambah 1 Stops (Opsional)
                                </label>
                                <select name="stop_out_code" id="stop_out_code" class="form-control select2" style="margin-top:8px; margin-bottom:8px;" data-type="stop">
                                    <option value="">-- Pilih Airport Transit --</option>
                                    @foreach($airports as $apt)
                                        <option value="{{ $apt->code }}">{{ $apt->name }} - {{ $apt->code }}</option>
                                    @endforeach
                                </select>
                                <div style="font-size:11px; font-weight:600; margin-top:6px;">Maskapai & No. Flight Leg 2</div>
                                <div style="display:flex; gap:6px; align-items:center; margin-top:4px;">
                                    <select name="stop_airline_out" id="stop_airline_out" class="form-control select2" style="font-size:12px;">
                                        <option value="">-- Pilih Maskapai Transit --</option>
                                        @foreach($airlines as $a)
                                            <option value="{{ $a->airlines_name }}">{{ $a->airlines_name }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" name="stop_flight_leg2_out" id="stop_flight_leg2_out" class="form-control" placeholder="Flight Leg 2 (Stop→Arrival) e.g. GA-456" style="font-size:12px;">
                                </div>
                            </div>

                            <label id="add_stop_out_label" style="margin-top:8px; font-weight:600; font-size:12px; cursor: pointer; color: #6366f1;">
                                <input type="checkbox" id="toggle_stop_out" style="margin-right:6px;"> Tambah 1 Stops (Opsional)
                            </label>

                            <input type="hidden" name="route_out" id="route_out" />
                        </div>

                        <div class="col-md-12" style="padding-left:0; padding-right:0;">
                            <div id="stop_out_wrapper_detail" style="margin-top:8px; display:none;">
                                <div class="row" style="margin: 0; margin-bottom: 8px;">
                                    <div class="col-md-12 form-group" style="margin-bottom: 0;">
                                        <label style="font-size:11px; margin-bottom:4px;">Waktu di Stop</label>
                                        <div class="row" style="margin:0;">
                                            <div class="col-md-4 form-group" style="padding-left:0; margin-bottom:0;">
                                                <input type="text" name="stop_time_out_arrival" id="stop_time_out_arrival" class="form-control" placeholder="Arrive transit" style="font-size:12px;">
                                            </div>
                                            <div class="col-md-8 form-group" style="padding-right:0; margin-bottom:0;">
                                                <input type="text" name="stop_time_out_depart" id="stop_time_out_depart" class="form-control" placeholder="Depart transit" style="font-size:12px;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 form-group"><label>Berangkat</label><input type="text" name="dep_out" class="form-control"></div>
                        <div class="col-md-6 form-group"><label>Tiba</label><input type="text" name="arr_out" class="form-control"></div>
                    </div>
                </div>

                <div class="col-md-6">
                    <p><strong><i class="fa fa-plane-arrival"></i> Pulang (Opsional)</strong></p>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>No. Flight</label>
                            <input type="text" name="flight_in" class="form-control">
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Departure</label>
                            <select name="departure_in_code" id="departure_in_code" class="form-control select2" data-type="departure">
                                <option value="">-- Pilih Bandara Keberangkatan --</option>
                                @foreach($airports as $apt)
                                    <option value="{{ $apt->code }}">{{ $apt->name }} - {{ $apt->code }}</option>
                                @endforeach
                            </select>

                            <label style="margin-top:8px;">Arrivals</label>
                            <select name="arrival_in_code" id="arrival_in_code" class="form-control select2" data-type="arrival">
                                <option value="">-- Pilih Bandara Kedatangan --</option>
                                @foreach($airports as $apt)
                                    <option value="{{ $apt->code }}">{{ $apt->name }} - {{ $apt->code }}</option>
                                @endforeach
                            </select>

                            <div id="stop_in_wrapper" style="margin-top:8px; display:none;">
                                <label style="font-weight:600; font-size:12px;">
                                    <input type="checkbox" id="has_stop_in" style="margin-right:6px;"> Tambah 1 Stops (Opsional)
                                </label>
                                <select name="stop_in_code" id="stop_in_code" class="form-control select2" style="margin-top:8px; margin-bottom:8px;" data-type="stop">
                                    <option value="">-- Pilih Airport Transit --</option>
                                    @foreach($airports as $apt)
                                        <option value="{{ $apt->code }}">{{ $apt->name }} - {{ $apt->code }}</option>
                                    @endforeach
                                </select>
                                <div style="font-size:11px; font-weight:600; margin-top:6px;">Maskapai & No. Flight Leg 2</div>
                                <div style="display:flex; gap:6px; align-items:center; margin-top:4px;">
                                    <select name="stop_airline_in" id="stop_airline_in" class="form-control select2" style="font-size:12px;">
                                        <option value="">-- Pilih Maskapai Transit --</option>
                                        @foreach($airlines as $a)
                                            <option value="{{ $a->airlines_name }}">{{ $a->airlines_name }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" name="stop_flight_leg2_in" id="stop_flight_leg2_in" class="form-control" placeholder="Flight Leg 2 (Stop→Arrival) e.g. GA-456" style="font-size:12px;">
                                </div>
                            </div>

                            <label id="add_stop_in_label" style="margin-top:8px; font-weight:600; font-size:12px; cursor: pointer; color: #6366f1;">
                                <input type="checkbox" id="toggle_stop_in" style="margin-right:6px;"> Tambah 1 Stops (Opsional)
                            </label>

                            <input type="hidden" name="route_in" id="route_in" />
                        </div>

                        <div class="col-md-12" style="padding-left:0; padding-right:0;">
                            <div id="stop_in_wrapper_detail" style="margin-top:8px; display:none;">
                                <div class="row" style="margin: 0; margin-bottom: 8px;">
                                    <div class="col-md-12 form-group" style="margin-bottom: 0;">
                                        <label style="font-size:11px; margin-bottom:4px;">Waktu di Stop</label>
                                        <div class="row" style="margin:0;">
                                            <div class="col-md-4 form-group" style="padding-left:0; margin-bottom:0;">
                                                <input type="text" name="stop_time_in_arrival" id="stop_time_in_arrival" class="form-control" placeholder="Arrive transit" style="font-size:12px;">
                                            </div>
                                            <div class="col-md-8 form-group" style="padding-right:0; margin-bottom:0;">
                                                <input type="text" name="stop_time_in_depart" id="stop_time_in_depart" class="form-control" placeholder="Depart transit" style="font-size:12px;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 form-group"><label>Berangkat</label><input type="text" name="dep_in" class="form-control"></div>
                        <div class="col-md-6 form-group"><label>Tiba</label><input type="text" name="arr_in" class="form-control"></div>
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
                    <tr>
                        <td><select name="passengers[0][title]" class="form-control"><option>MR</option><option>MRS</option><option>MS</option><option>MSTR</option><option>MISS</option></select></td>
                        <td><input type="text" name="passengers[0][name]" class="form-control" required></td>
                        <td><select name="passengers[0][type]" class="form-control"><option>Adult</option><option>Child</option><option>Infant</option></select></td>
                        <td><input type="text" name="passengers[0][ticket_num]" class="form-control"></td>
                        <td class="text-center">
                            <input type="checkbox" name="passengers[0][has_baggage]" value="1" style="width: 20px; height: 20px; cursor: pointer;">
                            <br><small class="text-muted">Munculkan Bagasi</small>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fa fa-times"></i></button>
                        </td>
                    </tr>
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
                            <input type="number" name="pax_paid" id="pax_paid" class="form-control" required placeholder="0">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Price (Harga Dasar Invoice)</label>
                            <input type="number" name="price" id="price" class="form-control" placeholder="0">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Discount</label>
                            <input type="number" name="discount" id="discount" class="form-control" value="0">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>NTA (Modal)</label>
                            <input type="number" name="nta_price" id="nta_price" class="form-control" required placeholder="0">
                        </div>
                        <input type="hidden" name="publish_price" id="publish_price">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="section-title" style="border-left-color: #f59e0b;">Rincian Tiket (Airfare PDF)</div>
                    <div class="row">
                        <div class="col-md-4 form-group"><label>Basic Fare</label><input type="number" name="basic_fare" id="basic_fare" class="form-control" placeholder="0"></div>
                        <div class="col-md-4 form-group"><label>Tax & Surcharge</label><input type="number" name="total_tax" id="total_tax" class="form-control" placeholder="0"></div>
                        <div class="col-md-4 form-group"><label>Fee Ticket</label><input type="number" name="fee_ticket" id="fee_ticket" class="form-control" placeholder="0"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>Free Baggage</label>
                            <div class="ticket-unit-wrapper"><input type="number" name="free_baggage" class="form-control ticket-unit-input" placeholder="0"><span class="ticket-unit-label">KG</span></div>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Add On (Qty)</label>
                            <div class="ticket-unit-wrapper"><input type="number" name="baggage_kg" id="baggage_kg" class="form-control ticket-unit-input" value="0"><span class="ticket-unit-label">KG</span></div>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Add On Price</label>
                            <div class="ticket-unit-wrapper"><span class="ticket-unit-label">Rp</span><input type="number" name="baggage_price" id="baggage_price" class="form-control ticket-unit-input" value="0"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="well well-sm" style="margin-top: 5px; background: #fffbeb; border: 1px solid #fde68a; display: flex; justify-content: space-between; align-items: center; padding: 10px 15px;">
                                <span style="color: #92400e; font-weight: 600;">Total Tertera di PDF:</span>
                                <strong id="total_ticket_display" style="font-size: 20px; color: #b45309;">Rp 0</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-right" style="margin-top: 20px;">
                <hr>
                <button type="submit" class="btn btn-primary btn-lg"><i class="fa fa-save"></i> SIMPAN TIKET & INVOICE</button>
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
    $('.select2').select2({ width: '100%', placeholder: '-- Pilih Data --', allowClear: true });

    function cleanNumber(val) { return parseFloat(val) || 0; }

    $('#basic_fare, #total_tax, #fee_ticket, #baggage_price').on('input', function() {
        let totalPDF = cleanNumber($('#basic_fare').val()) + cleanNumber($('#total_tax').val()) + cleanNumber($('#fee_ticket').val()) + cleanNumber($('#baggage_price').val());
        $('#total_ticket_display').text('Rp ' + totalPDF.toLocaleString('id-ID'));
        $('#publish_price').val(totalPDF);
    });

    if (window.flatpickr) {
        const dateTimeConfig = { enableTime: true, dateFormat: 'Y-m-d\\TH:i', altInput: true, altFormat: 'd-m-Y H:i', time_24hr: true };
        flatpickr('input[name="dep_out"], input[name="arr_out"], input[name="dep_in"], input[name="arr_in"], input[name="stop_time_out_arrival"], input[name="stop_time_out_depart"], input[name="stop_time_in_arrival"], input[name="stop_time_in_depart"]', dateTimeConfig);
    }

    let rowIdx = 1;
    $('#add-passenger').click(function() {
        let html = `<tr>
            <td><select name="passengers[${rowIdx}][title]" class="form-control"><option>MR</option><option>MRS</option><option>MS</option><option>MSTR</option><option>MISS</option></select></td>
            <td><input type="text" name="passengers[${rowIdx}][name]" class="form-control" required></td>
            <td><select name="passengers[${rowIdx}][type]" class="form-control"><option>Adult</option><option>Child</option><option>Infant</option></select></td>
            <td><input type="text" name="passengers[${rowIdx}][ticket_num]" class="form-control"></td>
            <td class="text-center"><input type="checkbox" name="passengers[${rowIdx}][has_baggage]" value="1" style="width: 20px; height: 20px; cursor: pointer;"><br><small class="text-muted">Munculkan Bagasi</small></td>
            <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fa fa-times"></i></button></td>
        </tr>`;
        $('#passenger-container').append(html);
        rowIdx++;
    });

    $('#passenger-container').on('click', '.remove-row', function() { $(this).closest('tr').remove(); });

    function buildRoute(prefix) {
        const dep = $(`#departure_${prefix}_code`).val() || '';
        const stop = $(`#stop_${prefix}_code`).val() || '';
        const arr = $(`#arrival_${prefix}_code`).val() || '';
        if (!dep && !arr) return '';
        return stop ? `${dep}-${stop}-${arr}` : `${dep}-${arr}`;
    }

    function syncRoutes() {
        $('#route_out').val(buildRoute('out'));
        $('#route_in').val(buildRoute('in'));
    }

    // --- LOGIC STOP OUT (DISAMAKAN DENGAN EDIT) ---
    $('#toggle_stop_out').on('change', function() { 
        if (this.checked) { 
            $('#add_stop_out_label').hide();
            $('#stop_out_wrapper').show();
            $('#stop_out_wrapper_detail').show();
            $('#has_stop_out').prop('checked', true);
        }
    });

    $('#has_stop_out').on('change', function() {
        if (!this.checked) {
            $('#toggle_stop_out').prop('checked', false);
            $('#add_stop_out_label').show();
            $('#stop_out_wrapper').hide();
            $('#stop_out_wrapper_detail').hide();
            $('#stop_out_code').val('').trigger('change');
            $('#stop_time_out_arrival').val('');
            $('#stop_time_out_depart').val('');
            $('#stop_airline_out').val('');
            $('#stop_flight_leg2_out').val('');
            syncRoutes();
        }
    });

    // --- LOGIC STOP IN (DISAMAKAN DENGAN EDIT) ---
    $('#toggle_stop_in').on('change', function() { 
        if (this.checked) { 
            $('#add_stop_in_label').hide();
            $('#stop_in_wrapper').show();
            $('#stop_in_wrapper_detail').show();
            $('#has_stop_in').prop('checked', true);
        }
    });

    $('#has_stop_in').on('change', function() {
        if (!this.checked) {
            $('#toggle_stop_in').prop('checked', false);
            $('#add_stop_in_label').show();
            $('#stop_in_wrapper').hide();
            $('#stop_in_wrapper_detail').hide();
            $('#stop_in_code').val('').trigger('change');
            $('#stop_time_in_arrival').val('');
            $('#stop_time_in_depart').val('');
            $('#stop_airline_in').val('');
            $('#stop_flight_leg2_in').val('');
            syncRoutes();
        }
    });

    $('#departure_out_code, #arrival_out_code, #stop_out_code, #departure_in_code, #arrival_in_code, #stop_in_code').on('change', function() { syncRoutes(); });

    syncRoutes();
});
</script>
@endsection