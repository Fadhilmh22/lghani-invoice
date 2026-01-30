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
                    <div id="phone-container">
                        <div class="input-group" style="margin-bottom: 10px; display: flex; gap: 5px;">
                            <input type="text" name="phone[]" class="elegant-form-control {{ $errors->has('phone') ? 'is-invalid':'' }}" placeholder="Contoh: 022-86619006" required>
                            <button type="button" class="btn btn-primary" id="add-phone" style="height: 40px; border-radius: 8px;">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <span class="text-danger">{{ $errors->first('phone') }}</span>
                    <small class="text-muted">Klik tombol + untuk menambah nomor telepon.</small>
                </div>
                
                <div class="elegant-form-group">
                    <label for="fax">Fax</label>
                    <input type="text" name="fax" id="fax" class="elegant-form-control {{ $errors->has('fax') ? 'is-invalid':'' }}" placeholder="Masukkan Fax Hotel" value="{{ old('fax') }}">
                    <span class="text-danger">{{ $errors->first('fax') }}</span>
                </div>
                
                <div class="elegant-form-group" style="margin-top: 20px;">
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

{{-- SCRIPT UNTUK TAMBAH/HAPUS INPUT TELEPON --}}
<script>
    document.getElementById('add-phone').addEventListener('click', function() {
        var container = document.getElementById('phone-container');
        var div = document.createElement('div');
        div.className = 'input-group';
        div.style.marginBottom = '10px';
        div.style.display = 'flex';
        div.style.gap = '5px';
        div.innerHTML = `
            <input type="text" name="phone[]" class="elegant-form-control" placeholder="Nomor tambahan" required>
            <button type="button" class="btn btn-danger remove-phone" style="height: 40px; border-radius: 8px; background-color: #ef4444; color: white; border: none; padding: 0 15px;">
                <i class="fa fa-minus"></i>
            </button>
        `;
        container.appendChild(div);
    });

    // Menghapus input tambahan
    document.addEventListener('click', function(e) {
        if (e.target && (e.target.classList.contains('remove-phone') || e.target.parentElement.classList.contains('remove-phone'))) {
            var targetBtn = e.target.classList.contains('remove-phone') ? e.target : e.target.parentElement;
            targetBtn.closest('.input-group').remove();
        }
    });
</script>

<style>
    /* Menambahkan sedikit style agar input-group terlihat rapi di dalam form elegant */
    .input-group input {
        flex: 1;
    }
    .btn-primary {
        background-color: #4f46e5;
        border: none;
        color: white;
        padding: 0 15px;
        transition: 0.3s;
    }
    .btn-primary:hover {
        background-color: #4338ca;
    }
</style>
@endsection