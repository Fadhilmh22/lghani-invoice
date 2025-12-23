@extends('master')

@section('konten')
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    
    <div class="elegant-container">
        <div class="row mb-4">
            <div class="col-md-12">
                <h1 class="page-title">Laporan Piutang</h1>
            </div>
        </div>
        
        <div class="card-elegant">
            <div class="card-body">
                <form action="{{ url('/report/piutang') }}" method="GET" class="mb-4">
                    @csrf
                    <div class="elegant-form-group">
                        <label for="customer_id">Pilih Pelanggan</label>
                        <select name="customer_id" id="customer_id" class="elegant-form-control select2 {{ $errors->has('customer_id') ? 'is-invalid':'' }}" required>
                            <option value="">Pilih</option>
                            @foreach ($customers as $customer) 
                            <option value="{{ $customer->id }}" @if( !empty($invoice) && $invoice->customer_id == $customer->id ) selected @endif>{{ $customer->booker }} - {{ $customer->company }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger">{{ $errors->first('customer_id') }}</span>
                    </div>
                    
                    <div class="elegant-form-group">
                        <button type="submit" class="btn-primary btn-sm">
                            <i class="fa fa-search"></i> Submit
                        </button>
                        <a href="javascript:void(0);" class="btn-primary btn-sm btn-print" style="margin-left: 10px;">
                            <i class="fa fa-print"></i> Print
                        </a>
                    </div>
                </form>
                
                @if(isset($totalBelumLunas))
                <div class="card-elegant" style="background-color: #f8fafc; padding: 20px; border-radius: 12px; margin-top: 20px;">
                    <h5 style="font-weight: 600; color: #334155; margin-bottom: 10px;"><b>Total Belum Lunas</b></h5>
                    <p style="font-size: 24px; font-weight: 700; color: #ef4444; margin: 0;">Rp {{ number_format($totalBelumLunas) }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- JavaScript untuk Piutang -->
    <script src="{{ asset('js/piutang.js') }}"></script>
@endsection
