@extends('master')

@section('konten')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tambah Data Hotel</h3>
                </div>
                <div class="card-body">
                    @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                    @endif

                    <form action="{{ url('/hotel') }}" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="">Kode Hotel</label>
                            <input type="text" name="hotel_code" class="form-control {{ $errors->has('hotel_code') ? 'is-invalid':'' }}" placeholder="Masukkan Kode Hotel">
                            <p class="text-danger">{{ $errors->first('hotel_code') }}</p>
                        </div>
                        <div class="form-group">
                            <label for="">Nama Hotel</label>
                            <input type="text" name="hotel_name" class="form-control {{ $errors->has('hotel_name') ? 'is-invalid':'' }}" placeholder="Masukkan Nama Hotel">
                            <p class="text-danger">{{ $errors->first('hotel_name') }}</p>
                        </div>
                        <div class="form-group">
                            <label for="">Region Hotel</label>
                            <input type="text" name="region" class="form-control {{ $errors->has('region') ? 'is-invalid':'' }}" placeholder="Masukkan Region Hotel">
                            <p class="text-danger">{{ $errors->first('region') }}</p>
                        </div>
                        <div class="form-group">
                            <label for="">Alamat Hotel</label>
                            <input type="text" name="address" class="form-control {{ $errors->has('address') ? 'is-invalid':'' }}" placeholder="Masukkan Alamat Hotel">
                            <p class="text-danger">{{ $errors->first('address') }}</p>
                        </div>
                        <div class="form-group">
                            <label for="">Phone</label>
                            <input type="text" name="phone" class="form-control {{ $errors->has('phone') ? 'is-invalid':'' }}" placeholder="Masukkan Nomor Telepon (Maks. 13 angka)">
                            <p class="text-danger">{{ $errors->first('phone') }}</p>
                        </div>
                        <div class="form-group">
                            <label for="">Fax</label>
                            <input type="text" name="fax" class="form-control {{ $errors->has('fax') ? 'is-invalid':'' }}" placeholder="Masukkan Fax Hotel">
                            <p class="text-danger">{{ $errors->first('fax') }}</p>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-success btn-sm">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection