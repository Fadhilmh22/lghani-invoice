@extends('master')

@section('konten')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2();
    });
</script>

<style type="text/css">
    #sample-room,
    #sample-room-detail,
    #sample-guest {
        display: none !important;
    }
</style>

<div class="elegant-container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="page-title">Edit Hotel Voucher</h1>
        </div>
    </div>
    
    <div class="card-elegant">
        <div class="card-body">
            @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
            @endif

            <form id="form-data" action="{{ route('hotelvoucher.update', ['id' => $voucher->id]) }}" method="post">
                @csrf
                <input type="hidden" name="_method" value="PUT" class="form-control">
                
                <!-- Invoice Detail Section -->
                <div class="card-elegant" style="background-color: #f8fafc; margin-bottom: 20px; padding: 20px;">
                    <h5 style="font-weight: 600; color: #334155; margin-bottom: 20px; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">Invoice Detail</h5>
                    
                    <div class="elegant-form-group">
                        <label for="customer_id">Customer</label>
                        <select name="customer_id" id="customer_id" class="elegant-form-control select2 {{ $errors->has('customer_id') ? 'is-invalid':'' }}" required>
                            <option value="">Pilih</option>
                            @foreach ($customers as $customer) 
                            <option value="{{ $customer->id }}" @if( !empty($invoice) && $invoice->customer_id == $customer->id ) selected="selected" @endif>{{ $customer->gender }} - {{ $customer->booker }} - {{ $customer->company }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger">{{ $errors->first('customer_id') }}</span>
                    </div>
                    
                    <div class="elegant-form-group">
                        <label for="hotel_due_date">Due Date Hotel</label>
                        <input type="text" id="hotel_due_date" name="hotel_due_date" class="elegant-form-control" required value="{{ !empty($invoice) ? $invoice->hotel_due_date : '' }}">
                        <span class="text-danger" id="error-hotel_due_date"></span>
                    </div>
                    
                    <div class="elegant-form-group">
                        <label for="payment_date">Date Payment</label>
                        <input type="text" id="payment_date" name="payment_date" class="elegant-form-control" required value="{{ !empty($invoice) ? $invoice->payment_date : '' }}">
                        <span class="text-danger" id="payment_date-error"></span>
                    </div>
                    
                    <div class="elegant-form-group">
                        <label for="office_code">Office Code</label>
                        <input type="text" id="office_code" name="office_code" class="elegant-form-control" placeholder="Office Code" required value="{{ !empty($invoice) ? $invoice->office_code : '' }}">
                        <span class="text-danger" id="office_code-error"></span>
                    </div>
                </div>
                
                <!-- Booker Detail Section -->
                <div class="card-elegant" style="background-color: #f8fafc; margin-bottom: 20px; padding: 20px;">
                    <h5 style="font-weight: 600; color: #334155; margin-bottom: 20px; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">Booker Detail</h5>
                    
                    <div class="elegant-form-group">
                        <label for="currency">Currency</label>
                        <select id="currency" name="currency" class="elegant-form-control" required>
                            <option value="">Pilih</option>
                            @foreach ($currencys as $currCode => $currName) 
                            <option value="{{ $currCode }}" @if( $currCode == $voucher->currency ) selected="selected" @endif>{{ $currName }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger" id="currency-error"></span>
                    </div>
                    
                    <div class="elegant-form-group">
                        <label for="booker">Booker</label>
                        <input type="text" id="booker" name="booker" class="elegant-form-control" placeholder="Masukkan Nama Booker" value="{{ $voucher->booker }}" required>
                        <span class="text-danger" id="booker-error"></span>
                    </div>
                    
                    <div class="elegant-form-group">
                        <label for="booker_agent">Booker Agent</label>
                        <input type="text" id="booker_agent" name="booker_agent" class="elegant-form-control" placeholder="Masukkan Booker Agent" value="{{ $voucher->booker_agent }}">
                    </div>
                    
                    <div class="elegant-form-group">
                        <label for="no_booker_agent">No Booker Agent</label>
                        <input type="text" id="no_booker_agent" name="no_booker_agent" class="elegant-form-control" placeholder="Masukkan No Booker Agent" value="{{ $voucher->no_booker_agent }}">
                    </div>
                    
                    <div class="elegant-form-group">
                        <label for="nationality">Nationality</label>
                        <input type="text" id="nationality" name="nationality" class="elegant-form-control" placeholder="Masukkan Nationality" value="{{ $voucher->nationality }}" required>
                        <span class="text-danger" id="error-nationality"></span>
                    </div>
                    
                    <div class="elegant-form-group">
                        <label for="attention">Attention</label>
                        <input type="text" id="attention" name="attention" class="elegant-form-control" placeholder="Attention" value="{{ $voucher->attention }}">
                    </div>
                </div>
                
                <!-- Hotel Detail Section -->
                <div class="card-elegant" style="background-color: #f8fafc; margin-bottom: 20px; padding: 20px;">
                    <h5 style="font-weight: 600; color: #334155; margin-bottom: 20px; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">Hotel Detail</h5>
                    
                    <div class="elegant-form-group">
                        <label for="hotel_id">Hotel</label>
                        <select id="hotel_id" name="hotel_id" class="elegant-form-control" required>
                            <option value="">Pilih</option>
                            @foreach ($hotels as $hotel) 
                            <option value="{{ $hotel->id }}" data-code="{{ $hotel->hotel_code }}" data-region="{{ $hotel->region }}" @if( $hotel->id == $voucher->hotel_id ) selected="selected" @endif>{{ $hotel->hotel_code }} - {{ $hotel->hotel_name }} @if ($hotel->region != '') | Region {{ $hotel->region }} @endif</option>
                            @endforeach
                        </select>
                        <span class="text-danger" id="error-hotel_id"></span>
                        <input type="hidden" id="hotel_code" name="hotel_code">
                        <input type="hidden" id="reset_room_and_guest" name="reset_room_and_guest" value="0">
                    </div>
                    
                    <div class="elegant-form-group">
                        <label for="region">Region</label>
                        <input type="text" id="region" name="region" class="elegant-form-control" placeholder="" readonly="readonly">
                    </div>
                    
                    <div class="elegant-form-group">
                        <label for="check_in">Check In</label>
                        <input type="text" id="check_in" name="check_in" class="elegant-form-control" value="{{ $voucher->check_in }}" required>
                        <span class="text-danger" id="error-check_in"></span>
                    </div>
                    
                    <div class="elegant-form-group">
                        <label for="check_out">Check Out</label>
                        <input type="text" id="check_out" name="check_out" class="elegant-form-control" value="{{ $voucher->check_out }}" required>
                        <span class="text-danger" id="error-check_out"></span>
                    </div>
                    
                    <div class="elegant-form-group">
                        <label for="confirm_by">Confirm By</label>
                        <input type="text" id="confirm_by" name="confirm_by" class="elegant-form-control" placeholder="Confirm By" value="{{ $voucher->confirm_by }}" required>
                        <span class="text-danger" id="error-confirm_by"></span>
                    </div>
                    
                    <div class="elegant-form-group">
                        <label for="remark">Remark</label>
                        <input type="text" id="remark" name="remark" class="elegant-form-control" placeholder="Remark" value="{{ $voucher->remark }}">
                        <span class="text-danger" id="error-remark"></span>
                    </div>
                    
                    <div class="elegant-form-group">
                        <label for="rsvn_and_payment_by">RSVN And Payment By</label>
                        <input type="text" id="rsvn_and_payment_by" name="rsvn_and_payment_by" class="elegant-form-control" placeholder="RSVN And Payment By" value="{{ $voucher->rsvn_and_payment_by }}" required>
                        <span class="text-danger" id="error-rsvn_and_payment_by"></span>
                    </div>
                    
                    <div class="elegant-form-group" style="display: none">
                        <label for="count_type_room">How Many Type Room</label>
                        <input type="number" id="count_type_room" name="count_type_room" class="elegant-form-control" placeholder="How Many Type Room" min="1" value="{{ $voucher->count_type_room }}" required>
                        <span class="text-danger" id="error-count_type_room"></span>
                    </div>
                </div>
                
                <div class="elegant-form-group">
                    <button type="submit" class="btn-primary btn-sm" id="btnSave">
                        <i class="fa fa-save"></i> Simpan
                    </button>
                    <a href="{{ url('/hotel-voucher') }}" class="btn-primary btn-sm" style="background-color: #6b7280; margin-left: 10px; text-decoration: none; display: inline-block;">
                        <i class="fa fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $('#hotel_id').change(function(e) {
        var id = $(this).val();
        
        var confirmed = true;
        if( e.originalEvent && id != {{ $voucher->hotel_id }}  ) {
            if( confirm("Aksi ini akan menghapus data room dan guest sebelumnya. Lanjutkan?") ) {
                confirmed = true;
            } else {
                confirmed = false;
                $('#reset_room_and_guest').val(1);
                $('#hotel_id').val({{ $voucher->hotel_id }});
                $('#hotel_id').trigger('change');
            }
        } else {
            $('#reset_room_and_guest').val(0);
        }
        
        if( confirmed ) {
            var region = $(this).find('option[value='+ id +']').attr('data-region');
            var code = $(this).find('option[value='+ id +']').attr('data-code');
            $('#region').val(region);
            $('#hotel_code').val(code);
        }
    })

    $('#check_in,#check_out').change(function() {
        if( $('#check_in').val() != '' && $('#check_out').val() != '' ) {
            var check_in = new Date( $('#check_in').val() );
            var check_out = new Date( $('#check_out').val() );
            var daysDiff = Math.round((check_out.getTime() - check_in.getTime()) / (24 * 60 * 60 * 1000));

            if( daysDiff < 1 ) {
                $('#check_in').val('');
                $('#check_out').val('');
                alert("Tanggal check in - check out tidak valid");
            }
        }
    })
    
    $('#hotel_id').trigger('change');
    $('#check_out').trigger('change');
</script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.flatpickr) {
            flatpickr('#hotel_due_date', { dateFormat: 'Y-m-d' });
            flatpickr('#payment_date', { dateFormat: 'Y-m-d' });
            flatpickr('#check_in', {
                dateFormat: 'Y-m-d',
                onChange: function(selectedDates, dateStr, instance) {
                    instance._input.dispatchEvent(new Event('change'));
                }
            });
            flatpickr('#check_out', {
                dateFormat: 'Y-m-d',
                onChange: function(selectedDates, dateStr, instance) {
                    instance._input.dispatchEvent(new Event('change'));
                }
            });
        }
    });
</script>
@endsection
