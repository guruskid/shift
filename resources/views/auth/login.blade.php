@extends('layouts.guest')
@section('title', 'Login')
@section('guestviewcontent')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
        integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>


    <div class="container-fluid p-0 m-0 login_container_flow">
        <div class="row login_page_container m-0 p-0">
            <div class="col-12 col-md-6 p-0 d-none d-md-block" style="height:100vh;">
                <img src="{{ asset('images/login_img_right.png') }}" class="img-fluid" style="height: 100%;width:100%;" />
            </div>
            <div class="col-12 col-md-6">
                <div class="position_form">
                    <span class="d-block mt-5 mb-0" style="color: #676B87;font-size: 18px;">Hello!</span>
                    <div class="d-block mb-3 login_welcomeText">Welcome Back</div>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        @foreach ($errors->all() as $error)
                            <p class="text-warning">{{ $error }}</p>
                        @endforeach
                        <div class="form-group">
                            <input type="email" name="email" placeholder="Email" class="form-control col-12 col-md-8"
                                value="" />
                        </div>
                        <div class="form-group">
                            <span id="removeobscure_pwd" class="removeobscure_pwd"><img id="toggleshowpassword"
                                    src="{{ asset('svg/obscure-password.svg') }}" /></span>
                            <input type="password" name="password" id="password_field" placeholder="Password"
                                class="form-control col-12 col-md-8 pr-4" value="" />
                        </div>
                        <div class="custom-control mr-4">
                            <input type="checkbox" name="remember" class="form-check-input">
                            <label class="form-check-label " for="check-1" style="color: #676B87;">Keep me logged in
                            </label>
                        </div>
                        <button type="submit" class="btn text-white col-12 col-md-8 mt-4"
                            style="background: #000070;border-radius: 5px;">Sign in</button>
                        <p>New here? <a href="{{ route('register') }}">Register</a></p>
                        <span class="d-block my-2 forgotpwd_text mb-md-0"> <a href="{{ route('password.request') }}"
                                style="color: #000070;font-size: 14px;">Forgot Password?</a> </span>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $('form').submit(function() {
            $(this).find("button[type='submit']").prop('disabled', true);
        });
    </script>
@endsection
