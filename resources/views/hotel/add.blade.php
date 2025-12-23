@extends('master')

@section('konten')
<div class="elegant-container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="page-title">Tambah Data Hotel</h1>
        </div>
    </div>
    
    <div class="card-elegant">
        <div class="card-body">
            @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
            @endif

            <form action="{{ url('/hotel') }}" method="post">
                @csrf
                <div class="elegant-form-group">
                    <label for="hotel_code">Kode Hotel</label>
                    <input type="text" name="hotel_code" id="hotel_code" class="elegant-form-control {{ $errors->has('hotel_code') ? 'is-invalid':'' }}" placeholder="Masukkan Kode Hotel" value="{{ old('hotel_code') }}">
                    <span class="text-danger">{{ $errors->first('hotel_code') }}</span>
                </div>
                
                <div class="elegant-form-group">
                    <label for="hotel_name">Nama Hotel</label>
                    <input type="text" name="hotel_name" id="hotel_name" class="elegant-form-control {{ $errors->has('hotel_name') ? 'is-invalid':'' }}" placeholder="Masukkan Nama Hotel" value="{{ old('hotel_name') }}">
                    <span class="text-danger">{{ $errors->first('hotel_name') }}</span>
                </div>
                
                <div class="elegant-form-group">
                    <label for="region">Region Hotel</label>
                    <input type="text" name="region" id="region" class="elegant-form-control {{ $errors->has('region') ? 'is-invalid':'' }}" placeholder="Masukkan Region Hotel" value="{{ old('region') }}">
                    <span class="text-danger">{{ $errors->first('region') }}</span>
                </div>
                
                <div class="elegant-form-group">
                    <label for="address">Alamat Hotel</label>
                    <input type="text" name="address" id="address" class="elegant-form-control {{ $errors->has('address') ? 'is-invalid':'' }}" placeholder="Masukkan Alamat Hotel" value="{{ old('address') }}">
                    <span class="text-danger">{{ $errors->first('address') }}</span>
                </div>
                
                <div class="elegant-form-group">
                    <label for="phone">Phone</label>
                    <input type="text" name="phone" id="phone" class="elegant-form-control {{ $errors->has('phone') ? 'is-invalid':'' }}" placeholder="Masukkan Nomor Telepon (Maks. 13 angka)" value="{{ old('phone') }}">
                    <span class="text-danger">{{ $errors->first('phone') }}</span>
                </div>
                
                <div class="elegant-form-group">
                    <label for="fax">Fax</label>
                    <input type="text" name="fax" id="fax" class="elegant-form-control {{ $errors->has('fax') ? 'is-invalid':'' }}" placeholder="Masukkan Fax Hotel" value="{{ old('fax') }}">
                    <span class="text-danger">{{ $errors->first('fax') }}</span>
                </div>
                
                <div class="elegant-form-group">
                    <button type="submit" class="btn-primary btn-sm">
                        <i class="fa fa-save"></i> Simpan
                    </button>
                    <a href="{{ url('/hotel') }}" class="btn-primary btn-sm" style="background-color: #6b7280; margin-left: 10px; text-decoration: none; display: inline-block;">
                        <i class="fa fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
