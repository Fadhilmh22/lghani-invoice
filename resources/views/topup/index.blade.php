@extends('master')

@section('konten')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    /* CSS TOTAL REPAIR - AGAR TIDAK TERGANTUNG EXTERNAL */
    .elegant-container { padding: 20px; font-family: 'Poppins', sans-serif; background: #f8fafc; min-height: 100vh; }
    .page-title { font-weight: 800; color: #0f172a; font-size: 28px; margin-bottom: 5px; }
    .text-muted { color: #64748b !important; }
    
    .card-elegant { 
        background: #fff; 
        border-radius: 16px; 
        border: none; 
        box-shadow: 0 4px 20px rgba(0,0,0,0.05); 
        margin-bottom: 30px;
        overflow: visible; 
    }
    .card-body { padding: 30px !important; }

    /* Form Alignment Fix */
    .row-flex-end { 
        display: flex !important; 
        align-items: flex-end !important; 
        flex-wrap: wrap;
    }
    .form-group-custom { margin-bottom: 0 !important; width: 100%; }
    .label-custom { 
        font-weight: 700; 
        color: #475569; 
        font-size: 12px; 
        text-transform: uppercase; 
        margin-bottom: 8px; 
        display: block; 
    }

    /* Input & Select2 Styling */
    .input-custom, .filter-input { 
        height: 48px !important; 
        border-radius: 12px !important; 
        border: 2px solid #e2e8f0 !important; 
        font-weight: 600 !important; 
        box-shadow: none !important;
        background-color: #fff !important; /* Agar putih bersih */
    }
    .input-group-custom { position: relative; display: flex; align-items: center; width: 100%; }
    .prefix-icon { 
        position: absolute; 
        left: 15px; 
        font-weight: 700; 
        color: #94a3b8; 
        z-index: 10; 
    }
    .input-with-prefix { padding-left: 45px !important; }

    /* Button Styling */
    .btn-topup { 
        height: 48px; 
        background: #4f46e5 !important; 
        color: #fff !important; 
        border: none; 
        border-radius: 12px; 
        font-weight: 600; 
        width: 100%;
        transition: 0.3s;
    }
    .btn-search-custom { 
        height: 48px; 
        background: #1e293b !important; 
        color: #fff !important; 
        border: none; 
        border-radius: 12px; 
        font-weight: 600; 
        width: 100%;
    }

    /* Table Styling */
    .elegant-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .elegant-table thead th { 
        background: #f8fafc; 
        color: #64748b; 
        font-weight: 700; 
        font-size: 11px; 
        text-transform: uppercase; 
        padding: 15px;
        border-bottom: 2px solid #edf2f7;
    }
    .elegant-table tbody td { 
        padding: 15px; 
        vertical-align: middle; 
        border-bottom: 1px solid #f1f5f9;
        font-size: 13px;
    }

    /* Badge & Info */
    .airline-info { display: flex; align-items: center; }
    .airline-logo-box { 
        width: 38px; height: 38px; 
        background: #eef2ff; color: #4f46e5; 
        border-radius: 10px; display: flex; 
        align-items: center; justify-content: center; 
        font-weight: 800; margin-right: 12px; 
    }
    .bg-lion-red { background: #fee2e2 !important; color: #dc2626 !important; }
    .user-pill { 
        background: #f1f5f9; color: #475569; 
        padding: 6px 12px; border-radius: 20px; 
        font-size: 11px; font-weight: 600; 
    }

    /* Pagination (Fix Sejajar Kanan) */
    .table-footer-controls { 
        display: flex; 
        justify-content: flex-end; 
        padding: 20px; 
        border-top: 1px solid #f1f5f9; 
    }
    .pagination > .active > span { background-color: #4f46e5 !important; border-color: #4f46e5 !important; }
    
    /* Toast Notification */
    .toast-custom { 
        position: fixed; top: 20px; right: 20px; 
        background: #fff; border-left: 5px solid #10b981; 
        padding: 15px 25px; border-radius: 12px; 
        box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
        display: flex; align-items: center; gap: 15px; z-index: 9999;
    }

    /* Fix Select2 Height */
    .select2-container--default .select2-selection--single {
        height: 48px !important;
        border: 2px solid #e2e8f0 !important;
        border-radius: 12px !important;
        line-height: 48px !important;
        padding-top: 10px !important;
    }
</style>

<div class="elegant-container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="page-title">Top Up Saldo Maskapai</h1>
            <p class="text-muted">Kelola pengisian saldo deposit untuk setiap maskapai</p>
        </div>
    </div>

    <div class="card-elegant">
        <div class="card-body">
            <form action="{{ route('topup.store') }}" method="POST">
                @csrf
                <div class="row row-flex-end">
                    <div class="col-md-5">
                        <div class="form-group-custom">
                            <label class="label-custom">Pilih Maskapai</label>
                            <select name="airline_id" class="form-control select2-custom" required>
                                <option value="" disabled selected>Cari maskapai...</option>
                                @foreach($airlines as $airline)
                                    <option value="{{ $airline->id }}">
                                        {{ $airline->airlines_name }} ({{ $airline->airlines_code }}) - Saldo: Rp {{ number_format($airline->balance) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group-custom">
                            <label class="label-custom">Nominal Saldo</label>
                            <div class="input-group-custom">
                                <span class="prefix-icon">Rp</span>
                                <input type="text" name="amount" id="amount" class="form-control input-custom input-with-prefix" placeholder="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn-topup">
                            <i class="fa fa-plus-circle"></i> Proses Saldo
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card-elegant" style="background: #fdfdfd; border: 1px dashed #e2e8f0;">
        <div class="card-body" style="padding: 20px !important;">
            <form action="{{ route('topup.index') }}" method="GET">
                <div class="row row-flex-end">
                    <div class="col-md-3">
                        <label class="label-custom">Maskapai</label>
                        <select name="airline_id" class="form-control select2-custom">
                            <option value="">-- Semua --</option>
                            @foreach($airlines as $airline)
                                <option value="{{ $airline->id }}" {{ request('airline_id') == $airline->id ? 'selected' : '' }}>
                                    {{ $airline->airlines_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="label-custom">Dari Tanggal</label>
                        <input type="text" name="start_date" id="start_date" class="form-control input-custom datepicker" value="{{ request('start_date') }}" placeholder="Pilih Tanggal">
                    </div>
                    <div class="col-md-3">
                        <label class="label-custom">Sampai Tanggal</label>
                        <input type="text" name="end_date" id="end_date" class="form-control input-custom datepicker" value="{{ request('end_date') }}" placeholder="Pilih Tanggal">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn-search-custom">
                            <i class="fa fa-search"></i> Cari Riwayat
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card-elegant">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table elegant-table">
                    <thead>
                        <tr>
                            <th>Waktu Transaksi</th>
                            <th>Maskapai</th>
                            <th>Nominal</th>
                            <th>Saldo Sebelum</th>
                            <th>Saldo Sesudah</th>
                            <th>Oleh User</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topups as $t)
                        <tr>
                            <td>
                                <div style="line-height: 1.2;">
                                    <div style="font-weight: 700; color: #1e293b;">{{ $t->created_at->format('d M Y') }}</div>
                                    <small class="text-muted">{{ $t->created_at->format('H:i') }} WIB</small>
                                </div>
                            </td>
                            <td>
                                <div class="airline-info">
                                    <div class="airline-logo-box {{ in_array(strtoupper($t->airline->airlines_code ?? ''), ['JT','ID','IW','IU']) ? 'bg-lion-red' : '' }}">
                                        {{ strtoupper(substr($t->airline->airlines_code ?? '??', 0, 2)) }}
                                    </div>
                                    <span style="font-weight: 600;">{{ $t->airline->airlines_name ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="{{ $t->amount < 0 ? 'text-danger' : 'text-success' }}" style="font-weight: 800;">
                                {{ $t->amount < 0 ? '-' : '+' }} Rp {{ number_format(abs($t->amount)) }}
                            </td>
                            <td class="text-muted">Rp {{ number_format($t->before_balance) }}</td>
                            <td style="font-weight: 700; color: #0f172a;">Rp {{ number_format($t->after_balance) }}</td>
                            <td>
                                <span class="user-pill"><i class="fa fa-user-circle"></i> {{ $t->user->name ?? 'System' }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center" style="padding: 50px; color: #94a3b8;">Belum ada data transaksi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="table-footer-controls">
                {!! $topups->appends(request()->input())->links() !!}
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<div id="successToast" class="toast-custom">
    <div style="width: 40px; height: 40px; background: #d1fae5; color: #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
        <i class="fa fa-check"></i>
    </div>
    <div>
        <h5 style="margin: 0; font-weight: 700; color: #1e293b;">Berhasil!</h5>
        <p style="margin: 0; font-size: 13px; color: #64748b;">{{ session('success') }}</p>
    </div>
    <button onclick="$('#successToast').fadeOut(200)" style="background: none; border: none; color: #94a3b8; margin-left: 10px;"><i class="fa fa-times"></i></button>
</div>
@endif

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    $(document).ready(function() {
        // Inisialisasi Select2
        $('.select2-custom').select2({ width: '100%' });
        
        // Inisialisasi Flatpickr
        $(".datepicker").flatpickr({
            dateFormat: "Y-m-d",
            allowInput: true
        });

        // Format Rupiah
        $('#amount').on('keyup', function() {
            let val = $(this).val().replace(/[^0-9-]/g, '');
            if (val !== "") $(this).val(new Intl.NumberFormat('id-ID').format(val));
        });

        // Success Toast
        if($('#successToast').length) {
            setTimeout(() => { $('#successToast').fadeOut(500); }, 4000);
        }
    });
</script>
@endsection