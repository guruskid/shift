<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Dantown Multi services</title>
    <link href=" {{asset('user_main.css')}} " rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="{{asset('fav.png')}}">
    <link rel="shortcut icon" href="{{asset('fav.png')}}">
    <style>
        * {
            box-sizing: border-box;
            font-family: "Roboto";
        }

        .app {
            background: #000070;
            /* margin: auto; */
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
            /* height: inherit; */
            background: url('images/login page background 1.png');
            background-size: contain;
            background-position: bottom;
            background-repeat: no-repeat;
        }

        #logo {
            position: relative;
            width: 150px;
            height: 40px;
            left: 30px;
            top: 30px;
        }

        h1 {
            position: relative;
            padding-left: 40px;
            width: inherit;
            top: 40px;
            font-style: normal;
            font-weight: 600;
            font-size: 90px;
            color: rgba(255, 255, 255, 0.52);
            opacity: 0.4;
        }

        p.paragraph {
            position: relative;
            padding-left: 50px;
            top: 50px;
            font-style: normal;
            font-weight: 500;
            font-size: 30px;
            color: #FFFFFF;
        }

        p.d-block {
            position: relative;
            padding-left: 50px;
            top: 30px;
            font-size: 16px;
            line-height: 30px;
            color: #FFFFFF;
        }

        a:hover {
            color: #FFFFFF;
            text-decoration: none;
        }


        form {
            position: relative;
            top: 45px;
            width: 65%;
        }

        .input {
            background: transparent;
            border: 0;
            color: white;
        }

        .input:focus {
            background: transparent;
        }

        #password:hover {
            cursor: pointer;
        }

        form .row .col .btn {
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
                margin: 0px;
                padding-bottom: 40px !important;
            }

            .app {
                width: 100% !important;
                height: auto;
                padding: 0px;
                margin: 0px;
            }

            h1 {
                font-size: 40px;
            }

            p.paragraph {
                font-size: 16px;
            }

            p.d-block {
                font-size: 12px;
            }

            form .row .col .btn {
                width: 50%;
                font-size: 12px;
            }

            form .row .col {
                font-size: 12px;
            }
        }

    </style>
</head>

<body>
    <div class="row app m-0 p-0 shadow">
        <div class="col-md-5 side-img-box d-md-flex d-none align-items-center">
            <img src="{{asset('images/city.png')}}" class="img-fluid" alt="">
        </div>
        <div class="col-md-7 p-md-5 login d-flex align-items-center" style="height: 100vh">
            <div>
                <img src="{{asset('logo.svg')}}" alt="" id="logo">
                <h1 class="mx-0  w-100">
                    Welcome Back
                </h1>
                <p class="paragraph">
                    Please, sign in to your account.
                </p>
                <p class="d-block mt-4" href="#">
                    Don’t have an account yet? <a href="{{route('register')}}" style="color: inherit;">Sign up now!</a>
                </p>

                <div class="row">
                    <div class="col-md-8">
                        <form action="{{route('login')}} " method="post" class="ml-5 mt-3">
                            @if (count($errors) > 0)
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li class="text-warning">{{ $error }}</li>
                                @endforeach
                            </ul>
                            @endif
                            @csrf
                            <div class="input-group input-group-sm border mb-3 border-top-0 border-left-0 border-right-0"
                                style="outline: none;">
                                <div class="input-group-prepend my-auto mr-4" style="outline: none;">
                                    <i class="fa fa-envelope text-white"></i>
                                </div>
                                <input type="email" name="email" required style="outline: none;" placeholder="Email"
                                    class="form-control input">
                            </div>
                            <div class="input-group input-group-sm border mb-3 border-top-0 border-left-0 border-right-0"
                                style="outline: none;">
                                <div class="input-group-prepend my-auto mr-4" id="password"
                                    style="outline: none; height: 16px;">
                                    <i class="fa fa-eye text-white"></i>
                                </div>
                                <input type="password" name="password" required style="outline: none;"
                                    placeholder="Password" class="form-control input">
                            </div>
                            <div class="row p-0 my-5">
                                <div class="col m-0">
                                    <button type="submit" class="btn w-100">LOGIN</button>
                                </div>
                                <div class="col m-0 px-0">
                                    <a href="{{ route('password.request') }}" class="text-white">Forgot Password?</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{asset('js/jquery-3.4.1.js')}} "></script>
    <script src="{{asset('js/popper.min.js')}} "></script>
    <script src="{{asset('main/bootstrap-4.4.1/dist/js/bootstrap.min.js')}} "></script>
    <script src="{{asset('main/fontawesome-free-5.12.1-web/js/all.min.js')}} "></script>

    <script>
        let img = document.getElementById("password");
        img.addEventListener("click", function () {
            if (this.nextElementSibling.type == 'password') {
                this.nextElementSibling.type = "text";
                this.firstElementChild.src = 'main/svg/visibility_off.svg';
                this.firstElementChild.style.height = '18px';
            } else {
                this.nextElementSibling.type = "password";
                this.firstElementChild.src = 'main/svg/Vector (3).svg';
                this.firstElementChild.style.height = '12px';
            }
        });

    </script>
</body>

</html>
