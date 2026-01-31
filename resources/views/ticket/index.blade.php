@extends('master')

@section('konten')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<div id="loadingOverlay" class="loading-overlay" style="display: none;">
    <div class="loading-content">
        <div class="spinner-elegant"></div>
        <p>Processing...</p>
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
                    <form id="bulkInvoiceForm" action="{{ route('ticket.bulkInvoice') }}" method="POST" style="display: inline-block;">
                        @csrf
                        <div id="bulkCheckboxesContainer"></div>
                        <button type="submit" class="btn btn-merge-elegant">
                            <i class="fa fa-copy"></i> Gabung Invoice
                        </button>
                    </form>

                    <a href="{{ route('ticket.create') }}" class="btn btn-primary-elegant">
                        <i class="fa fa-plus-circle"></i> Issue Ticket
                    </a>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table elegant-table">
                    <thead>
                        <tr>
                            <th width="30"><input type="checkbox" id="selectAll"></th>
                            <th>No Ticket</th>
                            <th>Booker</th>
                            <th>PNR</th>
                            <th>Maskapai</th>
                            <th>Rute</th>
                            <th class="text-center">Pax</th>
                            <th class="text-right">Total Ticket</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tickets as $key => $t)
                        <tr class="table-row-hover">
                            <td>
                                <input type="checkbox" value="{{ $t->id }}" class="ticket-checkbox">
                            </td>
                            <td style="font-size: 11px; color: #64748b; font-family: 'Courier New', Courier, monospace;">
                                TCKT{{ $t->created_at->format('Ymd') }}{{ str_pad($t->id, 3, '0', STR_PAD_LEFT) }}
                                <br>
                                <span style="
                                    display: inline-block; 
                                    margin-top: 4px; 
                                    background-color: #ef4444; 
                                    color: white; 
                                    padding: 2px 8px; 
                                    border-radius: 4px; 
                                    font-size: 10px; 
                                    font-weight: 800; 
                                    letter-spacing: 0.5px;
                                    box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);
                                    ">
                                    INV: {{ $t->invoice_id }}
                                </span>
                            </td>
                            <td><strong>{{ $t->invoice->customer->booker ?? '-' }}</strong></td>
                            <td><span class="badge-pnr">{{ $t->booking_code }}</span></td>
                            <td>
                                <div style="font-weight: 600; color: #1e293b;">{{ $t->airline->airlines_name ?? '-' }}</div>
                                <small style="color: #64748b;">
                                    {{ $t->airline->airlines_code ?? '' }} - {{ $t->flight_out }}
                                </small>                            
                            </td>
                            <td style="color: #475569;">{{ $t->route_out }}</td>
                            <td class="text-center">
                                <span class="badge" style="background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; font-weight: bold;">
                                    {{-- Jika total_pax ada pakai itu, jika tidak coba hitung manual dari relasi details --}}
                                    {{ $t->total_pax ?? $t->details->where('class', '!=', 'BAGASI_ONLY')->count() }} Pax                                </span>
                            </td>
                            <td class="text-right">
                                <strong class="text-primary">IDR {{ number_format($t->total_publish ?? 0) }}</strong>
                            </td>
                            
                            <td class="action-buttons text-center">
                                <a href="{{ route('ticket.print', $t->id) }}" target="_blank" class="btn-action print-action" title="Print E-Ticket">
                                    <i class="fa fa-print"></i>
                                </a>
                                
                                <button type="button" class="btn-action split-action btn-split-print" 
                                        data-id="{{ $t->id }}" 
                                        data-pnr="{{ $t->booking_code }}" 
                                        title="Split Passenger / Print Satuan">
                                    <i class="fa fa-users"></i>
                                </button>

                                <a href="{{ route('ticket.edit', $t->id) }}" class="btn-action edit-action" title="Edit Data">
                                    <i class="fa fa-pencil"></i>
                                </a>

                                <form action="{{ route('ticket.destroy', $t->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn-action delete-action delete-button" 
                                            data-info="{{ $t->booking_code }} - {{ $t->airline->airlines_name ?? '' }}" title="Hapus">
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
                <div>Showing {{ $tickets->firstItem() }} to {{ $tickets->lastItem() }} of {{ $tickets->total() }} entries</div>
                <div class="pagination-wrapper">{{ $tickets->links() }}</div>
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
        <div class="modal-footer" style="padding: 10px; border-top: 1px solid #eee; text-align: right;">
            <button type="button" class="btn-secondary-modal" onclick="$('#splitModal').fadeOut(200)" style="padding: 5px 15px; border-radius: 6px; border: 1px solid #ccc; background: #fff;">Tutup</button>
        </div>
    </div>
