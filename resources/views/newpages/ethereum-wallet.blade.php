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
                                        <span class="h3 giftcard-text" style="color: #000070;">Ethereum Wallet</span>
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

                                    <div class="d-flex justify-content-center align-items-center"
                                        style="height:60px;box-shadow: 0px 2px 10px rgba(207, 207, 207, 0.25);">
                                        <a class="mx-1 mx-lg-2 px-1" href="#">
                                            <img src="{{asset('svg/nairawallet_logo.svg')}}" class="img-fluid" alt="">
                                        </a>
                                        <a class="mx-1 mx-lg-2 px-1" href="#">
                                            <img src="{{asset('svg/bitcoinwallet_logo.svg')}}" class="img-fluid" alt="">
                                        </a>
                                        <a class="mx-1 mx-lg-2 px-1" href="#">
                                            <img src="{{asset('svg/ethereumwallet_logo.svg')}}" class="img-fluid"
                                                alt="">
                                        </a>
                                        <a class="mx-1 mx-lg-2 px-1" href="#">
                                            <img src="{{asset('svg/tetherwallet_logo.svg')}}" class="img-fluid" alt="">
                                        </a>
                                    </div>
                                    <div class="d-flex">
                                        <div class="mr-1 mr-lg-2" style="">
                                           {{--  {{ Auth::user()->nairaWallet ? '???'. number_format(Auth::user()->nairaWallet->amount) : 'No naira wallet' }} --}}
                                        </div>
                                    </div>
                                </div>
                                {{-- border line --}}
                                <div class="mt-4" style="width: 100%;border: 1px solid #C9CED6;"></div>

                                {{-- Bitcoin  menu  --}}
                                <div class="walletpage__menu-container mx-auto mt-4">
                                    @foreach ($errors->all() as $err)
                                    <p class="text-danger">{{ $err }}</p>
                                    @endforeach
                                    <div
                                        class="walletpage_menu d-flex flex-column flex-md-row justify-content-center justify-content-md-between align-items-center">
                                        <div class="mb-4 mb-lg-0">
                                            <span class="d-block" style="color: #565656;font-size: 16px;">Ethereum wallet
                                                Balance</span>
                                            <span class="d-block">
                                                <span
                                                    style="color: #000070;font-size: 30px;">{{ number_format((float) $eth_wallet->balance, 5) }}</span>
                                                <span style="color: #000070;font-size: 30px;">ETH</span>
                                            </span>
                                            <span class="d-block"
                                                style="color: #565656;font-size: 16px;opacity: 0.5;">${{ number_format((float)$eth_wallet->usd  , 2) }}
                                        </div>
                                        <div class="d-flex">
                                            <a id="bitcoin_send" class="btn walletpage_menu-active">
                                                <span class="d-block">
                                                    <img class="img-fluid"
                                                        src="{{asset('svg/bitcoin-send-icon.svg')}}" />
                                                </span>
                                                <span class="d-block"
                                                    style="color: #000000;font-size: 14px;">Send</span>
                                            </a>
                                            <a id="bitcoin_receive" class="btn">
                                                <span class="d-block">
                                                    <img class="img-fluid"
                                                        src="{{asset('svg/bitcoin-receive-icon.svg')}}" alt="">
                                                </span>
                                                <span class="d-block"
                                                    style="color: #000000;font-size: 14px;">Receive</span>
                                            </a>
                                            <a href="{{ route('ethereum.trade') }}"
                                                class="btn">
                                                <span class="d-block">
                                                    <img class="img-fluid"
                                                        src="{{asset('svg/bitcoin-buy-icon.svg')}}" />
                                                </span>
                                                <span class="d-block" style="color: #000000;font-size: 14px;">Buy</span>
                                            </a>
                                            <a href="{{ route('ethereum.trade') }}" class="btn">
                                                <span class="d-block">
                                                    <img class="img-fluid" src="{{asset('svg/bitcoin-sell-icon.svg')}}"
                                                        alt="">
                                                </span>
                                                <span class="d-block"
                                                    style="color: #000000;font-size: 14px;">Sell</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <ethereum-send-component :usd_eth="{{ $eth_rate }}" ></ethereum-send-component>

                                @include('newpages.tabs.ethereum-wallet-receive')

                            </div>
                        </div>
                        <div class="col-12 mt-3 mb-5">
                            <div class="card card-body">
                                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-center">
                                    <div class="mb-3 mb-lg-0">
                                        <span class="recent_trx_text">Recent Transactions</span>
                                    </div>
                                </div>
                                <div class="table-responsive mt-4 mt-lg-3">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr
                                                style="background-color: rgba(0, 0, 112, 0.05) !important;color:#000070;font-size:16px;">
                                                <th scope="col">ID</th>
                                                <th scope="col">TRANSACTION TYPE</th>
                                                <th scope="col">AMOUNT</th>
                                                <th scope="col">DATE</th>
                                                <th scope="col">TIME</th>
                                                <th scope="col">STATUS</th>
                                                {{-- <th scope="col"></th> --}}
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($transactions as $key => $transaction)
                                            @if ($transaction->amount != 0)
                                            <tr>
                                                <th scope="row">{{ $key++ }}</th>
                                                <td>
                                                    {{ $transaction->transactionType }}</td>
                                                <td>
                                                    @if ($transaction->amount > 0)
                                                    <span class="d-block text-success"
                                                        style="font-size: 14px;color: #000000;font-weight: 500;">eth
                                                        {{ number_format((float) $transaction->amount, 8) }}</span>
                                                    @else
                                                    <span class="d-block text-danger"
                                                        style="font-size: 14px;color: #000000;font-weight: 500;">eth
                                                        {{ number_format((float) $transaction->amount, 8) }}  </span>
                                                    @endif

                                                </td>
                                                <td style="color: #000000;font-size: 14px;">
                                                    {{ $transaction->created->format('d M Y') }}</td>
                                                <td style="font-weight: 500;">
                                                    {{ $transaction->created->format('h:ia')}}</td>
                                                <td>
                                                    <span class="status_success">Completed</span>
                                                </td>
                                                <td class="transaction_content">
                                                    @if (isset($transaction->txId))
                                                        <a target="_blank" href="https://blockexplorer.one/btc/mainnet/tx/{{ $transaction->txId }}" class="btn transaction_view_link">Explorer</a>

                                                    @endif
                                                </td>
                                            </tr>
                                            @endif
                                            @endforeach

                                        </tbody>
                                    </table>
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


