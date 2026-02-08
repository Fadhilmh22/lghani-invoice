@extends('master')

@section('konten')

<style>
    /* ===============================
       AIRPORTS EDIT FORM – INLINE CSS
       Bootstrap 3 SAFE
    =============================== */

    .elegant-container {
        width: 100%;
        padding: 20px 10px;
    }

    .card-elegant {
        background: #ffffff;
        border-radius: 16px;
        padding: 30px 34px;
        box-shadow: 0 12px 32px rgba(15, 23, 42, 0.1);
    }

    .page-title {
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 25px;
    }

    .form-spacing {
        margin-bottom: 22px;
    }

    .form-label {
        font-weight: 500;
        margin-bottom: 6px;
        display: block;
        color: #334155;
    }

    .form-action {
        margin-top: 35px;
        padding-top: 20px;
        border-top: 1px solid #e5e7eb;
        text-align: right;
    }

    .btn-back-elegant {
        background: #e2e8f0;
        color: #334155;
        border-radius: 10px;
        padding: 8px 20px;
        border: none;
    }

    .btn-back-elegant:hover {
        background: #cbd5e1;
        color: #1e293b;
    }
</style>

<div class="elegant-container">
    <div class="card card-elegant">

        <h4 class="page-title">Edit Data Bandara</h4>

        {{-- JANGAN UBAH LOGIC --}}
        <form action="{{ route('airports.update', $airport->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">

                <div class="col-md-3 form-spacing">
                    <label class="form-label">Kode Bandara (IATA)</label>
                    <input type="text"
                           name="code"
                           class="form-control {{ $errors->has('code') ? 'is-invalid' : '' }}"
                           value="{{ old('code', $airport->code) }}"
                           maxlength="3"
                           style="text-transform: uppercase">
                    @if ($errors->has('code'))
                        <span class="help-block text-danger">
                            {{ $errors->first('code') }}
                        </span>
                    @endif
                </div>

                <div class="col-md-5 form-spacing">
                    <label class="form-label">Nama Bandara</label>
                    <input type="text"
                           name="name"
                           class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                           value="{{ old('name', $airport->name) }}">
                    @if ($errors->has('name'))
                        <span class="help-block text-danger">
                            {{ $errors->first('name') }}
                        </span>
                    @endif
                </div>

                <div class="col-md-4 form-spacing">
                    <label class="form-label">Kota</label>
                    <input type="text"
                           name="city"
                           class="form-control {{ $errors->has('city') ? 'is-invalid' : '' }}"
                           value="{{ old('city', $airport->city) }}">
                    @if ($errors->has('city'))
                        <span class="help-block text-danger">
                            {{ $errors->first('city') }}
                        </span>
                    @endif
                </div>

            </div>

            <div class="form-action">
                <a href="{{ route('airports.index') }}" class="btn btn-back-elegant">
                    Kembali
                </a>
                <button type="submit" class="btn btn-primary" style="margin-left:12px;">
                    Update
                </button>
            </div>

        </form>

    </div>
</div>

@endsection