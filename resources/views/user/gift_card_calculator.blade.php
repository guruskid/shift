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
                                        <span class="h3 giftcard-text">Giftcards</span>
                                    </div>
                                    <div class="widget-n text-center" style="justify-content: center;">
                                        <span class="d-block" style="h6 walletbalance-text">Wallet Balance</span>
                                        <span class="d-block price">₦{{ number_format(Auth::user()->nairaWallet->amount)}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-12">
                            <div class="widget widget-chart-one">
                                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-center pb-4 mb-3"
                                    style="border-bottom: 1px solid #C9CED6;">
                                    <div class="list-cards-title primary-color">
                                        <a href="#">
                                            <span>
                                                <svg width="31" height="31" viewBox="0 0 41 41" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="20.5" cy="20.5" r="20.5" fill="#000070"
                                                        fill-opacity="0.25" />
                                                    <path
                                                        d="M24.41 15.41L23 14L17 20L23 26L24.41 24.59L19.83 20L24.41 15.41Z"
                                                        fill="#000070" />
                                                </svg>
                                            </span>
                                            <a href="{{ route('user.assets') }}"><span class="ml-1" style="color: rgba(0, 0, 112, 0.75);font-size:16px;">Back
                                                to
                                                cards</span></a>
                                        </a>
                                    </div>
                                </div>

                                @if($buy_sell == 1)
                                    {{-- Sell --}}
                                    @if($sell_gc_setting['settings_value'] == 1)
                                        <gift-card-component :card="{{ $card_rates }}" :buy_sell="{{ $buy_sell }}" ></gift-card-component>
                                    @else
                                        <h4 class="text-center p-2 text-white" style="background-color: #000070"><i class="fas fa-info-circle"></i> {{$sell_gc_setting['notice']}}</h4>
                                    @endif                                    
                                @endif

                                @if($buy_sell == 2)
                                    @if($buy_gc_setting['settings_value'] == '1')
                                        {{-- Buy --}}
                                        <gift-card-component :card="{{ $card_rates }}" :buy_sell="{{ $buy_sell }}" ></gift-card-component>
                                    @else
                                        <h4 class="text-center p-2 text-white" style="background-color: #000070"><i class="fas fa-info-circle"></i> {{$buy_gc_setting['notice']}}</h4>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <upload-modal-component></upload-modal-component>
        </div>
    </div>
</div>

@endsection
@section('scripts')
{{-- Calculator scripts --}}
<script src="{{asset('user_assets/js/calculator.js')}} "></script>
@endsection
