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
                                        <span class="d-block price realtime-wallet-balance"></span>
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
                                <div class="container mt-4 mt-lg-4">
                                    <div class="row m-0 p-0" style="">
                                        <div class="col-12">
                                            <span class="d-block mb-2"
                                                style="color: #000070;font-size: 20px;">Cable
                                                Subscription and TV</span>
                                            @foreach ($errors->all() as $err)
                                            <p class="text-danger">{{$err}}</p>
                                            @endforeach
                                        </div>

                                        <div class="container-fluid">
                                            <div class="row">
                                                <div
                                                    class="col-12 col-md-7 mx-auto d-flex flex-column align-items-center">
                                                    <span class="d-block">
                                                        Choose from a range of <b>DStv</b>,<b>Gotv</b> and <b>Startimes</b> bouquets for your entertainment.
                                                        Easy payment and quick value delivery.
                                                    </span>
                                                    <div class="row mt-2 mt-md-5">
                                                        <form method="post" action="{{route('user.paytv')}}">
                                                            @csrf
                                                            <div class="row">
                                                                <div class="col-12 my-1 my-md-0 col-md">
                                                                    <label for="electricy_board" class="mb-0 pb-0"
                                                                        style="color: #000070;">Provider</label>
                                                                    <select name="cable_provider" id="cable_provider"
                                                                        style="color: #000070;" class="custom-select">
                                                                        <option value="" selected>Select Provider</option>
                                                                        @foreach ($boards as $p)
                                                                            <option value="{{$p['serviceID']}}" >{{$p['name']}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="row mt-3 mt-md-4">
                                                                 <div class="col-12 my-1 my-md-0 col-md">
                                                                    <label for="metre_type" class="mb-0 pb-0"
                                                                        style="color: #000070;">Subscription Plan</label>
                                                                    <select name="subscription_plan" id="subscription_plan"
                                                                        style="color: #000070;" class="custom-select">
                                                                        <option value="" selected>Select Subscription Plan
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="row mt-3 mt-md-4">
                                                                <div class="col-12 my-1 my-md-0 col-lg">
                                                                    <label for="metrenumber" class="mb-0 pb-0"
                                                                        style="color: #000070;">Smartcard Number</label>
                                                                    <input type="number" name="smartcard_number" id="smartcard_number"
                                                                        value="{{old('smartcard_number')}}"
                                                                        class="form-control"
                                                                        placeholder="Smartcard Number" />
                                                                </div>
                                                            </div>

                                                            <div class="row mt-3 mt-md-4">
                                                                <div class="col-12 my-1 my-md-0 col-lg">
                                                                    <label for="metrenumber" class="mb-0 pb-0"
                                                                        style="color: #000070;">Owner's name</label>
                                                                    <input type="text" name="owner" id="owner"
                                                                        value="{{old('owner')}}"
                                                                        class="form-control" readonly
                                                                        placeholder="" />
                                                                </div>
                                                            </div>

                                                            <div class="row mt-3 mt-md-4">
                                                                <div class="col-12 my-1 my-md-0 col-lg">
                                                                    <label for="metre_type" class="mb-0 pb-0"style="color: #000070;">Phone number</label>
                                                                        <div class="input-group mb-0 number_inputgdroup mx-auto mx-md-0" style="">
                                                                            <div class="input-group-prepend"
                                                                                style="border: 1px solid rgba(0, 0, 112, 0.25);border-right:0px;border-top-left-radius:5px;border-bottom-left-radius:5px;">
                                                                                <select id="dialcode_select" name="phone_number"
                                                                                    class="signup_custom country_code_form">
                                                                                    <option value="+234">+234</option>
    {{--                                                                                <option value="+91">+91</option>--}}
    {{--                                                                                <option value="+14">+14</option>--}}
                                                                                </select>
                                                                            </div>
                                                                            <input name="phone" type="tel" id="phoneNumber4Power"
                                                                                value="{{old('phone')}}"
                                                                                placeholder="8141894420"
                                                                                class="form-control"
                                                                                style="border-left: 0px;"
                                                                                aria-label="Text input with dropdown button">
    {{--                                                                        <input type="hidden" name="phone"--}}
    {{--                                                                            id="phoneNumber" />--}}
                                                                        </div>
                                                                </div>
                                                            </div>
                                                            <div class="row mt-3 mt-md-4">
                                                                <div class="col-12 my-1 my-md-0 col-lg">
                                                                    <label for="email" class="mb-0 pb-0"
                                                                        style="color: #000070;">Email</label>
                                                                    <input type="email" name="email" id="email"
                                                                        value="{{old('email')}}"
                                                                        class="form-control" onchange="getElectUser()"
                                                                        placeholder="Enter email" />
                                                                </div>
                                                            </div>
                                                            <div class="row mt-3 mt-md-4">
                                                                <div class="col-12 my-1 my-md-0 col-md">
                                                                    <label for="metre_type" class="mb-0 pb-0"
                                                                        style="color: #000070;">Amount</label>
                                                                    <input type="number" name="amount" id="s_amount" class="form-control"
                                                                        value="{{old('amount')}}"
                                                                        placeholder="Amount" id="">
                                                                </div>
                                                            </div>
                                                            <div class="row mt-3 mt-md-4">
                                                                <div class="col-12 my-1 my-md-0 col-md">
                                                                    <label for="pin" class="mb-0 pb-0"
                                                                        style="color: #000070;">Pin</label>
                                                                    <span id="pwd_visibility_toggle" class="pwd_visibility_toggle" style="left: 84%;">
                                                                        <img id="pwd_visibility_toggle2" src="{{asset('svg/obscure-password.svg')}}" />
                                                                    </span>
                                                                    <input type="password" id="walletpin" name="password"
                                                                        class="form-control" placeholder="Pin" />
                                                                </div>
                                                            </div>
                                                            <div class="d-flex mx-auto">
                                                                <button type="submit" class="btn text-white px-4 mt-2 mt-md-4 mx-auto" style="background-color: #000070;">Continue</button>
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
                </div>
            </div>


        </div>
    </div>

    {{-- @include('layouts.partials.live-feeds') --}}
</div>
</div>
</div>

@endsection
