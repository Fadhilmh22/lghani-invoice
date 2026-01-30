@extends('master')

@section('konten')
    <div class="elegant-container">
        <div class="row mb-4">
            <div class="col-md-12">
                <h1 class="page-title">Laporan Hotel</h1>
            </div>
        </div>
        
        <div class="card-elegant">
            <div class="card-body">
                <form action="{{ route('report.printhotel') }}" method="post">
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
                        <select name="month" id="month" class="elegant-form-control select2 {{ $errors->has('month') ? 'is-invalid':'' }}">
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
                        <label for="hotel_id">Filter Hotel (opsional)</label>
                        <select name="hotel_id" id="hotel_id" class="elegant-form-control select2">
                            <option value="">Semua Hotel</option>
                            @foreach($hotels ?? [] as $hotel)
                                <option value="{{ $hotel->id }}">{{ $hotel->hotel_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="elegant-form-group">
                        <label for="customer_id">Filter Pelanggan (opsional)</label>
                        <select name="customer_id" id="customer_id" class="elegant-form-control select2">
                            <option value="">Semua Pelanggan</option>
                            @foreach($customers ?? [] as $customer)
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

    <!-- JavaScript untuk Report Hotel -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="{{ asset('js/report-hotel.js') }}"></script>

    <!-- Include Select2 but style it to match native elegant-form-control appearance -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <style>
        /* Make Select2 single selection look like .elegant-form-control */
        .select2-container--default .select2-selection--single {
            height: 40px;
            padding: 8px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-shadow: none;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 24px;
            color: #334155;
            padding-right: 20px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
        }
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79,70,229,0.1);
        }
        .select2-container--default .select2-selection__placeholder {
            color: #334155; /* match elegant-form-control text color */
        }
        .select2-container { width: 100% !important; }
    </style>

    <script type="text/javascript">
        $(document).ready(function(){
            $('#hotel_id, #customer_id').select2({
                placeholder: 'Pilih opsi',
                width: '100%',
                minimumResultsForSearch: 0
            });
        });
    </script>
@endsection
