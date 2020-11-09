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
                                        <span class="h3 giftcard-text" style="color: #000070;">Smart Budget</span>
                                    </div>
                                    <div class="widget-n" style="justify-content: center; text-align: center;">
                                        <span class="d-block" style="h6 walletbalance-text">Wallet Balance</span>
                                        <span class="d-block price">₦20,000</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="card card-body">
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
                                <div class="mt-4" style="width: 100%;border: 1px solid #C9CED6;"></div>
                                <div class="col-8 mx-auto mt-4 py-4 smartbudget_container">
                                    <div class="d-flex justify-content-center flex-wrap flex-lg-nowrap">
                                        <div class="dailysmartbudgetamnt mx-4">
                                            <span class="bugdetdailytext">Daily:</span>
                                            <span class="bugdetdailyamount">₦0.00</span>
                                        </div>
                                        <div class="dailysmartbudgetamnt mx-4">
                                            <span class="bugdetdailytext">Weekly:</span>
                                            <span class="bugdetdailyamount">₦100,000,000</span>
                                        </div>
                                        <div class="dailysmartbudgetamnt mx-4">
                                            <span class="bugdetdailytext">Monthly:</span>
                                            <span class="bugdetdailyamount">₦100,000</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="card card-body smartbudget_item_container">
                                            <div class="d-flex">
                                                <div>
                                                    <svg width="70" height="70" viewBox="0 0 70 70" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M46.666 11.666H23.3327C21.7219 11.666 20.416 12.9719 20.416 14.5827V55.416C20.416 57.0268 21.7219 58.3327 23.3327 58.3327H46.666C48.2768 58.3327 49.5827 57.0268 49.5827 55.416V14.5827C49.5827 12.9719 48.2768 11.666 46.666 11.666Z"
                                                            stroke="#8484BF" stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path d="M32.084 15.584H37.9173" stroke="#8484BF"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path d="M35 52.084V52.1132" stroke="#8484BF"
                                                            stroke-width="2.75" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <div class="d-flex flex-column">
                                                        <span class="titlename">Buy Airtime</span>
                                                        <span class="titlename">Buy Airtime</span>
                                                        <span class="titlename">Buy Airtime</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-3"></div>
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

@endsection