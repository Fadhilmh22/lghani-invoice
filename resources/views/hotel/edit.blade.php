@extends('master')

@section('konten')
<div class="elegant-container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="page-title">Ubah Data Hotel</h1>
        </div>
    </div>
    
    <div class="card-elegant">
        <div class="card-body">
            @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
            @endif

            <form action="{{ url('/hotel/' . $hotel->id) }}" method="post">
                @csrf
                <input type="hidden" name="_method" value="PUT" class="form-control">
                
                <div class="elegant-form-group">
                    <label for="hotel_code">Kode Hotel</label>
                    <input type="text" name="hotel_code" id="hotel_code" class="elegant-form-control {{ $errors->has('hotel_code') ? 'is-invalid':'' }}" value="{{ $hotel->hotel_code }}" placeholder="Masukkan Kode Hotel">
                    <span class="text-danger">{{ $errors->first('hotel_code') }}</span>
                </div>
                
                <div class="elegant-form-group">
                    <label for="hotel_name">Nama Hotel</label>
                    <input type="text" name="hotel_name" id="hotel_name" class="elegant-form-control {{ $errors->has('hotel_name') ? 'is-invalid':'' }}" value="{{ $hotel->hotel_name }}" placeholder="Masukkan Nama Hotel">
                    <span class="text-danger">{{ $errors->first('hotel_name') }}</span>
                </div>
                
                <div class="elegant-form-group">
                    <label for="region">Region Hotel</label>
                    <input type="text" name="region" id="region" class="elegant-form-control {{ $errors->has('region') ? 'is-invalid':'' }}" value="{{ $hotel->region }}" placeholder="Masukkan Region Hotel">
                    <span class="text-danger">{{ $errors->first('region') }}</span>
                </div>
                
                <div class="elegant-form-group">
                    <label for="address">Alamat Hotel</label>
                    <input type="text" name="address" id="address" class="elegant-form-control {{ $errors->has('address') ? 'is-invalid':'' }}" value="{{ $hotel->address }}" placeholder="Masukkan Alamat Hotel">
                    <span class="text-danger">{{ $errors->first('address') }}</span>
                </div>
                
                <div class="elegant-form-group">
                    <label for="phone">Phone</label>
                    <input type="text" name="phone" id="phone" class="elegant-form-control {{ $errors->has('phone') ? 'is-invalid':'' }}" value="{{ $hotel->phone }}" placeholder="Masukkan Nomor Telepon (Maks. 13 angka)">
                    <span class="text-danger">{{ $errors->first('phone') }}</span>
                </div>
                
                <div class="elegant-form-group">
                    <label for="fax">Fax</label>
                    <input type="text" name="fax" id="fax" class="elegant-form-control {{ $errors->has('fax') ? 'is-invalid':'' }}" value="{{ $hotel->fax }}" placeholder="Masukkan Fax Hotel">
                    <span class="text-danger">{{ $errors->first('fax') }}</span>
                </div>
                
                <div class="elegant-form-group">
                    <button type="submit" class="btn-primary btn-sm">
                        <i class="fa fa-save"></i> Update
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
