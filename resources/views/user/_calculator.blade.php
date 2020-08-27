@extends('layouts.app')
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
            <div class="app-page-title">
                <div class="page-title-wrapper">
                    <div class="page-title-heading">
                        <div class="page-title-icon">
                            <i class="pe-7s-keypad icon-gradient bg-night-sky">
                            </i>
                        </div>
                        <div>Crypto and gift card calculator
                            <div class="page-title-subheading">Select trade options and check the rate</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-md-around">
                <div class="col-md-6">
                    <div class="mb-3 card">
                        <div class="card-body">
                            <ul class="tabs-animated-shadow nav-justified tabs-animated nav mb-3">
                                <li class="nav-item">
                                    <a role="tab" class="nav-link active" onclick="changeRate('sell')" id="tab-c1-1"
                                        data-toggle="tab" href="#tab-animated1-1">
                                        <span class="nav-text">Sell (to Dantown)</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a role="tab" class="nav-link " onclick="changeRate('buy')" id="tab-c1-0"
                                        data-toggle="tab" href="#tab-animated1-0">
                                        <span class="nav-text">Buy (from Dantown)</span>
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="tab-animated1-0" role="tabpanel">
                                    <div id="form-container">
                                        <form action="#" id="rate-form">
                                            {{ csrf_field() }}
                                            <div class="form-group">
                                                <label for="">Asset type</label>
                                                <select name="card" name="name" id="card-name" class="form-control">
                                                    <option value=""></option>
                                                    @foreach ($cards as $card)
                                                    <option value="{{$card->name}}">{{ ucfirst($card->name) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group" id="country-div">
                                                <label for="">Country</label>
                                                <select name="country" required id="country" class="form-control">

                                                </select>
                                            </div>

                                            <div class="form-group" id="card-type-div">
                                                <label for="">Type</label>
                                                <select name="type" required id="card-type" class="form-control">

                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="">Amount</label>
                                                <input type="hidden" value="sell" name="rate_type" id="rate-type">
                                                <input type="number" required class="form-control"
                                                    disabled id="value" onkeyup="getRate()" name="value">

                                            </div>

                                            <button class="btn btn-block btn-primary" type="submit">Rate <img
                                                    src="{{asset('loader.gif')}}" height="20px" id="loader"
                                                    style="display: none;" alt=""> </button>

                                        </form>
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        <i class="spinner-grow spinner-grow-lg" id="c-loader"
                                            style="display: none; height: 150px; width: 150px"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3 card card-body">
                        <h3 class="mb-3 card-title">Trade Details</h3>

                        <h6>Trade type: <span id="trade-type" class="text-primary">sell</span></h6>
                        <h6>Asset type: <span id="asset-type" class="text-primary"></span></h6>
                        <h6>Conversion Rate: <span id="conv-rate" class="text-primary"></span></h6>
                        <input type="hidden" id="amount-paid">
                        <h6>Value: <span class="text-primary" id="conv-val"></span></h6>
                        <h6 id="wallet-id-text">Wallet Id: <input type="text" id="wallet-id" disabled
                                class="form-control input-group-sm">
                            <span onclick="copy()" class="fa fa-copy">Copy</span> </h6>

                        <div class="iconbox-dropdown dropdown">
                            <div class="iconbox btn-hovered-light" role="button" id="dropdownMenuLink"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <button class="mt-3 btn btn-block bg-malibu-beach text-white">
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
