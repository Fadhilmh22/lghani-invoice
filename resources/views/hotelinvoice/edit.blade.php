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
            <h1 class="page-title">Edit Hotel Invoice</h1>
        </div>
    </div>
    
    <div class="card-elegant">
        <div class="card-body">
            @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
            @endif

            <form id="form-data" action="{{ route('hotelinvoice.update', ['id' => $invoice->id]) }}" method="post">
                @csrf
                <input type="hidden" name="_method" value="PUT" class="form-control">
                
                <div class="elegant-form-group">
                    <label for="customer_id">Customer</label>
                    <select name="customer_id" id="customer_id" class="elegant-form-control select2 {{ $errors->has('customer_id') ? 'is-invalid':'' }}" required>
                        <option value="">Pilih</option>
                        @foreach ($customers as $customer) 
                        <option value="{{ $customer->id }}" @if( $customer->id == $invoice->customer_id ) selected="selected" @endif>{{ $customer->gender }} - {{ $customer->booker }} - {{ $customer->company }}</option>
                        @endforeach
                    </select>
                    <span class="text-danger">{{ $errors->first('customer_id') }}</span>
                </div>
                
                <div class="elegant-form-group">
                    <label for="hotel_due_date">Due Date Hotel</label>
                    <input type="text" id="hotel_due_date" name="hotel_due_date" class="elegant-form-control" value="{{ $invoice->hotel_due_date }}" required>
                    <span class="text-danger" id="error-hotel_due_date"></span>
                </div>
                
                <div class="elegant-form-group">
                    <label for="payment_date">Date Payment</label>
                    <input type="text" id="payment_date" name="payment_date" class="elegant-form-control" value="{{ $invoice->payment_date }}" required>
                    <span class="text-danger" id="payment_date-error"></span>
                </div>
                
                <div class="elegant-form-group">
                    <label for="office_code">Office Code</label>
                    <input type="text" id="office_code" name="office_code" class="elegant-form-control" value="{{ $invoice->office_code }}" placeholder="Office Code" required>
                    <span class="text-danger" id="office_code-error"></span>
                </div>
                
                <div class="elegant-form-group">
                    <label for="discount">Diskon</label>
                    <input type="text" id="discount" name="discount" class="elegant-form-control" value="{{ isset($invoice) ? $invoice->discount : '' }}" placeholder="Diskon" required>
                    <span class="text-danger" id="discount-error"></span>
                </div>
                
                <div class="elegant-form-group">
                    <button type="submit" class="btn-primary btn-sm" id="btnSave">
                        <i class="fa fa-save"></i> Simpan
                    </button>
                    <a href="{{ route('hotelinvoice.index') }}" class="btn-primary btn-sm" style="background-color: #6b7280; margin-left: 10px; text-decoration: none; display: inline-block;">
                        <i class="fa fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.flatpickr) {
            flatpickr('#hotel_due_date', { dateFormat: 'Y-m-d' });
            flatpickr('#payment_date', { dateFormat: 'Y-m-d' });
        }
    });
</script>
@endsection
