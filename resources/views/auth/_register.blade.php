<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Dantown Multi services</title>
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="{{asset('fav.png')}}">
    <link rel="shortcut icon" href="{{asset('fav.png')}}">
    <link href=" {{asset('user_main.css')}} " rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            font-family: "Roboto";
        }

        .app {
            background: #000070;
        }

        .col-lg-5 img {
            width: inherit;
        }

        .side-img-box {
            height: 100vh;
            background-color: #E8F0FE;
        }

        .login {
            width: inherit;
            background: url('images/login page background 1.png');
            background-size: contain;
            background-position: bottom;
            background-repeat: no-repeat;
        }

        #logo {
            display: block;
            width: 150px;
            height: 40px;
            margin-top: 40px;
            float: right;
        }


        .text-accent {
            color: #00B9CD !important;
        }

        .col-lg-12 {
            margin-top: 90px;
            display: flex;
        }

        .col-lg-12 .col-lg-2 {
            transform: rotate(-90deg);
            width: fit-content;
        }

        .col-lg-12 .col-lg-2 h1 {
            margin-left: -190px;
            margin-top: 160px;
            font-size: 120px;
            width: 686px;
            font-style: normal;
            font-weight: normal;
            text-align: center;
            opacity: 0.5;
            color: rgba(255, 255, 255, 0.52);
        }

        .col-lg-12 .col-lg-10 h3.text-white {
            font-size: 20px;
        }

        .col-lg-12 .col-lg-10 form {
            width: 65%;
        }

        .input {
            background: transparent;
            border: 0;
            color: white;
        }

        .input:focus {
            background: transparent;
            color: white;
        }

        form .btn {
            font-size: 12px;
            background-color: white;
            color: #21B8C7;
        }

        @media screen and (max-width: 768px) {
            .col-md-0 {
                display: none;
            }

            .col-md-12,
            .col-sm-12,
            .col-12 {
                padding: 5px;
                padding-bottom: 40px !important;
            }

            .app {
                width: 100% !important;
                height: auto;
                padding: 0px;
                margin: 0px;
            }

            .col-lg-12 {
                display: flex;
                flex-direction: row;
            }

            .col-lg-12 .col-lg-2 {
                transform: rotate(0deg);
            }


            .col-lg-12 .col-lg-2 h1 {
                font-size: 70px;
                margin-top: 0px;
                margin-left: 50px;
            }

            .col-lg-10.col-md-10.col-10.col-sm-10 {
                padding: 0px !important;
                margin-top: 50px;
            }

            .col-lg-12 .col-lg-10 h3.text-white {
                font-size: 12px;
            }

            form .btn {
                font-size: 1.1ch;
            }

            .col-lg-12 .col-lg-10 form {
                width: 90%;
                margin: 0px !important;
            }
        }

    </style>
</head>

<body>
    <div class="row app m-0 p-0">
        <div class="col-md-5 side-img-box d-md-flex d-none align-items-center">
            <img src="{{asset('images/city.png')}}" class="img-fluid" alt="">
        </div>
        <div class="col-md-7 m-0 p-0 login">
            <img src="{{asset('logo.svg')}}" id="logo">
            <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                <div class="col-lg-2 col-md-2 col-2 col-sm-2 ml-2 welcome-div">
                    <h1 class="w-100">Welcome!</h1>
                </div>

                <div class="col-lg-10 col-md-10 col-10 col-sm-10 pl-5 ml-1">
                    <h3 class="text-white mb-4 mt-5 mx-0">It only takes a few seconds to create your account</h3>
                    <p class="d-block mt-4 text-white">
                        Already registered? <a href="{{route('login')}}" style="color: inherit;">Login now!</a>
                    </p>
                    @if (count($errors) > 0)
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li class="text-warning">{{ $error }}</li>
                        @endforeach
                    </ul>
                    @endif
                    <form action="{{route('register')}} " method="post" class="ml-5">
                        {{ csrf_field() }}
                        {{-- <div class="input-group input-group-sm border mb-4 border-top-0 border-left-0 border-right-0"
                            style="outline: none;">
                            <div class="input-group-prepend my-auto mr-4" style="outline: none;">
                                <i class="fa fa-user text-white"></i>
                            </div>
                            <input type="text" name="first_name" required style="outline: none;"
                                placeholder="First name" class="form-control input">
                        </div>
                        <div class="input-group input-group-sm border mb-4 border-top-0 border-left-0 border-right-0"
                            style="outline: none;">
                            <div class="input-group-prepend my-auto mr-4" style="outline: none;">
                                <i class="fa fa-user text-white"></i>
                            </div>
                            <input type="text" name="last_name" required style="outline: none;" placeholder="Last name"
                                class="form-control input">
                        </div> --}}
                        <div class="input-group input-group-sm border mb-4 border-top-0 border-left-0 border-right-0"
                            style="outline: none;">
                            <div class="input-group-prepend my-auto mr-4" style="outline: none;">
                                <i class="fa fa-envelope text-white"></i>
                            </div>
                            <input type="email" name="email" required style="outline: none;" placeholder="Email"
                                class="form-control input">
                        </div>

                        <div class="input-group input-group-sm border mb-4 border-top-0 border-left-0 border-right-0"
                            style="outline: none;">
                            <div class="input-group-prepend my-auto mr-4" style="outline: none;"
                                onclick="passwordToggle(this)">
                                <i class="fa fa-eye text-white "></i>
                            </div>
                            <input type="password" name="password" style="outline: none;" placeholder="Password"
                                class="form-control input">
                        </div>
                        <div class="input-group input-group-sm border mb-4 border-top-0 border-left-0 border-right-0"
                            style="outline: none;">
                            <div class="input-group-prepend my-auto mr-4" style="outline: none;"
                                onclick="passwordToggle(this)">
                                <i class="fa fa-eye text-white "></i>
                            </div>
                            <input type="password" name="password_confirmation" style="outline: none;"
                                placeholder="Repeat password" class="form-control input">
                        </div>
                        <div class="custom-control custom-checkbox my-3">
                            <input type="checkbox" required class="custom-control-input" id="customCheck">
                            <label class="custom-control-label text-white" for="customCheck" style="font-size: 13px;">I
                                agree to your <a href="#" class="text-accent">terms & conditions</a></label>
                        </div>
                        <button type="submit" class="btn w-50">Sign up to DANTOWN</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{asset('js/jquery-3.4.1.js')}} "></script>
    <script src="{{asset('js/popper.min.js')}} "></script>
    <script src="{{asset('main/bootstrap-4.4.1/dist/js/bootstrap.min.js')}} "></script>
    <script src="{{asset('main/fontawesome-free-5.12.1-web/js/all.min.js')}} "></script>

    <script>
        function passwordToggle(params) {
            if (params.nextElementSibling.type == 'password') {
                params.nextElementSibling.type = "text";
                params.firstElementChild.src = 'main/svg/visibility_off.svg';
                params.firstElementChild.style.height = '18px';
            } else {
                params.nextElementSibling.type = "password";
                params.firstElementChild.src = 'main/svg/Vector (3).svg';
                params.firstElementChild.style.height = '12px';
            }
        }

    </script>
</body>

</html>
