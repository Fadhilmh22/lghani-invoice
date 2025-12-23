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
                <input type="password" name="password" placeholder="Password" required>
                <i class="fas fa-lock input-icon"></i>
            </div>

            <button type="submit" class="btn-login">Login</button>
        </form>
    </div>

    <p class="footer">© LGhani Tour & Travel 2025. All rights reserved.</p>
</div>

</body>
</html>
