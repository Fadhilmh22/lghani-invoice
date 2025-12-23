@extends('master')

@section('konten')

@php
    // =====================================================================
    // LOGIKA OTOMATISASI STATUS INVOICE HOTEL LAMA
    // PENTING: Logika ini (Update database di View) melanggar prinsip MVC, 
    // tetapi diimplementasikan di sini untuk memenuhi permintaan otomatisasi 
    // agar update terjadi saat halaman diakses. Sebaiknya, logika ini 
    // dipindahkan ke Scheduled Task (Cron Job) di Controller/Command.
    // =====================================================================
    
    // Pastikan kita bisa mengakses Carbon dan Model Hotel_invoice
    try {
        $carbon = new \Carbon\Carbon;
        $hotelInvoiceModel = new \App\Models\Hotel_invoice; 
    
        // Tentukan tanggal batas (14 hari yang lalu)
        $limitDate = $carbon->now()->subDays(14);
    
        // 1. Cari ID invoice hotel yang 'Belum Lunas' dan dibuat lebih dari 14 hari yang lalu
        $idsToUpdate = $hotelInvoiceModel::where('status_pembayaran', 'Belum Lunas')
            ->where('created_at', '<', $limitDate)
            ->pluck('id');
    
        if ($idsToUpdate->isNotEmpty()) {
            // 2. Lakukan update massal di database
            $updatedCount = $hotelInvoiceModel::whereIn('id', $idsToUpdate)
                ->update(['status_pembayaran' => 'Sudah Lunas']);
            
            // 3. Berikan notifikasi flash (session info)
            if ($updatedCount > 0) {
                 session()->flash('info', 'Status ' . $updatedCount . ' invoice hotel lama (lebih dari 14 hari) telah diperbarui secara otomatis menjadi "Sudah Lunas".');
            }
        }
    } catch (\Throwable $e) {
        // Tangani jika Model atau Carbon tidak dapat diakses (misalnya di lingkungan testing/simulasi)
        // echo "<script>console.error('Error auto-update status: " . $e->getMessage() . "');</script>";
    }
@endphp

