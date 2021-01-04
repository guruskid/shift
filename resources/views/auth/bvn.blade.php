@php
$countries = App\Country::orderBy('phonecode', 'asc')->get();
@endphp
@extends('layouts.guest')
@section('title', 'BVN Verification')
@section('guestviewcontent')

<div class="container-fluid p-0 m-0 login_container_flow">
    <div class="row login_page_container m-0 p-0">
        <div class="col-12 col-lg-6 p-0 d-none d-lg-block" style="height:100vh;">
            <img src="{{asset('images/login_img_right.png')}}" class="img-fluid" style="height: 100%;width:100%;" />
        </div>
        <div class="col-12 col-md-6 mx-md-auto">
            <div class="col-7 d-lg-none mx-auto mt-4 mb-md-5 mt-lg-0">
                <img class="img-fluid" src="{{asset('logo_bg.png')}}" />
            </div>
            <div class="position_form mt-md-5 mt-lg-0">
                <span class="d-block mt-5 mb-0 ml-2 ml-md-3" style="color: #676B87;font-size: 18px;">Hello!</span>
                <div class="d-block mb-3 ml-2 ml-md-3 login_welcomeText">BVN Verification</div>
                <form id="signup_form" method="POST" action="{{route('user.verify-bvn-otp')}}"> @csrf
                    @foreach ($errors->all() as $error)
                    <p class="text-warning">{{ $error }}</p>
                    @endforeach
                    <div class="mt-2 ">
                        <div class="form-group">
                            <input type="text" name="bvn" id="bvn" required  placeholder="BVN" class="form-control col-11 mx-auto mx-md-0 col-lg-8"  />
                            <a href="#" id="send-otp" onclick="verifyBvn()">Send code</a>
                        </div>
                        <div class="form-group">
                            <input type="text" name="otp" placeholder="OTP Code" class="form-control col-11 mx-auto mx-md-0 col-lg-8"  />
                        </div>
                        <button id="sign-up-btn"  class="btn text-white col-11 signup_first_step col-lg-8 mt-4"
                            style="background: #000070;border-radius: 5px;">Verify</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection
