@extends('master')

@section('konten')
<div class="elegant-container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="page-title">List Penumpang</h1>
        </div>
    </div>
    
    <div class="card-elegant">
        <div class="card-body">
            @if (session('success'))
            <div class="alert alert-success d-none" data-message="{!! session('success') !!}">
                {{-- Session success akan diambil oleh JS --}}
            </div>
            @endif
            
            @if (session('error'))
            <div class="alert alert-danger">
                {!! session('error') !!}
            </div>
            @endif

            <!-- FILTER & TOMBOL TAMBAH -->
            <div class="top-bar-controls">
                <div class="filter-search-group">
                    <form action="{{ url('/passenger') }}" method="GET" class="search-form">
                        <div class="input-group">
                            <input type="text" class="form-control elegant-input" placeholder="Cari nama penumpang" name="search" value="{{ request('search') }}">
                            <button class="btn btn-search" type="submit" title="Search Passenger">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
                
                <div class="action-group">
                    <a href="{{ url('/passenger/new') }}" class="btn btn-primary-elegant">
                        <i class="fa fa-plus-circle"></i> Tambah Data Penumpang
                    </a>
                </div>
            </div>
            
            <div class="clearfix mb-3"></div>

            <!-- TABEL PASSENGER -->
            <div class="table-responsive">
                <table class="table elegant-table">
                    <thead>
                        <tr>
                            <th>Nama Penumpang</th>
                            <th>ID Card</th>
                            <th>Date Birth</th>
                            <th>Garuda Frequent Flyer</th>
                            <th>No Telp</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($passengers as $passenger)
                        <tr class="table-row-hover">
                            <td class="uppercase-text">{{ $passenger->name }}</td>
                            <td>{{ $passenger->id_card }}</td>
                            <td>{{ \Carbon\Carbon::parse($passenger->date_birth)->format('d-m-Y') }}</td>
                            <td>{{ $passenger->gff ?: '-' }}</td>
                            <td>{{ $passenger->phone }}</td>
                            
                            <!-- Kolom Aksi -->
                            <td class="action-buttons text-center">
                                <a href="{{ url('/passenger/' . $passenger->id) }}" class="btn-action edit-action" title="Edit Penumpang">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                
                                <form action="{{ url('/passenger/' . $passenger->id) }}" method="POST" id="delete-passenger-form-{{ $passenger->id }}" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn-action delete-action delete-passenger-btn" data-passenger-id="{{ $passenger->id }}" data-passenger-name="{{ $passenger->name }}" title="Delete Penumpang">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center p-4">
                                <i class="fa fa-info-circle"></i> Tidak ada data penumpang ditemukan.
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
                        <li class="page-item"><a class="page-link" href="{{ $passengers->url(1) }}"><i class="fa fa-angle-double-left"></i></a></li>

                        @php
                        $startPage = max(1, $passengers->currentPage() - 2);
                        $endPage = min($passengers->lastPage(), $passengers->currentPage() + 2);
                        @endphp

                        @if ($startPage > 1)
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                        @endif

                        @foreach (range($startPage, $endPage) as $page)
                        @if ($page == $passengers->currentPage())
                        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                        @else
                        <li class="page-item"><a class="page-link" href="{{ $passengers->appends(['search' => request('search')])->url($page) }}">{{ $page }}</a></li>
                        @endif
                        @endforeach

                        @if ($endPage < $passengers->lastPage())
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                        @endif

                        <li class="page-item"><a class="page-link" href="{{ $passengers->url($passengers->lastPage()) }}"><i class="fa fa-angle-double-right"></i></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL KONFIRMASI HAPUS -->
<div id="deletePassengerModal" class="custom-modal-overlay" style="display: none;">
    <div class="custom-modal-content">
        <div class="modal-header-danger">
            <i class="fa fa-exclamation-triangle"></i> Konfirmasi Penghapusan
        </div>
        <div class="modal-body">
            Apakah Anda yakin ingin menghapus penumpang **<span id="passengerNamePlaceholder"></span>**? Tindakan ini tidak dapat dibatalkan.
        </div>
        <div class="modal-footer">
            <button id="cancelDeletePassengerBtn" class="btn btn-secondary-modal">Batal</button>
            <button id="confirmDeletePassengerBtn" class="btn btn-danger-modal">Ya, Hapus</button>
        </div>
    </div>
</div>

<!-- MODAL POP-UP SUKSES -->
<div id="successToast" class="success-toast" style="display: none;">
    <div class="success-toast-content">
        <div class="checkmark-circle">
            <i class="fa fa-check"></i>
        </div>
        <p class="success-text" id="successMessageText">Penumpang berhasil dihapus!</p>
    </div>
</div>

<script>
$(document).ready(function() {
    let targetFormId = '';
    
    // Delete Passenger Handler
    $('.delete-passenger-btn').on('click', function(e) {
        e.preventDefault();
        const passengerId = $(this).data('passenger-id');
        const passengerName = $(this).data('passenger-name');
        
        targetFormId = '#delete-passenger-form-' + passengerId;
        $('#passengerNamePlaceholder').text(passengerName);
        $('#deletePassengerModal').fadeIn(200);
    });
    
    $('#cancelDeletePassengerBtn').on('click', function() {
        $('#deletePassengerModal').fadeOut(200);
        targetFormId = '';
    });
    
    $('#confirmDeletePassengerBtn').on('click', function() {
        if (targetFormId) {
            $('#deletePassengerModal').fadeOut(200, function() {
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
