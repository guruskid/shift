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
                                        <span class="h3 giftcard-text" style="color: #000070;">Bitcoin Wallet</span>
                                    </div>
                                    <div class="widget-n" style="justify-content: center; text-align: center;">
                                        {{-- <span class="d-block" style="h6 walletbalance-text">Naira Wallet Balance</span>
                                        <span class="d-block price">₦{{ number_format(Auth::user()->nairaWallet->amount) }}</span> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="card card-body">
                                <div class="container px-4 d-flex justify-content-between align-items-center">
                                    <a href="{{ route('user.portfolio') }}">
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
                                    </a>
                                    <div class="d-flex">
                                        <div class="mr-1 mr-lg-2" style="">$ 8,452.98
                                        </div>
                                    </div>
                                </div>
                                {{-- border line --}}
                                <div class="mt-4" style="width: 100%;border: 1px solid #C9CED6;"></div>

                                {{-- Bitcoin  menu  --}}
                                <div class="walletpage__menu-container mx-auto mt-4">
                                    <div class="walletpage_menu d-flex flex-column flex-md-row justify-content-center justify-content-md-between align-items-center">
                                        <div class="mb-4 mb-lg-0">
                                            <span class="d-block" style="color: #565656;font-size: 16px;">Bitcoin wallet
                                                Balance</span>
                                            <span class="d-block">
                                                <span style="color: #000070;font-size: 30px;">{{ number_format((float) Auth::user()->bitcoinWallet->balance, 8) }}</span>
                                                <span style="color: #000070;font-size: 30px;">BTC</span>
                                            </span>{{--
                                            <span class="d-block"
                                                style="color: #565656;font-size: 16px;opacity: 0.5;">₦20,000</span> --}}
                                        </div>
                                        <div class="d-flex">
                                            <a id="bitcoin_send" class="btn walletpage_menu-active">
                                                <span class="d-block">
                                                <img class="img-fluid" src="{{asset('svg/bitcoin-send-icon.svg')}}" />
                                                </span>
                                                <span class="d-block"
                                                    style="color: #000000;font-size: 14px;">Send</span>
                                            </a>
                                            <a id="bitcoin_receive" class="btn">
                                                <span class="d-block">
                                                <img class="img-fluid" src="{{asset('svg/bitcoin-receive-icon.svg')}}" alt="">
                                                </span>
                                                <span class="d-block"
                                                    style="color: #000000;font-size: 14px;">Receive</span>
                                            </a>
                                            <a href="/user/assets" class="btn">
                                                <span class="d-block">
                                                <img class="img-fluid" src="{{asset('svg/bitcoin-buy-icon.svg')}}" />
                                                </span>
                                                <span class="d-block" style="color: #000000;font-size: 14px;">Buy</span>
                                            </a>
                                            <a href="/user/assets" class="btn">
                                                <span class="d-block">
                                                    <img class="img-fluid" src="{{asset('svg/bitcoin-sell-icon.svg')}}" alt="">
                                                </span>
                                                <span class="d-block"
                                                    style="color: #000000;font-size: 14px;">Sell</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                    @include('newpages.tabs.bitcoin-wallet-send')
                                    @include('newpages.tabs.bitcoin-wallet-receive')

                            </div>
                        </div>
                        <div class="col-12 mt-3">
                            <div class="card card-body">
                                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-center">
                                    <div class="mb-3 mb-lg-0">
                                        <span class="recent_trx_text">Recent Transactions</span>
                                    </div>
                                    <div class="d-flex flex-column flex-md-row justify-content-center align-items-center justify-content-lg-between">
                                        <div class="d-flex align-items-center">
                                            <span class="mr-1" style="color: #000070;font-size: 14px;">Start Date</span>
                                            <input type="text" class="col-7 form-control" name="" id="" value="14-05-2020">
                                        </div>
                                        <div class="d-flex align-items-center mt-3 mt-md-0">
                                            <span class="mr-1" style="color: #000070;font-size: 14px;">End Date</span>
                                            <input type="text" class="col-7 form-control" name="" id="" value="14-05-2020">
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive mt-4 mt-lg-3">
                                    <table class="table table-striped">
                                        <thead>
                                          <tr style="background-color: rgba(0, 0, 112, 0.05) !important;color:#000070;font-size:16px;">
                                            <th scope="col">ID</th>
                                            <th scope="col">TRANSACTION TYPE</th>
                                            <th scope="col">AMOUNT</th>
                                            <th scope="col">DATE</th>
                                            <th scope="col">TIME</th>
                                            <th scope="col">STATUS</th>
                                            <th scope="col"></th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                          <tr>
                                            <th scope="row">1</th>
                                            <td style="color: #FF001F;font-weight:600;">Sell</td>
                                            <td>
                                                <span class="d-block" style="font-size: 14px;color: #000000;font-weight: 500;">$200</span>
                                                <span class="d-block" style="font-size: 12px;color: #676B87;">N70,000</span>
                                            </td>
                                            <td style="color: #000000;font-size: 14px;">Aug. 10, 2019</td>
                                            <td style="font-weight: 500;">2 mins ago</td>
                                            <td>
                                                <span class="status_inprogress">in progress</span>
                                            </td>
                                            <td>
                                                <a href="#" style="color: #000070;font-size: 15px;font-weight: 600;">view</a>
                                            </td>
                                          </tr>
                                        </tbody>
                                      </table>
                                </div>
                                <a href="#" class="btn mx-auto text-white mt-3" style="background: #000070;border-radius: 25px;width: 140px;" class="btn">View more <span><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10 17.5C14.1421 17.5 17.5 14.1421 17.5 10C17.5 5.85786 14.1421 2.5 10 2.5C5.85786 2.5 2.5 5.85786 2.5 10C2.5 14.1421 5.85786 17.5 10 17.5Z" stroke="white" stroke-width="0.916667" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M6.66699 10L10.0003 13.3333" stroke="white" stroke-width="0.916667" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M10 6.66699V13.3337" stroke="white" stroke-width="0.916667" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M13.3333 10L10 13.3333" stroke="white" stroke-width="0.916667" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    </span></a>
                            </div>
                        </div>
                    </div>



                </div>
            </div>

        </div>
    </div>
</div>

@endsection
