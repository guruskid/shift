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
                                        <span class="h3 giftcard-text">Bitcoin</span>
                                    </div>
                                    <div class="widget-n text-center" style="justify-content: center;">
                                        <span class="d-block" style="h6 walletbalance-text">1 BTC = 56,758</span>
                                        {{-- <span class="d-block price">₦56,758</span> --}}
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
                                        <span class="ml-1" style="color: rgba(0, 0, 112, 0.75);">Buy/Sell Bitcoin</span>
                                    </div>
                                </div>

                                <div>
                                    <ul class="nav buy-sell-title mx-auto my-2 my-lg-4" id="myTab" role="tablist">
                                        <li class="nav-item active-title-item" role="presentation" style="width: 50%;">
                                            <a class="nav-link d-block text-center text-white" id="home-tab"
                                                data-toggle="tab" href="#home" role="tab" aria-controls="home"
                                                aria-selected="true">Sell</a>
                                        </li>
                                        <li class="nav-item" role="presentation" style="width:50%;">
                                            <a class="nav-link d-block text-center" id="profile-tab" data-toggle="tab"
                                                href="#profile" role="tab" aria-controls="profile"
                                                aria-selected="false">Buy</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content " id="myTabContent">
                                        <div class="text-center text-muted mb-3 mt-3 mt-lg-1" style="margin-top: -10px;">Buy or sell
                                            cryptocurrency in less than a minute</div>

                                        <div class="tab-pane fade show active mx-auto p-3 calculator_form"
                                            id="home" role="tabpanel" aria-labelledby="home-tab">
                                            <form action="" method="post">
                                                @csrf
                                                <div class="form-group mb-4">
                                                    <label for="inlineFormInputGroupUsername2"
                                                        style="color: rgba(0, 0, 112, 0.75);">USD equivalent</label>
                                                    <div class="input-group mb-2 mr-sm-2">
                                                        <div class="input-group-prepend" style="border-radius: 30px;">
                                                            <div class="input-group-text"
                                                                style="background-color: #fff;border-top-left-radius: 25px;border-bottom-left-radius: 25px;">
                                                                USD</div>
                                                        </div>
                                                        <input type="number" step="any" min="0" class="form-control bitcoin-input-radius" value=""
                                                            id="sell_usd_field">
                                                    </div>
                                                </div>
                                                <div class="form-group mb-4">
                                                    <label for="inlineFormInputGroupUsername2"
                                                        style="color: rgba(0, 0, 112, 0.75);">Bitcoin equivalent</label>
                                                    <div class="input-group mb-2 mr-sm-2">
                                                        <div class="input-group-prepend" style="border-radius: 30px;">
                                                            <div class="input-group-text"
                                                                style="background-color: #fff;border-top-left-radius: 25px;border-bottom-left-radius: 25px;">
                                                                BTC</div>
                                                        </div>
                                                        <input type="number" step="any" min="0" class="form-control bitcoin-input-radius" value=""
                                                            id="sell_btc_field">
                                                    </div>
                                                </div>
                                                <div class="form-group mb-4">
                                                    <label for="inlineFormInputGroupUsername2"
                                                        style="color: rgba(0, 0, 112, 0.75);">Naira equivalent</label>
                                                    <div class="input-group mb-2 mr-sm-2">
                                                        <div class="input-group-prepend" style="border-radius: 30px;">
                                                            <div class="input-group-text"
                                                                style="background-color: #fff;border-top-left-radius: 25px;border-bottom-left-radius: 25px;">
                                                                NGN</div>
                                                        </div>
                                                        <input readonly type="number" step="any" min="0" class="form-control bitcoin-input-radius" value=""
                                                            id="sell_ngn_field">
                                                    </div>
                                                </div>
                                                <div class="form-group mb-4">
                                                    <label for="wallet_address"
                                                        style="color: rgba(0, 0, 112, 0.75);">Wallet Address</label>
                                                    <span id="copied_text" class="text-success"
                                                        style="display: none;">Wallet address copied</span>
                                                    <div class="input-group mb-2 mr-sm-2">
                                                        <input type="text" class="form-control bitcoin-input-radius"
                                                            id="wallet_address"
                                                            style="border-top-left-radius: 25px;border-bottom-left-radius: 25px;">
                                                        <div class="input-group-append" style="border-radius: 30px;">
                                                            <div class="input-group-text"
                                                                style="background-color: #fff;border-top-right-radius: 25px;border-bottom-right-radius: 25px;">
                                                                <span id="copyWalletAddress" style="cursor: pointer;">
                                                                    <svg width="26" height="20" viewBox="0 0 32 32"
                                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <g clip-path="url(#clip0)">
                                                                            <path
                                                                                d="M12.9792 26.0012C9.85522 26.0012 7.3125 23.4585 7.3125 20.3345V6.66797H4.97925C2.95654 6.66797 1.3125 8.31177 1.3125 10.3345V28.3345C1.3125 30.3572 2.95654 32.0012 4.97925 32.0012H21.6458C23.6685 32.0012 25.3125 30.3572 25.3125 28.3345V26.0012H12.9792Z"
                                                                                fill="#000070" />
                                                                            <path
                                                                                d="M30.6458 3.66675C30.6458 1.64136 29.0044 0 26.9792 0H12.9792C10.9539 0 9.3125 1.64136 9.3125 3.66675V20.3333C9.3125 22.3586 10.9539 24 12.9792 24H26.9792C29.0044 24 30.6458 22.3586 30.6458 20.3333V3.66675Z"
                                                                                fill="#000070" />
                                                                        </g>
                                                                        <defs>
                                                                            <clipPath id="clip0">
                                                                                <rect width="32" height="32"
                                                                                    fill="white" />
                                                                            </clipPath>
                                                                        </defs>
                                                                    </svg>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button type="submit" id="sell_submit_btn" alt="sell" class="btn w-100 text-white mt-2"
                                                    style="font-weight:600;letter-spacing:0.6px;background: #000070;border-radius: 30px;height: 40px;">Sell</button>
                                            </form>
                                        </div>

                                        <div class="tab-pane fade mx-auto p-3 calculator_form" id="profile"
                                            role="tabpanel" aria-labelledby="profile-tab">
                                            <form action="" method="post">
                                                @csrf
                                                <div class="form-group mb-4">
                                                    <label for="inlineFormInputGroupUsername2"
                                                        style="color: rgba(0, 0, 112, 0.75);">USD equivalent</label>
                                                    <div class="input-group mb-2 mr-sm-2">
                                                        <div class="input-group-prepend" style="border-radius: 30px;">
                                                            <div class="input-group-text"
                                                                style="background-color: #fff;border-top-left-radius: 25px;border-bottom-left-radius: 25px;">
                                                                USD</div>
                                                        </div>
                                                        <input type="number" step="any" min="0" class="form-control bitcoin-input-radius"
                                                            id="buy_usd_field">
                                                    </div>
                                                </div>
                                                <div class="form-group mb-4">
                                                    <label for="inlineFormInputGroupUsername2"
                                                        style="color: rgba(0, 0, 112, 0.75);">Bitcoin equivalent</label>
                                                    <div class="input-group mb-2 mr-sm-2">
                                                        <div class="input-group-prepend" style="border-radius: 30px;">
                                                            <div class="input-group-text"
                                                                style="background-color: #fff;border-top-left-radius: 25px;border-bottom-left-radius: 25px;">
                                                                BTC</div>
                                                        </div>
                                                        <input type="number" step="any" min="0" class="form-control bitcoin-input-radius"
                                                            id="buy_btc_field">
                                                    </div>
                                                </div>
                                                <div class="form-group mb-4">
                                                    <label for="inlineFormInputGroupUsername2"
                                                        style="color: rgba(0, 0, 112, 0.75);">Naira equivalent</label>
                                                    <div class="input-group mb-2 mr-sm-2">
                                                        <div class="input-group-prepend" style="border-radius: 30px;">
                                                            <div class="input-group-text"
                                                                style="background-color: #fff;border-top-left-radius: 25px;border-bottom-left-radius: 25px;">
                                                                NGN</div>
                                                        </div>
                                                        <input readonly type="text" class="form-control bitcoin-input-radius"
                                                            id="buy_ngn_field">
                                                    </div>
                                                </div>
                                                <div class="form-group mb-4">
                                                    <label for="wallet_address"
                                                        style="color: rgba(0, 0, 112, 0.75);">Wallet Address</label>
                                                    <span id="copied_text" class="text-success"
                                                        style="display: none;">Wallet address copied</span>
                                                    <div class="input-group mb-2 mr-sm-2">
                                                        <input type="text" class="form-control bitcoin-input-radius"
                                                            id="wallet_address"
                                                            style="border-top-left-radius: 25px;border-bottom-left-radius: 25px;">
                                                    </div>
                                                </div>
                                                <button type="submit" class="btn w-100 text-white mt-3"
                                                    style="font-weight:600;letter-spacing:0.6px;background: #000070;border-radius: 30px;height: 40px;">Buy</button>
                                            </form>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>

            @include('newpages.modals.uploadcardmodal')
            @include('newpages.modals.popuploaded')
            {{-- @include('layouts.partials.live-feeds') --}}
        </div>
    </div>
</div>

@endsection