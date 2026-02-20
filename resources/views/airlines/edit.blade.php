@extends('master')

@section('konten')

<style>
    /* ===============================
       AIRLINES EDIT FORM – INLINE CSS
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

        <h4 class="page-title">Edit Data Maskapai</h4>

        {{-- JANGAN UBAH LOGIC --}}
        <form action="{{ url('/airline/'.$airlines->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">

                <div class="col-md-6 form-spacing">
                    <label class="form-label">Kode Maskapai</label>
                    <input type="text"
                           name="airlines_code"
                           class="form-control {{ $errors->has('airlines_code') ? 'is-invalid' : '' }}"
                           value="{{ old('airlines_code', $airlines->airlines_code) }}">
                    @if ($errors->has('airlines_code'))
                        <span class="help-block text-danger">
                            {{ $errors->first('airlines_code') }}
                        </span>
                    @endif
                </div>

                <div class="col-md-6 form-spacing">
                    <label class="form-label">Nama Maskapai</label>
                    <input type="text"
                           name="airlines_name"
                           class="form-control {{ $errors->has('airlines_name') ? 'is-invalid' : '' }}"
                           value="{{ old('airlines_name', $airlines->airlines_name) }}">
                    @if ($errors->has('airlines_name'))
                        <span class="help-block text-danger">
                            {{ $errors->first('airlines_name') }}
                        </span>
                    @endif
                </div>
                <div class="col-md-6 form-spacing">
                    <label class="form-label">Saldo Saat Ini</label>
                    <input type="text" class="form-control" value="IDR {{ number_format($airlines->balance) }}" readonly>
                </div>

            </div>

            <div class="form-action">
                <a href="{{ url('/airline') }}" class="btn btn-back-elegant">
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
