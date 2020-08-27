<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="keywords" content="Flingo, Messaging app, chat, chat app" />

    <title>Dantownms | Live Chat</title>
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="{{asset('fav.png')}}">
    <link rel="shortcut icon" href="{{asset('fav.png')}}">
    <link href="https://fonts.googleapis.com/css?family=Archivo:400,400i,500,500i,600,700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href=" {{asset('chat/assets/fonts/MaterialDesign/css/materialdesignicons.min.css')}} ">
    <link rel="stylesheet" href=" {{asset('chat/assets/vendors/perfect-scrollbar/perfect-scrollbar.css')}} ">
    <link rel="stylesheet" href=" {{asset('chat/assets/vendors/OwlCarousel2/owl.carousel.css')}} ">
    <link rel="stylesheet" href=" {{asset('chat/assets/vendors/bootstrap/bootstrap.min.css')}} ">
    <link rel="stylesheet" href="{{asset('chat/css/app.css')}} ">
    <link rel="stylesheet" href=" {{asset('chat/css/theme/default.css')}} ">


    <script>
            window.Laravel = {!! json_encode([
                'csrfToken'=> csrf_token(),
                'user'=> [
                    'authenticated' => auth()->check(),
                    'id' => auth()->check() ? auth()->user()->id : null,
                    'first_name' => auth()->check() ? auth()->user()->first_name : null,
                    'last_name' => auth()->check() ? auth()->user()->last_name : null,
                    'email' => auth()->check() ? auth()->user()->email : null,
                    'role' => auth()->check() ? auth()->user()->role : null,
                    ]
                ])
            !!};

    </script>
</head>
<body class="light-default-theme">
        
    
	@yield('content')


    <script src="/js/app.js"></script>
    <script src=" {{asset('chat/assets/vendors/jquery/jquery-3.4.1.min.js')}} "></script>
    <script src=" {{asset('chat/assets/vendors/bootstrap/bootstrap.bundle.min.js')}} "></script>
    <script src=" {{asset('chat/assets/vendors/material-floating-button/dist/mfb.min.js')}} "></script>
    <script src="{{asset('chat/assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js')}} "></script>
    <script src="{{asset('chat/assets/vendors/OwlCarousel2/owl.carousel.min.js')}} "></script>
    <script src=" {{asset('chat/js/app.js')}} "></script>
    
    <script>

    </script>
</body>

</html>