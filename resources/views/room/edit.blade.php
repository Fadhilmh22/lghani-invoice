@extends('master')

@section('konten')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<div class="elegant-container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="page-title">Edit Data Kamar</h1>
        </div>
    </div>
    
    <div class="card-elegant">
        <div class="card-body">
            @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
            @endif

            <form action="{{ url('/room/' . $room->id) }}" method="post">
                @csrf
                <input type="hidden" name="_method" value="PUT" class="form-control">
                <input type="hidden" name="redirect" value="{{ $redirect }}">
                
                <div class="elegant-form-group">
                    <label for="hotel_id">Hotel</label>
                    <select name="hotel_id" id="hotel_id" class="elegant-form-control select2 {{ $errors->has('hotel_id') ? 'is-invalid':'' }}" required>
                        <option value="">Pilih Hotel</option>
                        @foreach ($hotels as $hotel) 
                        @if ($room->hotel_id == $hotel->id)
                        <option value="{{ $hotel->id }}" selected="selected">{{ $hotel->hotel_code }} - {{ $hotel->hotel_name }} @if ($hotel->region != '') | Region {{ $hotel->region }} @endif</option>
                        @else
                        <option value="{{ $hotel->id }}">{{ $hotel->hotel_code }} - {{ $hotel->hotel_name }} @if ($hotel->region != '') | Region {{ $hotel->region }} @endif</option>
                        @endif
                        @endforeach
                    </select>
                    <span class="text-danger">{{ $errors->first('hotel_id') }}</span>
                </div>
                
                <div class="elegant-form-group">
                    <label for="room_code">Kode Kamar</label>
                    <input type="text" name="room_code" id="room_code" class="elegant-form-control {{ $errors->has('room_code') ? 'is-invalid':'' }}" value="{{ $room->room_code }}" placeholder="Masukkan Kode Kamar">
                    <span class="text-danger">{{ $errors->first('room_code') }}</span>
                </div>
                
                <div class="elegant-form-group">
                    <label for="room_name">Nama Kamar</label>
                    <input type="text" name="room_name" id="room_name" class="elegant-form-control {{ $errors->has('room_name') ? 'is-invalid':'' }}" value="{{ $room->room_name }}" placeholder="Masukkan Nama Kamar">
                    <span class="text-danger">{{ $errors->first('room_name') }}</span>
                </div>
                
                <div class="elegant-form-group">
                    <label for="room_type">Room Type</label>
                    <input type="text" name="room_type" id="room_type" class="elegant-form-control {{ $errors->has('room_type') ? 'is-invalid':'' }}" value="{{ $room->room_type }}" placeholder="Masukkan Tipe Kamar">
                    <span class="text-danger">{{ $errors->first('room_type') }}</span>
                </div>
                
                <div class="elegant-form-group">
                    <label for="bed_type">Bed Type</label>
                    <input type="text" name="bed_type" id="bed_type" class="elegant-form-control {{ $errors->has('bed_type') ? 'is-invalid':'' }}" value="{{ $room->bed_type }}" placeholder="Masukkan Tipe Bed">
                    <span class="text-danger">{{ $errors->first('bed_type') }}</span>
                </div>
                
                <div class="elegant-form-group">
                    <label for="weekday_price">Weekday Price</label>
                    <input type="text" name="weekday_price" id="weekday_price" class="elegant-form-control {{ $errors->has('weekday_price') ? 'is-invalid':'' }}" value="{{ $room->weekday_price }}" placeholder="Masukkan Harga Weekday">
                    <span class="text-danger">{{ $errors->first('weekday_price') }}</span>
                </div>
                
                <div class="elegant-form-group">
                    <label for="weekday_nta">Weekday NTA</label>
                    <input type="text" name="weekday_nta" id="weekday_nta" class="elegant-form-control {{ $errors->has('weekday_nta') ? 'is-invalid':'' }}" value="{{ $room->weekday_nta }}" placeholder="Masukkan NTA Weekday">
                    <span class="text-danger">{{ $errors->first('weekday_nta') }}</span>
                </div>
                
                <div class="elegant-form-group">
                    <label for="weekend_price">Weekend Price</label>
                    <input type="text" name="weekend_price" id="weekend_price" class="elegant-form-control {{ $errors->has('weekend_price') ? 'is-invalid':'' }}" value="{{ $room->weekend_price }}" placeholder="Masukkan Harga Weekend">
                    <span class="text-danger">{{ $errors->first('weekend_price') }}</span>
                </div>
                
                <div class="elegant-form-group">
                    <label for="weekend_nta">Weekend NTA</label>
                    <input type="text" name="weekend_nta" id="weekend_nta" class="elegant-form-control {{ $errors->has('weekend_nta') ? 'is-invalid':'' }}" value="{{ $room->weekend_nta }}" placeholder="Masukkan NTA Weekend">
                    <span class="text-danger">{{ $errors->first('weekend_nta') }}</span>
                </div>
                
                <div class="elegant-form-group">
                    <button type="submit" class="btn-primary btn-sm">
                        <i class="fa fa-save"></i> Update
                    </button>
                    <a href="{{ url('/room') }}" class="btn-primary btn-sm" style="background-color: #6b7280; margin-left: 10px; text-decoration: none; display: inline-block;">
                        <i class="fa fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        // Initialize Select2 dengan konfigurasi yang lebih compact
        $('#hotel_id').select2({
            placeholder: 'Pilih Hotel',
            allowClear: false,
            width: '100%',
            dropdownAutoWidth: false,
            language: {
                noResults: function() {
                    return "Hotel tidak ditemukan";
                },
                searching: function() {
                    return "Mencari...";
                }
            }
        });
    });
</script>
@endsection
