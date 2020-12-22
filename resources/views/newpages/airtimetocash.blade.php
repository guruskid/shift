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
                                        <span class="h3 giftcard-text">Airtime to Cash</span>
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
                            <div class="widget widget-chart-one">
                                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-center pb-2"
                                    style="border-bottom: 1px solid #C9CED6;">
                                    <div class="list-cards-title primary-color" style="line-height: 40px;">
                                        <span class="ml-1" style="color: rgba(0, 0, 112, 0.75);">Convert to cash</span>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between flex-column flex-lg-row mt-4">
                                    <div class="d-flex flex-wrap network_card_container justify-content-center justify-content-md-center">
                                        <div id="airtel" class="mx-2 my-2 my-lg-0 network_cards">
                                            <img class="img-fluid isp_image" src="{{asset('isp/airtel.png')}}" />
                                        </div>
                                        <div id="mtn" class="mx-2 my-2 my-lg-0 network_cards">
                                            <img class="img-fluid isp_image" src="{{asset('isp/mtn.png')}}" />
                                        </div>
                                        <div id="9mobile" class="mx-2 my-2 my-lg-0 network_cards">
                                            <img class="img-fluid isp_image" src="{{asset('isp/9mobile.png')}}" />
                                        </div>
                                        <div id="glo" class="mx-2 my-2 my-lg-0 network_cards airtime_card_last">
                                            <img class="img-fluid isp_image" src="{{asset('isp/glo.png')}}" />
                                        </div>
                                    </div>
<<<<<<< HEAD

=======
>>>>>>> 086b3efb610a3564af1d662aacb50750170c8871


                                    <div class="d-flex flex-column pt-4 mt-2 mt-md-5 mt-lg-0 align-items-center recharge_form_container">
                                        <div class="px-3"
                                            style="background-image: url('{{asset('newpages/assets/img/airtocash_bg.png')}}');width:90%;box-shadow: 0px 2px 10px rgba(166, 166, 166, 0.25);border-radius: 20px;">
                                            <div class="d-flex flex-column flex-md-row mb-0">
                                                <div class="ml-3 mt-3">
                                                    <img class="img-fluid" style="width: 113px;height:50px;" src="{{asset('isp/mtn.png')}}" />
                                                </div>
                                                <div>
                                                    <div class="airtime_percentage">
                                                        <span class="d-block"
                                                            style="color: rgba(0, 0, 112, 0.75);">You'll
                                                            receive <span style="font-size: 38px;">70%</span> </span>
                                                        <span class="d-block"
                                                            style="color: rgba(0, 0, 112, 0.75);position: relative;left:25%;">of
                                                            the amount sent</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-center mb-2 mt-3 airtime_info_text">To Send
                                                Dial *123*Phonenumber*amount*100#</div>
                                        </div>
                                        <div class="mt-4 mb-2 text-center"
                                            style="font-size:13px;color: rgba(0, 0, 112, 0.75);opacity: 0.7;">Please
                                            enter your phone number and the amount you wish to send</div>
                                        <form action="{{route('user.airtime-to-cash')}}" method="post">
                                            @csrf
                                            <input type="hidden" id="isp_input">
                                            <div class="row">
                                                <div class="col-11 mx-auto mt-2 mt-lg-0 col-lg">
                                                    <label for="phone_number" class="label_text">Phone number</label>
                                                    <div class="input-group mb-2 mr-sm-2">
                                                        <div class="input-group-prepend" style="border-radius: 30px;">
                                                            <div class="input-group-text"
                                                                style="background-color: #fff;border-top-left-radius: 25px;border-bottom-left-radius: 25px;">
                                                                +234</div>
                                                        </div>
                                                        <input  type="number"
                                                            class="form-control bitcoin-input-radius" value=""
                                                            id="phone_number">
                                                    </div>
                                                </div>
                                                <div class="col-11 mx-auto mt-2 mt-lg-0 col-lg-5">
                                                    <label for="airtime_amount" class="label_text">Amount</label>
                                                    <div class="input-group mb-2 mr-sm-2">
                                                        <input type="number"
                                                            class="form-control full-input-radius" value=""
                                                            id="airtime_amount" onchange="getAirtimeAmount()">
                                                    </div>
                                                </div>
                                            </div>
                                            <span class="d-block text-center my-3"
                                                style="color: rgba(0, 0, 112, 0.75);">You're getting: <span
<<<<<<< HEAD
                                                    style="font-size: 24px;" id="new-amount">N0</span> </span>
                                            <span class="d-block my-4"
=======
                                                    style="font-size: 24px;">N20,300</span> </span>
                                            <span class="d-block my-4 ml-2 ml-lg-0"
>>>>>>> 086b3efb610a3564af1d662aacb50750170c8871
                                                style="font-size: 12px;color: rgba(0, 0, 112, 0.75);opacity: 0.7;">Click
                                                <b>Done</b> after you've sent the airtime to us then wait for the cash
                                                to reflect in your wallet</span>
                                            <button type="submit" class="btn w-75 text-white mb-4"
                                                style="position:relative;left:13%;font-weight:600;letter-spacing:0.6px;background: #000070;border-radius: 30px;height: 40px;">Done</button>
                                        </form>
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
