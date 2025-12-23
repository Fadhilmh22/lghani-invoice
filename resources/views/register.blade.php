@extends('master')

@section('konten')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css">
<style>
    .register-wrap { max-width:520px; margin:30px auto; }
    .register-logo { text-align:center; margin-bottom:8px; }
    .register-actions { display:flex; gap:8px; align-items:center; justify-content:flex-end; margin-top:12px; }
    .elegant-card-body .text-danger { margin-top:6px; }
</style>

<div class="elegant-container register-wrap" style="font-family: 'poppins', sans-serif;">
    <div class="card-elegant">
        <div class="card-body">
            <div class="text-center" style="margin-bottom:20px;">
                <img src="{{ asset('logo-lghani.png') }}" alt="Logo" style="max-width:320px; height:auto;">
            </div>

            @if(session('message'))
            <div class="alert-info">
                {{ session('message') }}
            </div>
            @endif

            <form action="{{ route('actionregister') }}" method="post">
                @csrf

                <div class="elegant-form-group">
                    <label><i class="fa fa-envelope"></i> Email</label>
                    <input type="email" name="email" class="elegant-form-control" placeholder="Email" required>
                    @if($errors->has('email')) <span class="text-danger">{{ $errors->first('email') }}</span> @endif
                </div>

                <div class="elegant-form-group">
                    <label><i class="fa fa-user"></i> Nama Lengkap</label>
                    <input type="text" name="username" class="elegant-form-control" placeholder="Nama Lengkap" required>
                    @if($errors->has('username')) <span class="text-danger">{{ $errors->first('username') }}</span> @endif
                </div>

                <div class="elegant-form-group">
                    <label><i class="fa fa-key"></i> Password</label>
                    <input type="password" name="password" class="elegant-form-control" placeholder="Password" required>
                    @if($errors->has('password')) <span class="text-danger">{{ $errors->first('password') }}</span> @endif
                </div>

                <div class="elegant-form-group">
                    <label><i class="fa fa-address-book"></i> Role</label>
                    <input type="text" name="role" class="elegant-form-control" value="Staff" readonly>
                </div>

                <div class="form-group" style="display:flex; gap:8px; justify-content:flex-end; align-items:center; margin-top:8px;">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary-modal" style="display:inline-flex; align-items:center; gap:8px; padding:10px 20px; border-radius:8px; font-weight:600;"><i class="fa fa-arrow-left"></i> Kembali</a>
                    <button type="submit" class="btn btn-primary-elegant"><i class="fa fa-user"></i> Register</button>
                </div>

                <hr>
                <p class="text-center">Sudah punya akun silahkan <a href="{{ route('actionlogout') }}">Login Disini!</a></p>
            </form>
        </div>
    </div>
</div>

@endsection