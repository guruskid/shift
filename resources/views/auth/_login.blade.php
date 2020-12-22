<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="{{asset('fav.png')}}">
    <link rel="shortcut icon" href="{{asset('fav.png')}}">
    <!-- ========================================= Css files -->
    <link rel="stylesheet" href="{{asset('css/bootstrap/css/bootstrap.min.css')}} ">
    <link rel="stylesheet" href="{{asset('css/fa.min.css')}} ">
    <link rel="stylesheet" href="{{asset('user_assets/css/style.css')}} ">
    <title>Login | Dantown Multi Services</title>
</head>

<body class="account_page">
    <div class="has_cover">
        <div class="row">
            <div class=" col-lg-5 p-0 lg-hidden">
                <div class="left_part">
                    <div class="left_part_wrap">
                        <img src="{{asset('images/city.png')}}" alt="">
                    </div>
                </div>
            </div>
            <div class="col-lg-7 col-md-12 bg-blue ">
                <div class="right_part sign">
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
                        <h1 class="welcome-back mb-3">Welcome Back</h1>
                        <h4 class="sign_title login"> Please, sign in to your account.<br>Don’t have an account yet? <a
                                href="{{route('register')}}">SIGN UP NOW!</a> </h4>
                    </div>


                    <div class="sign-form sign">
                        <form method="POST" action="{{route('login')}} " > @csrf
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
                                    aria-describedby="inputGroupPrepend3" required="">
                            </div>

                            <div class="input-group mb-50 mb-sm-30">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <ion-icon name="lock-closed"></ion-icon>
                                    </span>
                                </div>
                                <input type="password" name="password" placeholder="Password" class="form-control password"
                                    aria-describedby="inputGroupPrepend3" required="">
                                <span class="toggle-password">
                                    <i class="show fas fa-eye"></i>
                                    <i class="hide fas fa-eye-slash"></i>
                                </span>
                            </div>

                            <div class="d-flex justify-content-between  mb-30 mb-sm-15">
                                <div class="custom-control mr-4">
                                    <input type="checkbox" name="remember" class="form-check-input">
                                    <label class="form-check-label " for="check-1">Keep me logged in
                                    </label>
                                </div>
                            </div>

                            <div class="flex around">
                                <button class="btn btn-primary "> LOGIN TO ACCOUNT  </button>
                                <a href="{{ route('password.request') }}"><button type="button" class="btn btn-primary outline"> Forgot Password?</button></a>

                            </div>
                    </div>
                    </form>

                </div>

            </div>
        </div>

    </div>
    </div>

    <!-- ====================================== js files  -->
    <script src="{{asset('js/jquery-3.2.1.min.js')}}"></script>
    <script src="{{asset('js/bootstrap.min.js')}} "></script>
    <script src="https://unpkg.com/ionicons@5.0.0/dist/ionicons.js"></script>
    <script src="{{asset('user_assets/js/main.js')}}"></script>
{{--
    <script src="../assets/js/plugins/jquery.waypoints.min.js"></script>
    <script src="../assets/js/plugins/jquery.counterup.min.js"></script> --}}



</body>

</html>
