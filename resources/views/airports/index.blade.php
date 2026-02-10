@extends('master')

@section('konten')
<div class="elegant-container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="page-title">Manajemen Bandara</h1>
        </div>
    </div>
    
    <div class="card-elegant">
        <div class="card-body">
            @if (session('success'))
            <div class="alert alert-success d-none" data-message="{!! session('success') !!}">
                {{-- Session success akan diambil oleh JS --}}
            </div>
            @endif

            <!-- FILTER & TOMBOL TAMBAH -->
            <div class="top-bar-controls">
                <div class="filter-search-group">
                    <form action="{{ route('airports.index') }}" method="GET" class="search-form">
                        <div class="input-group">
                            <input type="text" class="form-control elegant-input" placeholder="Cari Kode, Nama, atau Kota Bandara" name="search" value="{{ request('search') }}">
                            <button class="btn btn-search" type="submit" title="Search Airport">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="action-group">
                    <a href="{{ route('airports.create') }}" class="btn btn-primary-elegant">
                        <i class="fa fa-plus-circle"></i> Tambah Bandara
                    </a>
                </div>
            </div>
            
            <div class="clearfix mb-3"></div>

            <!-- TABEL AIRPORTS -->
            <div class="table-responsive">
                <table class="table elegant-table">
                    <thead>
                        <tr>
                            <th>Kode Bandara</th>
                            <th>Nama Bandara</th>
                            <th>Kota</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($airports as $airport)
                        <tr class="table-row-hover">
                            <td><strong>{{ $airport->code }}</strong></td>
                            <td class="uppercase-text">{{ $airport->name }}</td>
                            <td>{{ $airport->city }}</td>
                            
                            <!-- Kolom Aksi -->
                            <td class="action-buttons text-center">
                                <a href="{{ route('airports.edit', $airport->id) }}" class="btn-action edit-action" title="Edit Bandara">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                
                                <form action="{{ route('airports.destroy', $airport->id) }}" method="POST" id="delete-airport-form-{{ $airport->id }}" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn-action delete-action delete-airport-btn" data-airport-id="{{ $airport->id }}" data-airport-name="{{ $airport->name }}" title="Delete Bandara">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center p-4">
                                <i class="fa fa-info-circle"></i> Tidak ada data bandara ditemukan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="table-footer-controls">
            <div class="total-summary">
                Showing {{ $airports->firstItem() }} to {{ $airports->lastItem() }} of {{ $airports->total() }} entries
            </div>

            <div class="pagination-elegant">
                <ul class="pagination">
                    <li class="page-item {{ ($airports->currentPage() == 1) ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $airports->url(1) }}"><i class="fa fa-angle-double-left"></i></a>
                    </li>

                    @php
                        // Batasan jumlah kotak angka yang muncul
                        $limit = 1; // Menampilkan 1 angka sebelum dan sesudah halaman aktif
                        $startPage = max(1, $airports->currentPage() - 1);
                        $endPage = min($airports->lastPage(), $startPage + 3); // Mengunci total kotak sekitar 4
                        
                        // Adjustment jika sudah di akhir halaman agar tetap muncul 4 kotak
                        if ($endPage - $startPage < 2) {
                            $startPage = max(1, $endPage - 2);
                        }
                    @endphp

                    @if ($startPage > 1)
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                    @endif

                    @foreach (range($startPage, $endPage) as $page)
                        <li class="page-item {{ ($page == $airports->currentPage()) ? 'active' : '' }}">
                            @if ($page == $airports->currentPage())
                                <span class="page-link">{{ $page }}</span>
                            @else
                                <a class="page-link" href="{{ $airports->url($page) }}">{{ $page }}</a>
                            @endif
                        </li>
                    @endforeach

                    @if ($endPage < $airports->lastPage())
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                    @endif

                    <li class="page-item {{ ($airports->currentPage() == $airports->lastPage()) ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $airports->url($airports->lastPage()) }}"><i class="fa fa-angle-double-right"></i></a>
                    </li>
                </ul>
            </div>
        </div>
        </div>
    </div>
</div>

<!-- MODAL KONFIRMASI HAPUS -->
<div id="deleteAirportModal" class="custom-modal-overlay" style="display: none;">
    <div class="custom-modal-content">
        <div class="modal-header-danger">
            <i class="fa fa-exclamation-triangle"></i> Konfirmasi Penghapusan
        </div>
        <div class="modal-body">
            Apakah Anda yakin ingin menghapus bandara **<span id="airportNamePlaceholder"></span>**? Tindakan ini tidak dapat dibatalkan.
        </div>
        <div class="modal-footer">
            <button id="cancelDeleteAirportBtn" class="btn btn-secondary-modal">Batal</button>
            <button id="confirmDeleteAirportBtn" class="btn btn-danger-modal">Ya, Hapus</button>
        </div>
    </div>
</div>

<!-- MODAL POP-UP SUKSES -->
<div id="successToast" class="success-toast" style="display: none;">
    <div class="success-toast-content">
        <div class="checkmark-circle">
            <i class="fa fa-check"></i>
        </div>
        <p class="success-text" id="successMessageText">Bandara berhasil dihapus!</p>
    </div>
</div>

<script>
$(document).ready(function(){
    // Handle delete button clicks
    $('.delete-airport-btn').click(function(e){
        e.preventDefault();
        const airportId = $(this).data('airport-id');
        const airportName = $(this).data('airport-name');
        
        $('#airportNamePlaceholder').text(airportName);
        $('#deleteAirportModal').fadeIn(200);
        
        $('#confirmDeleteAirportBtn').off('click').on('click', function(){
            $('#delete-airport-form-' + airportId).submit();
        });
        
        $('#cancelDeleteAirportBtn').off('click').on('click', function(){
            $('#deleteAirportModal').fadeOut(200);
        });
    });

    // Handle success message
    const successAlert = $('.alert-success');
    if(successAlert.length) {
        const message = successAlert.data('message');
        $('#successMessageText').text(message || 'Bandara berhasil dihapus!');
        $('#successToast').fadeIn(200);
        setTimeout(function(){
            $('#successToast').fadeOut(200);
        }, 3000);
    }
});
</script>
<style>
/* Update Footer Tabel agar Pagination ke Kanan */
.table-footer-controls {
    display: flex;
    /* justify-content: space-between;  <-- Hapus atau ganti ini */
    justify-content: flex-end; /* Memaksa semua konten di dalamnya ke arah kanan */
    align-items: center;
    padding-top: 15px;
    border-top: 1px solid #f1f5f9;
}

/* Pastikan margin pagination rapi */
.pagination-elegant .pagination {
    margin: 0;
    display: flex;
    list-style: none;
    padding: 0;
}

.table-footer-controls {
    display: flex;
    justify-content: space-between; /* Teks kiri, Pagination kanan */
    align-items: center;
    padding-top: 15px;
    border-top: 1px solid #f1f5f9;
}

.total-summary {
    font-size: 13px;
    color: #64748b;
}
</style>
@endsection