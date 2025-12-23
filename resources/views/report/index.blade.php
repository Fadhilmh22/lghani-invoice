@extends('master')

@section('konten')
    <!-- Elegant UI CSS -->
    <link href="{{ asset('css/elegant-ui.css') }}" rel="stylesheet">

    <div class="elegant-container">
        <div class="row mb-4">
            <div class="col-md-12">
                <h1 class="page-title">Laporan Ticketing</h1>
            </div>
        </div>
        
        <div class="card-elegant">
            <div class="card-body">
                @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
                @endif

                <form action="{{ route('report.print') }}" method="post">
                    @csrf
                    <input type="hidden" name="_method" value="GET" class="form-control">

                    <div class="elegant-form-group">
                        <label for="report_type">Tipe Laporan</label>
                        <select name="report_type" id="report_type" class="elegant-form-control {{ $errors->has('report_type') ? 'is-invalid':'' }}" required>
                            <option value="">Pilih</option>
                            <option value="1" <?php if( old('report_type') == 1 ) echo 'selected=selected'; ?>>Bulanan</option>
                            <option value="2" <?php if( old('report_type') == 2 ) echo 'selected=selected'; ?>>Periode Tanggal</option>
                        </select>
                        <span class="text-danger">{{ $errors->first('report_type') }}</span>
                    </div>

                    <div class="elegant-form-group" id="report_month" style="display: none;">
                        <label for="month">Bulan</label>
                        <select name="month" id="month" class="elegant-form-control {{ $errors->has('month') ? 'is-invalid':'' }}">
                            <option value="">Pilih</option>
                            @foreach ($invoices as $invoice) 
                            <option value="{{ $invoice['monthlydate'] }}">{{ $months[ intval(substr($invoice['monthlydate'], 4, 2)) - 1 ] }} - {{ substr($invoice['monthlydate'], 0, 4) }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger">{{ $errors->first('month') }}</span>
                    </div>

                    <div class="elegant-form-group" id="report_start_date" style="display: none;">
                        <label for="start_date">Tanggal Mulai</label>
                        <input type="date" name="start_date" id="start_date" class="elegant-form-control {{ $errors->has('start_date') ? 'is-invalid':'' }}">
                        <span class="text-danger">{{ $errors->first('start_date') }}</span>
                    </div>

                    <div class="elegant-form-group" id="report_end_date" style="display: none;">
                        <label for="end_date">Tanggal Akhir</label>
                        <input type="date" name="end_date" id="end_date" class="elegant-form-control {{ $errors->has('end_date') ? 'is-invalid':'' }}">
                        <span class="text-danger">{{ $errors->first('end_date') }}</span>
                    </div>

                    <div class="elegant-form-group">
                        <label for="airline_id">Filter Maskapai (opsional)</label>
                        <select name="airline_id" id="airline_id" class="elegant-form-control">
                            <option value="">Semua Maskapai</option>
                            @foreach($airlines as $airline)
                                <option value="{{ $airline->id }}">{{ $airline->airlines_code }} - {{ $airline->airlines_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="elegant-form-group">
                        <label for="customer_id">Filter Pelanggan (opsional)</label>
                        <select name="customer_id" id="customer_id" class="elegant-form-control">
                            <option value="">Semua Pelanggan</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->booker }} - {{ $customer->company }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="elegant-form-group">
                        <button type="submit" class="btn-primary btn-sm">
                            <i class="fa fa-print"></i> Print
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript untuk Report -->
    <script src="{{ asset('js/report.js') }}"></script>
@endsection