@extends('master')

@section('konten')
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <div class="elegant-container">
        <div class="row mb-4">
            <div class="col-md-12">
                <h1 class="page-title">Buat Invoice</h1>
            </div>
        </div>
        
        <div class="card-elegant">
            <div class="card-body">
                @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
                @endif

                <form action="{{ route('invoice.store') }}" method="post">
                    @csrf
                    <div class="elegant-form-group">
                        <label for="customer_id">Filter Pelanggan (opsional)</label>
                        <select name="customer_id" id="customer_id" class="elegant-form-control select2">
                            <option value="">Semua Pelanggan</option>
                            @foreach($customers ?? [] as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->booker }} - {{ $customer->company }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="elegant-form-group">
                        <button type="submit" class="btn-primary btn-sm">
                            <i class="fa fa-check"></i> Buat
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2();
        });
    </script>
@endsection
