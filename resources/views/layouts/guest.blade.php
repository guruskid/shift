<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="{{asset('fav.png')}}">
    <link rel="shortcut icon" href="{{asset('fav.png')}}">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('css/bootstrap/css/bootstrap.min.css')}} ">
    <link rel="stylesheet" href="{{asset('css/fa.min.css')}} ">
    <link rel="stylesheet" href="{{asset('user_assets/css/main.css')}} ">
    <title>@yield('title') | Dantown Multi Services</title>

    <style>
        .swal-text{
            color: black;
        }

        .swal-button {
            height: 40px;
            padding: 9px 20px;
            border: 0px solid #000070 ;
            background: #000070 ;
            color: #fff;
            border-radius: 5px;
            font-family: 'Roboto', sans-serif;
            font-weight: 500;
            display: inline-block;
        }

        .swal-button:not([disabled]):hover {
            background: #31318d;
            color: #fff !important;
        }
    </style>
</head>
<body>

    @yield('guestviewcontent')

    {{-- <script src="{{asset('js/jquery-3.2.1.min.js')}}"></script> --}}
    <!-- jQuery and JS bundle w/ Popper.js -->
    <script src="{{asset('js/jquery-3.2.1.min.js')}} "></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
    {{-- <script src="{{asset('js/bootstrap.min.js')}} "></script> --}}
    <script src="https://unpkg.com/ionicons@5.0.0/dist/ionicons.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="{{asset('user_assets/js/main.js')}}"></script>
    <script src="{{asset('js/custom.js?v=3')}}"></script>
    <script src="{{asset('js/wallet.js')}} "></script>


    @if (session('error'))
    <script>
        swal('{{ session("error") }}')
    </script>
    @endif

    @if (session('success'))
    <script>
        swal('{{ session("success") }}')
    </script>
    @endif
</body>
</html>
