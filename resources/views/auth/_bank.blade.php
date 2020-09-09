<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
            height: 100vh;
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

        .input  {
            background: none;
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
                font-size: 40px;
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
    <div class="row app w-100 m-0 p-0 shadow">
        <div class="col-md-5 d-none d-md-flex align-items-center p-0 m-0" style="height: 100vh; background: #E8F0FE " >
            <div >
                <img src="{{asset('images/city.png')}}" class="img-fluid" alt="">
            </div>
        </div>
        <div class="col-md-7  m-0 p-0 login" style="height: 100vh" >
            <img src="{{asset('logo.svg')}} " class="mr-5" id="logo">
            <div class="col-lg-12 col-md-12 col-12 col-sm-12">
                <div class="col-lg-2 col-md-2 col-2 col-sm-2 ml-2 welcome-div">
                    <h1 class="w-100">Payment Info</h1>
                </div>

                <div class="col-lg-10 col-md-10 col-10 col-sm-10 pl-5 ml-1">
                    <h3 class="text-white mb-4 mt-5 mx-0">Just a few more seconds and you're done</h3>
                    @if (count($errors) > 0)
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li class="text-warning">{{ $error }}</li>
                        @endforeach
                    </ul>
                    @endif
                    <form action="{{route('signup.add-bank')}} " method="post" class="ml-5">
                        {{ csrf_field() }}
                        <div class="input-group input-group-sm border mb-4 border-top-0 border-left-0 border-right-0"
                            style="outline: none;">
                            <div class="input-group-prepend my-auto mr-4" style="outline: none;">
                                <i class="fa fa-briefcase text-white"></i>
                            </div>
                            <select name="bank_name" id="bank-name" class="form-control" style="outline: none; background-color: transparent; border: none">
                                <option value="">Select Bank Name</option>
                                @foreach ($banks as $bank)
                                <option value="{{$bank->code}}">{{$bank->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="input-group input-group-sm border mb-4 border-top-0 border-left-0 border-right-0"
                            style="outline: none;">
                            <div class="input-group-prepend my-auto mr-4"  style="outline: none;">
                                <i class="fa fa-address-card text-white"></i>
                            </div>
                            <input type="number" id="account-number" name="account_number" required style="outline: none;" placeholder="Account Number"
                                class="form-control input">
                        </div>
                        <div class="input-group input-group-sm border mb-4 border-top-0 border-left-0 border-right-0"
                            style="outline: none;">
                            <div class="input-group-prepend my-auto mr-4" style="outline: none;">
                                <i class="fa fa-user text-white"></i>
                            </div>
                            <input type="text" readonly name="account_name" required style="outline: none; background-color: transparent"
                                placeholder="Account name" class="form-control input acct-name">
                        </div>
                        <div class="input-group input-group-sm border mb-4 border-top-0 border-left-0 border-right-0"
                            style="outline: none;">
                            <div class="input-group-prepend my-auto mr-4" style="outline: none;">
                                <i class="fa fa-phone text-white"></i>
                            </div>
                            <input type="tel" name="phone" style="outline: none;" placeholder="Phone number"
                                class="form-control input">
                        </div>
                        <button type="submit" id="sign-up-btn" disabled class="btn w-50">Sign up to DANTOWN</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{asset('assets/scripts/main.js')}} "></script>
    <script src="{{asset('js/jquery-3.2.1.min.js')}} "></script>
    <script src="{{asset('js/popper.min.js')}} "></script>
    <script src="{{asset('js/bootstrap-notify.js')}}"></script>
    <script src="{{asset('js/wallet.js')}} "></script>

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
