<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Invoice Web App - LGhani Tour & Travel</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('logo-lghani.png') }}" height="15px">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css">
    <link href="{{ asset('css/elegant-ui.css') }}" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <style>
        /* Variabel CSS untuk kemudahan perubahan */
        :root {
            --sidebar-width: 290px;
            --sidebar-minimized-width: 80px;
            --primary-color: #0f172a;
        }

        body {
            margin: 0;
            background: #e9edff;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease; /* Tambahkan transisi ke body */
        }

        .layout-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* --- SIDEBAR FULL --- */
        .sidebar {
            width: var(--sidebar-width);
            box-sizing: border-box;
            background: linear-gradient(180deg, #0f172a 0%, #1e1b4b 100%);
            color: #fff;
            display: flex;
            flex-direction: column;
            padding: 24px 20px;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            overflow-y: auto;
            transition: width 0.3s ease, transform 0.3s ease; /* Tambahkan transisi lebar */
            z-index: 1000;
        }
        
        /* --- SIDEBAR MINIMIZED STYLES (Perbaikan Utama di sini) --- */
        
        /* Ketika body memiliki class 'sidebar-minimized' */
        body.sidebar-minimized .sidebar {
            width: var(--sidebar-minimized-width);
            padding: 24px 10px; /* Kurangi padding untuk mode minim */
        }

        body.sidebar-minimized .sidebar__brand img {
            width: 40px; /* Perkecil logo */
            margin: 0 auto;
        }

        body.sidebar-minimized .sidebar__user {
            justify-content: center; /* Pusatkan avatar */
            padding: 12px 0;
        }

        body.sidebar-minimized .sidebar__user div:last-child {
            display: none; /* Sembunyikan nama/role user */
        }

        /* Aturan umum untuk menyembunyikan semua teks (span) di menu */
        body.sidebar-minimized .sidebar__menu span,
        body.sidebar-minimized .sidebar__footer span {
            display: none;
        }
        
        /* Sembunyikan label menu */
        body.sidebar-minimized .menu-label {
            display: none;
        }
        
        /* Menu link pada mode minim: hanya tampilkan ikon */
        body.sidebar-minimized .menu-link {
            justify-content: center;
            padding: 10px 0;
        }

        body.sidebar-minimized .menu-link i {
            margin: 0; /* Hapus margin ikon */
        }
        
        /* Menu Group Header (Ticketing/Hotel Invoice) */
        body.sidebar-minimized .menu-group-header {
            justify-content: center; /* Pusatkan ikon */
            padding: 10px 0; 
        }

        /* Sembunyikan ikon chevron (panah kanan) pada menu group saat mode minim */
        body.sidebar-minimized .menu-group-header .chevron {
            display: none;
        }

        body.sidebar-minimized .submenu {
            padding-left: 0; /* Hapus indentasi submenu */
        }

        /* --- MAIN CONTENT ADJUSTMENT --- */
        .main-content {
            margin-left: var(--sidebar-width);
            flex: 1;
            min-height: 100vh;
            background: #f5f7fb;
            padding: 24px 30px 40px;
            transition: margin-left 0.3s ease; /* Tambahkan transisi margin */
        }

        /* Ketika body memiliki class 'sidebar-minimized' */
        body.sidebar-minimized .main-content {
            margin-left: var(--sidebar-minimized-width);
        }


        /* --- Gaya yang tidak berubah (Dipertahankan dari kode Anda) --- */
        .sidebar__brand img { 
          text-align: center;
          width: 250px;
          display: flex;
          justify-content: center; /* Pusatkan horizontal */
          align-items: center; /* Pusatkan vertikal */
          height: 150px; /* Tentukan tinggi area logo agar centering vertikal terlihat */
          margin-bottom: 10px;
        }
        .sidebar__user { display: flex; align-items: center; gap: 12px; margin: 24px 0; padding: 12px; background: rgba(255, 255, 255, 0.08); border-radius: 16px; }
        .sidebar__user { justify-content: flex-start; text-align: left; }
        .avatar { width: 46px; height: 46px; border-radius: 12px; background: rgba(255, 255, 255, 0.15); display: flex; align-items: center; justify-content: center; font-size: 18px; }
        .user-name { margin: 0; font-weight: 600; }
        .user-role { margin: 0; font-size: 12px; color: #cbd5f5; }
        .menu-label { font-size: 14px !important; text-transform: uppercase; letter-spacing: 0.08em; margin: 18px 0 8px; color: #94a3b8; font-weight: 600; }
        .menu-link { display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: 12px; color: #e2e8f0; text-decoration: none; margin-bottom: 6px; transition: background 0.2s; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size: 16px; line-height: 1.4; }
        .menu-link i { width: 20px; text-align: center; }
        .menu-link:hover, .menu-link.active { background: rgba(255, 255, 255, 0.15); color: #fff; }
        .sidebar__footer { margin-top: 24px; border-top: 1px solid rgba(148, 163, 184, 0.3); padding-top: 12px; }
        .menu-link.logout { color: #fca5a5; }
        .main-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .toggle-btn { border: none; background: #fff; padding: 10px 12px; border-radius: 12px; box-shadow: 0 8px 20px rgba(15, 23, 42, 0.15); cursor: pointer; }
        .welcome-text { color: #475569; font-weight: 500; }
        .content-area { min-height: calc(100vh - 80px); }
        .menu-group-header { display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; border-radius: 10px; color: #e2e8f0; cursor: pointer; margin-bottom: 4px; transition: background 0.2s; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size: 16px; }
        .menu-group-header i:first-child { margin-right: 10px; width: 18px; text-align: center; }
        .menu-group-header:hover { background: rgba(255, 255, 255, 0.12); }
        .menu-group-header .chevron { transition: transform 0.2s; font-size: 10px; }
        .menu-group-header.open .chevron { transform: rotate(90deg); }
        .submenu { padding-left: 8px; margin-bottom: 6px; display: none; }
        .submenu.open { display: block; }

        /* --- RESPONSIVE MOBILE STYLES (Dipertahankan) --- */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
                z-index: 1000;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding: 20px 16px;
            }
            
            /* Pada mobile, mode minimized tidak berlaku, hanya mode open/close */
            body.sidebar-minimized .sidebar {
                width: var(--sidebar-width); /* Kembalikan lebar penuh */
                transform: translateX(-100%);
            }
            body.sidebar-minimized .main-content {
                margin-left: 0;
            }
        }
        /* Modal and button styles for profile modal */
        .btn-primary-modal { background: linear-gradient(135deg,#4a6cf7,#7c3aed); color: #fff; border: none; padding: 8px 14px; border-radius: 10px; }
        .btn-secondary-modal { background: #f1f5f9; color: #475569; border: none; padding: 8px 14px; border-radius: 10px; }
        .custom-modal-overlay { position: fixed; inset: 0; display: flex; align-items: center; justify-content: center; background: rgba(2,6,23,0.35); z-index: 1200; }
        .custom-modal-content { width: 640px; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 20px 60px rgba(2,6,23,0.2); }
        .modal-header { padding: 14px 18px; font-weight: 700; border-bottom: 1px solid rgba(2,6,23,0.04); }
        .modal-body { padding: 16px 18px; }
        .modal-footer { padding: 12px 18px; display:flex; justify-content:flex-end; gap:8px; border-top: 1px solid rgba(2,6,23,0.04); }
    </style>
    <script type="text/javascript">
        function formatRupiah(angka){
            var number_string = String(angka).replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa  = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if(ribuan){
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            return rupiah;
        }
    </script>
</head>

<body>
    <div class="layout-wrapper">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar__brand">
                <img src="{{ asset('logo-lghani.png') }}" alt="Lghani Travel">
            </div>
            <button id="profileToggle" class="sidebar__user" style="border:none;background:transparent;cursor:pointer;">
                <div class="avatar"><i class="fa fa-user"></i></div>
                <div>
                    <p class="user-name">{{ Auth::user()->name }}</p>
                    <p class="user-role">{{ Auth::user()->role }}</p>
                </div>
            </button>

            <nav class="sidebar__menu">
                <p class="menu-label">Menu</p>
                
                <!-- Perbaikan HTML: Tambahkan <span> untuk semua teks link -->
                <a href="{{ route('home') }}" class="menu-link {{ request()->is('home') ? 'active' : '' }}">
                    <i class="fa fa-chart-pie"></i> <span>Dashboard</span>
                </a>
                <a href="{{ url('/customer') }}" class="menu-link {{ request()->is('customer*') ? 'active' : '' }}">
                    <i class="fa fa-address-book"></i> <span>Customer Contacts</span>
                </a>
                <a href="{{ url('/passenger') }}" class="menu-link {{ request()->is('passenger*') ? 'active' : '' }}">
                    <i class="fa fa-id-card"></i> <span>Passenger Identity</span>
                </a>

                <p class="menu-label">Application</p>

                <!-- Perbaikan HTML: Tambahkan <span> untuk teks di Menu Group Header -->
                <div class="menu-group">
                    <div class="menu-group-header" data-toggle="submenu" data-target="#ticketingSubmenu">
                        <div>
                            <i class="fa fa-ticket-alt"></i> <span>Ticketing Invoices</span>
                        </div>
                        <i class="fa fa-chevron-right chevron"></i>
                    </div>
                    <div class="submenu" id="ticketingSubmenu">
                        <a href="{{ url('/airline') }}" class="menu-link {{ request()->is('airline*') ? 'active' : '' }}">
                            <i class="fa fa-plane"></i> <span>Airlines</span>
                        </a>
                        <a href="{{ route('ticket.index') }}" class="menu-link {{ request()->is('ticket') ? 'active' : '' }}">
                            <i class="fa fa-plane-departure"></i> <span>Ticket Issued</span>
                        </a>
                        <a href="{{ route('invoice.create') }}" class="menu-link {{ request()->is('invoice/new') ? 'active' : '' }}">
                            <i class="fa fa-pencil-alt"></i> <span>Create Invoice</span>
                        </a>
                        <a href="{{ route('invoice.index') }}" class="menu-link {{ request()->is('invoice') ? 'active' : '' }}">
                            <i class="fa fa-file-invoice"></i> <span>Invoice Ticketing</span>
                        </a>
                    </div>
                </div>

                <!-- Perbaikan HTML: Tambahkan <span> untuk teks di Menu Group Header -->
                <div class="menu-group">
                    <div class="menu-group-header" data-toggle="submenu" data-target="#hotelSubmenu">
                        <div>
                            <i class="fa fa-hotel"></i> <span>Hotel Invoices</span>
                        </div>
                        <i class="fa fa-chevron-right chevron"></i>
                    </div>
                    <div class="submenu" id="hotelSubmenu">
                        <a href="{{ url('/hotel') }}" class="menu-link {{ request()->is('hotel*') ? 'active' : '' }}">
                            <i class="fa fa-hotel"></i> <span>Hotel</span>
                        </a>
                        <a href="{{ url('/room') }}" class="menu-link {{ request()->is('room*') ? 'active' : '' }}">
                            <i class="fa fa-bed"></i> <span>Hotel Room</span>
                        </a>
                        <a href="{{ url('/hotel-voucher') }}" class="menu-link {{ request()->is('hotel-voucher*') ? 'active' : '' }}">
                            <i class="fa fa-gift"></i> <span>Voucher Hotel</span>
                        </a>
                        <a href="{{ url('/hotel-invoice') }}" class="menu-link {{ request()->is('hotel-invoice*') ? 'active' : '' }}">
                            <i class="fa fa-file-contract"></i> <span>Invoice Hotel</span>
                        </a>
                    </div>
                </div>

                @if (auth()->user()->role == "Owner")
                <!-- Perbaikan HTML: Tambahkan <span> untuk teks di Menu Group Header -->
                <div class="menu-group">
                    <div class="menu-group-header" data-toggle="submenu" data-target="#reportSubmenu">
                        <div>
                            <i class="fa fa-chart-line"></i> <span>Reports</span>
                        </div>
                        <i class="fa fa-chevron-right chevron"></i>
                    </div>
                    <div class="submenu" id="reportSubmenu">
                        <a href="{{ url('/report') }}" class="menu-link {{ request()->is('report') ? 'active' : '' }}">
                            <i class="fa fa-plane"></i> <span>Laporan Ticketing</span>
                        </a>
                        <a href="{{ url('/report/hotel') }}" class="menu-link {{ request()->is('report/hotel') ? 'active' : '' }}">
                            <i class="fa fa-clipboard-list"></i> <span>Laporan Hotel</span>
                        </a>
                        <a href="{{ url('/report/piutang') }}" class="menu-link {{ request()->is('report/piutang') ? 'active' : '' }}">
                            <i class="fa fa-briefcase"></i> <span>Laporan Piutang</span>
                        </a>
                    </div>
                </div>
                @endif
            </nav>

            <div class="sidebar__footer">
                @if (auth()->user()->role == "Owner")
                <a href="{{ route('register') }}" class="menu-link">
                    <i class="fa fa-user-plus"></i> <span>Add Account</span>
                </a>
                @endif
                <a href="{{ route('actionlogout') }}" id="logoutButton" class="menu-link logout" data-logout-url="{{ route('actionlogout') }}">
                    <i class="fa fa-power-off"></i> <span>Log Out</span>
                </a>
            </div>
        </aside>

        <main class="main-content">
            <header class="main-header">
                <button id="sidebarToggle" class="toggle-btn"><i class="fa fa-bars"></i></button>
                <span class="welcome-text">Halo, {{ Auth::user()->name }}</span>
            </header>

            <section class="content-area">
                @yield('konten')
            </section>
        </main>
    </div>
</body>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Cek ukuran layar untuk menentukan mode
    function isDesktop() {
        // Menggunakan 992px sesuai media query yang Anda tentukan
        return window.innerWidth > 992; 
    }

    document.getElementById('sidebarToggle').addEventListener('click', function () {
        
        if (isDesktop()) {
            // Mode Desktop: Toggle class 'sidebar-minimized' pada elemen <body>
            document.body.classList.toggle('sidebar-minimized');
            
            // Pastikan class 'open' (untuk mobile) dihapus saat di desktop
            document.getElementById('sidebar').classList.remove('open');
        } else {
            // Mode Mobile: Toggle class 'open' pada elemen sidebar
            document.getElementById('sidebar').classList.toggle('open');
            
            // Pastikan class 'sidebar-minimized' (untuk desktop) dihapus saat di mobile
            document.body.classList.remove('sidebar-minimized');
        }
    });

    // Logika untuk toggle Submenu (Dipertahankan)
    document.querySelectorAll('[data-toggle="submenu"]').forEach(function (header) {
        header.addEventListener('click', function () {
            var target = document.querySelector(header.getAttribute('data-target'));
            if (!target) return;
            var isOpen = target.classList.contains('open');
            
            // Tutup semua submenu lain kecuali yang sedang diklik (atau jika diklik lagi untuk menutup)
            document.querySelectorAll('.submenu').forEach(function (sm) { sm.classList.remove('open'); });
            document.querySelectorAll('.menu-group-header').forEach(function (mh) { mh.classList.remove('open'); });
            
            // Jika sebelumnya tertutup, buka submenu yang diklik
            if (!isOpen) {
                target.classList.add('open');
                header.classList.add('open');
            }
        });
    });
    </script>

    <script type="text/javascript">
        // Logout confirmation modal handling
        $(document).ready(function() {
            var logoutUrl = null;
            $('#logoutButton').on('click', function(e) {
                e.preventDefault();
                logoutUrl = $(this).data('logout-url');
                $('#logoutConfirmModal').fadeIn(150);
            });

            $('#cancelLogoutBtn').on('click', function() {
                $('#logoutConfirmModal').fadeOut(150);
                logoutUrl = null;
            });

            $('#confirmLogoutBtn').on('click', function() {
                if (logoutUrl) {
                    window.location.href = logoutUrl;
                }
            });

            // Profile modal handlers with client-side validation
            $('#profileToggle').on('click', function(e){ e.preventDefault(); $('#profileModal').fadeIn(150); $('#profileErrors').hide().text(''); });
            $('#cancelProfileBtn').on('click', function(){ $('#profileModal').fadeOut(150); $('#profileErrors').hide().text(''); });
            $('#saveProfileBtn').on('click', function(e){
                e.preventDefault();
                var errors = [];
                var name = $('#profile_name').val().trim();
                var email = $('#profile_email').val().trim();
                var pwd = $('#profile_password').val();
                var pwdc = $('#profile_password_confirmation').val();

                if (!name) errors.push('Nama harus diisi.');
                if (!email) errors.push('Email harus diisi.');
                else {
                    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\\.,;:\s@\"]+\.)+[^<>()[\]\\.,;:\s@\"]{2,})$/i;
                    if (!re.test(email)) errors.push('Format email tidak valid.');
                }
                if (pwd) {
                    if (pwd.length < 6) errors.push('Password minimal 6 karakter.');
                    if (pwd !== pwdc) errors.push('Konfirmasi password tidak cocok.');
                }

                if (errors.length) {
                    $('#profileErrors').html('<ul style="margin:0;padding-left:18px;">' + errors.map(function(e){ return '<li>'+e+'</li>'; }).join('') + '</ul>').show();
                    return false;
                }

                // show confirmation modal before submit
                $('#confirmSaveModal').fadeIn(120);
            });

            // confirmation modal handlers
            $('#confirmCancel').on('click', function(){ $('#confirmSaveModal').fadeOut(120); });
            $('#confirmSave').on('click', function(){
                // perform AJAX submit
                var url = $('#profileForm').attr('action');
                var data = $('#profileForm').serialize();
                $('#confirmSaveModal').fadeOut(120);
                // disable buttons to avoid duplicate clicks
                $('#confirmSave, #saveProfileBtn').prop('disabled', true).addClass('disabled');
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: data,
                    success: function(res) {
                        $('#profileModal').fadeOut(150);
                        $('#profileErrors').hide().text('');
                        // update sidebar name and welcome text
                        if (res.user && res.user.name) {
                            $('.user-name').text(res.user.name);
                            $('.welcome-text').text('Halo, ' + res.user.name);
                        }
                        $('#successMessage').text(res.message || 'Perubahan berhasil disimpan.');
                        $('#successModal').fadeIn(150);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors || {};
                            var list = Object.keys(errors).map(function(k){ return errors[k].join('<br>'); }).join('<br>');
                            $('#profileErrors').html(list).show();
                            $('#confirmSaveModal').fadeOut(120);
                        } else {
                            $('#profileErrors').html('Terjadi kesalahan. Silakan coba lagi.').show();
                        }
                    },
                    complete: function(){
                        $('#confirmSave, #saveProfileBtn').prop('disabled', false).removeClass('disabled');
                    }
                });
            });
        });
        // success modal ok handler
        $(document).on('click','#successOk',function(){$('#successModal').fadeOut(150);});
    </script>

    <!-- LOGOUT CONFIRMATION MODAL (match delete modal style) -->
    <div id="logoutConfirmModal" class="custom-modal-overlay" style="display: none;">
        <div class="custom-modal-content">
            <div class="modal-header-danger">
                <i class="fa fa-exclamation-triangle"></i> Konfirmasi Logout
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin keluar dari aplikasi sekarang?
            </div>
            <div class="modal-footer">
                <button id="cancelLogoutBtn" class="btn btn-secondary-modal">Batal</button>
                <button id="confirmLogoutBtn" class="btn btn-danger-modal">Ya, Logout</button>
            </div>
        </div>
    </div>

    <!-- PROFILE EDIT MODAL -->
    <div id="profileModal" class="custom-modal-overlay" style="display: none;">
        <div class="custom-modal-content">
            <div class="modal-header">
                <i class="fa fa-user"></i> Edit Profile
            </div>
            <div class="modal-body">
                <form id="profileForm" method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    <div id="profileErrors" class="alert alert-danger" style="display:none;margin-bottom:12px;"></div>
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" id="profile_name" name="name" class="form-control" value="{{ Auth::user()->name }}" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" id="profile_email" name="email" class="form-control" value="{{ Auth::user()->email }}" required>
                    </div>
                    <div class="form-group">
                        <label>New Password (leave blank to keep)</label>
                        <input type="password" id="profile_password" name="password" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" id="profile_password_confirmation" name="password_confirmation" class="form-control">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button id="cancelProfileBtn" class="btn btn-secondary-modal">Batal</button>
                <button id="saveProfileBtn" class="btn btn-primary-modal">Simpan</button>
            </div>
        </div>
    </div>

    <!-- CONFIRM SAVE MODAL -->
    <div id="confirmSaveModal" class="custom-modal-overlay" style="display:none;">
        <div class="custom-modal-content">
            <div class="modal-header">
                Konfirmasi Simpan
            </div>
            <div class="modal-body">
                <p>Yakin ingin menyimpan perubahan profile?</p>
            </div>
            <div class="modal-footer">
                <button id="confirmCancel" class="btn btn-secondary-modal">Batal</button>
                <button id="confirmSave" class="btn btn-primary-modal">Ya, Simpan</button>
            </div>
        </div>
    </div>

    <!-- SUCCESS MODAL -->
    <div id="successModal" class="custom-modal-overlay" style="display:none;">
        <div class="custom-modal-content">
            <div class="modal-header">
                Berhasil
            </div>
            <div class="modal-body">
                <p id="successMessage">Perubahan berhasil disimpan.</p>
            </div>
            <div class="modal-footer">
                <button id="successOk" class="btn btn-primary-modal">OK</button>
            </div>
        </div>
    </div>

    <!-- PRINT LOADING OVERLAY -->
<!--<div id="printLoadingOverlay"
     style="display:none; position:fixed; inset:0; background:rgba(2,6,23,0.6); z-index:2000; align-items:center; justify-content:center;">
        <div style="width:360px; background:#fff; border-radius:12px; padding:22px; text-align:center; box-shadow:0 20px 60px rgba(2,6,23,0.25);">
            <div style="font-weight:700; margin-bottom:12px;">Mencetak...</div>
            <div style="height:120px; display:flex; align-items:center; justify-content:center;">
                <div id="printLoaderIcon" style="font-size:60px; color:#4f46e5; transition:opacity .2s;"></div>
            </div>
            <div style="margin-top:8px; color:#64748b;">Tunggu sebentar, menyiapkan dokumen untuk dicetak.</div>
            <div style="display:flex; gap:10px; justify-content:center; margin-top:14px;">
                <div class="small-logo" data-icon="fa-plane" style="width:40px;height:40px;border-radius:8px;display:flex;align-items:center;justify-content:center;background:#f1f5f9;color:#0f172a;"></div>
                <div class="small-logo" data-icon="fa-train" style="width:40px;height:40px;border-radius:8px;display:flex;align-items:center;justify-content:center;background:#f1f5f9;color:#0f172a;"></div>
                <div class="small-logo" data-icon="fa-bed" style="width:40px;height:40px;border-radius:8px;display:flex;align-items:center;justify-content:center;background:#f1f5f9;color:#0f172a;"></div>
                <div class="small-logo" data-icon="fa-suitcase" style="width:40px;height:40px;border-radius:8px;display:flex;align-items:center;justify-content:center;background:#f1f5f9;color:#0f172a;"></div>
            </div>
        </div>
    </div>

    <script>
    (function(){
        var icons = ['fa-plane','fa-train','fa-bed','fa-suitcase'];
        var current = 0;
        var intervalId = null;

        function showPrintLoader(){
            var $overlay = $('#printLoadingOverlay');
            $('#printLoaderIcon').attr('class', 'fa ' + icons[current]);
                // set initial small logos immediately
                $('.small-logo').each(function(i){
                    var idx = (current + i) % icons.length;
                    $(this).html('<i class="fa ' + icons[idx] + '"></i>');
                });
                $overlay.stop(true,true).css('opacity',0).show().animate({opacity:1},160);
            // animate central icon by cycling
            intervalId = setInterval(function(){
                current = (current + 1) % icons.length;
                    $('#printLoaderIcon').fadeOut(120, function(){
                        $(this).attr('class', 'fa ' + icons[current]).fadeIn(120);
                    });
                // animate small logos glow
                $('.small-logo').each(function(i){
                    var idx = (current + i) % icons.length;
                    $(this).html('<i class="fa ' + icons[idx] + '"></i>');
                });
            }, 450);
        }

        function hidePrintLoader(){
            $('#printLoadingOverlay').stop(true,true).animate({opacity:0},120, function(){ $(this).hide(); });
            if(intervalId) { clearInterval(intervalId); intervalId = null; }
        }

        // intercept anchor print actions and fetch the file to detect download completion
        $(document).on('click', 'a.print-action', function(e){
            e.preventDefault();
            var href = $(this).attr('href');
            showPrintLoader();

            fetch(href, { method: 'GET', credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function(response){
                if(!response.ok) throw new Error('Server error');
                var disposition = response.headers.get('content-disposition') || '';
                var filename = 'download.pdf';
                var match = disposition.match(/filename\*?=(?:UTF-8'')?"?([^";]+)"?/i);
                if(match && match[1]) filename = decodeURIComponent(match[1]);
                return response.blob().then(function(blob){ return { blob: blob, filename: filename }; });
            })
            .then(function(result){
                var url = window.URL.createObjectURL(result.blob);
                var a = document.createElement('a');
                a.href = url;
                a.download = result.filename || 'download.pdf';
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
                hidePrintLoader();
                $('#successMessage').text('Berhasil di Download');
                $('#successModal').fadeIn(200);
            })
            .catch(function(err){
                hidePrintLoader();
                alert('Terjadi kesalahan saat mengunduh.');
            });
        });

        // intercept forms that call print (action contains 'print') and submit via fetch to capture file
        $(document).on('submit', "form[action*='print']", function(e){
            e.preventDefault();
            var $form = $(this);
            var action = $form.attr('action');
            var method = ($form.attr('method') || 'GET').toUpperCase();
            showPrintLoader();

            var fetchOptions = { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } };

            if(method === 'GET'){
                var params = new URLSearchParams(new FormData($form)).toString();
                action = action + (action.indexOf('?') === -1 ? '?' : '&') + params;
                fetchOptions.method = 'GET';
            } else {
                fetchOptions.method = method;
                fetchOptions.body = new FormData($form[0]);
                var token = $('meta[name="csrf-token"]').attr('content');
                if(token) fetchOptions.headers['X-CSRF-TOKEN'] = token;
            }

            fetch(action, fetchOptions)
            .then(function(response){
                if(!response.ok) throw new Error('Server error');
                var disposition = response.headers.get('content-disposition') || '';
                var filename = 'download.pdf';
                var match = disposition.match(/filename\*?=(?:UTF-8'')?"?([^";]+)"?/i);
                if(match && match[1]) filename = decodeURIComponent(match[1]);
                return response.blob().then(function(blob){ return { blob: blob, filename: filename }; });
            })
            .then(function(result){
                var url = window.URL.createObjectURL(result.blob);
                var a = document.createElement('a');
                a.href = url;
                a.download = result.filename || 'download.pdf';
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
                hidePrintLoader();
                $('#successMessage').text('Berhasil di Download');
                $('#successModal').fadeIn(200);
            })
            .catch(function(err){
                hidePrintLoader();
                alert('Terjadi kesalahan saat mengunduh.');
            });
        });

        // also intercept button with print icon — show loader if standalone
        $(document).on('click', 'button:has(i.fa-print), .btn-print', function(e){
            var $form = $(this).closest('form');
            if(!$form.length){
                showPrintLoader();
                setTimeout(function(){ hidePrintLoader(); $('#successMessage').text('Berhasil di Download'); $('#successModal').fadeIn(200); }, 1500);
            }
        });
    })();
    </script>-->

    </html>