<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - LGhani Tour & Travel</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('logo-lghani.png') }}">
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('logo-lghani.png') }}">
    <link rel="icon" type="image/png" sizes="128x128" href="{{ asset('logo-lghani.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('logo-lghani.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('logo-lghani.png') }}">

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inclusive+Sans:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@500;600&display=swap" rel="stylesheet">
    
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('login.css') }}">
</head>

<body>

<div class="background"></div>

<div class="page-layout">
    <div class="login-container">

        <img src="{{ asset('logo-lghani.png') }}" class="logo">

        <form action="{{ route('actionlogin') }}" method="POST">
            @csrf

            @if(session('error'))
                <div class="alert-box">
                    {{ session('error') }}
                </div>
            @endif

            <div class="input-group">
                <input type="email" name="email" placeholder="Email" required>
                <i class="fas fa-user input-icon"></i>
            </div>

            <div class="input-group">
                <div class="password-wrapper" style="position: relative; width: 100%;">
                    <input type="password" name="password" id="password-field" placeholder="Password" required style="width: 100%;">
                    <i class="fas fa-lock input-icon"></i>
                </div>
                
                <div id="caps-warning" style="display: none; color: #ff4d4d; font-size: 11px; font-weight: 600; margin-top: 5px; position: absolute; bottom: -18px; left: 0;">
                    <i class="fas fa-exclamation-triangle"></i> Caps Lock is ON
                </div>
            </div>

            <button type="submit" class="btn-login">Login</button>
        </form>
    </div>

    <p class="footer">© LGhani Tour & Travel 2025. All rights reserved.</p>
</div>

</body>

<script>
    const passwordField = document.getElementById('password-field');
    const capsWarning = document.getElementById('caps-warning');

    function checkCapsLock(event) {
        if (event.getModifierState('CapsLock')) {
            capsWarning.style.display = 'block';
        } else {
            capsWarning.style.display = 'none';
        }
    }

    // Cek saat mengetik
    passwordField.addEventListener('keyup', checkCapsLock);
    
    // Cek saat klik input (kalau capslock sudah nyala dari awal)
    passwordField.addEventListener('click', checkCapsLock);
    
    // Sembunyikan jika kursor keluar dari input password (opsional)
    passwordField.addEventListener('blur', function() {
        capsWarning.style.display = 'none';
    });
</script>
</html>
