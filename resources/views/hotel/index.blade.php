@extends('master')

@section('konten')
    <div class="elegant-container">
        <div class="row mb-4">
            <div class="col-md-12">
                <h1 class="page-title">Data Hotel</h1>
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
                        <form action="{{ url('/hotel') }}" method="GET" class="search-form">
                            <div class="input-group">
                                <input type="text" class="form-control elegant-input" placeholder="Cari Nama Hotel" name="search" value="{{ request('search') }}">
                                <button class="btn btn-search" type="submit" title="Search Hotel">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="action-group">
                        <a href="{{ url('/hotel/new') }}" class="btn btn-primary-elegant">
                            <i class="fa fa-plus-circle"></i> Tambah Data Hotel
                        </a>
                    </div>
                </div>
                
                <div class="clearfix mb-3"></div>

                <!-- TABEL HOTEL -->
                <div class="table-responsive">
                    <table class="table elegant-table">
                        <thead>
                            <tr>
                                <th>Kode Hotel</th>
                                <th>Nama Hotel</th>
                                <th>Region</th>
                                <th>Address</th>
                                <th>No Telp</th>
                                <th>Fax</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($hotels as $hotel)
                            <tr class="table-row-hover">
                                <td><strong>{{ $hotel->hotel_code }}</strong></td>
                                <td>{{ $hotel->hotel_name }}</td>
                                <td>{{ $hotel->region }}</td>
                                <td>{{ $hotel->address }}</td>
                                <td>
                                    @php 
                                    
                                        $phones = explode(', ', $hotel->phone); 
                                    @endphp

                                    @foreach($phones as $index => $p)
                                        {{ $p }} 
                                        @if(!$loop->last) 
                                            <span style="color: #cbd5e1; margin: 0 5px;">/</span> 
                                        @endif
                                    @endforeach
                                </td>
                                <td>{{ $hotel->fax ?: '-' }}</td>
                                
                                <!-- Kolom Aksi -->
                                <td class="action-buttons text-center">
                                    <a href="{{ url('/hotel/' . $hotel->id) }}" class="btn-action edit-action" title="Edit Hotel">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    
                                    <form action="{{ url('/hotel/' . $hotel->id) }}" method="POST" id="delete-hotel-form-{{ $hotel->id }}" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn-action delete-action delete-hotel-btn" data-hotel-id="{{ $hotel->id }}" data-hotel-name="{{ $hotel->hotel_name }}" title="Delete Hotel">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center p-4">
                                    <i class="fa fa-info-circle"></i> Tidak ada data hotel ditemukan.
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
                            <li class="page-item"><a class="page-link" href="{{ $hotels->url(1) }}"><i class="fa fa-angle-double-left"></i></a></li>

                            @php
                            $startPage = max(1, $hotels->currentPage() - 2);
                            $endPage = min($hotels->lastPage(), $hotels->currentPage() + 2);
                            @endphp

                            @if ($startPage > 1)
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                            @endif

                            @foreach (range($startPage, $endPage) as $page)
                            @if ($page == $hotels->currentPage())
                            <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                            @else
                            <li class="page-item"><a class="page-link" href="{{ $hotels->appends(['search' => request('search')])->url($page) }}">{{ $page }}</a></li>
                            @endif
                            @endforeach

                            @if ($endPage < $hotels->lastPage())
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                            @endif

                            <li class="page-item"><a class="page-link" href="{{ $hotels->url($hotels->lastPage()) }}"><i class="fa fa-angle-double-right"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL KONFIRMASI HAPUS -->
    <div id="deleteHotelModal" class="custom-modal-overlay" style="display: none;">
        <div class="custom-modal-content">
            <div class="modal-header-danger">
                <i class="fa fa-exclamation-triangle"></i> Konfirmasi Penghapusan
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus hotel **<span id="hotelNamePlaceholder"></span>**? Tindakan ini tidak dapat dibatalkan.
            </div>
            <div class="modal-footer">
                <button id="cancelDeleteHotelBtn" class="btn btn-secondary-modal">Batal</button>
                <button id="confirmDeleteHotelBtn" class="btn btn-danger-modal">Ya, Hapus</button>
            </div>
        </div>
    </div>

    <!-- MODAL POP-UP SUKSES -->
    <div id="successToast" class="success-toast" style="display: none;">
        <div class="success-toast-content">
            <div class="checkmark-circle">
                <i class="fa fa-check"></i>
            </div>
            <p class="success-text" id="successMessageText">Hotel berhasil dihapus!</p>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        let targetFormId = '';
        
        // Delete Hotel Handler
        $('.delete-hotel-btn').on('click', function(e) {
            e.preventDefault();
            const hotelId = $(this).data('hotel-id');
            const hotelName = $(this).data('hotel-name');
            
            targetFormId = '#delete-hotel-form-' + hotelId;
            $('#hotelNamePlaceholder').text(hotelName);
            $('#deleteHotelModal').fadeIn(200);
        });
        
        $('#cancelDeleteHotelBtn').on('click', function() {
            $('#deleteHotelModal').fadeOut(200);
            targetFormId = '';
        });
        
        $('#confirmDeleteHotelBtn').on('click', function() {
            if (targetFormId) {
                $('#deleteHotelModal').fadeOut(200, function() {
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
