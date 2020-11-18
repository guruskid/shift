@extends('layouts.guest')
@section('guestviewcontent')

<div class="container-fluid">
    <div class="row login_page_container">
        <div class="col-6" style="background-color: #fff;">
            <div class="container mt-5 ml-5">
                <div class="chevron_arrow_bg d-flex align-items-center">
                    <span style="position: relative;top:2.8px;left:1px;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15.41 7.41L14 6L8 12L14 18L15.41 16.59L10.83 12L15.41 7.41Z" fill="#000070"/>
                        </svg>
                    </span>
                    <span class="ml-3" style="color: #000070;">Back</span>
                </div>
                <span class="d-block mt-4 mb-3" style="color: #676B87;font-size: 18px;">Hello!</span>
                <span class="d-block mb-4" style="color: #676B87;font-size: 30px;font-weight:500;">Welcome Back</span>
                <form method="POST" action="{{route('login')}}">
                    @csrf
                    @foreach ($errors->all() as $error)
                    <p class="text-warning">{{ $error }}</p>
                    @endforeach
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Email" class="form-control col-8" value="" />
                    </div>
                    <div class="form-group">
                        <span id="removeobscure_pwd" style="cursor:pointer;position: relative;left:62%;top:28px;z-index:2;"><img id="toggleshowpassword" src="{{asset('svg/obscure-password.svg')}}"/></span>
                        <input type="password" name="password" id="password_field" placeholder="Password" class="form-control col-8 pr-4" value="" />
                    </div>
                    <div class="custom-control mr-4">
                        <input type="checkbox" name="remember" class="form-check-input">
                        <label class="form-check-label " for="check-1" style="color: #676B87;">Keep me logged in
                        </label>
                    </div>
                    <button type="submit" class="btn text-white col-8 mt-4" style="background: #000070;border-radius: 5px;">Sign in</button>
                    <span class="d-block mt-3" style="position: relative;left:270px;">
                        <a href="{{ route('password.request') }}" style="color: #000070;font-size: 14px;
                        ">Forgot Password?</a>
                    </span>
                </form>
            </div>
        </div>
        <div class="col-md-6" style="height:100vh;">
            <img src="{{asset('images/login_img_right.png')}}" class="img-fluid" style="height: 100%;width:100%;" />
        </div>
    </div>
</div>
@endsection