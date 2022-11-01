@php
$countries = App\Country::orderBy('phonecode', 'asc')->get();
@endphp
@extends('layouts.guest')
@section('title', 'Phone number verification')
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
                <div class="d-block mb-3 ml-2 ml-md-3 login_welcomeText">Phone number verification</div>
                <form id="signup_form" method="POST" action="{{route('user.verify-phone-number')}}"> @csrf
                    @foreach ($errors->all() as $error)
                    <p class="text-warning">{{ $error }}</p>
                    @endforeach
                    <div class="mt-2 ">
                        <div class="form-group d-none">
                            <input type="text" name="username" required value="{{ Auth::user()->username ?? '' }}" placeholder="Username" class="form-control col-11 mx-auto mx-md-0 col-lg-8"  />
                        </div>
                        <div class="form-group">
                            <div class="input-group mb-0 number_inputgroup mx-auto mx-md-0 ">
                                <div class="input-group-prepend"
                                    style="border: 1px solid rgba(0, 0, 112, 0.25);border-right:0px;border-top-left-radius:5px;border-bottom-left-radius:5px;">
                                    <select name="country_id" id="country-id" class="form-control border-0">
                                        <option value="156">+234 (NG)</option>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->id }}">+{{ $country->phonecode }} ({{ $country->iso }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <input type="tel" id="signup_phonenumber" min="1" maxlength="10" name="phone" value="{{ Auth::user()->phone ?? '' }}"  placeholder="8141894420" class="form-control col-12" style="border-left: 0px;"
                                pattern="[1-9]\d*" title="Number not starting with 0" >
                            </div>
                            <small>Number must not start with '0'. <a href="#" id="otp-text" onclick="sendOtp()">Send OTP</a></small>
                        </div>
                        <div class="form-group">
                            <input type="text" name="otp" placeholder="Phone OTP Code" class="form-control col-11 mx-auto mx-md-0 col-lg-8"  />
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
