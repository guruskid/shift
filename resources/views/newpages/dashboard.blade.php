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

                    <div class="row user_dashboard_container">
                        @foreach ($notifications as $item)
                        <div class="col-sm-12 col-md-8 mb-3">
                            <div class="card card-body">
                                <div class="welcomeText">
                                    {{$item->title}}</div>
                                <div class="welcomeText">{{$item->body}} </div>
                                <span class="close_notification_icon">
                                    <svg width="30" height="30" viewBox="0 0 30 30" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M15 26.25C21.2132 26.25 26.25 21.2132 26.25 15C26.25 8.7868 21.2132 3.75 15 3.75C8.7868 3.75 3.75 8.7868 3.75 15C3.75 21.2132 8.7868 26.25 15 26.25Z"
                                            stroke="#676B87" stroke-width="0.916667" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <path d="M17.5 12.5L12.5 17.5M12.5 12.5L17.5 17.5L12.5 12.5Z" stroke="#676B87"
                                            stroke-width="0.916667" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                        @endforeach
                        <div class="col-sm-12 col-md-4 mb-3 mb-md-0">
                            <div class="card card-body py-5">
                            </div>
                        </div>
                        {{-- <div class="col-sm-4 mb-3">
                            <div class="card card-body" style="height: 118px;">
                                <div class="welcomeText" style="color: #000070;font-weight: 500;font-size: 24px;">Hi, Buhari,</div>
                            </div>
                        </div> --}}

                        <div class="col-6 d-md-none">
                            <div class="card mb-3 mini_border" style="margin: 0px;">
                                <div class="p-1 py-3 pt-2 card-body">
                                    <div class="row">
                                        <div class="col-12 col-8 col-md-8 text-center text-md-left">
                                            <h5 class="card-title mb-0 pb-2">N20, 000</h5>
                                            <p class="card-text" style="color:#2C3E50;">Wallet Balance</p>
                                        </div>
                                        <div class="d-none col-md-4" style="justify-content: right">
                                            <span style="width: 800px;">
                                                <img class="img-fluid" src="/svg/pendingtransaction.svg" />
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="card mb-3 mini_border" style="margin: 0px;">
                                <div class="p-1 py-3 pt-2 card-body">
                                    <div class="row">
                                        <div class="col-12 col-8 col-md-8 text-center text-md-left">
                                            <h5 class="card-title mb-0 pb-2">{{ number_format($p) }}</h5>
                                            <p class="card-text">Pending Transactions</p>
                                        </div>
                                        <div class="d-none col-md-4" style="justify-content: right">
                                            <span style="width: 800px;">
                                                <img class="img-fluid" src="/svg/pendingtransaction.svg" />
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="card mb-3 mini_border">
                                <div class="p-1 py-3 pt-2 card-body">
                                    <div class="row">
                                        <div class="col-12 col-8 col-md-8 text-center text-md-left">
                                            <h5 class="card-title mb-0 pb-2">{{ number_format($s) }}</h5>
                                            <p class="card-text">Successful Transactions</p>
                                        </div>
                                        <div class="d-none col-md-4">
                                            <span>
                                                <img class="img-fluid" src="/svg/successfultransaction.svg" />
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="card mb-3 mini_border">
                                <div class="p-1 py-3 pt-2 card-body">
                                    <div class="row">
                                        <div class="col-12 col-8 col-md-8 text-center text-md-left">
                                            <h5 class="card-title mb-0 pb-2">{{ number_format($d) }}</h5>
                                            <p class="card-text">Declined Transactions</p>
                                        </div>
                                        <div class="d-none col-md-4">
                                            <span>
                                                <img class="img-fluid" src="/svg/declinedtransaction.svg" />
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row flex-column flex-lg-row {{-- justify-content-between --}}">
                        <div class="d-flex flex-column list_assets col-md-12 col-lg-8">
                            <div class="card list_assets-cardc">
                                <div class="card-body px-3">
                                    <div class="row">
                                        <div class="col-6 col-md-4 my-1 my-md-3">
                                            <a href="{{ route('user.asset.rate', ['sell', 102, 'bitcoins']) }}">
                                                <div
                                                    class="mx-2 asset_card_container py-3 py-0 d-flex flex-column justify-content-center align-items-center">
                                                    <span class="d-block text-center mb-3">
                                                        <img class="img-fluid logos_assets" src="{{asset('newpages/svg/bitcoin.svg')}}">
                                                    </span>
                                                    <span class="d-block text-center asset_card_title">Bitcoin</span>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-6 col-md-4 my-1 my-md-3">
                                            <a href="{{ route('user.assets', 'digital assets') }}">
                                                <div class="mx-2 asset_card_container py-3 py-0 d-flex flex-column justify-content-center align-items-center">
                                                    <span class="d-block text-center mb-4">
                                                        <img class="img-fluid logos_assets" src="{{asset('newpages/svg/assets.svg')}}">
                                                    </span>
                                                    <span class="d-block text-center asset_card_title">Digital Assets</span>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-6 col-md-4 my-1 my-md-3">
                                            <a href="{{ route('user.assets', 'gift cards') }}">
                                                <div class="mx-2 asset_card_container py-3 py-0 d-flex flex-column justify-content-center align-items-center">
                                                    <span class="d-block text-center mb-4">
                                                        <img class="img-fluid logos_assets" src="{{asset('newpages/svg/cards.svg')}}">
                                                    </span>
                                                    <span class="d-block text-center asset_card_title">Gift cards</span>
                                                    <span class="d-block text-center asset_card_description">Buy & Sell your
                                                        cards</span>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-6 col-md-4 my-1 my-md-3">
                                            <a href="#">
                                                <div
                                                class="mx-2 asset_card_container py-3 py-0 d-flex flex-column justify-content-center align-items-center">
                                                <span class="d-block text-center mb-4">
                                                    <img class="img-fluid logos_assets" src="{{asset('newpages/svg/airtime.svg')}}">
                                                </span>
                                                <span class="d-block text-center asset_card_title">Airtime</span>
                                                <span class="d-block text-center asset_card_description">Buy & Convert your
                                                    airtime to cash</span>
                                            </div>
                                            </a>
                                        </div>
                                        <div class="col-6 col-md-4 my-1 my-md-3">
                                            <a href="#">
                                                <div
                                                class="mx-2 asset_card_container py-3 py-0 d-flex flex-column justify-content-center align-items-center">
                                                <span class="d-block text-center mb-4">
                                                    <img class="img-fluid logos_assets" src="{{asset('newpages/svg/bills.svg')}}">
                                                </span>
                                                <span class="d-block text-center asset_card_title">Pay bills</span>
                                                <span class="d-block text-center asset_card_description px-1">DSTV, GoTV, PHCN
                                                    and more</span>
                                            </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card card-body my-3 recent_transactions">
                                <span class="d-block mt-0"
                                    style="color: #000070;font-size: 22px;font-weight: 500;">Recent Transactions</span>
                                <div class="d-flex justify-content-center align-items-center mt-4">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-borderless table-hover">
                                            <thead id="headingtab">
                                                <tr
                                                    style="background: rgba(0, 0, 112, 0.05) !important;font-size: 15px;color: #000070;height:50px;">
                                                    <th scope="col">ID</th>
                                                    <th scope="col">ASSET</th>
                                                    <th scope="col">TYPE</th>
                                                    <th scope="col">AMOUNT</th>
                                                    <th scope="col">DATE</th>
                                                    <th scope="col">TIME</th>
                                                    <th scope="col">STATUS</th>
                                                    <th scope="col"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($transactions as $t)
                                                <tr>
                                                    <th scope="row">{{ $t->uid }}</th>
                                                    <td class="transaction_content">{{ $t->card }}</td>
                                                    <td class="transaction_content text-danger">{{ $t->type }}</td>
                                                    <td class="transaction_content">
                                                        ${{ $t->amount }}
                                                        <span
                                                            class="d-block ngn_amount">N{{ number_format($t->amount_paid) }}</span>
                                                    </td>
                                                    <td class="transaction_content">
                                                        {{ $t->created_at->format('M d Y') }}</td>
                                                    <td class="transaction_content">
                                                        {{ $t->created_at->diffForHumans() }}</td>
                                                    <td class="transaction_content">
                                                        @switch($t->status)
                                                        @case('in progress')
                                                        <span
                                                            class="d-block status_inprogress text-capitalize">{{ $t->status }}</span>
                                                        @break
                                                        @case('success')
                                                        <span
                                                            class="d-block status_success text-capitalize">{{ $t->status }}</span>
                                                        @break
                                                        @case('declined')
                                                        <span
                                                            class="d-block status_declined text-capitalize">{{ $t->status }}</span>
                                                        @break
                                                        @case('waiting')
                                                        <span
                                                            class="d-block status_waiting text-capitalize">{{ $t->status }}</span>
                                                        @break
                                                        @default
                                                        <span
                                                            class="d-block status_waiting text-capitalize">{{ $t->status }}</span>
                                                        @endswitch
                                                    </td>
                                                    <td class="transaction_content"><a
                                                            class="btn transaction_view_link">view</a></td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <thead id="headingtab">
                                                <td colspan="8" style="text-align: center">
                                                    <a href="{{ route('user.transactions') }}">
                                                        <button class="btn"
                                                            style="font-size: 14px;background: #000070;border-radius: 25px;color:#fff;padding:8px 24px;">
                                                            View more
                                                            <svg width="1em" height="1em" viewBox="0 0 16 16"
                                                                class="bi bi-arrow-right-circle" fill="currentColor"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd"
                                                                    d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                                                <path fill-rule="evenodd"
                                                                    d="M4 8a.5.5 0 0 0 .5.5h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H4.5A.5.5 0 0 0 4 8z" />
                                                            </svg>
                                                        </button></a>
                                                </td>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-column budget_card d-md-none d-lg-block col-lg-4">
                            <div class="card" style="height:390px;">
                                <div class="card-body d-flex flex-column align-items-center">
                                    <span class="d-block mt-4" style="color: #222222;font-size: 18px;">Transaction Analysis</span>

                                    <div id="chartContaine" style="height: 70%; width: 80%;">
                                        {!! $usersChart->container() !!}</div>
                                    <div class="my-4">
                                    </div>

                                </div>
                            </div>
                            <div class="card mt-3" style="height:219px;">
                            </div>
                        </div>
                    </div>
                    <!--Action Buttons-->

                </div>
            </div>

            @include('newpages.modals.uploadcardmodal')
            @include('newpages.modals.popuploaded')
            {{-- @include('layouts.partials.live-feeds') --}}
        </div>
    </div>
</div>


@if($usersChart)
{!! $usersChart->script() !!}
@endif
@endsection
