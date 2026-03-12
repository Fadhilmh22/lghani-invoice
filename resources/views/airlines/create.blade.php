@extends('master')

@section('konten')

<style>
    /* ===============================
       AIRLINES FORM – INLINE CSS
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

    /* spacing antar input */
    .form-spacing {
        margin-bottom: 22px;
    }

    /* label */
    .form-label {
        font-weight: 500;
        margin-bottom: 6px;
        display: block;
        color: #334155;
    }

    /* tombol area */
    .form-action {
        margin-top: 35px;
        padding-top: 20px;
        border-top: 1px solid #e5e7eb;
        text-align: right;
    }

    /* tombol kembali */
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

        <h4 class="page-title">Tambah Data Maskapai</h4>

        <input type="hidden" name="redirect_url" value="{{ url('/airline') . (request()->query('page') ? '?page=' . request()->query('page') : '') }}">

        {{-- FORM JANGAN DIUBAH LOGIC --}}
<form action="{{ url('/airline') }}" method="POST" enctype="multipart/form-data">


            @csrf

            <div class="row">

                <div class="col-md-6 form-spacing">
                    <label class="form-label">Kode Maskapai</label>
                    <input type="text"
                           name="airlines_code"
                           class="form-control {{ $errors->has('airlines_code') ? 'is-invalid' : '' }}"
                           placeholder="Contoh: GA"
                           value="{{ old('airlines_code') }}">
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
                           placeholder="Contoh: Garuda Indonesia"
                           value="{{ old('airlines_name') }}">
                    @if ($errors->has('airlines_name'))
                        <span class="help-block text-danger">
                            {{ $errors->first('airlines_name') }}
                        </span>
                    @endif
                </div>

            </div>

            <div class="col-md-12 form-spacing">
                <label class="form-label">Logo Maskapai</label>
                <input type="file" 
                       name="logo" 
                       id="logoInput" 
                       class="form-control {{ $errors->has('logo') ? 'is-invalid' : '' }}" 
                       accept="image/*">
                @if ($errors->has('logo'))
                    <span class="help-block text-danger">
                        {{ $errors->first('logo') }}
                    </span>
                @endif
                <img id="logoPreview" src="#" alt="Logo Preview" class="img-thumbnail mt-2 d-none" style="max-width: 100px; max-height: 100px;">
            </div>

            <div class="form-action">

                <a href="{{ url('/airline') }}" class="btn btn-back-elegant">
                    Kembali
                </a>
                <button type="submit" class="btn btn-primary" style="margin-left:12px;">
                    Simpan
                </button>
            </div>

        </form>

<script>
$(document).ready(function() {
    $('#logoInput').change(function(e) {
        var file = e.target.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#logoPreview').attr('src', e.target.result).removeClass('d-none');
            }
            reader.readAsDataURL(file);
        } else {
            $('#logoPreview').addClass('d-none');
        }
    });
});
</script>

    </div>
</div>

@endsection

