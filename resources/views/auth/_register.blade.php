<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="{{asset('fav.png')}}">
    <link rel="shortcut icon" href="{{asset('fav.png')}}">
    <!-- ========================================= Css files -->
    <link rel="stylesheet" href="{{asset('css/bootstrap/css/bootstrap.min.css')}} ">
    <link rel="stylesheet" href="{{asset('css/fa.min.css')}} ">
    <link rel="stylesheet" href="{{asset('user_assets/css/style.css')}} ">
    <title>Signup | Dantown Multi Services</title>
</head>

<body class="account_page">
    <div class="has_cover">
        <div class="row">
            <div class="col-lg-5 p-0 lg-hidden">
                <div class="left_part">
                    <div class="left_part_wrap">
                        <img src="{{asset('images/city.png')}}" alt="">
                    </div>
                </div>
            </div>
            <div class="col-lg-7 col-md-12 bg-blue">
                <h1 class="welcome">Welcome!</h1>
                <div class="right_part">
                    <div class="site-branding">
                        <div class="site-title">
                            <a href="#">
                                <div class="logo_sign">
                                    <img src="{{asset('images/logo.svg')}} " class="svg-logo" alt="ootancy">
                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="head">
                        <h4 class="sign_title">
                            It only takes a few seconds to create your account.
                        </h4>
                    </div>

                    <div class="sign-form">
                        <form action="{{route('register')}} " method="post">
                            @csrf
                            @foreach ($errors->all() as $error)
                            <p class="text-warning">{{ $error }}</p>
                            @endforeach

                            <div class="input-group mb-15">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <ion-icon name="mail"></ion-icon>
                                    </span>
                                </div>
                                <input type="text" name="email" class="form-control" placeholder="Email"
                                    aria-describedby="inputGroupPrepend3" required="" />
                            </div>

                            <div class="input-group mb-15">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <ion-icon name="lock-closed"></ion-icon>
                                    </span>
                                </div>
                                <input type="password" name="password" placeholder="Password"
                                    class="form-control password" aria-describedby="inputGroupPrepend3" required="" />
                                <span class="toggle-password">
                                    <i class="show fas fa-eye"></i>
                                    <i class="hide fas fa-eye-slash"></i>
                                </span>
                            </div>
                            <div class="input-group mb-50">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <ion-icon name="lock-closed"></ion-icon>
                                    </span>
                                </div>
                                <input type="password" name="password_confirmation" placeholder="Repeat password"
                                    class="form-control password" aria-describedby="inputGroupPrepend3" required="" />
                                <span class="toggle-password">
                                    <i class="show fas fa-eye"></i>
                                    <i class="hide fas fa-eye-slash"></i>
                                </span>
                            </div>

                            <button class="btn btn-primary d-flex m-auto mb-sm-15 mb-30">
                                CONTINUE TO PAYMENT INFO
                            </button>
                        </form>
                    </div>
                    {{-- <nav aria-label="breadcrumb ">
                        <ol class="breadcrumb has_style1 flex end mb-0 p-0 mt-50 mt-sm-0">
                            <li class="breadcrumb-item"><a href="#">Back to Website</a></li>
                        </ol>
                    </nav> --}}
                </div>
            </div>
        </div>
    </div>

    <!-- ====================================== js files  -->
    <script src="{{asset('js/jquery-3.2.1.min.js')}}"></script>
    <script src="{{asset('js/bootstrap.min.js')}} "></script>
    <script src="https://unpkg.com/ionicons@5.0.0/dist/ionicons.js"></script>
    <script src="{{asset('user_assets/js/main.js')}}"></script>
</body>

</html>
