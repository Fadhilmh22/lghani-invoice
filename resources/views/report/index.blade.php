@extends('master')

@section('konten')
    <!-- Elegant UI CSS -->
    <link href="{{ asset('css/elegant-ui.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>


    <style>
    .select2-container--default .select2-selection--single {
        height: 45px; /* Sesuaikan dengan tinggi input Abang */
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding-top: 8px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 43px;
    }

        #month_picker {
        background-color: #fff !important;
        cursor: pointer;
    }
    .input-with-icon {
        position: relative;
    }
    .icon-inside {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        pointer-events: none;
    }
    </style>

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
                        <label for="month">Pilih Bulan Laporan</label>
                        <div class="input-with-icon">
                            <input type="text" name="month" id="month_picker" class="elegant-form-control" placeholder="Klik untuk pilih bulan..." readonly>
                            <i class="fa fa-calendar-alt icon-inside"></i>
                        </div>
                        <span class="text-danger">{{ $errors->first('month') }}</span>
                    </div>

                    <div class="elegant-form-group" id="report_start_date" style="display: none;">
                        <label for="start_date">Tanggal Mulai</label>
                        <input type="text" name="start_date" id="start_date" class="elegant-form-control {{ $errors->has('start_date') ? 'is-invalid':'' }}">
                        <span class="text-danger">{{ $errors->first('start_date') }}</span>
                    </div>

                    <div class="elegant-form-group" id="report_end_date" style="display: none;">
                        <label for="end_date">Tanggal Akhir</label>
                        <input type="text" name="end_date" id="end_date" class="elegant-form-control {{ $errors->has('end_date') ? 'is-invalid':'' }}">
                        <span class="text-danger">{{ $errors->first('end_date') }}</span>
                    </div>

                    <div class="elegant-form-group">
                        <label for="airline_id">Filter Maskapai (opsional)</label>
                        {{-- Tambahkan class 'select2' di sini --}}
                        <select name="airline_id" id="airline_id" class="elegant-form-control select2">
                            <option value="">Semua Maskapai</option>
                            @foreach($airlines as $airline)
                                <option value="{{ $airline->id }}" {{ request('airline_id') == $airline->id ? 'selected' : '' }}>
                                    {{ $airline->airlines_code }} - {{ $airline->airlines_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="elegant-form-group">
                        <label for="customer_id">Filter Pelanggan (opsional)</label>
                        <select name="customer_id" id="customer_id" class="elegant-form-control select2">
                            <option value="">Semua Pelanggan</option>
                            @foreach($customers ?? [] as $customer)
                                <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->booker }} - {{ $customer->company }}
                                </option>
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
    <script src="{{ asset('js/report.js') }}">
    </script>

<script>
    $(document).ready(function() {
        // Pastikan JQuery sudah loading sebelum ini
        $('.select2').select2({
            placeholder: "Pilih...",
            allowClear: true,
            width: '100%' 
        });
    });


    $(document).ready(function() {
        // Inisialisasi Select2 yang sudah ada
        $('.select2').select2({
            placeholder: "Pilih...",
            allowClear: true,
            width: '100%'
        });

        // Inisialisasi Month Picker (Bulanan)
        flatpickr("#month_picker", {
            disableMobile: "true",
            plugins: [
                new monthSelectPlugin({
                    shorthand: false, // Set false agar nama bulan lengkap (Januari)
                    dateFormat: "Ym", // Nilai asli yang dikirim ke database (202601)
                    altFormat: "F - Y", // Tampilan yang dilihat user (Januari - 2026)
                    theme: "light" 
                })
            ],
            altInput: true, // Membuat input bayangan untuk tampilan estetik
            altInputClass: "elegant-form-control", // Menyamakan style dengan input asli
            // Kita paksa menggunakan bahasa Indonesia untuk nama bulan
            locale: {
                months: {
                    shorthand: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"],
                    longhand: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"]
                }
            }
        });

        // Inisialisasi Date Picker (Periode Tanggal)
        flatpickr('#start_date', {
            dateFormat: 'Y-m-d'
        });
        flatpickr('#end_date', {
            dateFormat: 'Y-m-d'
        });
    });
</script>
@endsection