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
                            <div class="card card-body mb-4">
                                <div class="container px-4 d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
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
                                <div class="mt-4" style="width: 100%;border: 1px solid #C9CED6;"></div>

                                {{-- Bitcoin  menu  --}}
                                <div class="container mt-4 mt-lg-5">
                                    <div class="row" style="">
                                        <div class="col-12 col-lg-6">
                                            <span class="d-block" style="color: #000070;font-size: 22px;">Airtime & Data
                                                Subscription</span>
                                            <div class="d-flex flex-column flex-lg-row">
                                                <div
                                                    class="airtime_network_card d-flex justify-content-center align-items-center">
                                                    <img class="img-fluid" src="/isp/airtel.png" style="height:80px;" />
                                                </div>
                                                <div
                                                    class="airtime_network_card d-flex justify-content-center align-items-center">
                                                    <img class="img-fluid" src="/isp/mtn.png" />
                                                </div>
                                                <div
                                                    class="airtime_network_card d-flex justify-content-center align-items-center">
                                                    <img class="img-fluid" src="/isp/9mobile.png" />
                                                </div>
                                                <div
                                                    class="airtime_network_card d-flex justify-content-center align-items-center">
                                                    <img class="img-fluid" src="/isp/glo.png" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-6 py-5"
                                            style="border: 1px solid #EFEFF8;border-radius: 20px;">
                                            <form action="" method="post">
                                                @csrf
                                                <div
                                                    class="d-flex flex-column align-items-center justify-content-around flex-lg-row">
                                                    <div class="d-flex flex-column">
                                                        <div
                                                            class="custom-control custom-radio custom-control-inline my-2">
                                                            <input type="radio" id="buydata" name="buydata"
                                                                class="custom-control-input">
                                                            <label class="custom-control-label" for="buydata"
                                                                style="color: #000070;font-size: 16px;">Data</label>
                                                        </div>
                                                        <div
                                                            class="custom-control custom-radio custom-control-inline my-2">
                                                            <input type="radio" id="buyairtime" name="buydata"
                                                                class="custom-control-input">
                                                            <label class="custom-control-label" for="buyairtime"
                                                                style="color: #000070;font-size: 16px;">Airtime</label>
                                                        </div>
                                                        <div class="form-group mt-4">
                                                            <label for="amount" style="color: #000070;">Amount</label>
                                                            <input type="text" class="form-control" id="amount" />
                                                        </div>
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <div
                                                            class="custom-control custom-radio custom-control-inline my-2">
                                                            <input type="radio" id="rechargemyself"
                                                                name="rechargemyself" class="custom-control-input">
                                                            <label class="custom-control-label" for="rechargemyself"
                                                                style="color: #000070;font-size: 16px;">Recharging for
                                                                myself</label>
                                                        </div>
                                                        <div
                                                            class="custom-control custom-radio custom-control-inline my-2">
                                                            <input type="radio" id="buyother" name="buyother"
                                                                class="custom-control-input">
                                                            <label class="custom-control-label" for="buyother"
                                                                style="color: #000070;font-size: 16px;">Other</label>
                                                        </div>
                                                        <div class="form-group mt-4">
                                                            <label for="pin" style="color: #000070;">Pin</label>
                                                            <span id="togglepinvisibility" style="cursor:pointer;position: relative;left:78%;top:36px;"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M9.99967 5.83333C12.2997 5.83333 14.1663 7.7 14.1663 10C14.1663 10.5417 14.058 11.05 13.8663 11.525L16.2997 13.9583C17.558 12.9083 18.5497 11.55 19.158 10C17.7163 6.34167 14.158 3.75 9.99134 3.75C8.82467 3.75 7.70801 3.95833 6.67467 4.33333L8.47467 6.13333C8.94967 5.94167 9.45801 5.83333 9.99967 5.83333ZM1.66634 3.55833L3.56634 5.45833L3.94967 5.84167C2.56634 6.91667 1.48301 8.35 0.833008 10C2.27467 13.6583 5.83301 16.25 9.99967 16.25C11.2913 16.25 12.5247 16 13.6497 15.55L13.9997 15.9L16.4413 18.3333L17.4997 17.275L2.72467 2.5L1.66634 3.55833ZM6.27467 8.16667L7.56634 9.45833C7.52467 9.63333 7.49967 9.81667 7.49967 10C7.49967 11.3833 8.61634 12.5 9.99967 12.5C10.183 12.5 10.3663 12.475 10.5413 12.4333L11.833 13.725C11.2747 14 10.658 14.1667 9.99967 14.1667C7.69967 14.1667 5.83301 12.3 5.83301 10C5.83301 9.34167 5.99967 8.725 6.27467 8.16667ZM9.86634 7.51667L12.4913 10.1417L12.508 10.0083C12.508 8.625 11.3913 7.50833 10.008 7.50833L9.86634 7.51667Z" fill="#000070"/>
                                                                </svg>
                                                                </span>
                                                            <input type="password" class="form-control" id="pinfortrx" style="padding-right: 30px;" />
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                                <button type="submit" class="btn text-white mt-4"
                                                    style="position: relative;left:35%;background: #000070;border-radius: 5px;width: 160px;height: 40px;">Recharge</button>

                                            </form>
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