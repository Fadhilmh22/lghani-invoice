@extends('master')

@section('konten')
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
            <h1 class="page-title">Create Hotel Invoice</h1>
        </div>
    </div>
    
    <div class="card-elegant">
        <div class="card-body">
            @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
            @endif

            <form id="form-data" action="{{ route('hotelinvoice.save') }}" method="post">
                @csrf
                <div class="elegant-form-group">
                    <label for="customer_id">Customer</label>
                    <select name="customer_id" id="customer_id" class="elegant-form-control {{ $errors->has('customer_id') ? 'is-invalid':'' }}" required>
                        <option value="">Pilih</option>
                        @foreach ($customers as $customer) 
                        <option value="{{ $customer->id }}">{{ $customer->booker }} - {{ $customer->email }}</option>
                        @endforeach
                    </select>
                    <span class="text-danger">{{ $errors->first('customer_id') }}</span>
                </div>
                
                <div class="elegant-form-group">
                    <label for="hotel_due_date">Due Date Hotel</label>
                    <input type="text" id="hotel_due_date" name="hotel_due_date" class="elegant-form-control" required>
                    <span class="text-danger" id="error-hotel_due_date"></span>
                </div>
                
                <div class="elegant-form-group">
                    <label for="payment_date">Date Payment</label>
                    <input type="text" id="payment_date" name="payment_date" class="elegant-form-control" required>
                    <span class="text-danger" id="payment_date-error"></span>
                </div>
                
                <div class="elegant-form-group">
                    <label for="office_code">Office Code</label>
                    <input type="text" id="office_code" name="office_code" class="elegant-form-control" placeholder="Office Code" required>
                    <span class="text-danger" id="office_code-error"></span>
                </div>
                
                <div class="elegant-form-group">
                    <label for="discount">Discount</label>
                    <input type="text" id="discount" name="discount" class="elegant-form-control" placeholder="Discount" required>
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
