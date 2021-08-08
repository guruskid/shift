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
                                        <span class="h3 giftcard-text">Pay Bills</span>
                                    </div>
                                    <div class="widget-n text-center" style="justify-content: center;">
                                        <span class="d-block" style="h6 walletbalance-text">Wallet Balance</span>
                                        <span
                                            class="d-block price realtime-wallet-balance"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="widget widget-chart-one">
                                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-center pb-2"
                                    style="">
                                    <div class="list-cards-title primary-color" style="line-height: 40px;">
                                        <span class="ml-1 rechargemenu_subtitle"
                                            style="color: rgba(0, 0, 112, 0.75);">Recharge and pay your utility
                                            bills</span>
                                    </div>
                                </div>
                                <div
                                    class="d-flex flex-row flex-wrap justify-content-between justify-content-lg-center py-3 px-2">
                                    <a class="mx-1 my-2" href="{{ route('user.airtime') }}">
                                        <div
                                            class="airtimepage_card d-flex flex-column justify-content-center align-items-center">
                                            <div>
                                                <img class="img-fluid rechargemenu_icons"
                                                    src="{{asset('svg/airtime-icon.svg')}}" alt="" />
                                            </div>
                                            <span class="d-block bills_type_text">Buy Airtime</span>
                                        </div>
                                    </a>
                                    <a class="mx-1 my-2" href="{{ route('user.data') }}">
                                        <div
                                            class="airtimepage_card d-flex flex-column justify-content-center align-items-center">
                                            <div>
                                                <img class="img-fluid rechargemenu_icons"
                                                    src="{{asset('svg/airtimedata-icon.svg')}}" alt="" />
                                            </div>
                                            <span class="d-block bills_type_text">Data Subscription</span>
                                        </div>
                                    </a>
                                    <a class="mx-1 my-2" href="{{ route('user.airtime-to-cash') }}">
                                        <div
                                        class="airtimepage_card d-flex flex-column justify-content-center align-items-center">
                                        <div>
                                            <img class="img-fluid rechargemenu_icons"
                                                src="{{asset('svg/airtimetocash-icon.svg')}}" alt="" />
                                        </div>
                                        <span class="d-block bills_type_text">Airtime to cash</span>
                                    </div>
                                    </a>
                                    <a class="mx-1 my-2" href="{{ route('user.discount-airtime') }}">
                                        <div
                                            class="airtimepage_card d-flex flex-column justify-content-center align-items-center">
                                            <div>
                                                <img class="img-fluid rechargemenu_icons"
                                                    src="{{asset('svg/discounted-icon.svg')}}" alt="" />
                                            </div>
                                            <span class="d-block bills_type_text">Buy Discounted Airtime</span>
                                        </div>
                                    </a>
                                    <a class="mx-1 my-2" href="{{ route('user.paytv') }}">
                                        <div
                                            class="airtimepage_card d-flex flex-column justify-content-center align-items-center">
                                            <div>
                                                <img class="img-fluid rechargemenu_icons"
                                                    src="{{asset('svg/cablesub-icon.svg')}}" alt="" />
                                            </div>
                                            <span class="d-block bills_type_text">Cable Subscription and TV</span>
                                        </div>
                                    </a>
                                    <a class="mx-1 my-2" href="{{ route('user.electricity') }}">
                                        <div
                                            class="airtimepage_card d-flex flex-column justify-content-center align-items-center">
                                            <div>
                                                <img class="img-fluid rechargemenu_icons"
                                                    src="{{asset('svg/electricitybills-icon.svg')}}" alt="" />
                                            </div>
                                            <span class="d-block bills_type_text">Electricity Bills</span>
                                        </div>
                                    </a>
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
