@extends('layouts.guest')
@section('title', 'Verify Email')
@section('guestviewcontent')


<div class="container-fluid p-0 m-0 login_container_flow">
    <div class="row  m-0 p-0">
        <div class="col-12 col-md-6 p-0 d-none d-md-block" style="height:100vh;">
            <img src="{{asset('images/login_img_right.png')}}" class="img-fluid" style="height: 100%;width:100%;" />
        </div>
        <div class="col-md-6 p-5 my-auto">
            <div class="d-block mb-3 login_welcomeText">Verify Email </div>

            @if (session('resent'))
            <div class="alert alert-success" role="alert">
                {{ __('A fresh verification link has been sent to your email address.') }}
            </div>
            @endif

            <p>{{ __('Before proceeding, please check your email for a verification link.') }} Click 'Resend' if you did
                not receive the email</p>

            <a href="{{ route('verification.resend') }}">
                <button class="btn btn-block text-white"
                    style="background: #000070;border-radius: 5px; ">{{ __('Resend') }}</button>
            </a>


        </div>
    </div>
</div>
@endsection