<div class="elegant-container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="page-title">Invoice Hotel</h1>
        </div>
    </div>
    
    <div class="card-elegant">
        <div class="card-body">
            {{-- Notifikasi Info (Untuk Status Update Otomatis) --}}
            @if (session('info'))
            <div class="alert alert-info alert-info-custom">
                <i class="fa fa-info-circle"></i> {{ session('info') }}
            </div>
            @endif
            
            {{-- Bagian session success/alert yang sudah ada, ini akan memicu pop-up kustom --}}
            @if (session('success'))
            <div class="alert alert-success d-none" data-message="{!! session('success') !!}">
                {{-- Session success tidak perlu ditampilkan di sini, akan diambil oleh JS --}}
            </div>
            @endif

            <!-- FILTER & TOMBOL BUAT INVOICE -->
            <div class="top-bar-controls">
                <div class="filter-search-group">
                    <form action="{{ route('hotelinvoice.index') }}" method="GET" class="search-form">
                        <div class="input-group">
                            <input type="text" class="form-control elegant-input" placeholder="Cari Nama Booker" name="search" value="{{ request('search') }}">
                            <button class="btn btn-search" type="submit" title="Search Invoice">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
                
                <div class="action-group">
                    <a href="{{ url('/hotel-invoice/new') }}" class="btn btn-primary-elegant">
                        <i class="fa fa-plus-circle"></i> Tambah Data Invoice
                    </a>
                </div>
            </div>
            
            <div class="clearfix mb-3"></div>

            <!-- TABEL INVOICE -->
            <div class="table-responsive">
                <table class="table elegant-table">
                    <thead>
                        <tr>
                            <th>Invoice ID</th>
                            <th>Voucher No</th>
                            <th>Issued Date</th>
                            <th>Due Date Payment</th>
                            <th>Booking By</th>
                            <th>Issued By</th>
                            <th class="text-center">Action</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                        @php
                            $statusClass = $invoice->status_pembayaran === 'Sudah Lunas' ? 'status-paid' : 'status-unpaid';
                            $statusIcon = $invoice->status_pembayaran === 'Sudah Lunas' ? 'fa-check-circle' : 'fa-times-circle';
                        @endphp
                        
                        <tr class="table-row-hover">
                            <td><strong>{{ $invoice->invoiceno }}</strong></td>
                            <td>{{ $invoice->voucherno ?: '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($invoice->created_at)->format('d-m-Y H:i:s') }}</td>
                            <td>{{ $invoice->hotel_due_date ? \Carbon\Carbon::parse($invoice->hotel_due_date)->format('d-m-Y') : '-' }}</td>
                            <td class="uppercase-text">
                                {{ !empty($invoice->customer) ? $invoice->customer->gender . '. ' . $invoice->customer->booker : '-' }}
                            </td>
                            <td>{{ $invoice->issued_by }}</td>
                            
                            <!-- Kolom Aksi -->
                            <td class="action-buttons text-center">
                                <form action="{{ url('/hotel-invoice/' . $invoice->id . '/delete') }}" method="POST" id="delete-form-{{ $invoice->id }}" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    
                                    @if(empty($invoice->voucherno))
                                    <a href="javascript:void(0)" class="btn-action print-action" title="Print (Voucher belum dibuat)" style="opacity: 0.5; cursor: not-allowed;">
                                        <i class="fa fa-print"></i>
                                    </a>
                                    <a href="javascript:void(0)" class="btn-action disc-action" title="Disc (Voucher belum dibuat)" style="opacity: 0.5; cursor: not-allowed;">
                                        <i class="fa fa-tag"></i>
                                    </a>
                                    <a href="{{ url('/hotel-voucher/new?bid=' . $invoice->id) }}" class="btn-action" style="background-color: #e0e7ff; color: #6366f1;" title="Buat Voucher">
                                        <i class="fa fa-gift"></i>
                                    </a>
                                    @else
                                    <a href="{{ route('hotelinvoice.print', $invoice->id) }}" class="btn-action print-action" title="Print Invoice">
                                        <i class="fa fa-print"></i>
                                    </a>
                                    <a href="{{ route('hotelinvoice.printdisc', $invoice->id) }}" class="btn-action disc-action" title="Discount">
                                        <i class="fa fa-tag"></i>
                                    </a>
                                    <a href="{{ url('/hotel-voucher/invoice/' . $invoice->id . '/print') }}" class="btn-action" style="background-color: #e0e7ff; color: #6366f1;" title="Print Voucher">
                                        <i class="fa fa-gift"></i>
                                    </a>
                                    @endif
                                    
                                    <a href="{{ url('/hotel-invoice/' . $invoice->id) }}" class="btn-action edit-action" title="Edit Invoice">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    
                                    <button type="button" class="btn-action delete-action delete-hotel-invoice-btn" data-invoice-id="{{ $invoice->id }}" data-invoice-no="{{ $invoice->invoiceno }}" title="Delete Invoice">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                            
                            <!-- Kolom Status -->
                            <td class="text-center">
                                <form method="POST" action="{{ route('hotelinvoice.ubah-status', $invoice->id) }}">
                                    @csrf
                                    @method('POST')
                                    <input type="hidden" name="current_page" value="{{ $invoices->currentPage() }}">
                                    <button type="button" class="toggleStatus status-badge {{ $statusClass }}" data-invoice-id="{{ $invoice->id }}">
                                        <i class="fa {{ $statusIcon }}"></i> {{ $invoice->status_pembayaran }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center p-4">
                                <i class="fa fa-info-circle"></i> Tidak ada data invoice ditemukan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer Tabel (Pagination) -->
            <div class="table-footer-controls">
                <div></div>
                
                <!-- Pagination -->
                <div class="pagination-elegant">
                    <ul class="pagination">
                        <li class="page-item"><a class="page-link" href="{{ $invoices->url(1) }}"><i class="fa fa-angle-double-left"></i></a></li>

                        @php
                        $startPage = max(1, $invoices->currentPage() - 2);
                        $endPage = min($invoices->lastPage(), $invoices->currentPage() + 2);
                        @endphp

                        @if ($startPage > 1)
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                        @endif

                        @foreach (range($startPage, $endPage) as $page)
                        @if ($page == $invoices->currentPage())
                        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                        @else
                        <li class="page-item"><a class="page-link" href="{{ $invoices->appends(['search' => request('search')])->url($page) }}">{{ $page }}</a></li>
                        @endif
                        @endforeach

                        @if ($endPage < $invoices->lastPage())
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                        @endif

                        <li class="page-item"><a class="page-link" href="{{ $invoices->url($invoices->lastPage()) }}"><i class="fa fa-angle-double-right"></i></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL KONFIRMASI HAPUS KUSTOM -->
<div id="deleteConfirmationModal" class="custom-modal-overlay" style="display: none;">
    <div class="custom-modal-content">
        <div class="modal-header-danger">
            <i class="fa fa-exclamation-triangle"></i> Konfirmasi Penghapusan
        </div>
        <div class="modal-body">
            Apakah Anda yakin ingin menghapus Invoice **<span id="invoiceNoPlaceholder"></span>**? Tindakan ini tidak dapat dibatalkan.
        </div>
        <div class="modal-footer">
            <button id="cancelDeleteBtn" class="btn btn-secondary-modal">Batal</button>
            <button id="confirmDeleteBtn" class="btn btn-danger-modal">Ya, Hapus</button>
        </div>
    </div>
</div>

<!-- MODAL POP-UP SUKSES KUSTOM -->
<div id="successToast" class="success-toast" style="display: none;">
    <div class="success-toast-content">
        <div class="checkmark-circle">
            <i class="fa fa-check"></i>
        </div>
        <p class="success-text" id="successMessageText">Invoice berhasil dihapus!</p>
    </div>
</div>

<!-- JavaScript untuk Hotel Invoice -->
<script src="{{ asset('js/hotelinvoice.js') }}"></script>

<script>
// Modal Delete Handler untuk Hotel Invoice
$(document).ready(function() {
    let targetFormId = '';
    
    $('.delete-hotel-invoice-btn').on('click', function(e) {
        e.preventDefault();
        const invoiceId = $(this).data('invoice-id');
        const invoiceNo = $(this).data('invoice-no');
        
        targetFormId = '#delete-form-' + invoiceId;
        $('#invoiceNoPlaceholder').text(invoiceNo);
        $('#deleteConfirmationModal').fadeIn(200);
    });
    
    $('#cancelDeleteBtn').on('click', function() {
        $('#deleteConfirmationModal').fadeOut(200);
        targetFormId = '';
    });
    
    $('#confirmDeleteBtn').on('click', function() {
        if (targetFormId) {
            $('#deleteConfirmationModal').fadeOut(200, function() {
                $(targetFormId).submit();
            });
        }
    });
    
    // Success Toast
    const successAlert = $('.alert-success[data-message]');
    if (successAlert.length) {
        const message = successAlert.data('message');
        $('#successMessageText').html(message);
        const toast = $('#successToast');
        toast.fadeIn(300).css('display', 'flex');
        setTimeout(function() {
            toast.fadeOut(500);
        }, 3000);
    }
});
</script>

@endsection
