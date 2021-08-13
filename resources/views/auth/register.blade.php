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
                <div class="d-block mb-3 ml-2 ml-md-3 login_welcomeText">Welcome to Dantown</div>
                <form id="signup_form" method="POST" action="{{route('register')}}">
                    @csrf
                    @foreach ($errors->all() as $error)
                    <p class="text-warning">{{ $error }}</p>
                    @endforeach
                    <div id="step_one">
                        <div class="form-group">
                            <input type="text" name="first_name" required placeholder="First Name" class="form-control col-11 mx-auto mx-md-0 col-lg-8">
                        </div>

                        <div class="form-group">
                            <input type="text" name="last_name"  placeholder="Lastname"
                                class="form-control col-11 mx-auto mx-md-0 col-lg-8" />
                        </div>
                        <div class="form-group">
                            <input type="text" id="username" name="username" required placeholder="Username"
                                class="form-control col-11 mx-auto mx-md-0 col-lg-8" />
                        </div>
                        <div class="form-group mb-0">
                            <input type="email" name="email" required placeholder="Email"
                                class="form-control col-11 mx-auto mx-md-0 col-lg-8">
                        </div>
                        {{-- <div class="form-group mb-0">
                            <select name="country_id" class="form-control col-11 mx-auto mx-md-0 col-lg-8">
                                <option value="156">Nigeria</option>
                                @foreach ($countries as $country)
                                <option value="{{ $country->id }}">{{ $country->nicename }}</option>
                                @endforeach
                            </select>
                        </div> --}}
                        {{-- <div class="input-group mb-0 number_inputgroup mx-auto mx-md-0" style="">
                            <div class="input-group-prepend"
                                style="border: 1px solid rgba(0, 0, 112, 0.25);border-right:0px;border-top-left-radius:5px;border-bottom-left-radius:5px;">
                                <select name="country_id" class="form-control">
                                    <option value="156">+234</option>
                                    @foreach ($countries as $country)
                                    <option value="{{ $country->id }}">+ {{ $country->phonecode }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <input type="tel" id="signup_phonenumber" min="1" maxlength="10" name="phone" autofocus
                                placeholder="8141894420" class="form-control col-12" style="border-left: 0px;"
                                pattern="[1-9]\d*" title="Number not starting with 0">
                        </div> --}}
                        <div class="form-group mb-0">
                            <span id="removeobscure_pwd" class="removeobscure_pwd"><img id="toggleshowpassword"
                                    src="{{asset('svg/obscure-password.svg')}}" /></span>
                            <input type="password" name="password" id="password_field" placeholder="Password"
                                class="form-control col-11 col-lg-8 mx-auto mx-md-0 pr-4" value="" />
                        </div>
                        <div class="form-group mb-0">
                            <span id="removeobscure_pwd" class="removeobscure_pwd"><img id="toggleshowpassword"
                                    src="{{asset('svg/obscure-password.svg')}}" /></span>
                            <input type="password" name="password_confirmation" id="password_field"
                                placeholder="Confirm password" class="form-control col-11 col-lg-8 mx-auto mx-md-0 pr-4"
                                value="" />
                        </div>
                        <button class="btn text-white col-11 signup_first_step col-lg-8 mt-4"
                            style="background: #000070;border-radius: 5px;">Continue</button>
                        <span class="d-block my-2  mb-md-0">Already have an account? <a href="{{ route('login') }}"
                                style="color: #000070;">Login</a> </span>
                    </div>
                </form>
            </div>
        </div>


    </div>
</div>


@endsection
@section('scripts')
<script>
    $('#username').keyup(function (e) {
        $(this).val($(this).val().split(" ").join(""));
        /* $(this)
        $('#store-username').val(username.trim()) */

    });

</script>
@endsection
