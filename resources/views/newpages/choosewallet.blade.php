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
                                        <span class="h3 giftcard-text" style="color: #000070;">Naira Wallet (₦) </span>
                                    </div>
                                    <div class="widget-n" style="justify-content: center; text-align: center;">
                                        <span class="d-block" style="h6 walletbalance-text">Wallet Balance</span>
                                        <span class="d-block price">₦20,000</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div id="content_bg" class="card card-body mb-4" style="height:550px;">
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
                                <div class="row">
                                    <div class="col-12 col-lg-5" style="box-shadow: 0px 2px 10px rgba(207, 207, 207, 0.25);border-radius: 5px;">
                                        <div class="d-flex">
                                            <div></div>
                                            <div>
                                                <span class="d-block" style="color: #000070;font-size: 18px;">Naira</span>
                                                <span class="" style="color: #000070;font-size: 24px;">500,560 </span><span style="color: #000070;">NGN</span>
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

@endsection