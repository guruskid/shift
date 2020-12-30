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
                                            class="d-block price">{{ Auth::user()->bitcoinWallet ? sprintf('%0.8f', Auth::user()->bitcoinWallet->balance) : 'No bitcoin wallet' }}</span>
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
                                            cryptocurrency in less than a minute</div>

                                        {{-- Sell Bitcoin --}}
                                       <bitcoin-sell-component :rate="{{ $rates }}" :real_btc="{{ $btc_real_time }}" :card_id="{{ $card->id }}" ></bitcoin-sell-component>


                                        {{-- Buy --}}
                                        <div class="tab-pane fade mx-auto p-3 calculator_form" id="profile"
                                            role="tabpanel" aria-labelledby="profile-tab">
                                            <form action="{{ route('user.trade-bitcoin') }}" class="disable-form" method="post">@csrf

                                                <div class="form-group mb-4">
                                                    <label for="inlineFormInputGroupUsername2"
                                                        style="color: rgba(0, 0, 112, 0.75);">USD equivalent</label>
                                                    <div class="input-group mb-2 mr-sm-2">
                                                        <div class="input-group-prepend" style="border-radius: 30px;">
                                                            <div class="input-group-text input_label">
                                                                USD</div>
                                                        </div>
                                                        <input type="number" required step="any" name="amount" min="0"
                                                            class="form-control bitcoin-input-radius"
                                                            id="buy_usd_field">
                                                    </div>
                                                </div>
                                                <div class="form-group mb-4">
                                                    <label for="inlineFormInputGroupUsername2"
                                                        style="color: rgba(0, 0, 112, 0.75);">Bitcoin equivalent</label>
                                                    <div class="input-group mb-2 mr-sm-2">
                                                        <div class="input-group-prepend" style="border-radius: 30px;">
                                                            <div class="input-group-text input_label">
                                                                BTC</div>
                                                        </div>
                                                        <input type="number" required step="any" name="quantity" min="0"
                                                            class="form-control bitcoin-input-radius"
                                                            id="buy_btc_field">
                                                        <input type="hidden" name="card_id" value="{{ $card->id }}">
                                                        <input type="hidden" name="type" value="buy">
                                                    </div>
                                                </div>
                                                <div class="form-group mb-4">
                                                    <label for="inlineFormInputGroupUsername2"
                                                        style="color: rgba(0, 0, 112, 0.75);">Naira equivalent</label>
                                                    <div class="input-group mb-2 mr-sm-2">
                                                        <div class="input-group-prepend" style="border-radius: 30px;">
                                                            <div class="input-group-text input_label">
                                                                NGN</div>
                                                        </div>
                                                        <input type="number" required name="amount_paid" class="form-control bitcoin-input-radius"
                                                            id="buy_ngn_field">
                                                    </div>
                                                </div>
                                               {{--  <div class="form-group mb-4">
                                                    <label for="wallet_address"
                                                        style="color: rgba(0, 0, 112, 0.75);">Wallet Address</label>
                                                    <span id="copied_text" class="text-success"
                                                        style="display: none;">Wallet address copied</span>
                                                    <div class="input-group mb-2 mr-sm-2">
                                                        <input type="text" min="34" minlength="34" maxlength="35"
                                                            max="35" class="form-control bitcoin-input-radius"
                                                            id="buy_wallet_address"
                                                            style="border-top-left-radius: 5px;border-bottom-left-radius: 5px;">
                                                    </div>
                                                </div> --}}
                                                <button type="submit" id="buy_btc_btn"
                                                    class="btn w-100 text-white mt-2 bitcoin_calculator_btn">Buy</button>
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



@include('newpages.modals.uploadcardmodal')
@include('newpages.modals.popuploaded')
@endsection

@section('scripts')
<script>
    bit_sell = {
        !!json_encode($rates->sell, JSON_HEX_TAG) !!
    };
    bit_buy = {
        !!json_encode($rates->buy, JSON_HEX_TAG) !!
    };

    sell_eth = {
        !!json_encode($rates->sell, JSON_HEX_TAG) !!
    };
    buy_eth = {
        !!json_encode($rates->buy, JSON_HEX_TAG) !!
    };

</script>
<script src="{{asset('newpages/js/main.js?v=495')}} "></script>
@endsection
