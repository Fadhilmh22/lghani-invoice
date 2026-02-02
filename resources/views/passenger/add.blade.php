@extends('master')

@section('konten')
<div class="elegant-container">
    <div class="card card-elegant">

        {{-- Header --}}
        <h2 class="page-title">Tambah Data Penumpang</h2>

        {{-- Alert Error --}}
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        {{-- FORM (ACTION TIDAK DIUBAH) --}}
        <form action="{{ url('/passenger') }}" method="post">
            @csrf

            <div class="elegant-form-group">
                <label>Nama Penumpang</label>
                <input 
                    type="text" 
                    name="name"
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
                    class="form-control elegant-form-control {{ $errors->has('id_card') ? 'is-invalid' : '' }}"
                    placeholder="Masukkan No KTP"
                >
                <span class="text-danger">{{ $errors->first('id_card') }}</span>
            </div>

            <div class="elegant-form-group">
                <label>Date Birth</label>
                <input 
                    type="text" 
                    name="date_birth"
                    class="form-control elegant-form-control {{ $errors->has('date_birth') ? 'is-invalid' : '' }}"
                >
                <span class="text-danger">{{ $errors->first('date_birth') }}</span>
            </div>

            <div class="elegant-form-group">
                <label>Garuda Frequent Flyer</label>
                <input 
                    type="text" 
                    name="gff"
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
                    class="form-control elegant-form-control {{ $errors->has('phone') ? 'is-invalid' : '' }}"
                    placeholder="Masukkan Nomor Telepon (Maks. 13 angka)"
                >
                <span class="text-danger">{{ $errors->first('phone') }}</span>
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

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.flatpickr) {
            flatpickr('input[name="date_birth"]', { dateFormat: 'Y-m-d' });
        }
    });
</script>
@endsection
