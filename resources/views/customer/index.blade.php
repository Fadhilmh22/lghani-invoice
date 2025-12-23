@extends('master')

@section('konten')
<div class="elegant-container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="page-title">List Pelanggan</h1>
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
                    <form action="{{ url('/customer') }}" method="GET" class="search-form">
                        <div class="input-group">
                            <input type="text" class="form-control elegant-input" placeholder="Cari nama pelanggan" name="search" value="{{ request('search') }}">
                            <button class="btn btn-search" type="submit" title="Search Customer">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
                
                <div class="action-group">
                    <a href="{{ url('/customer/new') }}" class="btn btn-primary-elegant">
                        <i class="fa fa-plus-circle"></i> Tambah Pelanggan
                    </a>
                </div>
            </div>
            
            <div class="clearfix mb-3"></div>

            <!-- TABEL CUSTOMER -->
            <div class="table-responsive">
                <table class="table elegant-table">
                    <thead>
                        <tr>
                            <th>Gender</th>
                            <th>Nama Booker</th>
                            <th>Nama Perusahaan</th>
                            <th>No Telp</th>
                            <th>Alamat</th>
                            <th>Email</th>
                            <th>Payment</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                        <tr class="table-row-hover">
                            <td class="uppercase-text">{{ $customer->gender }}</td>
                            <td class="uppercase-text">{{ $customer->booker }}</td>
                            <td>{{ $customer->company }}</td>
                            <td>{{ $customer->phone }}</td>
                            <td>{{ $customer->alamat }}</td>
                            <td><a href="mailto:{{ $customer->email }}">{{ $customer->email }}</a></td>
                            <td>{{ $customer->payment }}</td>
                            
                            <!-- Kolom Aksi -->
                            <td class="action-buttons text-center">
                                <a href="{{ url('/customer/' . $customer->id) }}" class="btn-action edit-action" title="Edit Customer">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                
                                <form action="{{ url('/customer/' . $customer->id) }}" method="POST" id="delete-customer-form-{{ $customer->id }}" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn-action delete-action delete-customer-btn" data-customer-id="{{ $customer->id }}" data-customer-name="{{ $customer->booker }}" title="Delete Customer">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                                
                                <form action="{{ route('invoice.store') }}" method="post" style="display: inline;">
                                    @csrf
                                    <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                                    <button type="submit" class="btn-action" style="background-color: #e0f2f1; color: #00897b;" title="Buat Invoice">
                                        <i class="fa fa-file-invoice"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center p-4">
                                <i class="fa fa-info-circle"></i> Tidak ada data pelanggan yang ditemukan.
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
                        <li class="page-item"><a class="page-link" href="{{ $customers->url(1) }}"><i class="fa fa-angle-double-left"></i></a></li>

                        @php
                        $startPage = max(1, $customers->currentPage() - 2);
                        $endPage = min($customers->lastPage(), $customers->currentPage() + 2);
                        @endphp

                        @if ($startPage > 1)
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                        @endif

                        @foreach (range($startPage, $endPage) as $page)
                        @if ($page == $customers->currentPage())
                        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                        @else
                        <li class="page-item"><a class="page-link" href="{{ $customers->appends(['search' => request('search')])->url($page) }}">{{ $page }}</a></li>
                        @endif
                        @endforeach

                        @if ($endPage < $customers->lastPage())
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                        @endif

                        <li class="page-item"><a class="page-link" href="{{ $customers->url($customers->lastPage()) }}"><i class="fa fa-angle-double-right"></i></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL KONFIRMASI HAPUS -->
<div id="deleteCustomerModal" class="custom-modal-overlay" style="display: none;">
    <div class="custom-modal-content">
        <div class="modal-header-danger">
            <i class="fa fa-exclamation-triangle"></i> Konfirmasi Penghapusan
        </div>
        <div class="modal-body">
            Apakah Anda yakin ingin menghapus pelanggan **<span id="customerNamePlaceholder"></span>**? Tindakan ini tidak dapat dibatalkan.
        </div>
        <div class="modal-footer">
            <button id="cancelDeleteCustomerBtn" class="btn btn-secondary-modal">Batal</button>
            <button id="confirmDeleteCustomerBtn" class="btn btn-danger-modal">Ya, Hapus</button>
        </div>
    </div>
</div>

<!-- MODAL POP-UP SUKSES -->
<div id="successToast" class="success-toast" style="display: none;">
    <div class="success-toast-content">
        <div class="checkmark-circle">
            <i class="fa fa-check"></i>
        </div>
        <p class="success-text" id="successMessageText">Pelanggan berhasil dihapus!</p>
    </div>
</div>

<script>
$(document).ready(function() {
    let targetFormId = '';
    
    // Delete Customer Handler
    $('.delete-customer-btn').on('click', function(e) {
        e.preventDefault();
        const customerId = $(this).data('customer-id');
        const customerName = $(this).data('customer-name');
        
        targetFormId = '#delete-customer-form-' + customerId;
        $('#customerNamePlaceholder').text(customerName);
        $('#deleteCustomerModal').fadeIn(200);
    });
    
    $('#cancelDeleteCustomerBtn').on('click', function() {
        $('#deleteCustomerModal').fadeOut(200);
        targetFormId = '';
    });
    
    $('#confirmDeleteCustomerBtn').on('click', function() {
        if (targetFormId) {
            $('#deleteCustomerModal').fadeOut(200, function() {
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
