@extends('master')

@section('konten')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Edit Data Maskapai</h3>
                    </div>
                    <div class="card-body">
                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form action="{{ url('/airline/' . $airlines->id) }}" method="post">
                            @csrf
                            <input type="hidden" name="_method" value="PUT" class="form-control">
                            <div class="form-group">
                                <label for="">Airlines Code</label>
                                <input type="text" name="airlines_code" class="form-control" value="{{ $airlines->airlines_code }}" placeholder="Masukkan Kode Maskapai">
                            </div>
                            <div class="form-group">
                                <label for="">Airlines Name</label>
                                <input type="text" name="airlines_name" class="form-control" value="{{ $airlines->airlines_name }}" placeholder="Masukkan Nama Maskapai">
                            </div>
                            <div class="form-group">
                                <button class="btn btn-warning btn-sm">Ubah</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection