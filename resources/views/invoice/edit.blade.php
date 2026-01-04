@extends('master')

@section('konten')

<style>
.mb-0 { margin-bottom: 0; }

.invoice-section {
    background: #fff;
    border-radius: 10px;
    padding: 28px;
    margin-bottom: 25px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.section-title {
    font-weight: 600;
    font-size: 16px;
    margin-bottom: 20px;
    border-left: 5px solid #4f46e5;
    padding-left: 12px;
}

.form-group label {
    font-size: 12px;
    font-weight: 600;
    margin-bottom: 6px;
}

.elegant-form-control {
    height: 40px;
    font-size: 13px;
}

/* BUTTON AREA */
.invoice-action {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 25px;
    padding-top: 15px;
    border-top: 1px solid #eee;
}

/* TABLE */
.table-invoice th {
    font-size: 12px;
    background: #f8f9fa;
    font-weight: 600;
}

.table-invoice td {
    font-size: 12px;
    vertical-align: middle;
}

.table-invoice tbody tr:hover {
    background: #fafafa;
}
</style>

<div class="elegant-container">

{{-- ALERT --}}
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert alert-danger">{{ session('error') }}</div>
@endif

<form action="{{ route('invoice.update', ['id' => $invoice->id]) }}" method="post">
@csrf
<input type="hidden" name="_method" value="PUT">

{{-- FORM INPUT --}}
<div class="invoice-section">
    <div class="section-title">Input Detail Invoice</div>

    <div class="row">
        <div class="col-lg-3 col-md-4 col-sm-6 form-group">
            <label>Gender</label>
            <input type="text" name="genre" class="elegant-form-control form-control">
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 form-group">
            <label>Name</label>
            <input type="text" name="name" class="elegant-form-control form-control">
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 form-group">
            <label>Booking Code</label>
            <input type="text" name="booking_code" class="elegant-form-control form-control">
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 form-group">
            <label>Airlines</label>
            <select name="airline_id" class="elegant-form-control form-control">
                <option value="">Select Airline</option>
                @foreach ($airlines as $airline)
                <option value="{{ $airline->id }}">
                    {{ $airline->airlines_code }} - {{ $airline->airlines_name }}
                </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3 col-md-4 col-sm-6 form-group">
            <label>Airlines No</label>
            <input type="text" name="airlines_no" class="elegant-form-control form-control">
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 form-group">
            <label>Class</label>
            <input type="text" name="class" class="elegant-form-control form-control">
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 form-group">
            <label>Ticket No</label>
            <input type="text" name="ticket_no" class="elegant-form-control form-control">
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 form-group">
            <label>Route</label>
            <input type="text" name="route" class="elegant-form-control form-control">
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3 col-md-4 col-sm-6 form-group">
            <label>Depart Date</label>
            <input type="date" name="depart_date" class="elegant-form-control form-control">
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 form-group">
            <label>Return Date</label>
            <input type="date" name="return_date" class="elegant-form-control form-control">
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 form-group">
            <label>Pax Paid</label>
            <input type="text" name="pax_paid" class="elegant-form-control form-control">
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 form-group">
            <label>Price</label>
            <input type="text" name="price" class="elegant-form-control form-control">
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3 col-md-4 col-sm-6 form-group">
            <label>Discount</label>
            <input type="text" name="discount" class="elegant-form-control form-control">
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 form-group">
            <label>NTA</label>
            <input type="text" name="nta" class="elegant-form-control form-control">
        </div>
    </div>

    {{-- BUTTON --}}
    <div class="invoice-action">
        <div>
            <button id="addBtn" class="btn btn-success btn-sm">+ Tambah</button>
            <button id="update-button" class="btn btn-warning btn-sm" style="display:none;">Ubah</button>
        </div>
        <div>
            <button type="button" id="cancelInvoiceBtn" class="btn btn-light btn-sm" data-href="{{ url()->previous() }}">Batal</button>
            <button name="redirect" value="true" class="btn btn-primary btn-sm">Simpan Invoice</button>
        </div>
    </div>
</div>
</form>

<div class="row">
    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="invoice-section">
            <div class="section-title">Customer</div>
            <table class="table table-borderless mb-0">
                <tr>
                    <td>Nama Booker</td>
                    <td>{{ $invoice->customer->booker }}</td>
                </tr>
                <tr>
                    <td>No Telp</td>
                    <td>{{ $invoice->customer->phone }}</td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td>{{ $invoice->customer->email }}</td>
                </tr>
                <tr>
                    <td>Payment</td>
                    <td>{{ $invoice->customer->payment }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="col-lg-6 col-md-6 col-sm-6">
        <div class="invoice-section">
            <div class="section-title">Company</div>
            <table class="table table-borderless mb-0">
                <tr>
                    <td>Perusahaan</td>
                    <td>Lghani Tour & Travel</td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>Komplek Permata 2 Blok M6</td>
                </tr>
                <tr>
                    <td>No Telp</td>
                    <td>+62 856-2151-280</td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td>lghani_travel@ymail.com</td>
                </tr>
            </table>
        </div>
    </div>
</div>

{{-- TABLE DETAIL --}}
<div class="invoice-section">
    <div class="section-title">Detail Invoice</div>

    <div class="table-responsive">
        <table class="table elegant-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Gender</th>
                    <th>Name</th>
                    <th>Booking Code</th>
                    <th>Airlines</th>
                    <th>Route</th>
                    <th>Pax Paid</th>
                    <th>Price</th>
                    <th>NTA</th>
                    <th>Profit</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach ($invoice->detail as $detail)
                <tr class="table-row-hover">
                    <td>{{ $no++ }}</td>
                    <td>{{ $detail->genre }}</td>
                    <td class="uppercase-text">{{ $detail->name }}</td>
                    <td>{{ $detail->booking_code }}</td>
                    <td>{{ $comboAirline[$detail->airline_id]['airlines_code'] }}</td>
                    <td>{{ $detail->route }}</td>
                    <td>Rp {{ number_format($detail->pax_paid) }}</td>
                    <td>Rp {{ number_format($detail->price) }}</td>
                    <td>Rp {{ number_format($detail->nta) }}</td>
                    <td>Rp {{ number_format($detail->profit) }}</td>

                    <td class="action-buttons">
                        <a href="#"
                           class="btn-action edit-action edit-button"
                           title="Ubah"
                           data-genre="{{ $detail->genre }}"
                           data-name="{{ $detail->name }}"
                           data-booking-code="{{ $detail->booking_code }}"
                           data-airline-id="{{ $detail->airline_id }}"
                           data-airlines-no="{{ $detail->airlines_no }}"
                           data-class="{{ $detail->class }}"
                           data-ticket-no="{{ $detail->ticket_no }}"
                           data-route="{{ $detail->route }}"
                           data-depart-date="{{ $detail->depart_date }}"
                           data-return-date="{{ $detail->return_date }}"
                           data-pax-paid="{{ $detail->pax_paid }}"
                           data-price="{{ $detail->price }}"
                           data-discount="{{ $detail->discount }}"
                           data-nta="{{ $detail->nta }}">
                            <i class="fa fa-pencil"></i>
                        </a>

                        <form id="delete-detail-form-{{ $detail->id }}" action="{{ route('invoice.delete_product', ['id' => $detail->id]) }}"
                            method="post" style="display:inline;" onsubmit="return showCustomConfirm(this);">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-action delete-action"
                                    title="Hapus">
                                <i class="fa fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- SCRIPT EDIT (ASLI, TIDAK DIUBAH) --}}
<script>
$(document).ready(function(){
$('.edit-button').click(function(){
$('input[name=genre]').val($(this).data('genre'));
$('input[name=name]').val($(this).data('name'));
$('input[name=booking_code]').val($(this).data('booking-code'));
$('select[name=airline_id]').val($(this).data('airline-id'));
$('input[name=airlines_no]').val($(this).data('airlines-no'));
$('input[name=class]').val($(this).data('class'));
$('input[name=ticket_no]').val($(this).data('ticket-no'));
$('input[name=route]').val($(this).data('route'));
$('input[name=depart_date]').val($(this).data('depart-date'));
$('input[name=return_date]').val($(this).data('return-date'));
$('input[name=pax_paid]').val($(this).data('pax-paid'));
$('input[name=price]').val($(this).data('price'));
$('input[name=discount]').val($(this).data('discount'));
$('input[name=nta]').val($(this).data('nta'));

$('#addBtn').hide();
$('#update-button').show();
});
});
</script>

<!-- GENERIC CONFIRM MODAL -->
<div id="confirmModal" class="custom-modal-overlay" style="display: none;">
    <div class="custom-modal-content">
        <div class="modal-header-danger">
            <i class="fa fa-exclamation-triangle"></i> Konfirmasi
        </div>
        <div class="modal-body">
            <span id="confirmMessage">Apakah Anda yakin?</span>
        </div>
        <div class="modal-footer">
            <button id="cancelConfirmBtn" class="btn btn-secondary-modal">Batal</button>
            <button id="confirmBtn" class="btn btn-danger-modal">Ya</button>
        </div>
    </div>
</div>

<!-- ALERT MODAL (single OK) -->
<div id="alertModal" class="custom-modal-overlay" style="display: none;">
    <div class="custom-modal-content">
        <div class="modal-header-danger">
            <i class="fa fa-exclamation-circle"></i> Peringatan
        </div>
        <div class="modal-body">
            <span id="alertMessage">Pesan</span>
        </div>
        <div class="modal-footer">
            <button id="okAlertBtn" class="btn btn-primary btn-sm">OK</button>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    var targetFormSelector = '';
    var cancelHref = null;
    var deleteUrl = null; // store delete form action for AJAX fallback

    // showCustomConfirm(form) will set pending form and open modal (used by form onsubmit)

    $('#cancelConfirmBtn').on('click', function(){
        $('#confirmModal').fadeOut(200);
        // clear any pending native form submit
        window._pendingConfirmForm = null;
        cancelHref = null;
    });

    // Central confirm handler: either perform delete (submit form) or navigate for cancel
    $('#confirmBtn').on('click', function(){
        if(cancelHref){
            $('#confirmModal').fadeOut(150, function(){
                window.location = cancelHref;
            });
        } else if(window._pendingConfirmForm){
            // submit the pending form natively to avoid JS interceptions
            $('#confirmModal').fadeOut(150, function(){
                try{
                    var f = window._pendingConfirmForm;
                    window._pendingConfirmForm = null;
                    if(f && typeof f.submit === 'function'){
                        f.submit();
                    }
                }catch(e){
                    $('#alertMessage').text('Gagal men-submit form: ' + (e.message || e));
                    $('#alertModal').fadeIn(200);
                }
            });
        } else if(deleteUrl){
            // Attempt AJAX delete as a more robust fallback
            $('#confirmModal').fadeOut(150, function(){
                fetch(deleteUrl, {
                    method: 'DELETE',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).then(function(resp){
                    if(resp.ok){
                        if(resp.redirected){
                            window.location = resp.url;
                        } else {
                            window.location.reload();
                        }
                    } else {
                        resp.text().then(function(text){
                            var msg = 'Gagal menghapus data. Status: ' + resp.status + '\n' + (text || '');
                            $('#alertMessage').text(msg);
                            $('#alertModal').fadeIn(200);
                        });
                    }
                }).catch(function(err){
                    $('#alertMessage').text('Terjadi kesalahan saat menghapus: ' + (err && err.message ? err.message : err));
                    $('#alertModal').fadeIn(200);
                });
            });
            // reset deleteUrl
            deleteUrl = null;
            targetFormSelector = '';
        } else {
            $('#confirmModal').fadeOut(150);
        }
        // reset
        targetFormSelector = '';
        cancelHref = null;
    });

    // Cancel invoice button - check if any input/select/textarea in the same form has a value
    $('#cancelInvoiceBtn').on('click', function(e){
        e.preventDefault();
        var href = $(this).data('href');
        var $form = $(this).closest('form');
        var hasData = false;

        $form.find('input:not([type=hidden]), select, textarea').each(function(){
            var val = $(this).val();
            if(val !== null && val.toString().trim() !== ''){
                hasData = true;
                return false; // break
            }
        });

        if(hasData){
            targetFormSelector = '';
            cancelHref = href;
            $('#confirmMessage').text('Anda yakin ingin membatalkan data yang sudah terisi/terubah?');
            $('#confirmBtn').text('Ya, Batal').removeClass('btn-danger-modal').addClass('btn-primary');
            $('#confirmModal').fadeIn(200);
        } else {
            window.location = href;
        }
    });

    // Alert modal OK
    $('#okAlertBtn').on('click', function(){
        $('#alertModal').fadeOut(150);
    });

    // Date validation: Depart Date must not be greater than Return Date
    function validateDates(){
        var depart = $('input[name=depart_date]').val();
        var ret = $('input[name=return_date]').val();
        if(depart && ret){
            var d = new Date(depart);
            var r = new Date(ret);
            if(d > r){
                $('#alertMessage').text('Depart Date tidak boleh lebih dari Return Date.');
                $('#alertModal').fadeIn(200);
                return false;
            }
        }
        return true;
    }

    $('input[name=depart_date], input[name=return_date]').on('change', function(){
        var ok = validateDates();
        if(!ok){
            // clear the changed value and focus
            $(this).val('');
            $(this).focus();
        }
    });
});
</script>

<script>
function showCustomConfirm(form){
    // store pending form for native submit on confirmation
    window._pendingConfirmForm = form;
    // also capture deleteUrl for AJAX fallback
    try{ window.deleteUrl = form.action || null; }catch(e){ window.deleteUrl = null; }
    // set modal text & styles
    $('#confirmMessage').text('Apakah Anda yakin ingin menghapus detail invoice ini?');
    $('#confirmBtn').text('Ya, Hapus').removeClass('btn-primary').addClass('btn-danger-modal');
    $('#confirmModal').fadeIn(200);
    return false; // prevent the form from submitting now
}
</script>

</div>
@endsection
