<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                            <a href="../index.html">
                                <div class="logo_sign">
                                    <img src="{{asset('images/logo.svg')}} " class="svg-logo" alt="ootancy">
                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="head">
                        <h4 class="sign_title">
                            Just a few more seconds and you’re done!
                        </h4>
                    </div>

                    <div class="sign-form">
                        <form action="{{route('signup.add-bank')}} " method="post">
                            @csrf
                            @foreach ($errors->all() as $error)
                            <p class="text-warning">{{ $error }}</p>
                            @endforeach
                            <div class="input-group mb-15">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <ion-icon name="briefcase"></ion-icon>
                                    </span>
                                </div>
                                <select name="bank_code" id="bank-name" class="form-control" style="background-color: transparent; color: #495057 !important">
                                    <option value="">Select Bank Name</option>
                                    @foreach ($banks as $bank)
                                    <option value="{{$bank->code}}">{{$bank->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="input-group mb-15">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <ion-icon name="card"></ion-icon>
                                    </span>
                                </div>
                                <input type="text" id="account-number" name="account_number" class="form-control" placeholder="Account Number"
                                     required="" />
                            </div>

                            <div class="input-group mb-15">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <ion-icon name="person"></ion-icon>
                                    </span>
                                </div>
                                <input type="text" class="acct-name form-control" readonly name="account_name" placeholder="Account Name"
                                     required="" style="outline: none; background-color: transparent" />
                            </div>

                            <div class="input-group mb-50 mb-sm-30">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <ion-icon name="call"></ion-icon>
                                    </span>
                                </div>
                                <input type="tel" name="phone" class="form-control" placeholder="Phone number"
                                    aria-describedby="inputGroupPrepend3" required="" />
                            </div>

                            <div class="d-flex justify-content-between mb-30 mb-sm-15">
                                <div class="custom-control mr-4">
                                    <input type="checkbox" class="form-check-input" />
                                    <label class="form-check-label" for="check-1">Agree to <a href="#">terms &amp;
                                            conditions</a>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="flex around">
                            <button id="sign-up-btn" disabled class="btn btn-primary">FINISH</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ====================================== js files  -->
    <script src="{{asset('js/bootstrap.min.js')}} "></script>
    <script src="{{asset('js/jquery-3.2.1.min.js')}} "></script>
    <script src="{{asset('js/popper.min.js')}} "></script>
    <script src="{{asset('user_assets/js/main.js')}}"></script>
    <script src="{{asset('js/bootstrap-notify.js')}}"></script>
    <script src="{{asset('js/wallet.js')}} "></script>
</body>

</html>
