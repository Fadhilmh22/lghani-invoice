@extends('master')

@section('konten')
<div class="elegant-container">
    <div class="card card-elegant">

        {{-- Header --}}
        <h2 class="page-title">Ubah Data Pelanggan</h2>

        {{-- Alert Error --}}
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        {{-- FORM (ACTION & METHOD TIDAK DIUBAH) --}}
        <form action="{{ url('/customer/' . $customer->id) }}" method="post">
            @csrf
            <input type="hidden" name="_method" value="PUT">

            <div class="elegant-form-group">
                <label>Gender</label>
                <input 
                    type="text" 
                    name="gender"
                    value="{{ $customer->gender }}"
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
                    value="{{ $customer->booker }}"
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
                    value="{{ $customer->company }}"
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
                    value="{{ $customer->phone }}"
                    class="form-control elegant-form-control {{ $errors->has('phone') ? 'is-invalid' : '' }}"
                >
                <span class="text-danger">{{ $errors->first('phone') }}</span>
            </div>

            <div class="elegant-form-group">
                <label>Alamat</label>
                <input 
                    type="text" 
                    name="alamat"
                    value="{{ $customer->alamat }}"
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
                    value="{{ $customer->email }}"
                    class="form-control elegant-form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                >
                <span class="text-danger">{{ $errors->first('email') }}</span>
            </div>

            <div class="elegant-form-group">
                <label>Payment</label>
                <select 
                    name="payment"
                    class="form-control elegant-form-control {{ $errors->has('payment') ? 'is-invalid' : '' }}"
                >
                    <option value="" disabled>Pilih Pembayaran Yang Akan Digunakan</option>
                    <option value="Cash" {{ $customer->payment === 'Cash' ? 'selected' : '' }}>Cash</option>
                    <option value="Credit" {{ $customer->payment === 'Credit' ? 'selected' : '' }}>Credit</option>
                </select>
                <span class="text-danger">{{ $errors->first('payment') }}</span>
            </div>

            {{-- Tombol --}}
            <div class="modal-footer">
                <button class="btn-primary btn-sm">
                    Ubah
                </button>
            </div>

        </form>

    </div>
</div>
@endsection
