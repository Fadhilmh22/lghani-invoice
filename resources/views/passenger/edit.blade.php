@extends('master')

@section('konten')
<div class="elegant-container">
    <div class="card card-elegant">

        {{-- Header --}}
        <h2 class="page-title">Edit Data Penumpang</h2>

        {{-- Alert Error --}}
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        {{-- FORM (ACTION & METHOD TIDAK DIUBAH) --}}
        <form action="{{ url('/passenger/' . $passenger->id) }}" method="post">
            @csrf
            <input type="hidden" name="_method" value="PUT">

            <div class="elegant-form-group">
                <label>Nama Penumpang</label>
                <input 
                    type="text" 
                    name="name"
                    value="{{ $passenger->name }}"
                    class="form-control elegant-form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                    placeholder="Masukkan Nama Lengkap"
                >
                <span class="text-danger">{{ $errors->first('name') }}</span>
            </div>

            <div class="elegant-form-group">
                <label>ID Card</label>
                <input 
                    type="text" 
                    name="id_card"
                    value="{{ $passenger->id_card }}"
                    class="form-control elegant-form-control {{ $errors->has('id_card') ? 'is-invalid' : '' }}"
                    placeholder="Masukkan No KTP"
                >
                <span class="text-danger">{{ $errors->first('id_card') }}</span>
            </div>

            <div class="elegant-form-group">
                <label>Date Birth</label>
                <input 
                    type="date" 
                    name="date_birth"
                    value="{{ $passenger->date_birth }}"
                    class="form-control elegant-form-control {{ $errors->has('date_birth') ? 'is-invalid' : '' }}"
                >
                <span class="text-danger">{{ $errors->first('date_birth') }}</span>
            </div>

            <div class="elegant-form-group">
                <label>Garuda Frequent Flyer</label>
                <input 
                    type="text" 
                    name="gff"
                    value="{{ $passenger->gff }}"
                    class="form-control elegant-form-control {{ $errors->has('gff') ? 'is-invalid' : '' }}"
                    placeholder="Masukkan No GFF"
                >
                <span class="text-danger">{{ $errors->first('gff') }}</span>
            </div>

            <div class="elegant-form-group">
                <label>Phone</label>
                <input 
                    type="text" 
                    name="phone"
                    value="{{ $passenger->phone }}"
                    class="form-control elegant-form-control {{ $errors->has('phone') ? 'is-invalid' : '' }}"
                >
                <span class="text-danger">{{ $errors->first('phone') }}</span>
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
