@extends('master')

@section('konten')
<style>
    .invoice-section { background: #fff; border-radius: 10px; padding: 25px; margin-bottom: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    .section-title { font-weight: 600; font-size: 15px; margin-bottom: 15px; border-left: 5px solid #4f46e5; padding-left: 12px; color: #1e293b; }
    .form-group label { font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 5px; }
    .form-control { font-size: 13px; border-radius: 6px; }
    .input-group { display: flex !important; width: 100%; }
    .input-group-addon {
        display: flex;
        align-items: center;
        padding: 6px 12px;
        font-size: 13px;
        color: #64748b;
        background-color: #f1f5f9;
        border: 1px solid #cbd5e1;
        white-space: nowrap;
    }
    .input-group .form-control:first-child { border-top-right-radius: 0; border-bottom-right-radius: 0; }
    .input-group-addon:last-child { border-left: 0; border-top-right-radius: 6px; border-bottom-right-radius: 6px; }
    .input-group-addon:first-child { border-right: 0; border-top-left-radius: 6px; border-bottom-left-radius: 6px; }
    .input-group .form-control:last-child { border-top-left-radius: 0; border-bottom-left-radius: 0; }

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
                    <select name="airline_id" class="form-control select2" required>
                        @foreach($airlines as $a)
                            <option value="{{ $a->id }}" {{ $ticket->airline_id == $a->id ? 'selected' : '' }}>
                                {{ $a->airlines_name }}
                            </option>
                        @endforeach
                    </select>
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
            <div class="row">
                <div class="col-md-6" style="border-right: 1px solid #f1f5f9;">
                    <p><strong><i class="fa fa-plane-outbound"></i> Pergi</strong></p>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>No. Flight</label>
                            <input type="text" name="flight_out" class="form-control" value="{{ $ticket->flight_out }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Rute</label>
                            <input type="text" name="route_out" class="form-control" value="{{ $ticket->route_out }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Berangkat</label>
                            <input type="datetime-local" name="dep_out" class="form-control" value="{{ $ticket->dep_time_out ? date('Y-m-d\TH:i', strtotime($ticket->dep_time_out)) : '' }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Tiba</label>
                            <input type="datetime-local" name="arr_out" class="form-control" value="{{ $ticket->arr_time_out ? date('Y-m-d\TH:i', strtotime($ticket->arr_time_out)) : '' }}">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <p><strong><i class="fa fa-plane-arrival"></i> Pulang (Opsional)</strong></p>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>No. Flight</label>
                            <input type="text" name="flight_in" class="form-control" value="{{ $ticket->flight_in }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Rute</label>
                            <input type="text" name="route_in" class="form-control" value="{{ $ticket->route_in }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Berangkat</label>
                            <input type="datetime-local" name="dep_in" class="form-control" value="{{ $ticket->dep_time_in ? date('Y-m-d\TH:i', strtotime($ticket->dep_time_in)) : '' }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Tiba</label>
                            <input type="datetime-local" name="arr_in" class="form-control" value="{{ $ticket->arr_time_in ? date('Y-m-d\TH:i', strtotime($ticket->arr_time_in)) : '' }}">
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
            <th width="20%">No. Tiket</th>
            <th width="15%" class="text-center">Baggage?</th> <th width="50px"></th>
        </tr>
    </thead>
    <tbody id="passenger-container">
        @foreach($passengers as $index => $pax)
        @php
            $splitName = explode('. ', $pax->name, 2);
            $title = $splitName[0] ?? 'MR';
            $realName = $splitName[1] ?? $pax->name;
            
            // Cek apakah orang ini sudah punya bagasi di database
            $hasBaggage = \DB::table('invoice_details')
                            ->where('invoice_id', $ticket->invoice_id)
                            ->where('class', 'BAGASI_ONLY')
                            ->where('ticket_no', $pax->id)
                            ->exists();
        @endphp
        <tr>
            <td>
                <select name="passengers[{{ $index }}][title]" class="form-control">
                    <option {{ $title == 'MR' ? 'selected' : '' }}>MR</option>
                    <option {{ $title == 'MRS' ? 'selected' : '' }}>MRS</option>
                    <option {{ $title == 'MS' ? 'selected' : '' }}>MS</option>
                    <option {{ $title == 'MSTR' ? 'selected' : '' }}>MSTR</option>
                    <option {{ $title == 'MISS' ? 'selected' : '' }}>MISS</option>
                </select>
            </td>
            <td><input type="text" name="passengers[{{ $index }}][name]" class="form-control" value="{{ $realName }}" required></td>
            <td><input type="text" name="passengers[{{ $index }}][ticket_num]" class="form-control" value="{{ $pax->ticket_no }}"></td>
            
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
                        <input type="number" name="pax_paid" id="pax_paid" class="form-control" value="{{ (int)($passengers->first()->pax_paid * $passengers->count()) }}" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Price (Harga Dasar di Invoice)</label>
                        <input type="number" name="price" id="price" class="form-control" value="{{ (int)($passengers->first()->price * $passengers->count()) }}">
                    </div>
                        <div class="col-md-6 form-group">
                            <label>Discount</label>
                            <input type="number" name="discount" id="discount" class="form-control" value="{{ (int)($passengers->first()->discount * $passengers->count()) }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>NTA (Modal)</label>
                            <input type="number" name="nta_price" id="nta_price" class="form-control" value="{{ (int)$passengers->first()->nta * $passengers->count() }}" required>
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
            <div class="input-group">
                <input type="number" name="free_baggage" class="form-control" value="{{ $ticket->free_baggage ?? 0 }}">
                <span class="input-group-addon">KG</span>
            </div>
        </div>
        <div class="col-md-4 form-group">
            <label>Add On (Qty)</label>
            <div class="input-group">
                <input type="number" name="baggage_kg" id="baggage_kg" class="form-control" value="{{ $ticket->baggage_kg }}">
                <span class="input-group-addon">KG</span>
            </div>
        </div>
        <div class="col-md-4 form-group">
            <label>Add On Price</label>
            <div class="input-group">
                <span class="input-group-addon">Rp</span>
                <input type="number" name="baggage_price" id="baggage_price" class="form-control" value="{{ (int)$ticket->baggage_price }}">
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

<script>
$(document).ready(function() {
    // INISIALISASI SELECT2
    $('.select2').select2({
        width: '100%',
        placeholder: '-- Pilih Data --'
    });

    function cleanNumber(val) {
        return parseFloat(val.toString().replace(/[^0-9.-]+/g, "")) || 0;
    }

    // Hitung Otomatis tapi TIDAK menimpa Pax Paid & Price di kiri
    $('#basic_fare, #total_tax, #fee_ticket, #baggage_price').on('input', function() {
        let basic   = cleanNumber($('#basic_fare').val());
        let tax     = cleanNumber($('#total_tax').val());
        let fee     = cleanNumber($('#fee_ticket').val());
        let baggage = cleanNumber($('#baggage_price').val());

        let totalPDF = basic + tax + fee + baggage;

        // Update tampilan kuning dan hidden field saja
        $('#total_ticket_display').text('Rp ' + totalPDF.toLocaleString('id-ID'));
        $('#publish_price').val(totalPDF);
    });

    // Fitur Tambah Penumpang
    let rowIdx = {{ count($passengers) }};
    $('#add-passenger').click(function() {
        let html = `<tr>
            <td><select name="passengers[${rowIdx}][title]" class="form-control"><option>MR</option><option>MRS</option><option>MS</option><option>MSTR</option><option>MISS</option></select></td>
            <td><input type="text" name="passengers[${rowIdx}][name]" class="form-control" required></td>
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
});
</script>
@endsection