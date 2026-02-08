@extends('master')

@section('konten')
<div class="elegant-container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="page-title">Manajemen Maskapai</h1>
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
                    <a href="{{ url('/airline/new') }}" class="btn btn-primary-elegant">
                        <i class="fa fa-plus-circle"></i> Tambah Maskapai
                    </a>
                </div>
            </div>
            
            <div class="clearfix mb-3"></div>

            <!-- TABEL AIRLINES -->
            <div class="table-responsive">
                <table class="table elegant-table">
                    <thead>
                        <tr>
                            <th>Kode Maskapai</th>
                            <th>Nama Maskapai</th>
                            <th>Tanggal Pembuatan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($additionalData as $airline)
                        <tr class="table-row-hover">
                            <td><strong>{{ $airline->airlines_code }}</strong></td>
                            <td class="uppercase-text">{{ $airline->airlines_name }}</td>
                            <td>{{ $airline->created_at->format('d-m-Y') }}</td>
                            
                            <!-- Kolom Aksi -->
                            <td class="action-buttons text-center">
                                <a href="{{ url('/airline/' . $airline->id) }}" class="btn-action edit-action" title="Edit Maskapai">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                
                                <form action="{{ url('/airline/' . $airline->id) }}" method="POST" id="delete-airline-form-{{ $airline->id }}" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn-action delete-action delete-airline-btn" data-airline-id="{{ $airline->id }}" data-airline-name="{{ $airline->airlines_name }}" title="Delete Maskapai">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center p-4">
                                <i class="fa fa-info-circle"></i> Tidak ada data maskapai ditemukan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer Tabel (Pagination) -->
            <div class="table-footer-controls">
                <!-- Pagination -->
                <div class="pagination-elegant">
                    <ul class="pagination">
                        <!-- Navigation Links -->
                        <li class="page-item"><a class="page-link"
                                href="{{ $additionalData->url(1) }}"><i class="fa fa-angle-double-left"></i></a></li>

                        @php
                        $startPage = max(1, $additionalData->currentPage() - 2);
                        $endPage = min($additionalData->lastPage(), $additionalData->currentPage() + 2);
                        @endphp

                        @if ($startPage > 1)
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                        @endif

                        @foreach (range($startPage, $endPage) as $page)
                        @if ($page == $additionalData->currentPage())
                        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                        @else
                        <li class="page-item"><a class="page-link"
                                href="{{ $additionalData->url($page) }}">{{ $page }}</a>
                        </li>
                        @endif
                        @endforeach

                        @if ($endPage < $additionalData->lastPage())
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                        @endif

                        <li class="page-item"><a class="page-link"
                                href="{{ $additionalData->url($additionalData->lastPage()) }}"><i class="fa fa-angle-double-right"></i></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL KONFIRMASI HAPUS -->
<div id="deleteAirlineModal" class="custom-modal-overlay" style="display: none;">
    <div class="custom-modal-content">
        <div class="modal-header-danger">
            <i class="fa fa-exclamation-triangle"></i> Konfirmasi Penghapusan
        </div>
        <div class="modal-body">
            Apakah Anda yakin ingin menghapus maskapai **<span id="airlineNamePlaceholder"></span>**? Tindakan ini tidak dapat dibatalkan.
        </div>
        <div class="modal-footer">
            <button id="cancelDeleteAirlineBtn" class="btn btn-secondary-modal">Batal</button>
            <button id="confirmDeleteAirlineBtn" class="btn btn-danger-modal">Ya, Hapus</button>
        </div>
    </div>
</div>

<!-- MODAL POP-UP SUKSES -->
<div id="successToast" class="success-toast" style="display: none;">
    <div class="success-toast-content">
        <div class="checkmark-circle">
            <i class="fa fa-check"></i>
        </div>
        <p class="success-text" id="successMessageText">Maskapai berhasil dihapus!</p>
    </div>
</div>

<script>
$(document).ready(function() {
    let targetFormId = '';
    
    // Delete Airline Handler
    $('.delete-airline-btn').on('click', function(e) {
        e.preventDefault();
        const airlineId = $(this).data('airline-id');
        const airlineName = $(this).data('airline-name');
        
        targetFormId = '#delete-airline-form-' + airlineId;
        $('#airlineNamePlaceholder').text(airlineName);
        $('#deleteAirlineModal').fadeIn(200);
    });
    
    $('#cancelDeleteAirlineBtn').on('click', function() {
        $('#deleteAirlineModal').fadeOut(200);
        targetFormId = '';
    });
    
    $('#confirmDeleteAirlineBtn').on('click', function() {
        if (targetFormId) {
            $('#deleteAirlineModal').fadeOut(200, function() {
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
