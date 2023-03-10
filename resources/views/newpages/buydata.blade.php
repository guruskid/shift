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
                                <div class="mt-4 d-none d-lg-block" style="width: 100%;border: 1px solid #C9CED6;"></div>

                                {{-- Bitcoin  menu  --}}
                                <div class="container mt-4 mt-lg-5">
                                    <div class="row m-0 p-0" style="">
                                        <div class="col-12 col-lg-6">
                                            <span class="d-block mb-2" style="color: #000070;font-size: 22px;">Data Subscription</span>
                                            <div class="d-flex flex-row flex-wrap justify-content-around justify-content-lg-between airtimechoice_container mx-0">
                                            <div
                                                    class="airtime_network_card data-card  my-2 my-lg-0 mx-0 mx-lg-0 d-flex justify-content-center align-items-center active_airtime_choice" alt="airtel-data">
                                                    <img class="img-fluid airtimelogo" src="/isp/airtel.png" />
                                                </div>
                                                <div
                                                    class="airtime_network_card data-card my-2 my-lg-0 mx-0 mx-lg-0 d-flex justify-content-center align-items-center" alt="mtn-data">
                                                    <img class="img-fluid airtimelogo" src="/isp/mtn.png" />
                                                </div>
                                                <div
                                                    class="airtime_network_card data-card my-2 my-lg-0 mx-0 mx-lg-0 d-flex justify-content-center align-items-center" alt="etisalat-data">
                                                    <img class="img-fluid airtimelogo" src="/isp/9mobile.png" />
                                                </div>
                                                <div
                                                    class="airtime_network_card data-card my-2 my-lg-0 mx-0 mx-lg-0 d-flex justify-content-center align-items-center" alt="glo-data">
                                                    <img class="img-fluid airtimelogo" src="/isp/glo.png" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-6 py-3 py-lg-5 mt-5 mt-lg-0 buyairtime_border">
                                            <form action="{{ route('user.buy-data') }}" method="post">
                                                @csrf
                                                <div
                                                    class="d-flex flex-column align-items-center justify-content-around">
                                                    <div class="d-flex flex-column flex-md-row">
                                                        <input type="hidden" name="network" class="airtimechoice datachoice" value="airtel-data" />
                                                        <input type="hidden" name="amount" class="airtimechoice datachoice bundle-amt" />
                                                        <div
                                                            class="custom-control custom-radio custom-control-inline my-2 px-1 px-lg-5 py-2 buyairtime_choice_layer">
                                                            <input type="radio" id="buydata" name="rechargetype" value="self" class="custom-control-input" checked>
                                                            <label class="custom-control-label rechargemyself_labeltext" for="buydata">Subscribing for myself</label>
                                                        </div>
                                                        <div
                                                            class="custom-control custom-radio custom-control-inline my-2 px-5 py-2 buyairtime_choice_layer">
                                                            <input type="radio" id="buyother" name="rechargetype"
                                                                class="custom-control-input" value="other">
                                                            <label class="custom-control-label rechargemyself_labeltext" for="buyother">Others</label>
                                                        </div>
                                                    </div>
                                                    <div class="mt-4 d-md-none" style="width: 240px;height:0px;border:1px solid #DBDBEE;"></div>
                                                    <div class="d-flex flex-column flex-md-row justify-content-center">
                                                        {{-- <div class="form-group mt-4 mx-2">
                                                            <label for="amount" style="color: #000070;">Package</label>
                                                            <input type="text" class="form-control recharge_amount" id="amount" />
                                                        </div> --}}
                                                        <div class="form-group mt-4 mx-2 col-12 col-md-6">
                                                            <label for="amount" style="color: #000070;">Data Bundle</label>
                                                            <select class="form-control recharge_amount col-12 data-bundle" name="bundle" id="">
                                                              <option>Select Bundle</option>
                                                              @foreach($variations as $key => $variation)
                                                                  <option value="{{$variation['variation_code']}}" data-amount="{{$variation['variation_amount']}}">{{$variation['variation_name']}}</option>
                                                              @endforeach
                                                            </select>
                                                          </div>
                                                        <div class="form-group mt-2 mt-md-4 mx-2">
                                                            <label for="pin" style="color: #000070;">Pin</label>
                                                            <span id="togglepinvisibility" class="togglevisibility">
                                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M9.99967 5.83333C12.2997 5.83333 14.1663 7.7 14.1663 10C14.1663 10.5417 14.058 11.05 13.8663 11.525L16.2997 13.9583C17.558 12.9083 18.5497 11.55 19.158 10C17.7163 6.34167 14.158 3.75 9.99134 3.75C8.82467 3.75 7.70801 3.95833 6.67467 4.33333L8.47467 6.13333C8.94967 5.94167 9.45801 5.83333 9.99967 5.83333ZM1.66634 3.55833L3.56634 5.45833L3.94967 5.84167C2.56634 6.91667 1.48301 8.35 0.833008 10C2.27467 13.6583 5.83301 16.25 9.99967 16.25C11.2913 16.25 12.5247 16 13.6497 15.55L13.9997 15.9L16.4413 18.3333L17.4997 17.275L2.72467 2.5L1.66634 3.55833ZM6.27467 8.16667L7.56634 9.45833C7.52467 9.63333 7.49967 9.81667 7.49967 10C7.49967 11.3833 8.61634 12.5 9.99967 12.5C10.183 12.5 10.3663 12.475 10.5413 12.4333L11.833 13.725C11.2747 14 10.658 14.1667 9.99967 14.1667C7.69967 14.1667 5.83301 12.3 5.83301 10C5.83301 9.34167 5.99967 8.725 6.27467 8.16667ZM9.86634 7.51667L12.4913 10.1417L12.508 10.0083C12.508 8.625 11.3913 7.50833 10.008 7.50833L9.86634 7.51667Z" fill="#000070"/>
                                                                </svg>
                                                                </span>
                                                                <span id="showvisibility" class="togglevisibility" style="display: none;">
                                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path d="M10 3.75C5.83334 3.75 2.27501 6.34167 0.833344 10C2.27501 13.6583 5.83334 16.25 10 16.25C14.1667 16.25 17.725 13.6583 19.1667 10C17.725 6.34167 14.1667 3.75 10 3.75ZM10 14.1667C7.70001 14.1667 5.83334 12.3 5.83334 10C5.83334 7.7 7.70001 5.83333 10 5.83333C12.3 5.83333 14.1667 7.7 14.1667 10C14.1667 12.3 12.3 14.1667 10 14.1667ZM10 7.5C8.61668 7.5 7.50001 8.61667 7.50001 10C7.50001 11.3833 8.61668 12.5 10 12.5C11.3833 12.5 12.5 11.3833 12.5 10C12.5 8.61667 11.3833 7.5 10 7.5Z" fill="black"/>
                                                                        </svg>
                                                                        
                                                                </span>
                                                            <input type="password" name="password" class="form-control" id="pinfortrx" style="padding-right: 30px;" />
                                                        </div>
                                                        
                                                    </div>
                                                    <div id="otherphonenumber" class="col-12 col-md-7" style="display: none;">
                                                        <div class="input-group mb-3">
                                                            <input type="hidden" id="fullphonenumber" name="phone" value="" />
                                                            <input type="hidden" id="dcode" value="+234" />
                                                            <div class="input-group-prepend">
                                                                <select id="swapcountrycode" style="border: 1px solid rgba(0, 0, 112, 0.25);">
                                                                    <option value="+234">+234</option>
                                                                    <option value="+91">+91</option>
                                                                </select>
                                                            </div>
                                                            <input type="text" id="phonenumber" class="form-control" style="border-left:0px;" aria-label="Text input with dropdown button">
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="recharge-submitbutton-container">
                                                    <button type="submit" id="rechargebtn" class="btn text-white mt-4 recharge-submitbutton">Recharge</button>
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

            {{-- @include('layouts.partials.live-feeds') --}}
        </div>
    </div>
</div>

@endsection