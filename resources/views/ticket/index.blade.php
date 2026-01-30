@extends('master')

@section('konten')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<div id="loadingOverlay" class="loading-overlay" style="display: none;">
    <div class="loading-content">
        <div class="spinner-elegant"></div>
        <p>Generating PDF Ticket...</p>
    </div>
</div>

<div class="elegant-container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="page-title">Data Issued Ticket</h1>
        </div>
    </div>

    <div class="card-elegant">
        <div class="card-body">
            @if(session('success'))
            <div class="alert alert-success d-none" data-message="{!! session('success') !!}"></div>
            @endif

            <div class="top-bar-controls">
                <div class="filter-search-group">
                    <form action="{{ route('ticket.index') }}" method="GET" class="search-form">
                        <div class="input-group">
                            <input type="text" class="form-control elegant-input" placeholder="Cari PNR atau Booker..." name="search" value="{{ request('search') }}">
                            <button class="btn btn-search" type="submit">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
                
                <div class="action-group">
                    <a href="{{ route('ticket.create') }}" class="btn btn-primary-elegant">
                        <i class="fa fa-plus-circle"></i> Issue Ticket
                    </a>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table elegant-table">
                    <thead>
                        <tr>
                            <th width="30">No</th>
                            <th>Booker</th>
                            <th>No Ticket</th>
                            <th>PNR</th>
                            <th>Maskapai</th>
                            <th>Rute</th>
                            <th class="text-center">Pax</th>
                            <th class="text-right">Total Invoice</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tickets as $key => $t)
                        <tr class="table-row-hover">
                            <td>{{ $tickets->firstItem() + $key }}</td>
                            <td><strong>{{ $t->invoice->customer->booker ?? '-' }}</strong></td>
                            <td style="font-size: 11px; color: #64748b; font-family: 'Courier New', Courier, monospace;">
                                TCKT{{ $t->created_at->format('Ymd') }}{{ str_pad($t->id, 3, '0', STR_PAD_LEFT) }}
                            </td>
                            <td><span class="badge-pnr">{{ $t->booking_code }}</span></td>
                            <td>
                                <div style="font-weight: 600; color: #1e293b;">{{ $t->airline->airlines_name ?? '-' }}</div>
                                <small style="color: #64748b;">{{ $t->flight_out }}</small>
                            </td>
                            <td style="color: #475569;">{{ $t->route_out }}</td>
                            <td class="text-center">
                                <span class="pax-count">
                                    {{ \App\Models\Invoice_detail::where('invoice_id', $t->invoice_id)->count() }}
                                </span>
                            </td>
                            <td class="text-right"><strong class="text-danger">IDR {{ number_format($t->invoice->total ?? 0) }}</strong></td>
                            
                            <td class="action-buttons text-center">
                                <a href="{{ route('ticket.print', $t->id) }}" target="_blank" class="btn-action print-action" title="Print Semua Penumpang">
                                    <i class="fa fa-print"></i>
                                </a>

                                <button type="button" class="btn-action split-action btn-split-print" 
                                        data-id="{{ $t->id }}" 
                                        data-pnr="{{ $t->booking_code }}"
                                        style="background-color: #e0e7ff; color: #4338ca;"
                                        title="Cetak Per Orang (Split)">
                                    <i class="fa fa-users"></i>
                                </button>

                                <a href="{{ route('ticket.edit', $t->id) }}" class="btn-action edit-action" title="Edit">
                                    <i class="fa fa-pencil"></i>
                                </a>

                                <form action="{{ route('ticket.destroy', $t->id) }}" method="POST" style="display:inline;">
                                    @csrf 
                                    @method('DELETE')
                                    <button type="button" class="btn-action delete-action delete-button" data-info="{{ $t->booking_code }}" title="Hapus">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="9" class="text-center p-5 text-muted">Data tidak ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="table-footer-controls">
                <div>
                    Showing {{ $tickets->firstItem() }} to {{ $tickets->lastItem() }} of {{ $tickets->total() }} entries
                </div>
                <div class="pagination-wrapper">
                    {{ $tickets->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<div id="splitModal" class="custom-modal-overlay" style="display: none;">
    <div class="custom-modal-content" style="width: 450px;">
        <div class="modal-header-primary" style="background: #f8fafc; padding: 15px; border-bottom: 1px solid #e2e8f0; font-weight: 600;">
            <i class="fa fa-print"></i> Pilih Penumpang (PNR: <span id="modalPnr"></span>)
        </div>
        <div class="modal-body" id="passengerList" style="max-height: 400px; overflow-y: auto; padding: 10px;">
            </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary-modal" onclick="$('#splitModal').fadeOut(200)">Tutup</button>
        </div>
    </div>
</div>

<div id="deleteConfirmationModal" class="custom-modal-overlay" style="display: none;">
    <div class="custom-modal-content">
        <div class="modal-header-danger"><i class="fa fa-exclamation-triangle"></i> Konfirmasi</div>
        <div class="modal-body">Yakin ingin menghapus tiket PNR <strong id="infoPlaceholder"></strong>?</div>
        <div class="modal-footer">
            <button id="cancelDeleteBtn" class="btn btn-secondary-modal">Batal</button>
            <button id="confirmDeleteBtn" class="btn btn-danger-modal">Hapus</button>
        </div>
    </div>
</div>

<style>
/* Style sama seperti sebelumnya, tambahkan ini untuk modal split */
.modal-header-primary { color: #1e293b; font-size: 15px; }
.pax-item { 
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
    padding: 12px; 
    border-bottom: 1px solid #f1f5f9;
}
.pax-item:last-child { border-bottom: none; }
.btn-print-sm { 
    background: #4338ca; 
    color: white; 
    border: none; 
    padding: 5px 12px; 
    border-radius: 6px; 
    font-size: 11px;
    text-decoration: none;
}

/* CSS UI Elegant tetap dipertahankan */
.elegant-container { max-width: 1400px; margin: 0 auto; padding: 20px; font-family: 'Poppins', sans-serif; }
.card-elegant { background: #fff; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); padding: 20px; }
.page-title { font-size: 22px; font-weight: 600; color: #0f172a; }
.top-bar-controls { display: flex; justify-content: space-between; margin-bottom: 20px; }
.elegant-input { border-radius: 8px 0 0 8px !important; border: 1px solid #e2e8f0; }
.btn-search { background: #1e1b4b; color: #fff; border-radius: 0 8px 8px 0 !important; }
.btn-primary-elegant { background: #10b981; color: #fff; padding: 8px 16px; border-radius: 8px; text-decoration: none; }
.elegant-table { width: 100%; border-spacing: 0 8px; border-collapse: separate; }
.elegant-table th { color: #64748b; font-size: 11px; text-transform: uppercase; padding: 10px; }
.elegant-table td { background: #fff; padding: 15px 10px; vertical-align: middle; border-top: 1px solid #f1f5f9; }
.badge-pnr { background: #e0e7ff; color: #4338ca; padding: 4px 8px; border-radius: 6px; font-weight: 600; }
.action-buttons { display: flex; gap: 5px; justify-content: center; }
.btn-action { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; border: none; transition: 0.2s; }
.print-action { background: #ccfbf1; color: #0d9488; }
.edit-action { background: #fef3c7; color: #d97706; }
.delete-action { background: #fee2e2; color: #dc2626; }
.custom-modal-overlay { position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); display:flex; justify-content:center; align-items:center; z-index:9999; }
.custom-modal-content { background:#fff; border-radius:12px; overflow:hidden; }
.loading-overlay { position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.8); z-index:10000; display:flex; justify-content:center; align-items:center; }
.spinner-elegant { width:40px; height:40px; border:4px solid #f3f3f3; border-top:4px solid #4338ca; border-radius:50%; animation:spin 1s linear infinite; }
@keyframes spin { 0% { transform:rotate(0deg); } 100% { transform:rotate(360deg); } }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Tombol Split Print Klik
    $('.btn-split-print').on('click', function() {
        let ticketId = $(this).data('id');
        let pnr = $(this).data('pnr');
        $('#modalPnr').text(pnr);
        $('#passengerList').html('<p class="text-center">Loading passengers...</p>');
        $('#splitModal').fadeIn(200);

        // Ambil data penumpang via AJAX
        $.get('/ticket/' + ticketId + '/passengers', function(data) {
        let html = '';
        // Kita pastikan data ada dan bentuknya array
        if(Array.isArray(data) && data.length > 0) {
            data.forEach(function(pax) {
                // Cek nama kolom di database, biasanya 'pax_name'
                // Kalau di database Abang nama kolomnya beda, sesuaikan di bawah ini
                let namaPenumpang = pax.name ? pax.name : 'Nama Tidak Terdeteksi';
                let tipePenumpang = pax.genre ? pax.genre : 'Pax';

                html += `
                <div class="pax-item" style="display: flex; justify-content: space-between; align-items: center; padding: 12px; border-bottom: 1px solid #f1f5f9;">
                    <div>
                        <div style="font-weight:600; color:#1e293b; text-transform: uppercase;">${namaPenumpang}</div>
                        <small style="color:#64748b;">${tipePenumpang}</small>
                    </div>
                    <a href="/ticket/print-split/${ticketId}/${pax.id}" target="_blank" class="btn-print-sm" style="background: #4338ca; color: white; padding: 5px 12px; border-radius: 6px; text-decoration: none; font-size: 11px;">
                        <i class="fa fa-print"></i> Cetak
                    </a>
                </div>`;
            });
        } else {
            html = '<p class="text-center" style="padding: 20px;">Data penumpang tidak ditemukan.</p>';
        }
        $('#passengerList').html(html);
    }).fail(function() {
        $('#passengerList').html('<p class="text-center" style="color: red; padding: 20px;">Gagal mengambil data dari server.</p>');
    });
    });

    // Handle Delete
    let formTarget = null;
    $('.delete-button').on('click', function() {
        formTarget = $(this).closest('form');
        $('#infoPlaceholder').text($(this).data('info'));
        $('#deleteConfirmationModal').fadeIn(200);
    });

    $('#cancelDeleteBtn').on('click', function() { $('#deleteConfirmationModal').fadeOut(200); });
    $('#confirmDeleteBtn').on('click', function() { if(formTarget) formTarget.submit(); });

    // Loading effect for print
    $(document).on('click', '.print-action, .btn-print-sm', function() {
        $('#loadingOverlay').fadeIn(200);
        setTimeout(() => { $('#loadingOverlay').fadeOut(300); }, 2500);
    });
});
</script>
@endsection