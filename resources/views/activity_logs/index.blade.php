@extends('master')

@section('konten')
<style>
    .activity-page { color: #1e293b; }
    .activity-page .page-heading { margin: 0 0 6px; font-size: 24px; font-weight: 700; }
    .activity-page .page-subtitle { margin: 0 0 22px; color: #64748b; font-size: 13px; }
    .activity-card { background: #fff; border: 1px solid #e8ecf4; border-radius: 16px; box-shadow: 0 8px 24px rgba(15,23,42,.04); margin-bottom: 20px; }
    .activity-card__body { padding: 20px; }
    .activity-filter label { color: #475569; font-size: 12px; font-weight: 600; margin-bottom: 6px; }
    .activity-filter .form-control { border: 1px solid #dbe2ee; border-radius: 9px; box-shadow: none; height: 40px; }
    .activity-filter .form-control:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.1); }
    .filter-actions { display: flex; align-items: flex-end; gap: 8px; padding-bottom: 15px; }
    .activity-btn { border: 0; border-radius: 9px; font-weight: 600; height: 40px; padding: 0 15px; }
    .activity-btn--primary { background: #4f46e5; color: #fff; }
    .activity-btn--primary:hover { background: #4338ca; color: #fff; }
    .activity-btn--reset { display: inline-flex; align-items: center; justify-content: center; background: #f1f5f9; color: #475569; text-decoration: none; }
    .activity-table { margin-bottom: 0; }
    .activity-table thead th { background: #f8fafc; border-bottom: 1px solid #e8ecf4; color: #64748b; font-size: 11px; letter-spacing: .04em; text-transform: uppercase; white-space: nowrap; }
    .activity-table tbody td { border-color: #eef2f7; color: #334155; font-size: 12px; vertical-align: middle; }
    .activity-table .activity-user { font-weight: 600; color: #1e293b; }
    .activity-table .activity-email,.activity-table .activity-meta { color: #64748b; font-size: 11px; }
    .activity-badge { display: inline-block; padding: 5px 9px; border-radius: 999px; font-size: 10px; font-weight: 700; white-space: nowrap; }
    .activity-badge--login_success { color: #047857; background: #d1fae5; }
    .activity-badge--login_failed { color: #b91c1c; background: #fee2e2; }
    .activity-badge--create { color: #1d4ed8; background: #dbeafe; }
    .activity-badge--edit { color: #b45309; background: #fef3c7; }
    .activity-badge--delete { color: #be123c; background: #ffe4e6; }
    .activity-badge--logout { color: #475569; background: #e2e8f0; }
    .activity-pagination { padding: 0 18px 14px; }
    .activity-empty { padding: 42px 15px !important; text-align: center; color: #94a3b8 !important; }
    @media(max-width:767px) {
        .activity-card__body { padding: 14px; }
        .filter-actions { margin-top: 0; padding-bottom: 15px; }
        .activity-page .page-heading { font-size: 21px; }
    }
</style>

<div class="activity-page">
    <h1 class="page-heading"><i class="fa fa-history" style="color:#4f46e5;"></i> Activity Logs</h1>
    <p class="page-subtitle">Riwayat login dan perubahan data dari seluruh akun.</p>

    <div class="activity-card">
        <div class="activity-card__body">
            @if($errors->has('end_date'))
                <div class="alert alert-danger">{{ $errors->first('end_date') }}</div>
            @endif
            <form method="GET" action="{{ route('activity-logs.index') }}" class="activity-filter">
                <div class="row">
                    <div class="col-md-3 col-sm-6 form-group">
                        <label for="activity-search">Kata kunci</label>
                        <input id="activity-search" type="search" name="search" class="form-control" value="{{ request('search') }}" placeholder="Nama, email, aktivitas, ID, atau IP">
                    </div>
                    <div class="col-md-3 col-sm-6 form-group">
                        <label for="activity-user">Akun</label>
                        <select id="activity-user" name="user_id" class="form-control">
                            <option value="">Semua akun</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ (string) request('user_id') === (string) $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-sm-6 form-group">
                        <label for="activity-action">Jenis aktivitas</label>
                        <select id="activity-action" name="action" class="form-control">
                            <option value="">Semua aktivitas</option>
                            @foreach(['login_success' => 'Login berhasil', 'login_failed' => 'Gagal login', 'create' => 'Tambah data', 'edit' => 'Edit data', 'delete' => 'Hapus data', 'logout' => 'Logout'] as $value => $label)
                                <option value="{{ $value }}" {{ request('action') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-sm-6 form-group">
                        <label for="activity-per-page">Baris per halaman</label>
                        <select id="activity-per-page" name="per_page" class="form-control">
                            @foreach([10, 20, 50, 100] as $size)
                                <option value="{{ $size }}" {{ (int) request('per_page', 20) === $size ? 'selected' : '' }}>{{ $size }} aktivitas</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 col-sm-6 form-group">
                        <label for="activity-start-date">Dari tanggal</label>
                        <input id="activity-start-date" type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3 col-sm-6 form-group">
                        <label for="activity-end-date">Sampai tanggal</label>
                        <input id="activity-end-date" type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-6 col-sm-12 filter-actions">
                        <button type="submit" class="activity-btn activity-btn--primary"><i class="fa fa-search"></i> Terapkan Filter</button>
                        <a href="{{ route('activity-logs.index') }}" class="activity-btn activity-btn--reset">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="activity-card">
        <div class="table-responsive">
            <table class="table activity-table">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Akun</th>
                        <th>Aktivitas</th>
                        <th>Detail</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td style="white-space:nowrap;">
                                <strong>{{ $log->created_at->format('d-m-Y') }}</strong>
                                <div class="activity-meta">{{ $log->created_at->format('H:i:s') }}</div>
                            </td>
                            <td>
                                <div class="activity-user">{{ $log->user_name ?: 'Tidak diketahui' }}</div>
                                <div class="activity-email">{{ $log->user_email ?: '-' }}</div>
                            </td>
                            <td><span class="activity-badge activity-badge--{{ $log->action }}">{{ ['login_success' => 'Login berhasil', 'login_failed' => 'Gagal login', 'create' => 'Tambah', 'edit' => 'Edit', 'delete' => 'Hapus', 'logout' => 'Logout'][$log->action] ?? $log->action }}</span></td>
                            <td>
                                <div>{{ $log->description }}</div>
                                @if($log->subject_type)
                                    <div class="activity-meta">{{ $log->subject_type }}{{ $log->subject_id ? ' #' . $log->subject_id : '' }}</div>
                                @endif
                            </td>
                            <td>{{ $log->ip_address ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="activity-empty"><i class="fa fa-search" style="font-size:24px;display:block;margin-bottom:8px;"></i>Tidak ada aktivitas yang cocok dengan filter.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="activity-pagination">{{ $logs->links() }}</div>
        @endif
    </div>
</div>
@endsection
