@extends('master')

@section('konten')
<div class="container" style="max-width:720px;">
    <h3>Edit Profile</h3>
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <form method="POST" action="{{ route('profile.update') }}">
        @csrf
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
        </div>
        <div class="form-group">
            <label>New Password (leave blank to keep)</label>
            <input type="password" name="password" class="form-control">
        </div>
        <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control">
        </div>
        <div style="margin-top:12px">
            <button class="btn btn-primary">Save</button>
            <a href="{{ route('home') }}" class="btn btn-default">Cancel</a>
        </div>
    </form>
</div>
@endsection
