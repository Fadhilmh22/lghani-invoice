<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Invoice App</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('lghani-fit.png') }}" height="15px">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
<div class="container" style="font-family: 'poppins', sans-serif;">
    <div class="col-md-12">
        <nav class="navbar navbar-default">
            <!-- Original navbar from bak -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{route('home')}}">
                    <img src="{{ asset('lghani-fit.png') }}" alt="" height="25px">
                </a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-left">
                    <li><a href="{{ route('invoice.create') }}">Buat Invoice</a></li>
                    <li><a href="{{ route('invoice.index') }}">List Invoice</a></li>
                    <li><a href="{{ url('/customer') }}">Pelanggan</a></li>
                    <li><a href="{{ url('/airline') }}">Maskapai</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">{{Auth::user()->name}} <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><b>{{Auth::user()->role}}</b></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="{{route('actionlogout')}}">Log Out</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
        @yield('konten')
    </div>
</div>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>
