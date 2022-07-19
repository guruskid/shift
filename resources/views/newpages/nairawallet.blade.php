@php
$countries = App\Country::orderBy('phonecode', 'asc')->get();
@endphp
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
                                        <span class="d-block" style="h6 walletbalance-text">Naira Wallet Balance</span>
                                        <span
                                            class="d-block price ">₦{{ number_format(Auth::user()->nairaWallet->amount) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div id="content_bg" class="card card-body mb-4" {{-- style="height:550px;" --}}>
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
                                    <div class="d-flex" style="visibility: hidden !important;">
                                        <div class="mr-1 mr-lg-2" style="">$ 8,452.98
                                        </div>
                                    </div>
                                </div>
                                {{-- border line --}}
                                <div class="mt-4" style="width: 100%;border: 1px solid #C9CED6;"></div>
                                @foreach ($errors->all() as $err)
                                <p class="text-danger">{{ $err }}</p>
                                @endforeach

                                {{-- Wallet  menu  --}}
                                <div class="row">
                                    <div class="col-12 col-lg-6">
                                        <div class="mx-auto mt-4">
                                            <div
                                                class="walletpage_menu d-flex flex-column flex-lg-row justify-content-center justify-content-lg-between align-items-center flex-wrap flex-lg-nowrap">
                                                <div>
                                                    <span class="d-block" style="color: #565656;font-size: 16px;">Naira
                                                        wallet
                                                        Balance</span>
                                                    <span class="d-block">
                                                        <span
                                                            style="color: #000070;font-size: 30px;">₦{{ number_format($n->amount) }}</span>
                                                    </span>
                                                </div>

                                                <div class="d-flex mt-3 mt-md-0">
                                                    {{--  <a id="naira_transfer" class="btn  naira_menu">
                                                        <span class="d-block">
                                                        <img src="{{asset('svg/bitcoin-send-icon.svg')}}" alt="">
                                                    </span>
                                                    <span class="d-block"
                                                        style="color: #000000;font-size: 14px;">Transfer</span>
                                                    </a> --}}
                                                    {{-- <a id="naira_withdraw"
                                                        class="btn naira_menu walletpage_menu-active">
                                                        <span class="d-block">
                                                            <img src="{{asset('svg/naira-withdraw-icon.svg')}}" alt="">
                                                        </span>
                                                        <span class="d-block"
                                                            style="color: #000000;font-size: 14px;">Withdraw</span>
                                                    </a> --}}
                                                    {{--  <a id="naira_deposit" class="btn naira_menu">
                                                        <span class="d-block">
                                                            <img src="{{asset('svg/naira-deposit-icon.svg')}}" alt="">
                                                    </span>
                                                    <span class="d-block"
                                                        style="color: #000000;font-size: 14px;">Deposit</span>
                                                    </a> --}}
                                                </div>
                                            </div>
                                            <div class="d-none">
                                                <div class="text-center p-3 text-white h5 rounded" style="background-color: #000070">
                                                    <h4><i class="fas fa-info-circle"></i> Notice:</h4>
                                                    Dantown have partnered with Pay-bridge for naira payments.
                                                    please download the Dantown app to withdraw
                                                </div>
                                                <div class="text-center p-3 text-white h5 rounded">
                                                    <a href="https://bit.ly/3ngtcEN" class="text-center p-1 text-white h5 rounded" style="background-color: #000070">Download App</a>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 text-center">
                                                    <p><strong class="text-primary mb-0">Daily Limit:</strong>
                                                        ₦{{ number_format(Auth::user()->daily_max) }} <strong
                                                            class="text-primary">Rem:
                                                        </strong>₦{{ number_format($daily_rem) }} </p>
                                                    <p><strong class="text-primary mb-0">Monthly Limit:</strong>
                                                        ₦{{ number_format(Auth::user()->monthly_max) }} <strong
                                                            class="text-primary">Rem:
                                                        </strong>₦{{ number_format($monthly_rem) }}</p>
                                                    <p>Please visit <a href="{{ route('user.profile') }}">Account
                                                            settings</a> to upgrade your limits</p>
                                                </div>
                                            </div>

                                            @if ($naira_charge and $tranx < 10)
                                                <div class="text-center h5 py-1 text-white alert alert-info">
                                                    <i class="fa fa-info-circle"></i>
                                                    You are eligible for {{10 - $tranx}} charge-free withdrawal transactions
                                                </div>
                                            @endif

                                            {{-- @include('newpages.tabs.naira-transfer-tab')
                                            @if($setting['settings_value'] == 1)
                                                @include('newpages.tabs.naira-withdraw-tab')
                                            @else --}}
                                                {{-- <h5 class="text-center p-2 text-center w-100 text-white" style="background-color: #000070"><i class="fas fa-info-circle"></i> {{$setting['notice']}}</h5> --}}
                                            {{-- @endif
                                            @include('newpages.tabs.naira-deposit-tab') --}}

                                            <div id="root" class="container">
                                                <tabs>
                                                    <tab name="Deposit via Pay-bridge" at="deposit" :selected="true">
                                                        <deposit-component></deposit-component>
                                                    </tab>
                                                    <tab name="Withdraw via Pay-bridge" at="withdraw">
                                                        <withdraw-component></withdraw-component>
                                                    </tab>
                                                </tabs>
                                            </div>


                                            {{-- Naira transfer modal --}}
                                            @include('newpages.modals.dantownTodantown-modal')
                                            @include('newpages.modals.dantownToOther-modal')

                                        </div>
                                    </div>
                                    <div class="col-12 col-lg-6 pt-2 nairawalletmain__transactionhistory-container">
                                        <span class="d-block txn_history_title mt-3">Transaction history</span>
                                        <div class="mt-3" style="width: 100%;border: 1px solid #EFEFF8;"></div>
                                        <div class="table-responsive">
                                            <table class="table table-borderless">
                                                <tbody class="text-center wallet_trxs">
                                                    @foreach ($nts as $t)
                                                    <tr class="my-2">
                                                        <th scope="row">
                                                            <span class="d-block"
                                                                style="font-size: 16px;">{{ $t->created_at->format('d M Y') }}</span>
                                                            <span class="d-block"
                                                                style="font-size: 14px;font-weight: normal;">{{ $t->created_at->format('h:i a') }}</span>
                                                        </th>
                                                        <td>₦{{number_format($t->amount)}}</td>
                                                        <td>
                                                            <span
                                                                style="color: #87676F;font-size: 16px;">{{ $t->transactionType->name }}</span>
                                                        </td>
                                                        <td>
                                                            @if ($t->status == 'pending')
                                                                <span class="px-3 py-2" style="font-size: 12px;color: #0f0e05;background: rgba(231, 224, 96, 0.3);border-radius: 15px;">{{ $t->status }}</span>
                                                                <div class="mt-1"><small>{{$t->pay_time}}</small></div>
                                                            @else
                                                                <span class="px-3 py-2" style="font-size: 12px;color: #219653;background: rgba(115, 219, 158, 0.3);border-radius: 15px;">{{ $t->status }}</span>
                                                            @endif


                                                        </td>
                                                        {{-- <td>
                                                            <span style="color: #87676F;font-size: 16px;"> {{ $t->transactionType->name }}</span>
                                                        </td> --}}
                                                    </tr>
                                                    @endforeach
                                                    {{ $nts->links() }}
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
    </div>
</div>


{{-- Add bank account --}}

@include('newpages.modals.addBankModal')


@endsection
