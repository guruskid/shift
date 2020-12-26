@php
$countries = App\Country::orderBy('phonecode', 'asc')->get();
@endphp
@extends('layouts.guest')
@section('title', 'Register')
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
                <div class="d-block mb-3 ml-2 ml-md-3 login_welcomeText">Bank Details</div>
                <form id="signup_form" method="POST" action="{{route('signup.add-bank')}}"> @csrf
                    @foreach ($errors->all() as $error)
                    <p class="text-warning">{{ $error }}</p>
                    @endforeach
                    <div class="mt-2 ">
                        <select name="bank_code" class="form-control col-11 mx-auto mx-md-0 col-lg-8" id="bank-name">
                            <option selected>Bank name</option>
                            @foreach ($banks as $bank)
                            <option value="{{$bank->code}}">{{$bank->name}}</option>
                            @endforeach
                        </select>
                        <div class="form-group mt-3">
                            <input type="text" id="account-number" name="account_number" class="form-control col-11 mx-auto mx-md-0 col-lg-8"
                                placeholder="Account number" />
                        </div>
                        <div class="form-group">
                            <input type="text" name="account_name" class="acct-name form-control col-11 mx-auto mx-md-0 col-lg-8" readonly />
                        </div>
                        <div class="form-group">
                            <input type="text" name="otp" placeholder="Phone OTP Code" class="form-control col-11 mx-auto mx-md-0 col-lg-8"  />
                            <small>Didn't get the OTP? <a href="#" id="otp-text" onclick="resendOtp()">Resend</a></small>
                        </div>
                        <div class="form-check ml-3 ml-md-0">
                            <input class="form-check-input" type="checkbox" value="" id="defaultCheck1">
                            <label class="form-check-label form-check-label-sm" for="defaultCheck1">
                                I agree to to the <a href="#" target="_blank" rel="noopener noreferrer">terms &
                                    conditions</a>
                            </label>
                        </div>
                        <button id="sign-up-btn" disabled class="btn text-white col-11 signup_first_step col-lg-8 mt-4"
                            style="background: #000070;border-radius: 5px;">Finish</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection
