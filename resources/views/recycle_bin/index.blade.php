@extends('master')

@section('konten')
<style>
    .recycle-page { color: #1e293b; }
    .recycle-page .page-heading { margin: 0 0 6px; font-size: 24px; font-weight: 700; }
    .recycle-page .page-subtitle { margin: 0 0 22px; color: #64748b; font-size: 13px; }
    .recycle-card { background: #fff; border: 1px solid #e8ecf4; border-radius: 16px; box-shadow: 0 8px 24px rgba(15,23,42,.04); margin-bottom: 20px; }
    .recycle-card__body { padding: 20px; }
    .recycle-filter label { color: #475569; font-size: 12px; font-weight: 600; margin-bottom: 6px; }
    .recycle-filter .form-control { border: 1px solid #dbe2ee; border-radius: 9px; box-shadow: none; height: 40px; }
    .recycle-filter .form-control:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.1); }
    .recycle-actions { display: flex; align-items: flex-end; gap: 8px; padding-bottom: 15px; }
    .recycle-btn { border: 0; border-radius: 9px; display: inline-flex; align-items: center; justify-content: center; font-weight: 600; min-height: 40px; padding: 0 14px; }
    .recycle-btn--primary { background: #4f46e5; color: #fff; }
    .recycle-btn--primary:hover { background: #4338ca; color: #fff; }
    .recycle-btn--reset { background: #f1f5f9; color: #475569; text-decoration: none; }
    .recycle-btn--danger { background: #fee2e2; color: #b91c1c; }
    .recycle-btn--restore { background: #dcfce7; color: #047857; }
    .recycle-btn--delete { background: #fff1f2; color: #be123c; }
    .recycle-table { margin-bottom: 0; }
    .recycle-table thead th { background: #f8fafc; border-bottom: 1px solid #e8ecf4; color: #64748b; font-size: 11px; letter-spacing: .04em; text-transform: uppercase; white-space: nowrap; }
    .recycle-table tbody td { border-color: #eef2f7; color: #334155; font-size: 12px; vertical-align: middle; }
    .recycle-meta { color: #64748b; font-size: 11px; }
    .recycle-type { display: inline-block; border-radius: 999px; padding: 5px 9px; color: #4338ca; background: #eef2ff; font-size: 10px; font-weight: 700; white-space: nowrap; }
    .recycle-item-actions { display: flex; gap: 6px; }
    .recycle-item-actions form { display: inline; }
    .recycle-pagination { padding: 0 18px 14px; }
    .recycle-empty { padding: 42px 15px !important; text-align: center; color: #94a3b8 !important; }
    .recycle-toolbar { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 16px; }
    .recycle-count { color: #64748b; font-size: 12px; }
    @media(max-width:767px) {
        .recycle-card__body { padding: 14px; }
        .recycle-actions { padding-bottom: 15px; }
        .recycle-page .page-heading { font-size: 21px; }
        .recycle-toolbar { align-items: flex-start; flex-direction: column; }
    }
</style>

<div class="recycle-page">
    <h1 class="page-heading"><i class="fa fa-trash" style="color:#4f46e5;"></i> Sampah</h1>
    <p class="page-subtitle">Pulihkan data yang dihapus atau hapus permanen jika sudah tidak diperlukan.</p>

    @if(session('success'))
        <div class="alert alert-success">{!! session('success') !!}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="recycle-card">
        <div class="recycle-card__body">
            <form method="GET" action="{{ route('recycle-bin.index') }}" class="recycle-filter">
                <div class="row">
                    <div class="col-md-3 col-sm-6 form-group">
                        <label for="recycle-search">Kata kunci</label>
                        <input id="recycle-search" type="search" name="search" class="form-control" value="{{ request('search') }}" placeholder="Nama data, ID, atau penghapus">
                    </div>
                    <div class="col-md-3 col-sm-6 form-group">
                        <label for="recycle-type">Jenis data</label>
                        <select id="recycle-type" name="type" class="form-control">
                            <option value="">Semua jenis data</option>
                            @foreach($types as $type)
                                <option value="{{ $type }}" {{ request('type') === $type ? 'selected' : '' }}>{{ $typeLabels[$type] ?? $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-sm-6 form-group">
                        <label for="recycle-user">Dihapus oleh</label>
                        <select id="recycle-user" name="deleted_by" class="form-control">
                            <option value="">Semua akun</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ (string) request('deleted_by') === (string) $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-sm-6 form-group">
                        <label for="recycle-per-page">Baris per halaman</label>
                        <select id="recycle-per-page" name="per_page" class="form-control">
                            @foreach([10, 20, 50, 100] as $size)
                                <option value="{{ $size }}" {{ (int) request('per_page', 20) === $size ? 'selected' : '' }}>{{ $size }} data</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 col-sm-6 form-group">
                        <label for="recycle-start-date">Dihapus dari tanggal</label>
                        <input id="recycle-start-date" type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3 col-sm-6 form-group">
                        <label for="recycle-end-date">Sampai tanggal</label>
                        <input id="recycle-end-date" type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-6 col-sm-12 recycle-actions">
                        <button type="submit" class="recycle-btn recycle-btn--primary"><i class="fa fa-search"></i>&nbsp; Terapkan Filter</button>
                        <a href="{{ route('recycle-bin.index') }}" class="recycle-btn recycle-btn--reset">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="recycle-card">
        <div class="recycle-card__body recycle-toolbar">
            <span class="recycle-count">{{ number_format($entries->total()) }} data di Sampah</span>
            @if($entries->total() > 0)
                <form method="POST" action="{{ route('recycle-bin.empty') }}" onsubmit="return confirm('Semua data di Sampah akan dihapus permanen dan tidak dapat dipulihkan. Lanjutkan?')">
                    @csrf
                    <button type="submit" class="recycle-btn recycle-btn--danger"><i class="fa fa-trash-alt"></i>&nbsp; Kosongkan Sampah</button>
                </form>
            @endif
        </div>
        <div class="table-responsive">
            <table class="table recycle-table">
                <thead>
                    <tr>
                        <th>Waktu dihapus</th>
                        <th>Data</th>
                        <th>Jenis</th>
                        <th>Keterangan</th>
                        <th>Dihapus oleh</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($entries as $entry)
                        <tr>
                            <td style="white-space:nowrap;">
                                <strong>{{ $entry->created_at->format('d-m-Y') }}</strong>
                                <div class="recycle-meta">{{ $entry->created_at->format('H:i:s') }}</div>
                            </td>
                            <td>
                                <strong>{{ $entry->label }}</strong>
                                <div class="recycle-meta">ID #{{ $entry->record_id }}</div>
                            </td>
                            <td><span class="recycle-type">{{ $typeLabels[$entry->resource_type] ?? $entry->resource_type }}</span></td>
                            <td>{{ $entry->description ?: '-' }}</td>
                            <td>
                                <strong>{{ $entry->deleted_by_name ?: 'Tidak diketahui' }}</strong>
                                <div class="recycle-meta">{{ $entry->deleted_by_email ?: '-' }}</div>
                            </td>
                            <td>
                                <div class="recycle-item-actions">
                                    <form method="POST" action="{{ route('recycle-bin.restore', $entry->id) }}">
                                        @csrf
                                        <button type="submit" class="recycle-btn recycle-btn--restore" title="Pulihkan"><i class="fa fa-undo"></i></button>
                                    </form>
                                    <form method="POST" action="{{ route('recycle-bin.destroy', $entry->id) }}" onsubmit="return confirm('Data ini akan dihapus permanen dan tidak dapat dipulihkan. Lanjutkan?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="recycle-btn recycle-btn--delete" title="Hapus permanen"><i class="fa fa-times"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="recycle-empty"><i class="fa fa-trash" style="font-size:24px;display:block;margin-bottom:8px;"></i>Sampah kosong atau tidak ada data yang sesuai filter.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($entries->hasPages())
            <div class="recycle-pagination">{{ $entries->links() }}</div>
        @endif
    </div>
</div>
@endsection
