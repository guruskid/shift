@extends('layouts.guest')
@section('guestviewcontent')


<div class="container-fluid p-0 m-0 login_container_flow">
    <div class="row login_page_container m-0 p-0">
        <div class="col-12 col-md-6 p-0 d-none d-md-block" style="height:100vh;">
            <img src="{{asset('images/login_img_right.png')}}" class="img-fluid" style="height: 100%;width:100%;" />
        </div>
        <div class="col-12 col-md-6">
            <div class="position_form">
                <span class="d-block mt-5 mb-0 ml-2 ml-md-3" style="color: #676B87;font-size: 18px;">Hello!</span>
                <div class="d-block mb-3 ml-2 ml-md-3 login_welcomeText">Welcome to Dantown</div>
                <form id="signup_form" method="POST" action="{{route('login')}}">
                    @csrf
                    @foreach ($errors->all() as $error)
                    <p class="text-warning">{{ $error }}</p>
                    @endforeach
                    <div id="step_one">
                        <div class="form-group">
                            <input type="text" name="username" placeholder="Username"
                                class="form-control col-11 mx-auto mx-md-0 col-lg-8" value="" />
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" placeholder="Email" class="form-control col-11 mx-auto mx-md-0 col-lg-8"
                                value="" />
                        </div>
                        <div class="input-group mb-0 number_inputgroup mx-auto mx-md-0" style="">
                            <div class="input-group-prepend"
                                style="border: 1px solid rgba(0, 0, 112, 0.25);border-right:0px;border-top-left-radius:5px;border-bottom-left-radius:5px;">
                                <button class="btn btn-outline-secondary signup_custom dropdown-toggle" type="button"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">+234</button>
                                <div class="dropdown-menu" id="dialcode_select">
                                    <a class="dropdown-item" href="#">+234</a>
                                    <a class="dropdown-item" href="#">+81</a>
                                </div>
                            </div>
                            <input type="tel" id="signup_phonenumber" placeholder="8141894420" class="form-control col-12" style="border-left: 0px;"
                                aria-label="Text input with dropdown button">
                                <input type="hidden" id="phone" />
                        </div>
                        <div class="form-group mb-0">
                            <span id="removeobscure_pwd" class="removeobscure_pwd"><img id="toggleshowpassword"
                                    src="{{asset('svg/obscure-password.svg')}}" /></span>
                            <input type="password" name="password" id="password_field" placeholder="Password"
                                class="form-control col-11 col-lg-8 mx-auto mx-md-0 pr-4" value="" />
                        </div>
                        <div class="form-group mb-0">
                            <span id="removeobscure_pwd" class="removeobscure_pwd"><img id="toggleshowpassword"
                                    src="{{asset('svg/obscure-password.svg')}}" /></span>
                            <input type="password" name="password" id="password_field" placeholder="Confirm password"
                                class="form-control col-11 col-lg-8 mx-auto mx-md-0 pr-4" value="" />
                        </div>
                        <button type="button" id="step_one_btn" class="btn text-white col-11 signup_first_step col-lg-8 mt-4"
                            style="background: #000070;border-radius: 5px;">Continue</button>
                    </div>



                    <div id="step_two" class="mt-2 step_two_container">
                        <select class="form-control col-11 mx-auto mx-md-0 col-lg-8" id="exampleFormControlSelect1">
                            <option selected>Bank name</option>
                            <option>1</option>
                            <option>2</option>
                            <option>3</option>
                        </select>
                        <div class="form-group mt-3">
                            <input type="text" class="form-control col-11 mx-auto mx-md-0 col-lg-8" placeholder="Account number" />
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control col-11 mx-auto mx-md-0 col-lg-8" readonly />
                        </div>
                        <div class="form-check ml-3 ml-md-0">
                            <input class="form-check-input" type="checkbox" value="" id="defaultCheck1">
                            <label class="form-check-label form-check-label-sm" for="defaultCheck1">
                                I agree to to the <a href="#" target="_blank" rel="noopener noreferrer">terms &
                                    conditions</a>
                            </label>
                        </div>
                        <button type="button" class="btn text-white col-11 signup_first_step col-lg-8 mt-4"
                        style="background: #000070;border-radius: 5px;">Sign up</button>
                    </div>

                </form>
            </div>
        </div>


    </div>
</div>


@endsection
