<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>LGhani Tour & Travel</title>
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
            transition: all 0.3s ease;
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
            transition: width 0.3s ease, transform 0.3s ease;
            z-index: 1000;
        }
        
        body.sidebar-minimized .sidebar {
            width: var(--sidebar-minimized-width);
            padding: 24px 10px;
        }

        body.sidebar-minimized .sidebar__brand img {
            width: 40px;
            margin: 0 auto;
        }

        body.sidebar-minimized .sidebar__user div:last-child {
            display: none;
        }

        body.sidebar-minimized .sidebar__menu span,
        body.sidebar-minimized .sidebar__footer span {
            display: none;
        }
        
        body.sidebar-minimized .menu-label {
            display: none;
        }
        
        body.sidebar-minimized .menu-link {
            justify-content: center;
            padding: 10px 0;
        }

        body.sidebar-minimized .menu-link i {
            margin: 0;
        }
        
        body.sidebar-minimized .menu-group-header {
            justify-content: center;
            padding: 10px 0; 
        }

        body.sidebar-minimized .menu-group-header .chevron {
            display: none;
        }

        body.sidebar-minimized .submenu {
            padding-left: 0;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            flex: 1;
            min-height: 100vh;
            background: #f5f7fb;
            padding: 24px 30px 40px;
            transition: margin-left 0.3s ease;
        }

        body.sidebar-minimized .main-content {
            margin-left: var(--sidebar-minimized-width);
        }

        .sidebar__brand img { 
          text-align: center;
          width: 250px;
          display: flex;
          justify-content: center;
          align-items: center;
          height: 150px;
          margin-bottom: 10px;
        }

        .sidebar__user { display: flex; align-items: center; gap: 12px; margin: 24px 0; padding: 12px; background: rgba(255, 255, 255, 0.08); border-radius: 16px; justify-content: flex-start; text-align: left; border:none; cursor:pointer; width:100%; }
        .avatar { width: 46px; height: 46px; border-radius: 12px; background: rgba(255, 255, 255, 0.15); display: flex; align-items: center; justify-content: center; font-size: 18px; }
        .user-name { margin: 0; font-weight: 600; color:#fff; }
        .user-role { margin: 0; font-size: 12px; color: #cbd5f5; }
        .menu-label { font-size: 14px !important; text-transform: uppercase; letter-spacing: 0.08em; margin: 18px 0 8px; color: #94a3b8; font-weight: 600; }
        .menu-link { display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: 12px; color: #e2e8f0; text-decoration: none; margin-bottom: 6px; transition: background 0.2s; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size: 16px; line-height: 1.4; }
        .menu-link i { width: 20px; text-align: center; }
        .menu-link:hover, .menu-link.active { background: rgba(255, 255, 255, 0.15); color: #fff; text-decoration: none; }
        .sidebar__footer { margin-top: 24px; border-top: 1px solid rgba(148, 163, 184, 0.3); padding-top: 12px; }
        .menu-link.logout { color: #fca5a5; }

        /* --- HEADER & SALDO STYLE (NEW & CLEAN) --- */
        .main-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fff;
            padding: 12px 20px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            margin-bottom: 25px;
        }

        .toggle-btn { border: none; background: #f1f5f9; padding: 10px 12px; border-radius: 12px; cursor: pointer; transition: 0.2s; }
        .toggle-btn:hover { background: #e2e8f0; }

        .airline-balances {
            display: grid; /* Gunakan Grid, bukan Flex lagi */
            grid-template-columns: repeat(3, 1fr); /* PAKSA jadi 3 kolom sama rata */
            gap: 10px; /* Jarak antar kotak */
            flex: 1; /* Agar mengambil ruang yang tersedia */
            margin: 0 15px;
        }

.balance-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 6px 10px; /* Diperkecil dikit biar muat */
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            width: 100%; /* Biar ngikutin lebar kolom grid */
        }

        .balance-card:hover {
            border-color: #4f46e5;
            background: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.1);
        }

        .balance-actions {
            display: flex;
            align-items: center;
            padding-left: 10px;
        }

        .balance-icon {
            width: 35px;
            height: 35px;
            color: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
        }

        .bg-lion { background: linear-gradient(135deg, #ef4444, #b91c1c); }
        .bg-other { background: linear-gradient(135deg, #4f46e5, #3b82f6); }

        .balance-info { display: flex; flex-direction: column; }
        .balance-label { font-size: 10px; text-transform: uppercase; color: #64748b; font-weight: 600; letter-spacing: 0.05em; margin-bottom: 2px; }
        .balance-value { font-size: 13px; font-weight: 700; color: #1e293b; }
        .text-danger-custom { color: #dc2626 !important; }

        .btn-add-balance {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: #4f46e5;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.2s;
            margin-left: 5px;
        }
        .btn-add-balance:hover { transform: scale(1.1); color: white; background: #4338ca; }

        /* --- MENU GROUP & SUBMENU --- */
        .menu-group-header { display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; border-radius: 10px; color: #e2e8f0; cursor: pointer; margin-bottom: 4px; transition: background 0.2s; font-size: 16px; }
        .menu-group-header i:first-child { margin-right: 10px; width: 18px; text-align: center; }
        .menu-group-header .chevron { transition: transform 0.2s; font-size: 10px; }
        .menu-group-header.open .chevron { transform: rotate(90deg); }
        .submenu { padding-left: 8px; margin-bottom: 6px; display: none; }
        .submenu.open { display: block; }

        .header-right-actions {
            display: flex;
            align-items: center;
            padding-left: 10px;
        }

        /* --- RESPONSIVE --- */
        @media (max-width: 1200px) {
            .sidebar { transform: translateX(-100%); z-index: 1000; }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; padding: 20px 16px; }
            .airline-balances {
                grid-template-columns: repeat(2, 1fr); /* Layar sedang jadi 2 kolom */
            }
        }

        /* --- MODALS STYLE --- */
        .btn-primary-modal { background: linear-gradient(135deg,#4a6cf7,#7c3aed); color: #fff; border: none; padding: 8px 14px; border-radius: 10px; font-weight: 600; }
        .btn-secondary-modal { background: #f1f5f9; color: #475569; border: none; padding: 8px 14px; border-radius: 10px; font-weight: 600; }
        .btn-danger-modal { background: #fee2e2; color: #ef4444; border: none; padding: 8px 14px; border-radius: 10px; font-weight: 600; }
        .custom-modal-overlay { position: fixed; inset: 0; display: flex; align-items: center; justify-content: center; background: rgba(2,6,23,0.35); z-index: 1200; display: none; }
        .custom-modal-content { width: 640px; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 20px 60px rgba(2,6,23,0.2); border: none; }
        .modal-header { padding: 14px 18px; font-weight: 700; border-bottom: 1px solid rgba(2,6,23,0.04); font-size: 16px; }
        .modal-header-danger { padding: 14px 18px; font-weight: 700; background: #fee2e2; color: #b91c1c; font-size: 16px; }
        .modal-body { padding: 16px 18px; font-size: 14px; color: #475569; }
        .modal-footer { padding: 12px 18px; display:flex; justify-content:flex-end; gap:8px; border-top: 1px solid rgba(2,6,23,0.04); }
    </style>
</head>

<body>
    <div class="layout-wrapper">
        <aside class="sidebar" id="sidebar">
            <button id="sidebarToggle" class="toggle-btn sidebar-toggle"><i class="fa fa-bars"></i></button>
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
                @if(in_array(auth()->user()->role, ['Admin','Staff','Owner']))
                <div class="menu-group">
                    <div class="menu-group-header" data-toggle="submenu" data-target="#ticketingSubmenu">
                        <div><i class="fa fa-ticket-alt"></i> <span>Ticketing Invoices</span></div>
                        <i class="fa fa-chevron-right chevron"></i>
                    </div>
                    <div class="submenu" id="ticketingSubmenu">
                        <a href="{{ url('/airline') }}" class="menu-link {{ request()->is('airline*') ? 'active' : '' }}"><i class="fa fa-plane"></i> <span>Airlines</span></a>
                        <a href="{{ route('airports.index') }}" class="menu-link {{ Request::is('airports*') ? 'active' : '' }}"><i class="fa fa-map-marker"></i> <span>Airports</span></a>
                        <a href="{{ route('ticket.index') }}" class="menu-link {{ request()->is('ticket') ? 'active' : '' }}"><i class="fa fa-plane-departure"></i> <span>Ticket Issued</span></a>
                        <a href="{{ route('invoice.create') }}" class="menu-link {{ request()->is('invoice/new') ? 'active' : '' }}"><i class="fa fa-pencil-alt"></i> <span>Create Invoice</span></a>
                        <a href="{{ route('invoice.index') }}" class="menu-link {{ request()->is('invoice') ? 'active' : '' }}"><i class="fa fa-file-invoice"></i> <span>Invoice Ticketing</span></a>
                    </div>
                </div>

                <div class="menu-group">
                    <div class="menu-group-header" data-toggle="submenu" data-target="#hotelSubmenu">
                        <div><i class="fa fa-hotel"></i> <span>Hotel Invoices</span></div>
                        <i class="fa fa-chevron-right chevron"></i>
                    </div>
                    <div class="submenu" id="hotelSubmenu">
                        <a href="{{ url('/hotel') }}" class="menu-link"><i class="fa fa-hotel"></i> <span>Hotel</span></a>
                        <a href="{{ url('/room') }}" class="menu-link"><i class="fa fa-bed"></i> <span>Hotel Room</span></a>
                        <a href="{{ url('/hotel-voucher') }}" class="menu-link"><i class="fa fa-gift"></i> <span>Voucher Hotel</span></a>
                        <a href="{{ url('/hotel-invoice') }}" class="menu-link"><i class="fa fa-file-contract"></i> <span>Invoice Hotel</span></a>
                    </div>
                </div>
                @endif

                @if (auth()->user()->role == "Owner")
                <div class="menu-group">
                    <div class="menu-group-header" data-toggle="submenu" data-target="#reportSubmenu">
                        <div><i class="fa fa-chart-line"></i> <span>Reports</span></div>
                        <i class="fa fa-chevron-right chevron"></i>
                    </div>
                    <div class="submenu" id="reportSubmenu">
                        <a href="{{ url('/report') }}" class="menu-link"> <i class="fa fa-plane"></i> <span>Laporan Ticketing</span></a>
                        <a href="{{ url('/report/hotel') }}" class="menu-link"><i class="fa fa-clipboard-list"></i> <span>Laporan Hotel</span></a>
                        <a href="{{ url('/report/piutang') }}" class="menu-link"><i class="fa fa-briefcase"></i> <span>Laporan Piutang</span></a>
                    </div>
                </div>
                @endif
            </nav>

            <div class="sidebar__footer">
                @if (auth()->user()->role == "Owner")
                <a href="{{ route('register') }}" class="menu-link"><i class="fa fa-user-plus"></i> <span>Add Account</span></a>
                @endif
                @if(in_array(auth()->user()->role,['Admin','Staff','Owner']))
                <a href="{{ route('topup.index') }}" class="menu-link"><i class="fa fa-wallet"></i> <span>Top Up Airlines</span></a>
                @endif
                <a href="{{ route('actionlogout') }}" id="logoutButton" class="menu-link logout" data-logout-url="{{ route('actionlogout') }}"><i class="fa fa-power-off"></i> <span>Log Out</span></a>
            </div>
        </aside>

        <main class="main-content">
            <header class="main-header">


            <div class="airline-balances">
                @php
                    $allAirlines = \App\Models\Airlines::all();
                    $lionGroupCodes = ['JT', 'IU', 'IW', 'ID'];
                    $lionMain = $allAirlines->where('airlines_code', 'JT')->first();
                    $lionBalance = $lionMain ? $lionMain->balance : 0;
                @endphp

                <div class="balance-card" title="Lion Group">
                    <div class="balance-icon bg-lion">LG</div>
                    <div class="balance-info">
                        <span class="balance-label">Lion Group</span>
                        <span class="balance-value {{ $lionBalance < 0 ? 'text-danger-custom' : '' }}">
                            Rp {{ number_format($lionBalance, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                @foreach($allAirlines->whereNotIn('airlines_code', $lionGroupCodes) as $ab)
                    <div class="balance-card" title="{{ $ab->airlines_name }}">
                        <div class="balance-icon bg-other">{{ $ab->airlines_code }}</div>
                        <div class="balance-info">
                            <span class="balance-label">{{ $ab->airlines_name }}</span>
                            <span class="balance-value {{ $ab->balance < 0 ? 'text-danger-custom' : '' }}">
                                Rp {{ number_format($ab->balance, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="header-right-actions">
                @if(in_array(auth()->user()->role,['Admin','Staff','Owner']))
                    <a href="{{ route('topup.index') }}" class="btn-add-balance" title="Top Up Saldo">
                        <i class="fa fa-plus"></i>
                    </a>
                @endif
            </div>
        </header>
            <section class="content-area">@yield('konten')</section>
        </main>
    </div>

    <div id="logoutConfirmModal" class="custom-modal-overlay" style="display: none;">
        <div class="custom-modal-content">
            <div class="modal-header-danger"><i class="fa fa-exclamation-triangle"></i> Konfirmasi Logout</div>
            <div class="modal-body">Apakah Anda yakin ingin keluar dari aplikasi sekarang?</div>
            <div class="modal-footer">
                <button id="cancelLogoutBtn" class="btn btn-secondary-modal">Batal</button>
                <button id="confirmLogoutBtn" class="btn btn-danger-modal">Ya, Logout</button>
            </div>
        </div>
    </div>

    <div id="profileModal" class="custom-modal-overlay" style="display: none;">
        <div class="custom-modal-content">
            <div class="modal-header"><i class="fa fa-user"></i> Edit Profile</div>
            <div class="modal-body">
                <form id="profileForm" method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    <div id="profileErrors" class="alert alert-danger" style="display:none;margin-bottom:12px;"></div>
                    <div class="form-group"><label>Name</label><input type="text" id="profile_name" name="name" class="form-control" value="{{ Auth::user()->name }}" required></div>
                    <div class="form-group"><label>Email</label><input type="email" id="profile_email" name="email" class="form-control" value="{{ Auth::user()->email }}" required></div>
                    <div class="form-group"><label>New Password</label><input type="password" id="profile_password" name="password" class="form-control"></div>
                    <div class="form-group"><label>Confirm Password</label><input type="password" id="profile_password_confirmation" name="password_confirmation" class="form-control"></div>
                </form>
            </div>
            <div class="modal-footer"><button id="cancelProfileBtn" class="btn btn-secondary-modal">Batal</button><button id="saveProfileBtn" class="btn btn-primary-modal">Simpan</button></div>
        </div>
    </div>

    <div id="confirmSaveModal" class="custom-modal-overlay" style="display:none;"><div class="custom-modal-content"><div class="modal-header">Konfirmasi Simpan</div><div class="modal-body"><p>Yakin ingin menyimpan perubahan profile?</p></div><div class="modal-footer"><button id="confirmCancel" class="btn btn-secondary-modal">Batal</button><button id="confirmSave" class="btn btn-primary-modal">Ya, Simpan</button></div></div></div>
    <div id="successModal" class="custom-modal-overlay" style="display:none;"><div class="custom-modal-content"><div class="modal-header">Berhasil</div><div class="modal-body"><p id="successMessage">Perubahan berhasil disimpan.</p></div><div class="modal-footer"><button id="successOk" class="btn btn-primary-modal">OK</button></div></div></div>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script>
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        function isDesktop() { return window.innerWidth > 992; }

        document.getElementById('sidebarToggle').addEventListener('click', function () {
            if (isDesktop()) { document.body.classList.toggle('sidebar-minimized'); } 
            else { document.getElementById('sidebar').classList.toggle('open'); }
        });

        document.querySelectorAll('[data-toggle="submenu"]').forEach(function (header) {
            header.addEventListener('click', function () {
                var target = document.querySelector(header.getAttribute('data-target'));
                if (!target) return;
                var isOpen = target.classList.contains('open');
                document.querySelectorAll('.submenu').forEach(function (sm) { sm.classList.remove('open'); });
                document.querySelectorAll('.menu-group-header').forEach(function (mh) { mh.classList.remove('open'); });
                if (!isOpen) { target.classList.add('open'); header.classList.add('open'); }
            });
        });

        $('#logoutButton').on('click', function(e) { e.preventDefault(); $('#logoutConfirmModal').fadeIn(150); });
        $('#cancelLogoutBtn').on('click', function() { $('#logoutConfirmModal').fadeOut(150); });
        $('#confirmLogoutBtn').on('click', function() { window.location.href = $(this).closest('.custom-modal-overlay').prevAll().find('#logoutButton').data('logout-url') || $('#logoutButton').data('logout-url'); });

        $('#profileToggle').on('click', function(){ $('#profileModal').fadeIn(150); });
        $('#cancelProfileBtn').on('click', function(){ $('#profileModal').fadeOut(150); });
        $('#successOk').on('click', function(){ $('#successModal').fadeOut(150); });
        $('#saveProfileBtn').on('click', function(){ $('#confirmSaveModal').fadeIn(120); });
        $('#confirmCancel').on('click', function(){ $('#confirmSaveModal').fadeOut(120); });
        $('#confirmSave').on('click', function(){
            $.ajax({
                url: $('#profileForm').attr('action'),
                method: 'POST',
                data: $('#profileForm').serialize(),
                success: function(res) {
                    $('#confirmSaveModal').hide();
                    $('#profileModal').fadeOut(150);
                    $('#successMessage').text(res.message || 'Profil berhasil diperbarui.');
                    $('#successModal').fadeIn(150);
                    setTimeout(() => location.reload(), 1000);
                }
            });
        });
    </script>

    <div id="printLoadingOverlay" style="display:none; position:fixed; inset:0; background:rgba(2,6,23,0.6); z-index:2000; align-items:center; justify-content:center;">
        <div style="width:360px; background:#fff; border-radius:12px; padding:22px; text-align:center;">
            <div style="font-weight:700; margin-bottom:12px;">Mencetak...</div>
            <div id="printLoaderIcon" style="font-size:60px; color:#4f46e5;"></div>
            <div style="margin-top:8px; color:#64748b;">Tunggu sebentar, menyiapkan dokumen.</div>
        </div>
    </div>
</body>
</html>