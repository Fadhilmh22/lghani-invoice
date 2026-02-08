@extends('master')

@section('konten')

@php
    // NOTE: Automatic status updates were removed. No DB changes occur when
    // loading this view. Any scheduled or manual status updates should be
    // handled explicitly via controller actions or scheduled commands.
@endphp

<div>
    <!-- Link Font Awesome (tetap dipertahankan, meskipun idealnya di master) -->
    <link href="{{ asset('node_modules/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
</div>

<div class="elegant-container">
    <div class="row mb-4">
        <div class="col-md-12">
            <!-- Judul Halaman Baru, Lebih Menonjol -->
            <h1 class="page-title">Invoice Ticketing</h1>
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

            <!-- FILTER & TOMBOL BUAT INVOICE (Diatur agar rapi sejajar) -->
            <div class="top-bar-controls">
                <div class="filter-search-group">
                    <form action="{{ url('/invoice') }}" method="GET" class="search-form">
                        <!-- Perubahan Struktur: input dan button langsung di bawah input-group -->
                        <div class="input-group">
                            <input type="text" class="form-control elegant-input" placeholder="Find Booker Name" name="search" value="{{ request('search') }}">
                            <button class="btn btn-search" type="submit" title="Search Invoice">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Tombol "Buat Invoice" yang menonjol dan elegan -->
                <div class="action-group">
                    <a href="{{ route('invoice.create') }}" class="btn btn-primary-elegant">
                        <i class="fa fa-plus-circle"></i> Create Invoice
                    </a>
                </div>
            </div>
            
            <!-- Ruang kosong setelah filter -->
            <div class="clearfix mb-3"></div>

            <!-- TABEL INVOICE (Menggunakan Class elegant-table) -->
            <div class="table-responsive">
                <table class="table elegant-table">
                    <thead>
                        <tr>
                            <th>Invoice ID</th>
                            <th>Date</th>
                            <th>Booker</th>
                            <th>Company</th> 
                            <th>Phone Number</th>
                            <th>Issue By</th>
                            <th class="text-right">Total</th>
                            <th class="text-center">Action</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalBelumLunas = 0;
                        @endphp
                        @forelse ($invoice as $row)
                        @php
                            // Periksa status saat rendering (Ini akan menggunakan data terbaru dari DB)
                            $currentStatus = $row->status_pembayaran;
                            $totalBelumLunas += ($currentStatus === 'Belum Lunas') ? $row->total : 0;
                            
                            // Menentukan kelas status
                            $statusClass = $currentStatus === 'Sudah Lunas' ? 'status-paid' : 'status-unpaid';
                            $statusIcon = $currentStatus === 'Sudah Lunas' ? 'fa-check-circle' : 'fa-times-circle';
                        @endphp
                        
                        <tr class="table-row-hover">
                            <td><strong>{{ $row->invoiceno }}</strong></td>
                            <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d-m-Y H:i:s') }}</td>
                            <td class="wider-column uppercase-text">{{ $row->customer->gender }}. {{ $row->customer->booker }}</td>
                            <td>{{ $row->customer->company }}</td> 
                            <td>{{ $row->customer->phone }}</td>
                            <td>{{ $row->edited }}</td>
                            <td class="text-right">
                                <strong>Rp {{ number_format($row->detail->sum('pax_paid')) }}</strong>
                            </td>                            
                            <!-- Kolom Aksi yang Rapi dengan Icon -->
                            <td class="action-buttons text-center">
                                <form action="{{ route('invoice.destroy', $row->id) }}" method="POST" id="delete-form-{{ $row->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <!-- Print -->
                                    <a href="{{ route('invoice.print', $row->id) }}" class="btn-action print-action" title="Print Invoice">
                                        <i class="fa fa-print"></i>
                                    </a>
                                    <!-- Print Disc -->
                                    <a href="{{ route('invoice.printdisc', $row->id) }}" class="btn-action disc-action" title="Discount">
                                        <i class="fa fa-tag"></i>
                                    </a>
                                    <!-- Ubah -->
                                    <a href="{{ route('invoice.edit', $row->id) }}" class="btn-action edit-action" title="Edit Invoice">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    <!-- Hapus - Panggil fungsi JavaScript kustom -->
                                    <button type="button" class="btn-action delete-action delete-button" data-invoice-id="{{ $row->id }}" data-invoice-no="{{ $row->invoiceno }}" title="Delete Invoice">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                            
                            <!-- Kolom Status (Badge) -->
                            <td class="text-center">
                                <form method="POST" action="{{ route('invoice.ubah-status', $row->id) }}">
                                    @csrf
                                    @method('POST')
                                    <input type="hidden" name="current_page" value="{{ $invoice->currentPage() }}">
                                    <button type="submit" class="toggleStatus status-badge {{ $statusClass }}" data-invoice-id="{{ $row->id }}">
                                        <i class="fa {{ $statusIcon }}"></i> {{ $currentStatus }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center p-4">
                                <i class="fa fa-info-circle"></i> Tidak ada data invoice ditemukan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer Tabel (Total dan Pagination) -->
            <div class="table-footer-controls">
                <!-- Total Belum Lunas -->
                <div class="total-summary">
                    <strong>Total Belum Lunas:</strong> <span class="text-danger">Rp {{ number_format($totalBelumLunas) }}</span>
                </div>
                
                <!-- Pagination -->
                <div class="pagination-elegant">
                    <ul class="pagination">
                        <!-- Navigation Links -->
                        <li class="page-item"><a class="page-link"
                                href="{{ $invoice->url(1) }}"><i class="fa fa-angle-double-left"></i></a></li>

                        @php
                        $startPage = max(1, $invoice->currentPage() - 2);
                        $endPage = min($invoice->lastPage(), $invoice->currentPage() + 2);
                        @endphp

                        @if ($startPage > 1)
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                        @endif

                        @foreach (range($startPage, $endPage) as $page)
                        @if ($page == $invoice->currentPage())
                        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                        @else
                        <li class="page-item"><a class="page-link"
                                href="{{ $invoice->appends(['search' => request('search')])->url($page) }}">{{ $page }}</a>
                        </li>
                        @endif
                        @endforeach

                        @if ($endPage < $invoice->lastPage())
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                        @endif

                        <li class="page-item"><a class="page-link"
                                href="{{ $invoice->url($invoice->lastPage()) }}"><i class="fa fa-angle-double-right"></i></a></li>
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
<!-- AKHIR MODAL KONFIRMASI HAPUS KUSTOM -->

<!-- MODAL POP-UP SUKSES KUSTOM (ANIMASI CHECKLIST) -->
<div id="successToast" class="success-toast" style="display: none;">
    <div class="success-toast-content">
        <div class="checkmark-circle">
            <i class="fa fa-check"></i>
        </div>
        <p class="success-text" id="successMessageText">Invoice berhasil dihapus!</p>
    </div>
</div>
<!-- AKHIR MODAL POP-UP SUKSES KUSTOM -->

<script>
    // Pastikan skrip berjalan setelah DOM dan jQuery dimuat sepenuhnya
    $(document).ready(function () {
        
        let targetFormId = ''; // Variabel untuk menyimpan ID form yang akan dihapus

        // --- LOGIKA MODAL HAPUS INVOICE KUSTOM ---
        $('.delete-button').on('click', function(e) {
            e.preventDefault();
            const invoiceId = $(this).data('invoice-id');
            const invoiceNo = $(this).data('invoice-no');
            
            // Set ID form target
            targetFormId = '#delete-form-' + invoiceId;

            // Update teks modal dengan nomor invoice yang relevan
            $('#invoiceNoPlaceholder').text(invoiceNo);

            // Tampilkan modal
            $('#deleteConfirmationModal').fadeIn(200);
        });

        // Handler untuk tombol Batal
        $('#cancelDeleteBtn').on('click', function() {
            $('#deleteConfirmationModal').fadeOut(200);
            targetFormId = ''; // Reset target
        });

        // Handler untuk tombol Konfirmasi Hapus
        $('#confirmDeleteBtn').on('click', function() {
            if (targetFormId) {
                // Sembunyikan modal
                $('#deleteConfirmationModal').fadeOut(200, function() {
                    // Kirim form (ini akan memicu redirect dan session flash success)
                    $(targetFormId).submit();
                });
            }
        });


        // --- LOGIKA ANIMASI POP-UP SUKSES ---
        // Cek jika ada session success dari Laravel setelah page load (misalnya setelah DELETE)
        const successAlert = $('.alert-success[data-message]');
        if (successAlert.length) {
            const message = successAlert.data('message');
            
            // Update teks pesan di toast
            $('#successMessageText').html(message);
            
            // Tampilkan toast
            const toast = $('#successToast');
            toast.fadeIn(300).css('display', 'flex');

            // Sembunyikan toast setelah 3 detik
            setTimeout(function() {
                toast.fadeOut(500);
            }, 3000);
        }

        // --- LOGIKA TOGGLE STATUS PEMBAYARAN ---
        $('.toggleStatus').on('click', function(e) {
            e.preventDefault(); // Mencegah form submit bawaan

            var $toggleStatusButton = $(this);
            var form = $toggleStatusButton.closest('form');

            // 1. Dapatkan data saat ini secara dinamis
            var invoiceId = $toggleStatusButton.data('invoice-id');
            var currentPage = form.find('[name="current_page"]').val();
            
            // 2. Ekstrak status pembayaran dengan lebih aman
            var currentStatusText = $toggleStatusButton.text().trim();
            var isLunas = currentStatusText.includes('Sudah Lunas');
            var newStatus = isLunas ? "Belum Lunas" : "Sudah Lunas";
            
            // 3. Gunakan AJAX untuk mengubah status
            $.ajax({
                url: '/invoice/ubah-status/' + invoiceId, 
                method: 'POST',
                data: {
                    status: newStatus,
                    _token: form.find('input[name="_token"]').val() 
                },
                success: function (response) {
                    // Redirect kembali ke halaman saat ini setelah update
                    // Ini juga akan memicu reload dan menampilkan session('success') jika ada di controller ubah-status
                    window.location.href = '/invoice?page=' + currentPage;
                },
                error: function (xhr, status, error) {
                    // Tampilkan pesan error di atas halaman
                    const errorMessage = xhr.responseJSON ? (xhr.responseJSON.message || 'Error tidak diketahui.') : 'Gagal terhubung ke server.';
                    
                    // Pastikan hanya satu alert error yang muncul
                    $('.alert-error-custom').remove(); 
                    
                    $('.page-title').after('<div class="alert alert-danger alert-error-custom">Terjadi kesalahan saat mengubah status: ' + errorMessage + '</div>');
                    
                    // Sembunyikan alert setelah 5 detik
                    setTimeout(function(){
                        $('.alert-error-custom').slideUp();
                    }, 5000);
                }
            });
        });
    });
</script>

<!-- CSS BARU UNTUK TAMPILAN ELEGAN DAN MODAL KUSTOM (TERMASUK TOAST SUKSES) -->
<style>
/* PENTING: Perbaikan untuk menghindari pergeseran (shift) modal. */
body {
    overflow-y: scroll !important; 
}

/* ------------------------------------------- */
/* 1. Tampilan Kontainer & Kartu (Card) */
/* ------------------------------------------- */
.elegant-container {
    max-width: 1300px;
    margin: 0 auto;
    padding: 20px;
}

.card-elegant {
    background-color: #ffffff;
    border-radius: 16px; 
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
    border: none;
    padding: 25px;
}

.page-title {
    font-size: 28px;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 25px;
}

/* ------------------------------------------- */
/* 2. Filter, Search & Tombol Aksi */
/* ------------------------------------------- */
.top-bar-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.filter-search-group {
    flex-grow: 1;
    max-width: 250px;
}

.search-form {
    display: flex;
}

/* Perbaikan Penting: Memastikan input-group menggunakan flex */
.input-group {
    display: flex;
    width: 100%; /* Memastikan mengambil lebar penuh dari parent */
}

.elegant-input {
    flex-grow: 1; /* Input mengambil sisa ruang */
    border-radius: 8px 0 0 8px !important;
    border: 1px solid #e2e8f0;
    padding: 10px 15px;
    box-shadow: none !important;
    height: 40px;
}

.btn-search {
    flex-shrink: 0; /* Mencegah tombol menyusut */
    background-color: #1e1b4b;
    color: white;
    border: 1px solid #1e1b4b;
    border-left: none; /* Menghilangkan border kiri agar menyambung sempurna dengan input */
    /* Hanya bagian kanan yang melengkung, menyambung dengan input */
    border-radius: 0 8px 8px 0 !important;
    padding: 10px 15px; 
    width: 40px; /* Lebar ditetapkan agar tombol terlihat kotak */
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-search:hover {
    background-color: #4338ca;
}

.btn-primary-elegant {
    background-color: #10b981;
    color: white;
    font-weight: 600;
    padding: 10px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(16, 185, 129, 0.2);
    transition: background-color 0.2s;
    border: none;
}

.btn-primary-elegant:hover {
    background-color: #059669;
}

/* ------------------------------------------- */
/* 3. Gaya Tabel Tanpa Border (Elegant Table) */
/* ------------------------------------------- */
.elegant-table {
    width: 100%;
    border-collapse: separate; 
    border-spacing: 0 8px;
}

.elegant-table thead th {
    background-color: #f8fafc;
    color: #475569;
    font-weight: 600;
    padding: 12px 15px;
    border: none !important;
    text-transform: uppercase;
    font-size: 13px;
    letter-spacing: 0.03em;
}

.elegant-table tbody tr {
    background-color: #ffffff;
    transition: all 0.2s;
    border-radius: 8px; 
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.elegant-table tbody tr:hover {
    background-color: #f1f5f9;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
}

.elegant-table td {
    padding: 15px 15px;
    color: #334155;
    border: none !important;
    font-weight: 400;
    font-size: 14px;
    line-height: 1.2;
}

/* ------------------------------------------- */
/* 4. Status Badge & Aksi Buttons */
/* ------------------------------------------- */
.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    border: none;
    transition: all 0.2s;
}

.status-paid {
    background-color: #d1fae5;
    color: #059669;
}

.status-unpaid {
    background-color: #fee2e2;
    color: #ef4444;
}

.action-buttons {
    display: flex;
    gap: 5px;
    justify-content: center;
}

.btn-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    border-radius: 6px;
    transition: background-color 0.2s;
    font-size: 14px;
    text-decoration: none;
    border: none;
}

.print-action { background-color: #e0f2f1; color: #00897b; }
.print-action:hover { background-color: #b2dfdb; }

.disc-action { background-color: #e3f2fd; color: #42a5f5; }
.disc-action:hover { background-color: #90caf9; }

.edit-action { background-color: #fff3e0; color: #ffb300; }
.edit-action:hover { background-color: #ffe0b2; }

.delete-action { background-color: #ffebee; color: #e53935; border: none; }
.delete-action:hover { background-color: #ffcdd2; }

/* ------------------------------------------- */
/* 5. Footer Tabel (Total & Pagination) */
/* ------------------------------------------- */
.table-footer-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 15px;
    border-top: 1px solid #f1f5f9;
}

.total-summary {
    font-size: 14px;
    color: #334155;
}

.pagination-elegant .pagination {
    margin: 0;
}

.pagination-elegant .page-link {
    border-radius: 8px;
    margin: 0 4px;
    color: #4f46e5;
    border: 1px solid #e2e8f0;
    transition: all 0.2s;
}

.pagination-elegant .page-link:hover {
    background-color: #eff0ff;
    border-color: #c7c9ff;
}

.pagination-elegant .page-item.active .page-link {
    background-color: #4f46e5;
    border-color: #4f46e5;
    color: white;
}

/* --- Override Global Classes --- */
.wider-column { width: 150px; }
.uppercase-text { text-transform: capitalize; }
.text-right { text-align: right; }
.text-center { text-align: center; }

/* ------------------------------------------- */
/* 6. Custom Modal (Delete) Styles */
/* ------------------------------------------- */
.custom-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
    /* Pastikan overlay tidak menyebabkan scrollbar tambahan */
    overflow: auto; 
}

.custom-modal-content {
    background-color: white;
    padding: 0;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    width: 90%;
    max-width: 400px;
    animation: fadeIn 0.3s ease-out;
    /* Tambahkan margin agar konten modal terlihat di tengah vertikal saat overflow auto */
    margin: auto; 
}

.modal-header-danger {
    background-color: #fca5a5;
    color: #b91c1c;
    padding: 15px 20px;
    border-radius: 12px 12px 0 0;
    font-weight: 600;
    font-size: 16px;
    display: flex;
    align-items: center;
}

.modal-header-danger .fa {
    margin-right: 10px;
}

.modal-body {
    padding: 20px;
    font-size: 14px;
    color: #334155;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    padding: 15px 20px;
    border-top: 1px solid #f1f5f9;
    gap: 10px;
}

.btn-secondary-modal, .btn-danger-modal {
    padding: 8px 15px;
    border-radius: 8px;
    font-weight: 500;
    border: none;
    cursor: pointer;
    transition: background-color 0.2s;
}

.btn-secondary-modal {
    background-color: #e2e8f0;
    color: #475569;
}

.btn-secondary-modal:hover {
    background-color: #cbd5e1;
}

.btn-danger-modal {
    background-color: #ef4444;
    color: white;
}

.btn-danger-modal:hover {
    background-color: #dc2626;
}

/* ------------------------------------------- */
/* 7. Success Toast (Animasi Checklist) Styles */
/* ------------------------------------------- */
.success-toast {
    position: fixed;
    top: 50%;
    left: 50%;
    /* Menggunakan translate untuk centering absolut yang tidak terpengaruh oleh margin atau padding */
    transform: translate(-50%, -50%); 
    z-index: 1100;
    padding: 30px 40px;
    background-color: #ffffff;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
    display: none; /* Default hidden, controlled by JS */
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.success-toast-content {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.checkmark-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background-color: #10b981; /* Hijau sukses */
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 15px;
    transform: scale(0);
    animation: scaleIn 0.3s ease-out forwards;
}

.checkmark-circle .fa {
    font-size: 30px;
    color: white;
    /* Optional: Small checkmark animation */
    opacity: 0;
    transform: rotate(-10deg);
    animation: checkmarkAppear 0.5s ease 0.3s forwards;
}

.success-text {
    font-size: 18px;
    font-weight: 600;
    color: #10b981;
    margin: 0;
}

/* Keyframes untuk Animasi */
@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}

@keyframes scaleIn {
    0% { transform: scale(0); }
    100% { transform: scale(1); }
}

@keyframes checkmarkAppear {
    0% { opacity: 0; transform: rotate(-10deg); }
    100% { opacity: 1; transform: rotate(0deg); }
}

/* Custom Alert untuk Error AJAX */
.alert-danger {
    padding: 15px;
    margin-bottom: 15px;
    border-radius: 8px;
    background-color: #fca5a5;
    color: #b91c1c;
    border: 1px solid #f87171;
    font-weight: 500;
}

/* Custom Alert untuk Info Otomatisasi */
.alert-info {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 8px;
    background-color: #bfdbfe; /* Warna biru muda */
    color: #1e40af; /* Warna biru tua */
    border: 1px solid #93c5fd;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 10px;
}
</style>
@endsection