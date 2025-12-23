@extends('master')

@section('konten')
<div class="elegant-container">
    <div class="card card-elegant">

        {{-- Header --}}
        <h2 class="page-title">Tambah Data Pelanggan</h2>

        {{-- Alert Error --}}
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        {{-- FORM (ACTION & ROUTE TIDAK DIUBAH) --}}
        <form action="{{ url('/customer') }}" method="post">
            @csrf

            <div class="elegant-form-group">
                <label>Gender</label>
                <input 
                    type="text" 
                    name="gender"
                    class="form-control elegant-form-control {{ $errors->has('gender') ? 'is-invalid' : '' }}"
                    placeholder="Masukkan Gender Pelanggan"
                >
                <span class="text-danger">{{ $errors->first('gender') }}</span>
            </div>

            <div class="elegant-form-group">
                <label>Nama Booker</label>
                <input 
                    type="text" 
                    name="booker"
                    class="form-control elegant-form-control {{ $errors->has('booker') ? 'is-invalid' : '' }}"
                    placeholder="Masukkan Nama Lengkap"
                >
                <span class="text-danger">{{ $errors->first('booker') }}</span>
            </div>

            <div class="elegant-form-group">
                <label>Nama Perusahaan</label>
                <input 
                    type="text" 
                    name="company"
                    class="form-control elegant-form-control {{ $errors->has('company') ? 'is-invalid' : '' }}"
                    placeholder="Masukkan Nama Perusahaan"
                >
                <span class="text-danger">{{ $errors->first('company') }}</span>
            </div>

            <div class="elegant-form-group">
                <label>Phone</label>
                <input 
                    type="text" 
                    name="phone"
                    class="form-control elegant-form-control {{ $errors->has('phone') ? 'is-invalid' : '' }}"
                    placeholder="Masukkan Nomor Telepon (Maks. 13 angka)"
                >
                <span class="text-danger">{{ $errors->first('phone') }}</span>
            </div>

            <div class="elegant-form-group">
                <label>Alamat</label>
                <input 
                    type="text" 
                    name="alamat"
                    class="form-control elegant-form-control {{ $errors->has('alamat') ? 'is-invalid' : '' }}"
                    placeholder="Masukkan Alamat Pelanggan"
                >
                <span class="text-danger">{{ $errors->first('alamat') }}</span>
            </div>

            <div class="elegant-form-group">
                <label>Email</label>
                <input 
                    type="text" 
                    name="email"
                    class="form-control elegant-form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                    placeholder="Masukkan Email Booker"
                >
                <span class="text-danger">{{ $errors->first('email') }}</span>
            </div>

            <div class="elegant-form-group">
                <label>Payment</label>
                <select 
                    name="payment"
                    class="form-control elegant-form-control select {{ $errors->has('payment') ? 'is-invalid' : '' }}"
                >
                    <option value="" disabled selected>Pilih Pembayaran Yang Akan Digunakan</option>
                    <option value="Cash">Cash</option>
                    <option value="Credit">Credit</option>
                </select>
                <span class="text-danger">{{ $errors->first('payment') }}</span>
            </div>

            {{-- Tombol --}}
            <div class="modal-footer">
                <button class="btn-primary btn-sm">
                    Simpan
                </button>
            </div>

        </form>

    </div>
</div>
@endsection
