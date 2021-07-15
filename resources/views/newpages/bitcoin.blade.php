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
                                        <span class="d-block" style="h6 walletbalance-text">Bitcoin Wallet Balance</span>
                                        <span
                                            class="d-block price">{{ Auth::user()->bitcoinWallet ? number_format((float)Auth::user()->bitcoinWallet->balance, 8) : 'No bitcoin wallet' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                            <div class="widget widget-chart-one">
                                <div class="widget-heading d-flex justify-content-between mt-2">
                                    <a href="{{ route('user.bitcoin-wallet') }}">
                                        <button class="btn btn-primary">Send BTC</button>
                                    </a>
                                    <a href="{{ route('user.bitcoin-wallet') }}">
                                        <button class="btn btn-primary">Receive BTC</button>
                                    </a>
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
                                @foreach ($errors->all() as $err)
                                <p class="text-danger text-center">{{ $err }}</p>
                                @endforeach
                                @if (!Auth::user()->bitcoinWallet)
                                <p class="text-danger text-center">Please create a Bitcoin wallet before initiating a
                                    trade. <a href="{{ route('user.portfolio') }}">Create wallet</a> </p>
                                @endif

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
                                        <div class="text-center text-muted mb-3 mt-3 mt-lg-1"
                                            style="margin-top: -10px;">Buy or sell
                                            cryptocurrency in less than a minute
                                            
                                        </div>

                                        {{-- Sell Bitcoin --}}
                                       <bitcoin-sell-component :rate="{{ $rates }}" :real_btc="{{ $btc_real_time - $tp }}" :card_id="{{ $card->id }}"  :charge={{ $charge }} ></bitcoin-sell-component>


                                        {{-- Buy --}}
                                        <bitcoin-buy-component :rate="{{ $rates }}" :real_btc="{{ $btc_real_time + $tp }}" :card_id="{{ $card->id }}" ></bitcoin-buy-component>

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



@include('newpages.modals.uploadcardmodal')
@include('newpages.modals.popuploaded')
@endsection

@section('scripts')
<script>
    /* bit_sell = {
        !!json_encode($rates->sell, JSON_HEX_TAG) !!
    };
    bit_buy = {
        !!json_encode($rates->buy, JSON_HEX_TAG) !!
    }; */

    sell_eth = {
        !!json_encode($rates->sell, JSON_HEX_TAG) !!
    };
    buy_eth = {
        !!json_encode($rates->buy, JSON_HEX_TAG) !!
    };

</script>
<script src="{{asset('newpages/js/main.js?v=495')}} "></script>
@endsection
