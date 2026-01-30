@extends('master')

@section('konten')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<style type="text/css">
    #sample-room,
    #sample-room-detail,
    #sample-guest {
        display: none !important;
    }

    /* Elegant UI Styles - Sama persis dengan Edit */
    .elegant-container { padding: 30px; background-color: #f1f5f9; min-height: 100vh; font-family: 'Poppins', sans-serif; }
    .page-title { font-size: 24px; font-weight: 700; color: #1e293b; margin-bottom: 5px; }
    .card-elegant { background: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border: none; padding: 25px; }
    .elegant-form-group { margin-bottom: 20px; }
    .elegant-form-group label { display: block; font-size: 14px; font-weight: 600; color: #475569; margin-bottom: 8px; }
    .elegant-form-control { 
        width: 100%; padding: 10px 12px; font-size: 14px; line-height: 1.5; color: #1e293b; 
        background-color: #fff; border: 1px solid #cbd5e1; border-radius: 8px; transition: all 0.2s; 
    }
    .elegant-form-control:focus { border-color: #2563eb; outline: none; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }
    
    .btn-primary { 
        background-color: #2563eb; color: white; border: none; padding: 10px 20px; 
        border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.2s; 
        text-decoration: none; display: inline-block;
    }
    .btn-primary:hover { background-color: #1d4ed8; text-decoration: none; color: white; }

    /* Custom Select2 Styling untuk menyamakan dengan Elegant Control */
    .select2-container--default .select2-selection--single {
        height: 42px !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        padding: 6px 12px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px !important;
        color: #1e293b !important;
        padding-left: 0 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
    }
</style>

<div class="elegant-container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="page-title">Create Hotel Voucher</h1>
        </div>
    </div>

    <div class="card-elegant">
        <div class="card-body">
            @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
            @endif

            <form id="form-data" action="{{ route('hotelvoucher.save') }}" method="post">
                @csrf
                <input type="hidden" name="booking_id" value="{{ $booking_id }}">

                <div class="card-elegant" style="background-color: #f8fafc; margin-bottom: 20px; padding: 20px;">
                    <h5 style="font-weight: 600; color: #334155; margin-bottom: 20px; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">Invoice Detail</h5>
                    
                    <div class="elegant-form-group">
                        <label for="customer_id">Customer</label>
                        <select name="customer_id" id="customer_id" class="elegant-form-control select2 {{ $errors->has('customer_id') ? 'is-invalid':'' }}" required>
                            <option value="">Pilih</option>
                            @foreach ($customers as $customer) 
                            <option value="{{ $customer->id }}">{{ $customer->gender }} - {{ $customer->booker }} - {{ $customer->company }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger">{{ $errors->first('customer_id') }}</span>
                    </div>

                    <div class="elegant-form-group">
                        <label for="hotel_due_date">Due Date Hotel</label>
                        <input type="date" id="hotel_due_date" name="hotel_due_date" class="elegant-form-control" required value="{{ !empty($invoice) ? $invoice->hotel_due_date : '' }}">
                        <span class="text-danger" id="error-hotel_due_date"></span>
                    </div>

                    <div class="elegant-form-group">
                        <label for="payment_date">Date Payment</label>
                        <input type="date" id="payment_date" name="payment_date" class="elegant-form-control" required value="{{ !empty($invoice) ? $invoice->payment_date : '' }}">
                        <span class="text-danger" id="payment_date-error"></span>
                    </div>

                    <div class="elegant-form-group">
                        <label for="office_code">Office Code</label>
                        <input type="text" id="office_code" name="office_code" class="elegant-form-control" placeholder="Office Code" required value="{{ !empty($invoice) ? $invoice->office_code : '' }}">
                        <span class="text-danger" id="office_code-error"></span>
                    </div>

                    <div class="elegant-form-group">
                        <label for="discount">Discount</label>
                        <input type="text" id="discount" name="discount" class="elegant-form-control" placeholder="Discount" required>
                        <span class="text-danger" id="discount-error"></span>
                    </div>
                </div>

                <div class="card-elegant" style="background-color: #f8fafc; margin-bottom: 20px; padding: 20px;">
                    <h5 style="font-weight: 600; color: #334155; margin-bottom: 20px; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">Booker Detail</h5>
                    
                    <div class="elegant-form-group">
                        <label for="currency">Currency</label>
                        <select id="currency" name="currency" class="elegant-form-control" required>
                            <option value="">Pilih</option>
                            @foreach ($currencys as $currCode => $currName) 
                                <option value="{{ $currCode }}">{{ $currName }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger" id="currency-error"></span>
                    </div>

                    <div class="elegant-form-group">
                        <label for="booker">Booker</label>
                        <input type="text" id="booker" name="booker" class="elegant-form-control" placeholder="Masukkan Nama Booker" required>
                        <span class="text-danger" id="booker-error"></span>
                    </div>

                    <div class="elegant-form-group">
                        <label for="booker_agent">Booker Agent</label>
                        <input type="text" id="booker_agent" name="booker_agent" class="elegant-form-control" placeholder="Masukkan Booker Agent">
                    </div>

                    <div class="elegant-form-group">
                        <label for="no_booker_agent">No Booker Agent</label>
                        <input type="text" id="no_booker_agent" name="no_booker_agent" class="elegant-form-control" placeholder="Masukkan No Booker Agent">
                    </div>

                    <div class="elegant-form-group">
                        <label for="nationality">Nationality</label>
                        <input type="text" id="nationality" name="nationality" class="elegant-form-control" placeholder="Masukkan Nationality" required>
                        <span class="text-danger" id="error-nationality"></span>
                    </div>

                    <div class="elegant-form-group">
                        <label for="attention">Attention</label>
                        <input type="text" id="attention" name="attention" class="elegant-form-control" placeholder="Attention">
                    </div>
                </div>

                <div class="card-elegant" style="background-color: #f8fafc; margin-bottom: 20px; padding: 20px;">
                    <h5 style="font-weight: 600; color: #334155; margin-bottom: 20px; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">Hotel Detail</h5>
                    
                    <div class="elegant-form-group">
                        <label for="hotel_id">Hotel</label>
                        <select id="hotel_id" name="hotel_id" class="elegant-form-control select2" required>
                            <option value="">Pilih</option>
                            @foreach ($hotels as $hotel) 
                                <option value="{{ $hotel->id }}" data-code="{{ $hotel->hotel_code }}" data-region="{{ $hotel->region }}">{{ $hotel->hotel_code }} - {{ $hotel->hotel_name }} @if ($hotel->region != '') | Region {{ $hotel->region }} @endif</option>
                            @endforeach
                        </select>
                        <span class="text-danger" id="error-hotel_id"></span>
                        <input type="hidden" id="hotel_code" name="hotel_code">
                    </div>

                    <div class="elegant-form-group">
                        <label for="region">Region</label>
                        <input type="text" id="region" name="region" class="elegant-form-control" placeholder="" readonly="readonly">
                    </div>

                    <div class="elegant-form-group">
                        <label for="check_in">Check In</label>
                        <input type="date" id="check_in" name="check_in" class="elegant-form-control" required>
                        <span class="text-danger" id="error-check_in"></span>
                    </div>

                    <div class="elegant-form-group">
                        <label for="check_out">Check Out</label>
                        <input type="date" id="check_out" name="check_out" class="elegant-form-control" required>
                        <span class="text-danger" id="error-check_out"></span>
                    </div>

                    <div class="elegant-form-group">
                        <label for="confirm_by">Confirm By</label>
                        <input type="text" id="confirm_by" name="confirm_by" class="elegant-form-control" placeholder="Confirm By" required>
                        <span class="text-danger" id="error-confirm_by"></span>
                    </div>

                    <div class="elegant-form-group">
                        <label for="remark">Remark</label>
                        <input type="text" id="remark" name="remark" class="elegant-form-control" placeholder="Remark">
                        <span class="text-danger" id="error-remark"></span>
                    </div>

                    <div class="elegant-form-group">
                        <label for="rsvn_and_payment_by">RSVN And Payment By</label>
                        <input type="text" id="rsvn_and_payment_by" name="rsvn_and_payment_by" class="elegant-form-control" placeholder="RSVN And Payment By" required>
                        <span class="text-danger" id="error-rsvn_and_payment_by"></span>
                    </div>

                    <div class="elegant-form-group" style="display: none">
                        <label for="count_type_room">How Many Type Room</label>
                        <input type="number" id="count_type_room" name="count_type_room" class="elegant-form-control" placeholder="How Many Type Room" value="1" min="1">
                        <span class="text-danger" id="error-count_type_room"></span>
                    </div>
                </div>

                <div class="elegant-form-group">
                    <button type="submit" class="btn-primary" id="btnSave" style="font-size: 14px; padding: 12px 24px;">
                        <i class="fa fa-save"></i> Simpan
                    </button>
                    <a href="{{ url('/hotel-voucher') }}" class="btn-primary" style="background-color: #6b7280; margin-left: 10px; font-size: 14px; padding: 12px 24px;">
                        <i class="fa fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        // Initialize Select2 dengan width 100%
        $('.select2').select2({ width: '100%' });

        $('#hotel_id').change(function() {
            var id = $(this).val();
            if (id) {
                var region = $(this).find('option[value='+ id +']').attr('data-region');
                var code = $(this).find('option[value='+ id +']').attr('data-code');
                $('#region').val(region);
                $('#hotel_code').val(code);
            }
        });

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
        });
    });
</script>
@endsection