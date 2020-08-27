@php
$s = Auth::user()->transactions->where('status', 'success')->count();
$w = Auth::user()->transactions->where('status', 'waiting')->count();
$d = Auth::user()->transactions->where('status', 'declined')->count();
$f = Auth::user()->transactions->where('status', 'failed')->count();
@endphp


@extends('layouts.user')
@section('title', 'Assets Calculator | ' )
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
            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="col-md-12  px-0">
                            <div class="m-0 p-3 c-rounded-top  bg-custom text-white">
                                <strong>Calculator</strong>
                            </div>
                            <div class="bg-custom-gradient p-4 ">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                                <div class="calculator-box ">
                                    <form method="post" id="rate-form">
                                        <input type="hidden" name="is_crypto" id="is_crypto" value="{{$is_crypto}}">
                                        <div class="d-flex p-0 my-2 bg-custom  rounded">
                                            <div class="col-6 p-1 text-center bg-custom-accent rounded-left"
                                                id="sell-trade" onclick="changeRate('sell')">
                                                <a href="#" class="text-white">Sell to Dantown</a>
                                            </div>
                                            <div class="col-6 p-1 text-center rounded-right " id="buy-trade"
                                                onclick="changeRate('buy')">
                                                <a href="#" class="text-white">Buy from Dantown</a>
                                            </div>
                                        </div>
                                        <div class="form-group mx-4">
                                            <label for="asset">Asset Type</label>
                                            <select class="form-control form-control-sm" name="card" id="card-name">
                                                <option value=""></option>
                                                @foreach ($cards as $card)
                                                <option data-is-crypto="{{$card->is_crypto}}" value="{{$card->name}}">
                                                    {{ ucfirst($card->name) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group mx-4" id="country-div">
                                            <label for="Currency">Currency</label>
                                            <select class="form-control form-control-sm" name="country" id="country">

                                            </select>
                                        </div>
                                        @if ($is_crypto != 1)
                                        <div class="form-group mx-4" id="card-type-div">
                                            <label for="type">Type</label>
                                            <select class="form-control form-control-sm" name="type" id="card-type">

                                            </select>
                                        </div>
                                        @endif
                                        <div class="mx-4" id="pay-with-box" style="display: none" >
                                            <div class="form-check-inline">
                                                <label class="form-check-label">
                                                    <input type="radio" value="wallet" checked class="form-check-input" name="pay_with">Pay with wallet
                                                </label>
                                            </div>
                                        </div>

                                        <div class="form-group mx-4">
                                            <input type="hidden" value="sell" name="rate_type" id="rate-type">
                                            <input type="number" class="form-control" disabled id="value" name="value"
                                                placeholder="Amount" onkeyup="getRate()">
                                        </div>
                                        <button type="submit" class="btn bg-custom-accent c-rounded btn-block"
                                            style="font-size: unset">
                                            <img src="{{asset('loader.gif')}}" height="20px" id="loader"
                                                style="display: none;" alt="">
                                            Rate</button>
                                    </form>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                                <div class="trade-details-box card">
                                    <div class="card-header bg-custom-accent "> Trade Details </div>
                                    <div class="card-body ">
                                        <p class="mb-3  mt-3">
                                            <span>Asset Type:</span>
                                            <span class="float-right" id="asset-type">xxxx-xxxx</span>
                                            <input type="hidden" id="amount-paid">

                                        </p>
                                        <p class="mb-3">
                                            <span>Trade Type:</span>
                                            <span id="trade-type" class="float-right"
                                                style="text-transform:capitalize">Sell</span>
                                        </p>
                                        <p class="mb-3">
                                            <span>Rate:</span>
                                            <span class="float-right" id="conv-rate">xxxx</span>
                                        </p>
                                        <p class="mb-3">
                                            <span>Amount payable:</span>
                                            <span class="float-right" id="conv-val">xxxx</span>
                                        </p>
                                        @if ($is_crypto == 1)
                                        <p class="mb-3">
                                            <span>Crypto Equiv:</span>
                                            <span class="float-right" id="equiv">xxxx</span>
                                        </p>
                                        @endif
                                        <p class="mb-1 row pr-2">
                                            <span id="wallet-id-text" class="col-4">Wallet Id:</span>
                                            <input type="text" class="form-control  col-8" name="wallet_id"
                                                id="wallet-id" disabled>
                                            </p>
                                            <a href="#"  onclick="copy()"><i class="fa fa-copy float-right mb-3">Copy</i></a>
                                        <button class="btn  btn-block bg-custom c-rounded  " onclick="x(1)">
                                            TRADE
                                            <img src="{{asset('loader.gif')}}" height="20px" id="t-loader"
                                                style="display: none;">
                                        </button>
                                        {{--  <div class=" dropdown">
                                            <div class="iconbox btn-hovered-light" role="button" id="dropdownMenuLink"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <button class="btn  btn-block bg-custom c-rounded  ">
                                                    TRADE
                                                    <img src="{{asset('loader.gif')}}" height="20px" id="t-loader"
                                        style="display: none;">
                                        </button>
                                    </div>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink">
                                        <h6 class="card-title text-center">Select an agent</h6>
                                        @foreach ($agents as $agent)
                                        <a class="dropdown-item" href="javascript:;" onclick="x({{$agent->id}})">
                                            <span><i class="mdi mdi-star-outline"></i></span>
                                            <span>{{$agent->first_name." ".$agent->last_name}}</span>
                                        </a>
                                        @endforeach
                                    </div>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                        <div class="trade-details-box card">
                            <div class="card-header bg-custom-accent "> Guide</div>
                            <div class="card-body">
                                <ol>
                                    <li>Enter the transaction details on the calculator</li>
                                    <li>Confirm your trade details</li>
                                    <li>Click the rade button to instantiate a trade</li>
                                    <li>You will be redireected to a page where you can add transaction images for the
                                        transaction</li>
                                    <li>An agent will attend to your transaction and you will be updated when changes
                                        are made</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @include('layouts.partials.live-feeds')
</div>
</div>
</div>
@endsection
