@extends('layouts.user')
@section('content')
<div class="app-main">
    <div class="app-sidebar sidebar-shadow">
        <div class="app-header__logo">
            <div class="logo-src"></div>
            <div class="header__pane ml-auto">
                <div>
                    <button type="button" class="hamburger close-sidebar-btn hamburger--elastic"
                        data-class="closed-sidebar">
                        <span class="hamburger-box">
                            <span class="hamburger-inner"></span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
        <div class="app-header__mobile-menu">
            <div>
                <button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
                    <span class="hamburger-box">
                        <span class="hamburger-inner"></span>
                    </span>
                </button>
            </div>
        </div>
        <div class="app-header__menu">
            <span>
                <button type="button" class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
                    <span class="btn-icon-wrapper">
                        <i class="fa fa-ellipsis-v fa-w-6"></i>
                    </span>
                </button>
            </span>
        </div>
        {{-- User Side bar --}}
        @include('layouts.partials.user')
    </div>

    {{-- Content Starts here --}}
    <div class="app-main__outer">
        <div class="app-main__inner">

            <div id="content" class="main-content">
                <div class="layout-px-spacing">
                    <div class="row layout-top-spacing"></div>
                    <div class="row layout-top-spacing">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                            <div class="widget widget-chart-one">
                                <div class="widget-heading">
                                    <div>
                                        <span class="h3 giftcard-text">Recharge</span>
                                    </div>
                                    <div class="widget-n text-center" style="justify-content: center;">
                                        <span class="d-block" style="h6 walletbalance-text">Wallet Balance</span>
                                        <span class="d-block price">₦56,758</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="card card-body mb-4 card-body_buyairtime">
                                <div class="container px-4 d-flex justify-content-between align-items-center">
                                    <div class="d-none d-md-flex align-items-center">
                                        <div
                                            style="background: rgba(0, 0, 112, 0.25);width:24px;height:24px;border-radius:12px;">
                                            <span style="position: relative;left:33%;top:0;">
                                                <svg width="8" height="12" viewBox="0 0 8 12" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M7.41 1.41L6 0L0 6L6 12L7.41 10.59L2.83 6L7.41 1.41Z"
                                                        fill="#000070" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="ml-2" style="color: #000070;font-size: 20px;">Back</div>
                                    </div>
                                </div>
                                {{-- border line --}}
                                <div class="mt-4 d-none d-lg-block" style="width: 100%;border: 1px solid #C9CED6;">
                                </div>

                                {{-- Bitcoin  menu  --}}
                                <div class="container mt-4 mt-lg-5">
                                    <div class="row m-0 p-0" style="">
                                        <div class="col-12 col-lg-6">
                                            <span class="d-block mb-2" style="color: #000070;font-size: 20px;">Cable
                                                Subscription and TV</span>
                                            @foreach ($errors->all() as $err)
                                            <p class="text-danger">{{$err}}</p>
                                            @endforeach
                                            <div
                                                class="d-flex flex-row flex-wrap justify-content-around justify-content-lg-between airtimechoice_container mx-0">

                                                <div class="airtime_network_card my-2 my-lg-0 mx-0 mx-lg-0 d-flex justify-content-center align-items-center "
                                                    alt="dstv">
                                                    <img class="img-fluid airtimelogo" src="{{asset('dstv.png')}}" />
                                                </div>
                                                <div class="airtime_network_card my-2 my-lg-0 mx-0 mx-lg-0 d-flex justify-content-center align-items-center"
                                                    alt="hitv">
                                                    <img class="img-fluid airtimelogo" src="{{asset('hitv.png')}}" />
                                                </div>
                                                <div class="airtime_network_card my-2 my-lg-0 mx-0 mx-lg-0 d-flex justify-content-center align-items-center"
                                                    alt="gotv">
                                                    <img class="img-fluid airtimelogo" src="{{asset('gotv.png')}}" />
                                                </div>
                                                <div class="airtime_network_card my-2 my-lg-0 mx-0 mx-lg-0 d-flex justify-content-center align-items-center"
                                                    alt="startimes">
                                                    <img class="img-fluid airtimelogo"
                                                        src="{{asset('startimes.png')}}" />
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-12 col-lg-6 py-3 py-lg-5 mt-5 mt-lg-0 buyairtime_border">
                                            <div class="d-flex flex-column flex-md-row justify-content-md-between">
                                                <div class="d-flex justify-content-center align-items-center" style="border: 2px solid #EFEFF8;border-radius: 5px;width:25%;height:20%;">
                                                    <img class="img-fluid airtimelogo" src="{{asset('dstv.png')}}" />
                                                </div>
                                                <div class="mt-md-3 mt-lg-0 ml-md-2">
                                                    <span class="h5 d-block" style="color: #000070;opacity: 0.7;">Dstv
                                                        Subscription</span>
                                                    <span class="d-block">
                                                        Choose from a range of DStv bouquets for your entertainment.
                                                        Easy payment and quick value delivery.
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="p-2">
                                                <form action="" method="post">
                                                    @csrf
                                                    <div class="row mt-lg-3">
                                                        <div class="col-12 col-md my-2 my-lg-0">
                                                            <label for="bouquet" class="mb-0 pb-0">Bouquet</label>
                                                            <select name="" id="bouquet" class="custom-select">
                                                                <option value="">Starter</option>
                                                                <option value="">Family</option>
                                                                <option value="">Generation</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-12 col-md my-2 my-lg-0">
                                                            <label for="phone" class="mb-0 pb-0">Phone number</label>
                                                            <div class="input-group mb-0 number_inputgdroup mx-auto mx-md-0"
                                                                style="">
                                                                <div class="input-group-prepend"
                                                                    style="border: 1px solid rgba(0, 0, 112, 0.25);border-right:0px;border-top-left-radius:5px;border-bottom-left-radius:5px;">
                                                                    <select id="dialcode_select" name="phone"
                                                                        class="signup_custom country_code_form">
                                                                        <option value="+234">+234</option>
                                                                        <option value="+91">+91</option>
                                                                        <option value="+14">+14</option>
                                                                    </select>
                                                                </div>
                                                                <input type="tel" id="phoneNumber"
                                                                    placeholder="8141894420" class="form-control"
                                                                    style="border-left: 0px;"
                                                                    aria-label="Text input with dropdown button">
                                                                <input type="hidden" name="phone" id="signup_phone" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-lg-3">
                                                        <div class="col-12 col-md my-2 my-lg-0">
                                                            <label for="scn" class="mb-0 pb-0">Dstv Smartcard
                                                                Number</label>
                                                            <input type="number" name=""
                                                                placeholder="Enter smartcard number" id="scn"
                                                                class="form-control" />
                                                        </div>
                                                        <div class="col-12 col-md my-2 my-lg-0">
                                                            <label for="email" class="mb-0 pb-0">Email Address</label>
                                                            <input type="email" name="" id="email" class="form-control"
                                                                placeholder="Email Address" />
                                                        </div>
                                                    </div>

                                                    <div class="row mt-lg-3">
                                                        <div class="col-12 col-md my-2 my-lg-0">
                                                            <label for="amount" class="mb-0 pb-0">Amount</label>
                                                            <input type="number" name="" placeholder="Enter amount"
                                                                id="amount" class="form-control" />
                                                        </div>
                                                        <div class="col-12 col-md my-2 my-lg-0">
                                                            <label for="pin" class="mb-0 pb-0">Pin</label>
                                                            <span id="pwd_visibility_toggle" class="pwd_visibility_toggle">
                                                                <img id="pwd_visibility_toggle2" src="{{asset('svg/obscure-password.svg')}}" />
                                                            </span>
                                                            <input type="password" id="walletpin" class="form-control"
                                                                placeholder="Pin" />
                                                        </div>
                                                    </div>
                                                    <div class="d-flex justify-content-center mt-4 ">
                                                        <button type="submit" class="btn text-white px-lg-3" style="background: #000070;">Continue</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>

            {{-- @include('layouts.partials.live-feeds') --}}
        </div>
    </div>
</div>

@endsection