</div>

<div id="deleteConfirmationModal" class="custom-modal-overlay" style="display: none;">
    <div class="custom-modal-content" style="max-width: 400px; text-align: center; padding: 20px;">
        <div style="font-size: 50px; color: #ef4444; margin-bottom: 15px;"><i class="fa fa-exclamation-circle"></i></div>
        <h3>Yakin Hapus Data?</h3>
        <p id="infoPlaceholder" style="color: #64748b; margin-bottom: 25px;"></p>
        <div style="display: flex; gap: 10px; justify-content: center;">
            <button type="button" id="cancelDeleteBtn" class="btn-secondary-elegant">Batal</button>
            <button type="button" id="confirmDeleteBtn" class="btn-danger-elegant" style="background: #ef4444; color: #fff; border: none; padding: 8px 20px; border-radius: 8px;">Hapus</button>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Checkbox Select All
    $('#selectAll').on('click', function() {
        $('.ticket-checkbox').prop('checked', this.checked);
    });

    // Validasi Bulk Invoice & Memindahkan Checkbox ke Form
    $('#bulkInvoiceForm').on('submit', function(e) {
        let selected = $('.ticket-checkbox:checked');
        if (selected.length === 0) {
            e.preventDefault();
            alert('Pilih minimal 2 tiket untuk digabungkan!');
        } else {
            $('#bulkCheckboxesContainer').html('');
            selected.each(function() {
                $('#bulkCheckboxesContainer').append('<input type="hidden" name="ticket_ids[]" value="'+$(this).val()+'">');
            });
            $('#loadingOverlay').fadeIn(200);
        }
    });

    // Tombol Split Sesuai Logika Notepad
    $('.btn-split-print').on('click', function() {
        let ticketId = $(this).data('id');
        let pnr = $(this).data('pnr');
        $('#modalPnr').text(pnr);
        $('#passengerList').html('<p class="text-center" style="padding: 20px;">Loading passengers...</p>');
        $('#splitModal').fadeIn(200);

        // Ambil data penumpang via AJAX (Sesuai Notepad)
        $.get('/ticket/' + ticketId + '/passengers', function(data) {
            let html = '';
            if(Array.isArray(data) && data.length > 0) {
                data.forEach(function(pax) {
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

<style>
.elegant-container { max-width: 1400px; margin: 0 auto; padding: 20px; font-family: 'Poppins', sans-serif; }
.card-elegant { background: #fff; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
.page-title { font-size: 22px; font-weight: 600; color: #0f172a; }
.top-bar-controls { display: flex; justify-content: space-between; margin-bottom: 20px; }
.elegant-table { width: 100%; border-collapse: separate; border-spacing: 0; }
.elegant-table thead th { background: #f8fafc; color: #64748b; font-weight: 600; text-transform: uppercase; font-size: 11px; padding: 15px; border-bottom: 1px solid #e2e8f0; }
.elegant-table td { padding: 15px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; background: #fff; }
.badge-pnr { background: #e0e7ff; color: #4338ca; padding: 4px 8px; border-radius: 6px; font-weight: 600; font-size: 12px; }
.btn-action { width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; border: none; margin: 0 2px; transition: all 0.2s; }
.print-action { background: #ccfbf1; color: #0d9488; }
.split-action { background: #e0e7ff; color: #4338ca; }
.edit-action { background: #fef3c7; color: #d97706; }
.delete-action { background: #fee2e2; color: #dc2626; }
.custom-modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; justify-content: center; }
.custom-modal-content { background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
.loading-overlay { position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.8); z-index:10000; display:flex; justify-content:center; align-items:center; }
.spinner-elegant { width:40px; height:40px; border:4px solid #f3f3f3; border-top:4px solid #4338ca; border-radius:50%; animation:spin 1s linear infinite; }
@keyframes spin { 0% { transform:rotate(0deg); } 100% { transform:rotate(360deg); } }
.pagination-wrapper .pagination { margin-bottom: 0; }
</style>
@endsection