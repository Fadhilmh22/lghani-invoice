@extends('master')

@section('konten')
<div class="elegant-container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="page-title">Hotel Voucher</h1>
        </div>
    </div>
    
    <div class="card-elegant">
        <div class="card-body">
            @if (session('success'))
            <div class="alert alert-success d-none" data-message="{!! session('success') !!}">
                {{-- Session success akan diambil oleh JS --}}
            </div>
            @endif

            <!-- TOMBOL TAMBAH -->
            <div class="top-bar-controls">
                <div></div>
                <div class="action-group">
                    <a href="{{ url('/hotel-voucher/new') }}" class="btn btn-primary-elegant">
                        <i class="fa fa-plus-circle"></i> Tambah Data Voucher
                    </a>
                </div>
            </div>
            
            <div class="clearfix mb-3"></div>

            <!-- TABEL HOTEL VOUCHER -->
            <div class="table-responsive">
                <table class="table elegant-table">
                    <thead>
                        <tr>
                            <th>Voucher ID</th>
                            <th>Booking ID</th>
                            <th>Booker</th>
                            <th>Nationality</th>
                            <th>Created At</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($hotelVouchers as $voucher)
                        <tr class="table-row-hover">
                            <td><strong>{{ $voucher->voucher_no }}</strong></td>
                            <td>{{ $voucher->booking_no }}</td>
                            <td>{{ $voucher->booker }}</td>
                            <td>{{ $voucher->nationality }}</td>
                            <td>{{ \Carbon\Carbon::parse($voucher->created_at)->format('d-m-Y H:i:s') }}</td>
                            
                            <!-- Kolom Aksi -->
                            <td class="action-buttons text-center">
                                <a href="{{ route('hotelvoucher.print', $voucher->id) }}" class="btn-action print-action" title="Print Voucher">
                                    <i class="fa fa-print"></i>
                                </a>
                                
                                <a href="{{ url('/hotel-voucher/' . $voucher->id) }}" class="btn-action edit-action" title="Edit Voucher">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                
                                <a href="{{ url('/hotel-voucher/room/' . $voucher->id) }}" class="btn-action" style="background-color: #e0e7ff; color: #6366f1;" title="Room">
                                    <i class="fa fa-bed"></i>
                                </a>
                                
                                <form action="{{ url('/hotel-voucher/' . $voucher->id . '/delete') }}" method="POST" id="delete-voucher-form-{{ $voucher->id }}" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn-action delete-action delete-voucher-btn" data-voucher-id="{{ $voucher->id }}" data-voucher-no="{{ $voucher->voucher_no }}" title="Delete Voucher">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center p-4">
                                <i class="fa fa-info-circle"></i> Tidak ada data voucher ditemukan.
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
                        <li class="page-item"><a class="page-link" href="{{ $hotelVouchers->url(1) }}"><i class="fa fa-angle-double-left"></i></a></li>

                        @php
                        $startPage = max(1, $hotelVouchers->currentPage() - 2);
                        $endPage = min($hotelVouchers->lastPage(), $hotelVouchers->currentPage() + 2);
                        @endphp

                        @if ($startPage > 1)
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                        @endif

                        @foreach (range($startPage, $endPage) as $page)
                        @if ($page == $hotelVouchers->currentPage())
                        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                        @else
                        <li class="page-item"><a class="page-link" href="{{ $hotelVouchers->url($page) }}">{{ $page }}</a></li>
                        @endif
                        @endforeach

                        @if ($endPage < $hotelVouchers->lastPage())
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                        @endif

                        <li class="page-item"><a class="page-link" href="{{ $hotelVouchers->url($hotelVouchers->lastPage()) }}"><i class="fa fa-angle-double-right"></i></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL KONFIRMASI HAPUS -->
<div id="deleteVoucherModal" class="custom-modal-overlay" style="display: none;">
    <div class="custom-modal-content">
        <div class="modal-header-danger">
            <i class="fa fa-exclamation-triangle"></i> Konfirmasi Penghapusan
        </div>
        <div class="modal-body">
            Apakah Anda yakin ingin menghapus voucher **<span id="voucherNoPlaceholder"></span>**? Tindakan ini tidak dapat dibatalkan.
        </div>
        <div class="modal-footer">
            <button id="cancelDeleteVoucherBtn" class="btn btn-secondary-modal">Batal</button>
            <button id="confirmDeleteVoucherBtn" class="btn btn-danger-modal">Ya, Hapus</button>
        </div>
    </div>
</div>

<!-- MODAL POP-UP SUKSES -->
<div id="successToast" class="success-toast" style="display: none;">
    <div class="success-toast-content">
        <div class="checkmark-circle">
            <i class="fa fa-check"></i>
        </div>
        <p class="success-text" id="successMessageText">Voucher berhasil dihapus!</p>
    </div>
</div>

<script>
$(document).ready(function() {
    let targetFormId = '';
    
    // Delete Voucher Handler
    $('.delete-voucher-btn').on('click', function(e) {
        e.preventDefault();
        const voucherId = $(this).data('voucher-id');
        const voucherNo = $(this).data('voucher-no');
        
        targetFormId = '#delete-voucher-form-' + voucherId;
        $('#voucherNoPlaceholder').text(voucherNo);
        $('#deleteVoucherModal').fadeIn(200);
    });
    
    $('#cancelDeleteVoucherBtn').on('click', function() {
        $('#deleteVoucherModal').fadeOut(200);
        targetFormId = '';
    });
    
    $('#confirmDeleteVoucherBtn').on('click', function() {
        if (targetFormId) {
            $('#deleteVoucherModal').fadeOut(200, function() {
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
