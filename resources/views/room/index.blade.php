@extends('master')

@section('konten')
<div class="elegant-container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="page-title">Rooms</h1>
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
                    <form action="{{ url('/room') }}" method="GET" class="search-form">
                        <div class="input-group">
                            <input type="text" class="form-control elegant-input" placeholder="Cari Nama Hotel" name="search" value="{{ request('search') }}">
                            <button class="btn btn-search" type="submit" title="Search Room">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
                
                <div class="action-group">
                    <a href="{{ url('/room/new') }}" class="btn btn-primary-elegant">
                        <i class="fa fa-plus-circle"></i> Tambah Data Kamar
                    </a>
                </div>
            </div>
            
            <div class="clearfix mb-3"></div>

            <!-- TABEL ROOM -->
            <div class="table-responsive">
                <table class="table elegant-table">
                    <thead>
                        <tr>
                            <th>Hotel</th>
                            <th>Room Code</th>
                            <th>Room Name</th>
                            <th>Room Type</th>
                            <th>Bed Type</th>
                            <th class="text-right">Weekday Price</th>
                            <th class="text-right">Weekday NTA</th>
                            <th class="text-right">Weekend Price</th>
                            <th class="text-right">Weekend NTA</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rooms as $room)
                        <tr class="table-row-hover">
                            <td>{{ $comboHotel[$room->hotel_id]['hotel_name'] }}</td>
                            <td><strong>{{ $room->room_code }}</strong></td>
                            <td>{{ $room->room_name }}</td>
                            <td>{{ $room->room_type }}</td>
                            <td>{{ $room->bed_type }}</td>
                            <td class="text-right">Rp {{ number_format($room->weekday_price) }}</td>
                            <td class="text-right">Rp {{ number_format($room->weekday_nta) }}</td>
                            <td class="text-right">Rp {{ number_format($room->weekend_price) }}</td>
                            <td class="text-right">Rp {{ number_format($room->weekend_nta) }}</td>
                            
                            <!-- Kolom Aksi -->
                            <td class="action-buttons text-center">
                                <a href="{{ url('/room/' . $room->id) }}" class="btn-action edit-action" title="Edit Room">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                
                                <form action="{{ url('/room/' . $room->id) }}" method="POST" id="delete-room-form-{{ $room->id }}" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn-action delete-action delete-room-btn" data-room-id="{{ $room->id }}" data-room-name="{{ $room->room_name }}" title="Delete Room">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center p-4">
                                <i class="fa fa-info-circle"></i> Tidak ada data room ditemukan.
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
                        <li class="page-item"><a class="page-link" href="{{ $rooms->url(1) }}"><i class="fa fa-angle-double-left"></i></a></li>

                        @php
                        $startPage = max(1, $rooms->currentPage() - 2);
                        $endPage = min($rooms->lastPage(), $rooms->currentPage() + 2);
                        @endphp

                        @if ($startPage > 1)
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                        @endif

                        @foreach (range($startPage, $endPage) as $page)
                        @if ($page == $rooms->currentPage())
                        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                        @else
                        <li class="page-item"><a class="page-link" href="{{ $rooms->appends(['search' => request('search')])->url($page) }}">{{ $page }}</a></li>
                        @endif
                        @endforeach

                        @if ($endPage < $rooms->lastPage())
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                        @endif

                        <li class="page-item"><a class="page-link" href="{{ $rooms->url($rooms->lastPage()) }}"><i class="fa fa-angle-double-right"></i></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL KONFIRMASI HAPUS -->
<div id="deleteRoomModal" class="custom-modal-overlay" style="display: none;">
    <div class="custom-modal-content">
        <div class="modal-header-danger">
            <i class="fa fa-exclamation-triangle"></i> Konfirmasi Penghapusan
        </div>
        <div class="modal-body">
            Apakah Anda yakin ingin menghapus room **<span id="roomNamePlaceholder"></span>**? Tindakan ini tidak dapat dibatalkan.
        </div>
        <div class="modal-footer">
            <button id="cancelDeleteRoomBtn" class="btn btn-secondary-modal">Batal</button>
            <button id="confirmDeleteRoomBtn" class="btn btn-danger-modal">Ya, Hapus</button>
        </div>
    </div>
</div>

<!-- MODAL POP-UP SUKSES -->
<div id="successToast" class="success-toast" style="display: none;">
    <div class="success-toast-content">
        <div class="checkmark-circle">
            <i class="fa fa-check"></i>
        </div>
        <p class="success-text" id="successMessageText">Room berhasil dihapus!</p>
    </div>
</div>

<script>
$(document).ready(function() {
    let targetFormId = '';
    
    // Delete Room Handler
    $('.delete-room-btn').on('click', function(e) {
        e.preventDefault();
        const roomId = $(this).data('room-id');
        const roomName = $(this).data('room-name');
        
        targetFormId = '#delete-room-form-' + roomId;
        $('#roomNamePlaceholder').text(roomName);
        $('#deleteRoomModal').fadeIn(200);
    });
    
    $('#cancelDeleteRoomBtn').on('click', function() {
        $('#deleteRoomModal').fadeOut(200);
        targetFormId = '';
    });
    
    $('#confirmDeleteRoomBtn').on('click', function() {
        if (targetFormId) {
            $('#deleteRoomModal').fadeOut(200, function() {
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
