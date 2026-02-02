@extends('master')

@section('konten')
    <div class="elegant-container">
        <div class="row mb-4">
            <div class="col-md-12">
                <h1 class="page-title">Laporan Hotel</h1>
            </div>
        </div>
        
        <div class="card-elegant">
            <div class="card-body" id="formContainer"> {{-- ID tambahan untuk parent Select2 --}}
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

                    {{-- Month Picker dengan format estetik --}}
                    <div class="elegant-form-group" id="report_month" style="display: none;">
                        <label for="month">Pilih Bulan Laporan</label>
                        <div class="input-with-icon">
                            {{-- Input ini hanya untuk tampilan (Januari - 2026) --}}
                            <input type="text" id="month_picker_display" class="elegant-form-control" placeholder="Pilih Bulan..." readonly style="background: #fff; cursor: pointer;">
                            {{-- Input hidden ini yang mengirim data asli (202601) ke Controller --}}
                            <input type="hidden" name="month" id="month_actual_value">
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

    {{-- CSS tetap di bawah sesuai permintaan --}}
    <style>
        .input-with-icon { position: relative; }
        .icon-inside { position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; }
        
        /* Mengunci agar dropdown Select2 selalu muncul di bawah input */
        .select2-container--default .select2-selection--single {
            height: 40px !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            padding-top: 5px !important;
            position: relative;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 38px !important; }
        .select2-container { width: 100% !important; }
        
        /* Memaksa list dropdown berada di bawah */
        .select2-dropdown {
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
            z-index: 9999 !important;
        }
    </style>

    {{-- ASSETS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    {{-- Script Report Hotel Asli --}}
    <script src="{{ asset('js/report-hotel.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function(){
            // 1. Perbaikan Select2: Menggunakan dropdownParent agar posisi tidak berantakan
            $('.select2').select2({
                placeholder: 'Pilih opsi',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#formContainer') 
            });

            // 2. Perbaikan Flatpickr: Format "Januari - 2026"
            flatpickr("#month_picker_display", {
                disableMobile: "true",
                plugins: [
                    new monthSelectPlugin({
                        shorthand: false,
                        dateFormat: "Ym", // Data asli untuk Controller (202601)
                        altFormat: "F - Y", // Tampilan User (Januari - 2026)
                        theme: "light"
                    })
                ],
                altInput: true, // Mengaktifkan input tampilan
                altInputClass: "elegant-form-control",
                locale: {
                    months: {
                        shorthand: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"],
                        longhand: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"]
                    }
                },
                onChange: function(selectedDates, dateStr) {
                    // Update input hidden saat user memilih bulan
                    $('#month_actual_value').val(dateStr);
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