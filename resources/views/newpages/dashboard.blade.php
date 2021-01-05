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
                        <div class="col-sm-12 col-md-4 mb-3 mb-md-0 d-none d-md-block">
                            <div class="card card-body py-5">
                            <div class="row">
                                        <div class="col-12 col-8 col-md-8 text-center text-md-left">
                                            <h6 class="card-title mb-0 pb-2 realtime-wallet-balance">Loading . . .</h6>
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
                        <div class="col-6 d-md-none">
                            <div class="card mb-3 mini_border" style="margin: 0px;">
                                <div class="p-1 py-3 pt-2 card-body">
                                    <div class="row">
                                        <div class="col-12 col-8 col-md-8 text-center text-md-left">
                                            <h5 class="card-title mb-0 pb-2 realtime-wallet-balance">Loading . . .</h5>
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
                                        <div class="d-none d-lg-block col-md-4" style="justify-content: right">
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
                                        <div class="d-none d-lg-block col-md-4">
                                            <span>
                                                <img class="img-fluid"
                                                    src="{{asset('svg/successfultransaction.svg')}}" />
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
                                        <div class="d-none d-lg-block col-md-4">
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
                                                        <img class="img-fluid logos_assets"
                                                            src="{{asset('newpages/svg/bitcoin.svg')}}">
                                                    </span>
                                                    <span class="d-block text-center asset_card_title">Bitcoin</span>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-6 col-md-4 my-1 my-md-3">
                                            <a href="{{ route('user.assets', 'digital assets') }}">
                                                <div
                                                    class="mx-2 asset_card_container py-3 py-0 d-flex flex-column justify-content-center align-items-center">
                                                    <span class="d-block text-center mb-4">
                                                        <img class="img-fluid logos_assets"
                                                            src="{{asset('newpages/svg/assets.svg')}}">
                                                    </span>
                                                    <span class="d-block text-center asset_card_title">Digital
                                                        Assets</span>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-6 col-md-4 my-1 my-md-3">
                                            <a href="{{ route('user.assets', 'gift cards') }}">
                                                <div
                                                    class="mx-2 asset_card_container py-3 py-0 d-flex flex-column justify-content-center align-items-center">
                                                    <span class="d-block text-center mb-4">
                                                        <img class="img-fluid logos_assets"
                                                            src="{{asset('newpages/svg/cards.svg')}}">
                                                    </span>
                                                    <span class="d-block text-center asset_card_title">Gift cards</span>
                                                    <span class="d-block text-center asset_card_description">Buy & Sell
                                                        your
                                                        cards</span>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-6 col-md-4 my-1 my-md-3">
                                            <a href="#">
                                                <div
                                                    class="mx-2 asset_card_container py-3 py-0 d-flex flex-column justify-content-center align-items-center">
                                                    <span class="d-block text-center mb-4">
                                                        <img class="img-fluid logos_assets"
                                                            src="{{asset('newpages/svg/airtime.svg')}}">
                                                    </span>
                                                    <span class="d-block text-center asset_card_title">Airtime</span>
                                                    <span class="d-block text-center asset_card_description">Buy &
                                                        Convert your
                                                        airtime to cash</span>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-6 col-md-4 my-1 my-md-3">
                                            <a href="#">
                                                <div
                                                    class="mx-2 asset_card_container py-3 py-0 d-flex flex-column justify-content-center align-items-center">
                                                    <span class="d-block text-center mb-4">
                                                        <img class="img-fluid logos_assets"
                                                            src="{{asset('newpages/svg/bills.svg')}}">
                                                    </span>
                                                    <span class="d-block text-center asset_card_title">Pay bills</span>
                                                    <span class="d-block text-center asset_card_description px-1">DSTV,
                                                        GoTV, PHCN
                                                        and more</span>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-6 col-md-4 my-1 my-md-3">
                                            <a href="#">
                                                <div
                                                    class="mx-2 asset_card_container py-3 py-0 d-flex flex-column justify-content-center align-items-center">
                                                    <span class="d-block text-center mb-4">
                                                        <img class="img-fluid logos_assets"
                                                            src="{{asset('svg/tetherwallet_logo.svg')}}">
                                                    </span>
                                                    <span class="d-block text-center asset_card_title">Stable coin</span>
                                                    <span class="d-block text-center asset_card_description px-1">DSTV,
                                                        GoTV, PHCN
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
                            <div class="card" style="height:150px;">
                                <div class="card-body d-flex flex-column align-items-start">

                                    {{-- <div class="d-block mt-1" style="color: #222222;font-size: 18px;">
                                        <span><svg width="17" height="17" viewBox="0 0 25 25" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="12.125" cy="12.5" r="12" fill="#C4C4C4" />
                                                <path
                                                    d="M8.79199 9.16602C8.79199 8.50297 9.09928 7.86709 9.64626 7.39825C10.1932 6.92941 10.9351 6.66602 11.7087 6.66602H12.542C13.3155 6.66602 14.0574 6.92941 14.6044 7.39825C15.1514 7.86709 15.4587 8.50297 15.4587 9.16602C15.4893 9.70706 15.3434 10.2434 15.0428 10.6943C14.7422 11.1452 14.3032 11.4862 13.792 11.666C13.2808 11.9057 12.8418 12.3604 12.5412 12.9616C12.2406 13.5628 12.0946 14.278 12.1253 14.9993"
                                                    stroke="white" stroke-width="1.83333" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path d="M12.125 18.334V18.344" stroke="white" stroke-width="1.83333"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                        <span>Bitcoin chart</span>
                                    </div> --}}

                                    <div class="d-block mt-1" style="color: #222222;font-size: 18px;">
                                        <span>
                                            <svg width="17" height="17" viewBox="0 0 25 25" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="12.125" cy="12.5" r="12" fill="#C4C4C4" />
                                                <path
                                                    d="M8.79199 9.16602C8.79199 8.50297 9.09928 7.86709 9.64626 7.39825C10.1932 6.92941 10.9351 6.66602 11.7087 6.66602H12.542C13.3155 6.66602 14.0574 6.92941 14.6044 7.39825C15.1514 7.86709 15.4587 8.50297 15.4587 9.16602C15.4893 9.70706 15.3434 10.2434 15.0428 10.6943C14.7422 11.1452 14.3032 11.4862 13.792 11.666C13.2808 11.9057 12.8418 12.3604 12.5412 12.9616C12.2406 13.5628 12.0946 14.278 12.1253 14.9993"
                                                    stroke="white" stroke-width="1.83333" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path d="M12.125 18.334V18.344" stroke="white" stroke-width="1.83333"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                        <span>Verification</span>
                                    <div class="progress custom_progress mt-3">
                                        <div class="progress-bar" role="progressbar" style="width: {{$v_progress}}%" ></div>
                                    </div>
                                    <span class="d-block float-lg-right" style="font-size: 14px;">{{$v_progress}}% completed</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card mt-3 p-2" style="height:335px;">
                                <span class="d-block mb-2" style="color: #2C3E50;">Crypto chart</span>
                                <div class="row mt-4">
                                    <div class="col-4">
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <svg width="48" height="49" viewBox="0 0 48 49" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M22.5474 23.0119C24.071 23.0624 27.374 23.1718 27.4143 20.8689C27.4544 18.5045 24.2512 18.5388 22.6933 18.5555C22.5217 18.5574 22.37 18.559 22.2449 18.5572L22.172 23.0013C22.279 23.003 22.4055 23.0072 22.5474 23.0119ZM22.4846 30.1543C24.309 30.2113 28.2853 30.3355 28.3257 27.8019C28.3751 25.2163 24.5563 25.2341 22.6822 25.2429C22.4688 25.2439 22.2806 25.2447 22.1263 25.2416L22.0451 30.1424C22.1708 30.1445 22.3188 30.1491 22.4846 30.1543ZM31.9354 19.5032C32.0939 21.4681 31.2586 22.6295 29.9323 23.2757C32.0814 23.8316 33.4103 25.1413 33.0961 28.0043C32.7009 31.5723 30.0248 32.4852 26.2161 32.6332L26.1527 36.3877L23.8911 36.3453L23.955 32.6485C23.3684 32.6363 22.7681 32.6203 22.1531 32.5962L22.0873 36.3127L19.8271 36.2764L19.8892 32.5159C19.3602 32.5033 18.8237 32.486 18.2765 32.4776L15.3327 32.426L15.8272 29.7325C15.8272 29.7325 17.5018 29.7891 17.4765 29.7629C18.1132 29.7698 18.2898 29.3121 18.339 29.0249L18.4403 23.1C18.5237 23.0999 18.601 23.1013 18.6783 23.1027C18.5846 23.0859 18.5073 23.0845 18.4421 23.0803L18.5173 18.8484C18.4362 18.3861 18.1456 17.8501 17.2315 17.8367C17.2245 17.8062 15.5884 17.8055 15.5884 17.8055L15.6276 15.3909L18.7517 15.4458L18.7485 15.4594C19.2184 15.4664 19.6991 15.4644 20.1873 15.4672L20.2531 11.7507L22.5147 11.7931L22.4503 15.4323C23.0537 15.4341 23.6619 15.4284 24.2546 15.4391L24.3185 11.8256L26.5787 11.8619L26.519 15.577C29.4323 15.8859 31.7277 16.8249 31.9354 19.5032ZM24.333 4.27111C13.2932 4.08077 4.18614 12.8739 3.99532 23.9128C3.79981 34.9593 12.5933 44.0651 23.6332 44.2555C34.6806 44.4505 43.7876 35.6574 43.977 24.6124C44.1739 13.572 35.379 4.46005 24.333 4.27111Z" fill="#FFBA50"/>
                                                    </svg>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <span class="d-block my-0 py-0" style="color: #676B87;font-size: 15px;">Bitcoin</span>
                                                <span class="d-block my-0 py-0" style="color: #676B87;font-size: 12px;">BTC</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 text-right">
                                        <span class="coin_chart_price bitcoin-price">- - -</span>
                                    </div>
                                    {{-- <div class="col-4">
                                        <div class="d-flex flex-column">
                                            <div>
                                                <svg width="60" height="27" viewBox="0 0 92 27" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M2.08311 1.18267C1.82908 0.928694 1.41721 0.928694 1.16318 1.18267C0.909148 1.43664 0.909148 1.84841 1.16318 2.10239C1.70693 2.64601 2.25492 3.18847 2.80358 3.73157C8.23833 9.11137 13.7381 14.5555 15.8199 21.8404C15.8424 21.9057 15.9305 22.0593 16.0038 22.1393C16.1223 22.2263 16.3942 22.3103 16.5336 22.3064C16.6285 22.2861 16.7734 22.2239 16.8246 22.1907C16.8559 22.1668 16.9051 22.1226 16.9237 22.1033C16.9788 22.0437 17.0112 21.9847 17.0139 21.9797C17.0302 21.9509 17.0425 21.9242 17.0464 21.9156C17.0576 21.8911 17.0699 21.8617 17.0812 21.8343C17.105 21.7766 17.1378 21.6939 17.1775 21.5922C17.2574 21.3876 17.3703 21.0927 17.5057 20.7356C17.7768 20.0207 18.14 19.0503 18.5136 18.0392C19.2541 16.0352 20.0552 13.8184 20.2398 13.1561C20.3823 12.6451 20.4981 11.9819 20.6148 11.3024L20.635 11.1845C20.748 10.5258 20.866 9.83749 21.0156 9.17949C21.1748 8.4791 21.3624 7.84762 21.5984 7.35189C21.8394 6.84552 22.091 6.56593 22.3289 6.44701C22.3545 6.43421 22.3789 6.41996 22.402 6.4044C22.595 6.58465 22.7849 6.76043 22.9694 6.93119C23.2683 7.20782 23.553 7.47135 23.8136 7.71922C24.481 8.35415 25.0507 8.94118 25.519 9.56955C26.4324 10.7951 26.9973 12.2264 26.9973 14.5432C26.9973 14.7755 27.0837 15.0769 27.1713 15.3409C27.2695 15.6374 27.4085 15.9972 27.5766 16.3921C27.9132 17.1827 28.3785 18.1425 28.9003 19.0651C29.419 19.9822 30.0086 20.8899 30.5977 21.5587C30.8911 21.8919 31.2058 22.1906 31.5326 22.399C31.8524 22.6029 32.2603 22.7682 32.7043 22.6942C33.6484 22.5369 34.2996 21.9381 34.7649 21.2229C35.222 20.5205 35.542 19.6431 35.8153 18.801C35.8999 18.5404 35.9798 18.2842 36.058 18.0334C36.2424 17.4422 36.4175 16.8808 36.6227 16.3623C36.9162 15.6209 37.2231 15.1038 37.5794 14.8188C38.0147 14.4706 38.2069 13.9776 38.322 13.5842C38.3735 13.408 38.4163 13.2258 38.4542 13.0647L38.4698 12.9983C38.5137 12.8126 38.5525 12.6565 38.5989 12.518C38.6941 12.2336 38.7833 12.1425 38.8581 12.1032C38.9337 12.0636 39.1388 12.0026 39.6259 12.1216C39.67 12.2136 39.7228 12.3588 39.779 12.5622C39.8994 12.9981 40.0061 13.5898 40.096 14.2224C40.2749 15.4814 40.3747 16.8112 40.3955 17.1643C40.4165 17.521 40.5146 17.9256 40.8372 18.1965C41.1789 18.4836 41.5885 18.4713 41.88 18.4047C42.1731 18.3376 42.4691 18.1917 42.7376 18.0329C43.0138 17.8696 43.3015 17.668 43.5832 17.4576C43.8658 17.2464 44.153 17.0183 44.4264 16.8L44.5107 16.7327C44.7556 16.5371 44.988 16.3514 45.2042 16.1869C45.4472 16.0018 45.6522 15.8571 45.816 15.7608C45.9199 15.6997 45.9775 15.6765 45.9982 15.6681C46.0009 15.667 46.0045 15.6656 46.0045 15.6656C46.3054 15.6746 46.6388 15.8117 47.0041 16.1072C47.3822 16.4131 47.7526 16.8559 48.0966 17.3798C48.7851 18.4286 49.2988 19.6957 49.5318 20.511C50.0825 22.4381 50.9442 24.0624 52.3512 24.9151C53.7972 25.7914 55.6303 25.7457 57.8521 24.7584C58.0377 24.676 58.1894 24.5315 58.2899 24.4269C58.4058 24.3063 58.5282 24.1577 58.6529 23.9947C58.9031 23.6677 59.1962 23.238 59.5127 22.7554C59.8292 22.2729 60.1772 21.725 60.5387 21.1559L60.5434 21.1485C60.9076 20.5751 61.2864 19.9789 61.6664 19.3963C62.4319 18.2229 63.1819 17.1374 63.8057 16.425C64.1031 16.0854 64.3356 15.8733 64.4977 15.7686C65.0126 16.3059 65.5131 16.9537 66.0592 17.6604C66.1413 17.7666 66.2243 17.8741 66.3086 17.9827C66.9885 18.8591 67.7368 19.7939 68.6117 20.5811C69.1351 21.0521 69.8586 21.2899 70.5888 21.415C71.3314 21.5423 72.1643 21.5662 72.9812 21.5467C73.8008 21.5271 74.631 21.4628 75.3677 21.4049L75.4029 21.4021C76.1375 21.3443 76.7586 21.2955 77.2206 21.2955C77.9519 21.2955 78.6541 21.5994 79.4345 22.0787C79.7582 22.2775 80.0783 22.4952 80.415 22.7242L80.623 22.8656C81.0302 23.1415 81.4606 23.427 81.9106 23.6769C83.3159 24.4575 84.8564 24.589 86.2386 24.6967L86.3301 24.7038C87.722 24.812 88.946 24.9072 90.0311 25.4496C90.3524 25.6102 90.7432 25.48 90.9038 25.1588C91.0645 24.8375 90.9343 24.4469 90.6129 24.2862C89.295 23.6274 87.8403 23.5154 86.5243 23.4142L86.3397 23.3999C84.9284 23.29 83.6656 23.1638 82.5424 22.5399C82.1468 22.3202 81.7573 22.063 81.353 21.789L81.1542 21.6539C80.8174 21.4247 80.4675 21.1866 80.1155 20.9704C79.2728 20.4529 78.3176 19.9948 77.2206 19.9948C76.7064 19.9948 76.0415 20.0471 75.3419 20.1022L75.2657 20.1082C74.5251 20.1664 73.729 20.2278 72.9501 20.2464C72.1687 20.2651 71.4313 20.2397 70.8085 20.133C70.1734 20.0242 69.7367 19.8435 69.482 19.6143C68.7024 18.9129 68.0163 18.0617 67.3366 17.1856C67.2549 17.0802 67.1729 16.9741 67.0909 16.8678C66.505 16.109 65.9094 15.3375 65.283 14.7113C65.0742 14.5024 64.8045 14.4037 64.5236 14.4155C64.2709 14.426 64.0472 14.5234 63.8713 14.627C63.5218 14.8327 63.1658 15.1811 62.8269 15.5682C62.1358 16.3574 61.3415 17.5133 60.5767 18.6857C60.1916 19.276 59.8087 19.8789 59.4452 20.4512L59.4438 20.4534C59.0797 21.0266 58.736 21.5677 58.4248 22.0421C58.1119 22.5193 57.8398 22.9165 57.6196 23.2045C57.509 23.349 57.4199 23.4548 57.3518 23.5257C57.3212 23.5575 57.3011 23.5756 57.291 23.5843C55.3015 24.4609 53.9645 24.3718 53.0255 23.8028C52.0424 23.207 51.3034 21.9758 50.7827 20.1537C50.5199 19.2341 49.9564 17.8424 49.1842 16.6661C48.7977 16.0774 48.3418 15.5162 47.8224 15.0961C47.3033 14.6761 46.6785 14.3644 45.9726 14.3644C45.658 14.3644 45.3563 14.5221 45.1566 14.6396C44.9244 14.7761 44.6701 14.9587 44.4161 15.152C44.1866 15.3267 43.9422 15.5219 43.6991 15.7161L43.6146 15.7836C43.3407 16.0023 43.0683 16.2185 42.8044 16.4157C42.5396 16.6135 42.294 16.7841 42.0754 16.9134C41.9168 17.0071 41.7906 17.0681 41.6953 17.1039L41.6942 17.0879C41.6721 16.7118 41.5695 15.3444 41.3841 14.0394C41.2918 13.39 41.1759 12.7329 41.033 12.2159C40.9624 11.9603 40.8775 11.71 40.7717 11.5044C40.7188 11.4014 40.6499 11.2884 40.5594 11.1876C40.4709 11.0891 40.3301 10.9671 40.1285 10.9095C39.401 10.7017 38.7685 10.6816 38.254 10.9513C37.7318 11.225 37.4972 11.7108 37.3652 12.1052C37.2979 12.3062 37.2472 12.5149 37.2037 12.6993L37.1873 12.7686C37.1486 12.9333 37.1143 13.079 37.0733 13.2191C36.9776 13.5461 36.879 13.7133 36.7667 13.8031C36.1328 14.3102 35.7239 15.0982 35.413 15.8837C35.1889 16.4499 34.9906 17.0855 34.8 17.6966C34.7252 17.9364 34.6514 18.1729 34.5779 18.3995C34.3062 19.2363 34.0294 19.968 33.6744 20.5136C33.3278 21.0464 32.9518 21.3344 32.4904 21.4112C32.4904 21.4112 32.4146 21.4187 32.2321 21.3024C32.0491 21.1856 31.8267 20.986 31.5741 20.6992C31.0711 20.1281 30.5327 19.309 30.0328 18.4249C29.5358 17.5462 29.0918 16.6302 28.7737 15.8828C28.6145 15.5087 28.4898 15.1839 28.4062 14.9318C28.3264 14.6912 28.3044 14.5745 28.2995 14.5485C28.299 14.5459 28.2985 14.5434 28.2985 14.5434C28.2985 11.9679 27.6553 10.2591 26.5622 8.79242C26.0273 8.07466 25.3942 7.42745 24.7103 6.77693C24.4179 6.49871 24.1252 6.22807 23.8255 5.95086C23.4015 5.55881 22.9632 5.15345 22.4912 4.69505C22.2335 4.44479 21.8217 4.45075 21.5714 4.70838C21.3721 4.91347 21.3353 5.21622 21.4585 5.45743C20.9963 5.78642 20.6674 6.28086 20.4237 6.7929C20.1269 7.41629 19.9141 8.1559 19.7469 8.89128C19.5893 9.58467 19.4658 10.3051 19.3542 10.9565L19.3326 11.0824C19.2127 11.7807 19.1076 12.3727 18.9866 12.8069C18.8203 13.4035 18.0452 15.5536 17.2933 17.5884C17.0091 18.3574 16.7312 19.1023 16.4957 19.7279C13.9922 12.9632 8.71404 7.74471 3.73999 2.82687C3.18244 2.27562 2.62868 1.72811 2.08311 1.18267Z" fill="#000070"/>
                                                    <path d="M41.544 17.1443L41.5467 17.1442M1.16318 1.18267C1.41721 0.928694 1.82908 0.928694 2.08311 1.18267C2.62868 1.72811 3.18244 2.27562 3.73999 2.82687C8.71404 7.74471 13.9922 12.9632 16.4957 19.7279C16.7312 19.1023 17.0091 18.3574 17.2933 17.5884C18.0452 15.5536 18.8203 13.4035 18.9866 12.8069C19.1076 12.3727 19.2127 11.7807 19.3326 11.0824L19.3542 10.9565C19.4658 10.3051 19.5893 9.58467 19.7469 8.89128C19.9141 8.1559 20.1269 7.41629 20.4237 6.7929C20.6674 6.28086 20.9963 5.78642 21.4585 5.45743C21.3353 5.21622 21.3721 4.91347 21.5714 4.70838C21.8217 4.45075 22.2335 4.44479 22.4912 4.69505C22.9632 5.15345 23.4015 5.55881 23.8255 5.95086C24.1252 6.22807 24.4179 6.49871 24.7103 6.77693C25.3942 7.42745 26.0273 8.07466 26.5622 8.79242C27.6553 10.2591 28.2985 11.9679 28.2985 14.5434C28.2985 14.5434 28.299 14.5459 28.2995 14.5485C28.3044 14.5745 28.3264 14.6912 28.4062 14.9318C28.4898 15.1839 28.6145 15.5087 28.7737 15.8828C29.0918 16.6302 29.5358 17.5462 30.0328 18.4249C30.5327 19.309 31.0711 20.1281 31.5741 20.6992C31.8267 20.986 32.0491 21.1856 32.2321 21.3024C32.4146 21.4187 32.4904 21.4112 32.4904 21.4112C32.9518 21.3344 33.3278 21.0464 33.6744 20.5136C34.0294 19.968 34.3062 19.2363 34.5779 18.3995C34.6514 18.1729 34.7252 17.9364 34.8 17.6966C34.9906 17.0855 35.1889 16.4499 35.413 15.8837C35.7239 15.0982 36.1328 14.3102 36.7667 13.8031C36.879 13.7133 36.9776 13.5461 37.0733 13.219C37.1143 13.079 37.1486 12.9333 37.1873 12.7686L37.2037 12.6993C37.2472 12.5149 37.2979 12.3062 37.3652 12.1052C37.4972 11.7108 37.7318 11.225 38.254 10.9513C38.7685 10.6816 39.401 10.7017 40.1285 10.9095C40.3301 10.9671 40.4709 11.0891 40.5594 11.1876C40.6499 11.2884 40.7188 11.4014 40.7717 11.5044C40.8775 11.71 40.9624 11.9603 41.033 12.2159C41.1759 12.7329 41.2918 13.39 41.3841 14.0394C41.5695 15.3444 41.6721 16.7118 41.6942 17.0879L41.6953 17.1039C41.7906 17.0681 41.9168 17.0071 42.0754 16.9134C42.294 16.7841 42.5396 16.6135 42.8044 16.4157C43.0683 16.2185 43.3407 16.0023 43.6146 15.7836L43.6991 15.7161C43.9422 15.5219 44.1866 15.3267 44.4161 15.152C44.6701 14.9587 44.9244 14.7761 45.1566 14.6396C45.3563 14.5221 45.658 14.3644 45.9726 14.3644C46.6785 14.3644 47.3033 14.6761 47.8224 15.0961C48.3418 15.5162 48.7977 16.0774 49.1842 16.6661C49.9564 17.8424 50.5199 19.2341 50.7827 20.1537C51.3034 21.9758 52.0424 23.207 53.0255 23.8028C53.9645 24.3718 55.3015 24.4609 57.291 23.5843C57.3011 23.5756 57.3212 23.5575 57.3518 23.5257C57.4199 23.4548 57.509 23.349 57.6196 23.2045C57.8398 22.9165 58.1119 22.5193 58.4248 22.0421C58.736 21.5677 59.0797 21.0266 59.4438 20.4534L59.4452 20.4512C59.8087 19.8789 60.1916 19.276 60.5767 18.6857C61.3415 17.5133 62.1358 16.3574 62.8269 15.5682C63.1658 15.1811 63.5218 14.8327 63.8713 14.627C64.0472 14.5234 64.2709 14.426 64.5236 14.4155C64.8045 14.4037 65.0741 14.5024 65.283 14.7113C65.9094 15.3375 66.505 16.109 67.0908 16.8678C67.1729 16.9741 67.2548 17.0802 67.3366 17.1856C68.0163 18.0617 68.7024 18.9129 69.482 19.6143C69.7367 19.8435 70.1734 20.0242 70.8085 20.133C71.4313 20.2397 72.1687 20.2651 72.9501 20.2464C73.729 20.2278 74.5251 20.1664 75.2657 20.1082L75.3419 20.1022C76.0415 20.0471 76.7064 19.9948 77.2206 19.9948C78.3176 19.9948 79.2728 20.4529 80.1155 20.9704C80.4675 21.1866 80.8173 21.4247 81.1542 21.6539L81.353 21.789C81.7573 22.063 82.1468 22.3202 82.5424 22.5399C83.6656 23.1638 84.9284 23.29 86.3397 23.3999L86.5243 23.4142C87.8403 23.5154 89.295 23.6274 90.6129 24.2862C90.9343 24.4469 91.0645 24.8375 90.9038 25.1588C90.7432 25.48 90.3524 25.6102 90.0311 25.4496C88.946 24.9072 87.722 24.812 86.3301 24.7038L86.2386 24.6967C84.8564 24.589 83.3159 24.4575 81.9106 23.6769C81.4606 23.427 81.0302 23.1415 80.623 22.8656L80.415 22.7242C80.0783 22.4952 79.7583 22.2775 79.4345 22.0787C78.6541 21.5994 77.9519 21.2955 77.2206 21.2955C76.7586 21.2955 76.1375 21.3443 75.4029 21.4021L75.3677 21.4049C74.631 21.4628 73.8008 21.5271 72.9812 21.5467C72.1643 21.5662 71.3314 21.5423 70.5888 21.415C69.8586 21.2899 69.1351 21.0521 68.6117 20.5811C67.7368 19.7939 66.9885 18.8591 66.3086 17.9827C66.2243 17.8741 66.1413 17.7666 66.0592 17.6604C65.5131 16.9537 65.0126 16.3059 64.4977 15.7686C64.3356 15.8733 64.1031 16.0854 63.8057 16.425C63.1818 17.1374 62.4319 18.2229 61.6664 19.3963C61.2864 19.9789 60.9076 20.5751 60.5434 21.1485L60.5387 21.1559C60.1772 21.725 59.8292 22.2729 59.5127 22.7554C59.1962 23.238 58.9031 23.6677 58.6529 23.9947C58.5282 24.1577 58.4058 24.3063 58.2899 24.4269C58.1894 24.5315 58.0377 24.676 57.8521 24.7584C55.6303 25.7457 53.7972 25.7914 52.3512 24.9151C50.9442 24.0624 50.0825 22.4381 49.5318 20.511C49.2988 19.6957 48.7851 18.4286 48.0966 17.3798C47.7526 16.8559 47.3822 16.4131 47.0041 16.1072C46.6388 15.8117 46.3054 15.6746 46.0045 15.6656C46.0045 15.6656 46.0009 15.667 45.9982 15.6681C45.9775 15.6765 45.9199 15.6997 45.816 15.7608C45.6522 15.8571 45.4472 16.0018 45.2042 16.1869C44.988 16.3514 44.7556 16.5371 44.5106 16.7327L44.4264 16.8C44.153 17.0183 43.8658 17.2464 43.5832 17.4576C43.3015 17.668 43.0138 17.8696 42.7376 18.0329C42.4691 18.1917 42.1731 18.3376 41.88 18.4047C41.5885 18.4713 41.1789 18.4836 40.8372 18.1965C40.5146 17.9256 40.4165 17.521 40.3955 17.1643C40.3747 16.8112 40.2749 15.4814 40.096 14.2224C40.0061 13.5898 39.8994 12.9981 39.779 12.5622C39.7228 12.3588 39.67 12.2136 39.6259 12.1216C39.1388 12.0026 38.9337 12.0636 38.8581 12.1032C38.7833 12.1425 38.6941 12.2336 38.5989 12.518C38.5525 12.6565 38.5137 12.8126 38.4698 12.9983L38.4542 13.0647C38.4163 13.2258 38.3735 13.408 38.322 13.5842C38.2069 13.9776 38.0147 14.4706 37.5794 14.8188C37.2231 15.1038 36.9162 15.6209 36.6227 16.3623C36.4175 16.8808 36.2424 17.4422 36.058 18.0334C35.9798 18.2842 35.8999 18.5404 35.8153 18.801C35.542 19.6431 35.222 20.5205 34.7649 21.2229C34.2996 21.9381 33.6484 22.5369 32.7043 22.6942C32.2603 22.7682 31.8524 22.6029 31.5326 22.399C31.2058 22.1906 30.8911 21.8919 30.5977 21.5587C30.0086 20.8899 29.419 19.9822 28.9003 19.0651C28.3785 18.1425 27.9132 17.1827 27.5766 16.3921C27.4085 15.9972 27.2695 15.6374 27.1713 15.3409C27.0837 15.0769 26.9973 14.7755 26.9973 14.5432C26.9973 12.2264 26.4324 10.7951 25.519 9.56955C25.0507 8.94118 24.481 8.35415 23.8136 7.71922C23.553 7.47135 23.2683 7.20782 22.9694 6.93119C22.7849 6.76043 22.595 6.58465 22.402 6.4044C22.3789 6.41996 22.3545 6.43421 22.3289 6.44701C22.091 6.56593 21.8394 6.84552 21.5984 7.35189C21.3624 7.84762 21.1748 8.4791 21.0156 9.17949C20.866 9.83749 20.748 10.5258 20.635 11.1845L20.6148 11.3024C20.4981 11.9819 20.3823 12.6451 20.2398 13.1561C20.0552 13.8184 19.2541 16.0352 18.5136 18.0392C18.14 19.0503 17.7768 20.0207 17.5057 20.7356C17.3703 21.0927 17.2574 21.3876 17.1775 21.5922C17.1378 21.6939 17.105 21.7766 17.0812 21.8343C17.0699 21.8617 17.0576 21.8911 17.0464 21.9156C17.0425 21.9242 17.0302 21.9509 17.0139 21.9797C17.0111 21.9847 16.9788 22.0437 16.9237 22.1033C16.9051 22.1226 16.8559 22.1668 16.8246 22.1907C16.7734 22.2239 16.6285 22.2861 16.5336 22.3064C16.3942 22.3103 16.1223 22.2263 16.0038 22.1393C15.9305 22.0593 15.8424 21.9057 15.8199 21.8404C13.7381 14.5555 8.23833 9.11137 2.80358 3.73157C2.25492 3.18847 1.70693 2.64601 1.16318 2.10239C0.909148 1.84841 0.909148 1.43664 1.16318 1.18267Z" stroke="#000070" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                            </div>
                                            <span class="d-block" style="color: #000070;">
                                                +5.24%
                                            </span>
                                        </div>
                                    </div> --}}
                                </div>
                                <div class="row mt-4">
                                    <div class="col-4">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <svg width="40" height="41" viewBox="0 0 40 41" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="20" cy="20.373" r="20" fill="#11263A"/>
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M11.3809 20.6218L19.9885 25.5153L19.9931 25.5197L19.9947 25.5188L19.9964 25.5197V25.5178L19.997 25.5174V25.5224L28.6124 20.6245L28.6075 20.6224L28.6086 20.6218L19.9964 6.8783V6.87312L19.9948 6.87571L19.9931 6.87305L19.9899 6.88347L11.3809 20.6218ZM19.9964 27.0892V27.0943L19.9976 27.0929L28.6182 22.1975L19.9976 33.874L19.9961 33.87L11.3809 22.1938L19.9964 27.0892Z" fill="white"/>
                                                    </svg>
                                            </div>
                                            <div class="d-flex flex-column ml-2">
                                                <span class="d-block my-0 py-0" style="color: #676B87;font-size: 15px;">Ethereum</span>
                                                <span class="d-block my-0 py-0" style="color: #676B87;font-size: 12px;">ETH</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 text-right">
                                        <span class="coin_chart_price ethereum-price">- - -</span>
                                    </div>
                                    {{-- <div class="col-4">
                                        <div class="d-flex flex-column">
                                            <div style="width: 10px;">
                                                <svg width="60" height="27" viewBox="0 0 92 27" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M2.08311 1.18267C1.82908 0.928694 1.41721 0.928694 1.16318 1.18267C0.909148 1.43664 0.909148 1.84841 1.16318 2.10239C1.70693 2.64601 2.25492 3.18847 2.80358 3.73157C8.23833 9.11137 13.7381 14.5555 15.8199 21.8404C15.8424 21.9057 15.9305 22.0593 16.0038 22.1393C16.1223 22.2263 16.3942 22.3103 16.5336 22.3064C16.6285 22.2861 16.7734 22.2239 16.8246 22.1907C16.8559 22.1668 16.9051 22.1226 16.9237 22.1033C16.9788 22.0437 17.0112 21.9847 17.0139 21.9797C17.0302 21.9509 17.0425 21.9242 17.0464 21.9156C17.0576 21.8911 17.0699 21.8617 17.0812 21.8343C17.105 21.7766 17.1378 21.6939 17.1775 21.5922C17.2574 21.3876 17.3703 21.0927 17.5057 20.7356C17.7768 20.0207 18.14 19.0503 18.5136 18.0392C19.2541 16.0352 20.0552 13.8184 20.2398 13.1561C20.3823 12.6451 20.4981 11.9819 20.6148 11.3024L20.635 11.1845C20.748 10.5258 20.866 9.83749 21.0156 9.17949C21.1748 8.4791 21.3624 7.84762 21.5984 7.35189C21.8394 6.84552 22.091 6.56593 22.3289 6.44701C22.3545 6.43421 22.3789 6.41996 22.402 6.4044C22.595 6.58465 22.7849 6.76043 22.9694 6.93119C23.2683 7.20782 23.553 7.47135 23.8136 7.71922C24.481 8.35415 25.0507 8.94118 25.519 9.56955C26.4324 10.7951 26.9973 12.2264 26.9973 14.5432C26.9973 14.7755 27.0837 15.0769 27.1713 15.3409C27.2695 15.6374 27.4085 15.9972 27.5766 16.3921C27.9132 17.1827 28.3785 18.1425 28.9003 19.0651C29.419 19.9822 30.0086 20.8899 30.5977 21.5587C30.8911 21.8919 31.2058 22.1906 31.5326 22.399C31.8524 22.6029 32.2603 22.7682 32.7043 22.6942C33.6484 22.5369 34.2996 21.9381 34.7649 21.2229C35.222 20.5205 35.542 19.6431 35.8153 18.801C35.8999 18.5404 35.9798 18.2842 36.058 18.0334C36.2424 17.4422 36.4175 16.8808 36.6227 16.3623C36.9162 15.6209 37.2231 15.1038 37.5794 14.8188C38.0147 14.4706 38.2069 13.9776 38.322 13.5842C38.3735 13.408 38.4163 13.2258 38.4542 13.0647L38.4698 12.9983C38.5137 12.8126 38.5525 12.6565 38.5989 12.518C38.6941 12.2336 38.7833 12.1425 38.8581 12.1032C38.9337 12.0636 39.1388 12.0026 39.6259 12.1216C39.67 12.2136 39.7228 12.3588 39.779 12.5622C39.8994 12.9981 40.0061 13.5898 40.096 14.2224C40.2749 15.4814 40.3747 16.8112 40.3955 17.1643C40.4165 17.521 40.5146 17.9256 40.8372 18.1965C41.1789 18.4836 41.5885 18.4713 41.88 18.4047C42.1731 18.3376 42.4691 18.1917 42.7376 18.0329C43.0138 17.8696 43.3015 17.668 43.5832 17.4576C43.8658 17.2464 44.153 17.0183 44.4264 16.8L44.5107 16.7327C44.7556 16.5371 44.988 16.3514 45.2042 16.1869C45.4472 16.0018 45.6522 15.8571 45.816 15.7608C45.9199 15.6997 45.9775 15.6765 45.9982 15.6681C46.0009 15.667 46.0045 15.6656 46.0045 15.6656C46.3054 15.6746 46.6388 15.8117 47.0041 16.1072C47.3822 16.4131 47.7526 16.8559 48.0966 17.3798C48.7851 18.4286 49.2988 19.6957 49.5318 20.511C50.0825 22.4381 50.9442 24.0624 52.3512 24.9151C53.7972 25.7914 55.6303 25.7457 57.8521 24.7584C58.0377 24.676 58.1894 24.5315 58.2899 24.4269C58.4058 24.3063 58.5282 24.1577 58.6529 23.9947C58.9031 23.6677 59.1962 23.238 59.5127 22.7554C59.8292 22.2729 60.1772 21.725 60.5387 21.1559L60.5434 21.1485C60.9076 20.5751 61.2864 19.9789 61.6664 19.3963C62.4319 18.2229 63.1819 17.1374 63.8057 16.425C64.1031 16.0854 64.3356 15.8733 64.4977 15.7686C65.0126 16.3059 65.5131 16.9537 66.0592 17.6604C66.1413 17.7666 66.2243 17.8741 66.3086 17.9827C66.9885 18.8591 67.7368 19.7939 68.6117 20.5811C69.1351 21.0521 69.8586 21.2899 70.5888 21.415C71.3314 21.5423 72.1643 21.5662 72.9812 21.5467C73.8008 21.5271 74.631 21.4628 75.3677 21.4049L75.4029 21.4021C76.1375 21.3443 76.7586 21.2955 77.2206 21.2955C77.9519 21.2955 78.6541 21.5994 79.4345 22.0787C79.7582 22.2775 80.0783 22.4952 80.415 22.7242L80.623 22.8656C81.0302 23.1415 81.4606 23.427 81.9106 23.6769C83.3159 24.4575 84.8564 24.589 86.2386 24.6967L86.3301 24.7038C87.722 24.812 88.946 24.9072 90.0311 25.4496C90.3524 25.6102 90.7432 25.48 90.9038 25.1588C91.0645 24.8375 90.9343 24.4469 90.6129 24.2862C89.295 23.6274 87.8403 23.5154 86.5243 23.4142L86.3397 23.3999C84.9284 23.29 83.6656 23.1638 82.5424 22.5399C82.1468 22.3202 81.7573 22.063 81.353 21.789L81.1542 21.6539C80.8174 21.4247 80.4675 21.1866 80.1155 20.9704C79.2728 20.4529 78.3176 19.9948 77.2206 19.9948C76.7064 19.9948 76.0415 20.0471 75.3419 20.1022L75.2657 20.1082C74.5251 20.1664 73.729 20.2278 72.9501 20.2464C72.1687 20.2651 71.4313 20.2397 70.8085 20.133C70.1734 20.0242 69.7367 19.8435 69.482 19.6143C68.7024 18.9129 68.0163 18.0617 67.3366 17.1856C67.2549 17.0802 67.1729 16.9741 67.0909 16.8678C66.505 16.109 65.9094 15.3375 65.283 14.7113C65.0742 14.5024 64.8045 14.4037 64.5236 14.4155C64.2709 14.426 64.0472 14.5234 63.8713 14.627C63.5218 14.8327 63.1658 15.1811 62.8269 15.5682C62.1358 16.3574 61.3415 17.5133 60.5767 18.6857C60.1916 19.276 59.8087 19.8789 59.4452 20.4512L59.4438 20.4534C59.0797 21.0266 58.736 21.5677 58.4248 22.0421C58.1119 22.5193 57.8398 22.9165 57.6196 23.2045C57.509 23.349 57.4199 23.4548 57.3518 23.5257C57.3212 23.5575 57.3011 23.5756 57.291 23.5843C55.3015 24.4609 53.9645 24.3718 53.0255 23.8028C52.0424 23.207 51.3034 21.9758 50.7827 20.1537C50.5199 19.2341 49.9564 17.8424 49.1842 16.6661C48.7977 16.0774 48.3418 15.5162 47.8224 15.0961C47.3033 14.6761 46.6785 14.3644 45.9726 14.3644C45.658 14.3644 45.3563 14.5221 45.1566 14.6396C44.9244 14.7761 44.6701 14.9587 44.4161 15.152C44.1866 15.3267 43.9422 15.5219 43.6991 15.7161L43.6146 15.7836C43.3407 16.0023 43.0683 16.2185 42.8044 16.4157C42.5396 16.6135 42.294 16.7841 42.0754 16.9134C41.9168 17.0071 41.7906 17.0681 41.6953 17.1039L41.6942 17.0879C41.6721 16.7118 41.5695 15.3444 41.3841 14.0394C41.2918 13.39 41.1759 12.7329 41.033 12.2159C40.9624 11.9603 40.8775 11.71 40.7717 11.5044C40.7188 11.4014 40.6499 11.2884 40.5594 11.1876C40.4709 11.0891 40.3301 10.9671 40.1285 10.9095C39.401 10.7017 38.7685 10.6816 38.254 10.9513C37.7318 11.225 37.4972 11.7108 37.3652 12.1052C37.2979 12.3062 37.2472 12.5149 37.2037 12.6993L37.1873 12.7686C37.1486 12.9333 37.1143 13.079 37.0733 13.2191C36.9776 13.5461 36.879 13.7133 36.7667 13.8031C36.1328 14.3102 35.7239 15.0982 35.413 15.8837C35.1889 16.4499 34.9906 17.0855 34.8 17.6966C34.7252 17.9364 34.6514 18.1729 34.5779 18.3995C34.3062 19.2363 34.0294 19.968 33.6744 20.5136C33.3278 21.0464 32.9518 21.3344 32.4904 21.4112C32.4904 21.4112 32.4146 21.4187 32.2321 21.3024C32.0491 21.1856 31.8267 20.986 31.5741 20.6992C31.0711 20.1281 30.5327 19.309 30.0328 18.4249C29.5358 17.5462 29.0918 16.6302 28.7737 15.8828C28.6145 15.5087 28.4898 15.1839 28.4062 14.9318C28.3264 14.6912 28.3044 14.5745 28.2995 14.5485C28.299 14.5459 28.2985 14.5434 28.2985 14.5434C28.2985 11.9679 27.6553 10.2591 26.5622 8.79242C26.0273 8.07466 25.3942 7.42745 24.7103 6.77693C24.4179 6.49871 24.1252 6.22807 23.8255 5.95086C23.4015 5.55881 22.9632 5.15345 22.4912 4.69505C22.2335 4.44479 21.8217 4.45075 21.5714 4.70838C21.3721 4.91347 21.3353 5.21622 21.4585 5.45743C20.9963 5.78642 20.6674 6.28086 20.4237 6.7929C20.1269 7.41629 19.9141 8.1559 19.7469 8.89128C19.5893 9.58467 19.4658 10.3051 19.3542 10.9565L19.3326 11.0824C19.2127 11.7807 19.1076 12.3727 18.9866 12.8069C18.8203 13.4035 18.0452 15.5536 17.2933 17.5884C17.0091 18.3574 16.7312 19.1023 16.4957 19.7279C13.9922 12.9632 8.71404 7.74471 3.73999 2.82687C3.18244 2.27562 2.62868 1.72811 2.08311 1.18267Z" fill="#000070"/>
                                                    <path d="M41.544 17.1443L41.5467 17.1442M1.16318 1.18267C1.41721 0.928694 1.82908 0.928694 2.08311 1.18267C2.62868 1.72811 3.18244 2.27562 3.73999 2.82687C8.71404 7.74471 13.9922 12.9632 16.4957 19.7279C16.7312 19.1023 17.0091 18.3574 17.2933 17.5884C18.0452 15.5536 18.8203 13.4035 18.9866 12.8069C19.1076 12.3727 19.2127 11.7807 19.3326 11.0824L19.3542 10.9565C19.4658 10.3051 19.5893 9.58467 19.7469 8.89128C19.9141 8.1559 20.1269 7.41629 20.4237 6.7929C20.6674 6.28086 20.9963 5.78642 21.4585 5.45743C21.3353 5.21622 21.3721 4.91347 21.5714 4.70838C21.8217 4.45075 22.2335 4.44479 22.4912 4.69505C22.9632 5.15345 23.4015 5.55881 23.8255 5.95086C24.1252 6.22807 24.4179 6.49871 24.7103 6.77693C25.3942 7.42745 26.0273 8.07466 26.5622 8.79242C27.6553 10.2591 28.2985 11.9679 28.2985 14.5434C28.2985 14.5434 28.299 14.5459 28.2995 14.5485C28.3044 14.5745 28.3264 14.6912 28.4062 14.9318C28.4898 15.1839 28.6145 15.5087 28.7737 15.8828C29.0918 16.6302 29.5358 17.5462 30.0328 18.4249C30.5327 19.309 31.0711 20.1281 31.5741 20.6992C31.8267 20.986 32.0491 21.1856 32.2321 21.3024C32.4146 21.4187 32.4904 21.4112 32.4904 21.4112C32.9518 21.3344 33.3278 21.0464 33.6744 20.5136C34.0294 19.968 34.3062 19.2363 34.5779 18.3995C34.6514 18.1729 34.7252 17.9364 34.8 17.6966C34.9906 17.0855 35.1889 16.4499 35.413 15.8837C35.7239 15.0982 36.1328 14.3102 36.7667 13.8031C36.879 13.7133 36.9776 13.5461 37.0733 13.219C37.1143 13.079 37.1486 12.9333 37.1873 12.7686L37.2037 12.6993C37.2472 12.5149 37.2979 12.3062 37.3652 12.1052C37.4972 11.7108 37.7318 11.225 38.254 10.9513C38.7685 10.6816 39.401 10.7017 40.1285 10.9095C40.3301 10.9671 40.4709 11.0891 40.5594 11.1876C40.6499 11.2884 40.7188 11.4014 40.7717 11.5044C40.8775 11.71 40.9624 11.9603 41.033 12.2159C41.1759 12.7329 41.2918 13.39 41.3841 14.0394C41.5695 15.3444 41.6721 16.7118 41.6942 17.0879L41.6953 17.1039C41.7906 17.0681 41.9168 17.0071 42.0754 16.9134C42.294 16.7841 42.5396 16.6135 42.8044 16.4157C43.0683 16.2185 43.3407 16.0023 43.6146 15.7836L43.6991 15.7161C43.9422 15.5219 44.1866 15.3267 44.4161 15.152C44.6701 14.9587 44.9244 14.7761 45.1566 14.6396C45.3563 14.5221 45.658 14.3644 45.9726 14.3644C46.6785 14.3644 47.3033 14.6761 47.8224 15.0961C48.3418 15.5162 48.7977 16.0774 49.1842 16.6661C49.9564 17.8424 50.5199 19.2341 50.7827 20.1537C51.3034 21.9758 52.0424 23.207 53.0255 23.8028C53.9645 24.3718 55.3015 24.4609 57.291 23.5843C57.3011 23.5756 57.3212 23.5575 57.3518 23.5257C57.4199 23.4548 57.509 23.349 57.6196 23.2045C57.8398 22.9165 58.1119 22.5193 58.4248 22.0421C58.736 21.5677 59.0797 21.0266 59.4438 20.4534L59.4452 20.4512C59.8087 19.8789 60.1916 19.276 60.5767 18.6857C61.3415 17.5133 62.1358 16.3574 62.8269 15.5682C63.1658 15.1811 63.5218 14.8327 63.8713 14.627C64.0472 14.5234 64.2709 14.426 64.5236 14.4155C64.8045 14.4037 65.0741 14.5024 65.283 14.7113C65.9094 15.3375 66.505 16.109 67.0908 16.8678C67.1729 16.9741 67.2548 17.0802 67.3366 17.1856C68.0163 18.0617 68.7024 18.9129 69.482 19.6143C69.7367 19.8435 70.1734 20.0242 70.8085 20.133C71.4313 20.2397 72.1687 20.2651 72.9501 20.2464C73.729 20.2278 74.5251 20.1664 75.2657 20.1082L75.3419 20.1022C76.0415 20.0471 76.7064 19.9948 77.2206 19.9948C78.3176 19.9948 79.2728 20.4529 80.1155 20.9704C80.4675 21.1866 80.8173 21.4247 81.1542 21.6539L81.353 21.789C81.7573 22.063 82.1468 22.3202 82.5424 22.5399C83.6656 23.1638 84.9284 23.29 86.3397 23.3999L86.5243 23.4142C87.8403 23.5154 89.295 23.6274 90.6129 24.2862C90.9343 24.4469 91.0645 24.8375 90.9038 25.1588C90.7432 25.48 90.3524 25.6102 90.0311 25.4496C88.946 24.9072 87.722 24.812 86.3301 24.7038L86.2386 24.6967C84.8564 24.589 83.3159 24.4575 81.9106 23.6769C81.4606 23.427 81.0302 23.1415 80.623 22.8656L80.415 22.7242C80.0783 22.4952 79.7583 22.2775 79.4345 22.0787C78.6541 21.5994 77.9519 21.2955 77.2206 21.2955C76.7586 21.2955 76.1375 21.3443 75.4029 21.4021L75.3677 21.4049C74.631 21.4628 73.8008 21.5271 72.9812 21.5467C72.1643 21.5662 71.3314 21.5423 70.5888 21.415C69.8586 21.2899 69.1351 21.0521 68.6117 20.5811C67.7368 19.7939 66.9885 18.8591 66.3086 17.9827C66.2243 17.8741 66.1413 17.7666 66.0592 17.6604C65.5131 16.9537 65.0126 16.3059 64.4977 15.7686C64.3356 15.8733 64.1031 16.0854 63.8057 16.425C63.1818 17.1374 62.4319 18.2229 61.6664 19.3963C61.2864 19.9789 60.9076 20.5751 60.5434 21.1485L60.5387 21.1559C60.1772 21.725 59.8292 22.2729 59.5127 22.7554C59.1962 23.238 58.9031 23.6677 58.6529 23.9947C58.5282 24.1577 58.4058 24.3063 58.2899 24.4269C58.1894 24.5315 58.0377 24.676 57.8521 24.7584C55.6303 25.7457 53.7972 25.7914 52.3512 24.9151C50.9442 24.0624 50.0825 22.4381 49.5318 20.511C49.2988 19.6957 48.7851 18.4286 48.0966 17.3798C47.7526 16.8559 47.3822 16.4131 47.0041 16.1072C46.6388 15.8117 46.3054 15.6746 46.0045 15.6656C46.0045 15.6656 46.0009 15.667 45.9982 15.6681C45.9775 15.6765 45.9199 15.6997 45.816 15.7608C45.6522 15.8571 45.4472 16.0018 45.2042 16.1869C44.988 16.3514 44.7556 16.5371 44.5106 16.7327L44.4264 16.8C44.153 17.0183 43.8658 17.2464 43.5832 17.4576C43.3015 17.668 43.0138 17.8696 42.7376 18.0329C42.4691 18.1917 42.1731 18.3376 41.88 18.4047C41.5885 18.4713 41.1789 18.4836 40.8372 18.1965C40.5146 17.9256 40.4165 17.521 40.3955 17.1643C40.3747 16.8112 40.2749 15.4814 40.096 14.2224C40.0061 13.5898 39.8994 12.9981 39.779 12.5622C39.7228 12.3588 39.67 12.2136 39.6259 12.1216C39.1388 12.0026 38.9337 12.0636 38.8581 12.1032C38.7833 12.1425 38.6941 12.2336 38.5989 12.518C38.5525 12.6565 38.5137 12.8126 38.4698 12.9983L38.4542 13.0647C38.4163 13.2258 38.3735 13.408 38.322 13.5842C38.2069 13.9776 38.0147 14.4706 37.5794 14.8188C37.2231 15.1038 36.9162 15.6209 36.6227 16.3623C36.4175 16.8808 36.2424 17.4422 36.058 18.0334C35.9798 18.2842 35.8999 18.5404 35.8153 18.801C35.542 19.6431 35.222 20.5205 34.7649 21.2229C34.2996 21.9381 33.6484 22.5369 32.7043 22.6942C32.2603 22.7682 31.8524 22.6029 31.5326 22.399C31.2058 22.1906 30.8911 21.8919 30.5977 21.5587C30.0086 20.8899 29.419 19.9822 28.9003 19.0651C28.3785 18.1425 27.9132 17.1827 27.5766 16.3921C27.4085 15.9972 27.2695 15.6374 27.1713 15.3409C27.0837 15.0769 26.9973 14.7755 26.9973 14.5432C26.9973 12.2264 26.4324 10.7951 25.519 9.56955C25.0507 8.94118 24.481 8.35415 23.8136 7.71922C23.553 7.47135 23.2683 7.20782 22.9694 6.93119C22.7849 6.76043 22.595 6.58465 22.402 6.4044C22.3789 6.41996 22.3545 6.43421 22.3289 6.44701C22.091 6.56593 21.8394 6.84552 21.5984 7.35189C21.3624 7.84762 21.1748 8.4791 21.0156 9.17949C20.866 9.83749 20.748 10.5258 20.635 11.1845L20.6148 11.3024C20.4981 11.9819 20.3823 12.6451 20.2398 13.1561C20.0552 13.8184 19.2541 16.0352 18.5136 18.0392C18.14 19.0503 17.7768 20.0207 17.5057 20.7356C17.3703 21.0927 17.2574 21.3876 17.1775 21.5922C17.1378 21.6939 17.105 21.7766 17.0812 21.8343C17.0699 21.8617 17.0576 21.8911 17.0464 21.9156C17.0425 21.9242 17.0302 21.9509 17.0139 21.9797C17.0111 21.9847 16.9788 22.0437 16.9237 22.1033C16.9051 22.1226 16.8559 22.1668 16.8246 22.1907C16.7734 22.2239 16.6285 22.2861 16.5336 22.3064C16.3942 22.3103 16.1223 22.2263 16.0038 22.1393C15.9305 22.0593 15.8424 21.9057 15.8199 21.8404C13.7381 14.5555 8.23833 9.11137 2.80358 3.73157C2.25492 3.18847 1.70693 2.64601 1.16318 2.10239C0.909148 1.84841 0.909148 1.43664 1.16318 1.18267Z" stroke="#000070" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                            </div>
                                            <span class="d-block" style="color: #000070;">
                                                +5.24%
                                            </span>
                                        </div>
                                    </div> --}}
                                </div>
                                <div class="row mt-4">
                                    <div class="col-4">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <svg width="40" height="41" viewBox="0 0 40 41" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M14.0593 20.1246L16.9122 9.40625H23.0982L20.9169 17.6195L23.9442 16.5135L23.9704 16.5847L23.2053 19.4595L20.1297 20.5831L18.8311 25.4744H29.1089L28.0533 29.4064H11.5893L13.2706 23.0893L10.8906 23.9586L11.6777 20.9946L14.0593 20.1246ZM20 0.132812C8.95422 0.132812 0 9.08721 0 20.1327C0 31.1784 8.95422 40.1328 20 40.1328C31.0458 40.1328 40 31.1784 40 20.1327C40 9.08721 31.0458 0.132812 20 0.132812Z" fill="#347AF0"/>
                                                    </svg>
                                            </div>
                                            <div class="d-flex flex-column ml-2">
                                                <span class="d-block my-0 py-0" style="color: #676B87;font-size: 15px;">Litecoin</span>
                                                <span class="d-block my-0 py-0" style="color: #676B87;font-size: 12px;">LTC</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 text-right">
                                        <span class="coin_chart_price litecoin-price">- - -</span>
                                    </div>
                                    {{-- <div class="col-4">
                                        <div class="d-flex flex-column">
                                            <div>
                                                <svg width="60" height="27" viewBox="0 0 92 27" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M2.08311 1.18267C1.82908 0.928694 1.41721 0.928694 1.16318 1.18267C0.909148 1.43664 0.909148 1.84841 1.16318 2.10239C1.70693 2.64601 2.25492 3.18847 2.80358 3.73157C8.23833 9.11137 13.7381 14.5555 15.8199 21.8404C15.8424 21.9057 15.9305 22.0593 16.0038 22.1393C16.1223 22.2263 16.3942 22.3103 16.5336 22.3064C16.6285 22.2861 16.7734 22.2239 16.8246 22.1907C16.8559 22.1668 16.9051 22.1226 16.9237 22.1033C16.9788 22.0437 17.0112 21.9847 17.0139 21.9797C17.0302 21.9509 17.0425 21.9242 17.0464 21.9156C17.0576 21.8911 17.0699 21.8617 17.0812 21.8343C17.105 21.7766 17.1378 21.6939 17.1775 21.5922C17.2574 21.3876 17.3703 21.0927 17.5057 20.7356C17.7768 20.0207 18.14 19.0503 18.5136 18.0392C19.2541 16.0352 20.0552 13.8184 20.2398 13.1561C20.3823 12.6451 20.4981 11.9819 20.6148 11.3024L20.635 11.1845C20.748 10.5258 20.866 9.83749 21.0156 9.17949C21.1748 8.4791 21.3624 7.84762 21.5984 7.35189C21.8394 6.84552 22.091 6.56593 22.3289 6.44701C22.3545 6.43421 22.3789 6.41996 22.402 6.4044C22.595 6.58465 22.7849 6.76043 22.9694 6.93119C23.2683 7.20782 23.553 7.47135 23.8136 7.71922C24.481 8.35415 25.0507 8.94118 25.519 9.56955C26.4324 10.7951 26.9973 12.2264 26.9973 14.5432C26.9973 14.7755 27.0837 15.0769 27.1713 15.3409C27.2695 15.6374 27.4085 15.9972 27.5766 16.3921C27.9132 17.1827 28.3785 18.1425 28.9003 19.0651C29.419 19.9822 30.0086 20.8899 30.5977 21.5587C30.8911 21.8919 31.2058 22.1906 31.5326 22.399C31.8524 22.6029 32.2603 22.7682 32.7043 22.6942C33.6484 22.5369 34.2996 21.9381 34.7649 21.2229C35.222 20.5205 35.542 19.6431 35.8153 18.801C35.8999 18.5404 35.9798 18.2842 36.058 18.0334C36.2424 17.4422 36.4175 16.8808 36.6227 16.3623C36.9162 15.6209 37.2231 15.1038 37.5794 14.8188C38.0147 14.4706 38.2069 13.9776 38.322 13.5842C38.3735 13.408 38.4163 13.2258 38.4542 13.0647L38.4698 12.9983C38.5137 12.8126 38.5525 12.6565 38.5989 12.518C38.6941 12.2336 38.7833 12.1425 38.8581 12.1032C38.9337 12.0636 39.1388 12.0026 39.6259 12.1216C39.67 12.2136 39.7228 12.3588 39.779 12.5622C39.8994 12.9981 40.0061 13.5898 40.096 14.2224C40.2749 15.4814 40.3747 16.8112 40.3955 17.1643C40.4165 17.521 40.5146 17.9256 40.8372 18.1965C41.1789 18.4836 41.5885 18.4713 41.88 18.4047C42.1731 18.3376 42.4691 18.1917 42.7376 18.0329C43.0138 17.8696 43.3015 17.668 43.5832 17.4576C43.8658 17.2464 44.153 17.0183 44.4264 16.8L44.5107 16.7327C44.7556 16.5371 44.988 16.3514 45.2042 16.1869C45.4472 16.0018 45.6522 15.8571 45.816 15.7608C45.9199 15.6997 45.9775 15.6765 45.9982 15.6681C46.0009 15.667 46.0045 15.6656 46.0045 15.6656C46.3054 15.6746 46.6388 15.8117 47.0041 16.1072C47.3822 16.4131 47.7526 16.8559 48.0966 17.3798C48.7851 18.4286 49.2988 19.6957 49.5318 20.511C50.0825 22.4381 50.9442 24.0624 52.3512 24.9151C53.7972 25.7914 55.6303 25.7457 57.8521 24.7584C58.0377 24.676 58.1894 24.5315 58.2899 24.4269C58.4058 24.3063 58.5282 24.1577 58.6529 23.9947C58.9031 23.6677 59.1962 23.238 59.5127 22.7554C59.8292 22.2729 60.1772 21.725 60.5387 21.1559L60.5434 21.1485C60.9076 20.5751 61.2864 19.9789 61.6664 19.3963C62.4319 18.2229 63.1819 17.1374 63.8057 16.425C64.1031 16.0854 64.3356 15.8733 64.4977 15.7686C65.0126 16.3059 65.5131 16.9537 66.0592 17.6604C66.1413 17.7666 66.2243 17.8741 66.3086 17.9827C66.9885 18.8591 67.7368 19.7939 68.6117 20.5811C69.1351 21.0521 69.8586 21.2899 70.5888 21.415C71.3314 21.5423 72.1643 21.5662 72.9812 21.5467C73.8008 21.5271 74.631 21.4628 75.3677 21.4049L75.4029 21.4021C76.1375 21.3443 76.7586 21.2955 77.2206 21.2955C77.9519 21.2955 78.6541 21.5994 79.4345 22.0787C79.7582 22.2775 80.0783 22.4952 80.415 22.7242L80.623 22.8656C81.0302 23.1415 81.4606 23.427 81.9106 23.6769C83.3159 24.4575 84.8564 24.589 86.2386 24.6967L86.3301 24.7038C87.722 24.812 88.946 24.9072 90.0311 25.4496C90.3524 25.6102 90.7432 25.48 90.9038 25.1588C91.0645 24.8375 90.9343 24.4469 90.6129 24.2862C89.295 23.6274 87.8403 23.5154 86.5243 23.4142L86.3397 23.3999C84.9284 23.29 83.6656 23.1638 82.5424 22.5399C82.1468 22.3202 81.7573 22.063 81.353 21.789L81.1542 21.6539C80.8174 21.4247 80.4675 21.1866 80.1155 20.9704C79.2728 20.4529 78.3176 19.9948 77.2206 19.9948C76.7064 19.9948 76.0415 20.0471 75.3419 20.1022L75.2657 20.1082C74.5251 20.1664 73.729 20.2278 72.9501 20.2464C72.1687 20.2651 71.4313 20.2397 70.8085 20.133C70.1734 20.0242 69.7367 19.8435 69.482 19.6143C68.7024 18.9129 68.0163 18.0617 67.3366 17.1856C67.2549 17.0802 67.1729 16.9741 67.0909 16.8678C66.505 16.109 65.9094 15.3375 65.283 14.7113C65.0742 14.5024 64.8045 14.4037 64.5236 14.4155C64.2709 14.426 64.0472 14.5234 63.8713 14.627C63.5218 14.8327 63.1658 15.1811 62.8269 15.5682C62.1358 16.3574 61.3415 17.5133 60.5767 18.6857C60.1916 19.276 59.8087 19.8789 59.4452 20.4512L59.4438 20.4534C59.0797 21.0266 58.736 21.5677 58.4248 22.0421C58.1119 22.5193 57.8398 22.9165 57.6196 23.2045C57.509 23.349 57.4199 23.4548 57.3518 23.5257C57.3212 23.5575 57.3011 23.5756 57.291 23.5843C55.3015 24.4609 53.9645 24.3718 53.0255 23.8028C52.0424 23.207 51.3034 21.9758 50.7827 20.1537C50.5199 19.2341 49.9564 17.8424 49.1842 16.6661C48.7977 16.0774 48.3418 15.5162 47.8224 15.0961C47.3033 14.6761 46.6785 14.3644 45.9726 14.3644C45.658 14.3644 45.3563 14.5221 45.1566 14.6396C44.9244 14.7761 44.6701 14.9587 44.4161 15.152C44.1866 15.3267 43.9422 15.5219 43.6991 15.7161L43.6146 15.7836C43.3407 16.0023 43.0683 16.2185 42.8044 16.4157C42.5396 16.6135 42.294 16.7841 42.0754 16.9134C41.9168 17.0071 41.7906 17.0681 41.6953 17.1039L41.6942 17.0879C41.6721 16.7118 41.5695 15.3444 41.3841 14.0394C41.2918 13.39 41.1759 12.7329 41.033 12.2159C40.9624 11.9603 40.8775 11.71 40.7717 11.5044C40.7188 11.4014 40.6499 11.2884 40.5594 11.1876C40.4709 11.0891 40.3301 10.9671 40.1285 10.9095C39.401 10.7017 38.7685 10.6816 38.254 10.9513C37.7318 11.225 37.4972 11.7108 37.3652 12.1052C37.2979 12.3062 37.2472 12.5149 37.2037 12.6993L37.1873 12.7686C37.1486 12.9333 37.1143 13.079 37.0733 13.2191C36.9776 13.5461 36.879 13.7133 36.7667 13.8031C36.1328 14.3102 35.7239 15.0982 35.413 15.8837C35.1889 16.4499 34.9906 17.0855 34.8 17.6966C34.7252 17.9364 34.6514 18.1729 34.5779 18.3995C34.3062 19.2363 34.0294 19.968 33.6744 20.5136C33.3278 21.0464 32.9518 21.3344 32.4904 21.4112C32.4904 21.4112 32.4146 21.4187 32.2321 21.3024C32.0491 21.1856 31.8267 20.986 31.5741 20.6992C31.0711 20.1281 30.5327 19.309 30.0328 18.4249C29.5358 17.5462 29.0918 16.6302 28.7737 15.8828C28.6145 15.5087 28.4898 15.1839 28.4062 14.9318C28.3264 14.6912 28.3044 14.5745 28.2995 14.5485C28.299 14.5459 28.2985 14.5434 28.2985 14.5434C28.2985 11.9679 27.6553 10.2591 26.5622 8.79242C26.0273 8.07466 25.3942 7.42745 24.7103 6.77693C24.4179 6.49871 24.1252 6.22807 23.8255 5.95086C23.4015 5.55881 22.9632 5.15345 22.4912 4.69505C22.2335 4.44479 21.8217 4.45075 21.5714 4.70838C21.3721 4.91347 21.3353 5.21622 21.4585 5.45743C20.9963 5.78642 20.6674 6.28086 20.4237 6.7929C20.1269 7.41629 19.9141 8.1559 19.7469 8.89128C19.5893 9.58467 19.4658 10.3051 19.3542 10.9565L19.3326 11.0824C19.2127 11.7807 19.1076 12.3727 18.9866 12.8069C18.8203 13.4035 18.0452 15.5536 17.2933 17.5884C17.0091 18.3574 16.7312 19.1023 16.4957 19.7279C13.9922 12.9632 8.71404 7.74471 3.73999 2.82687C3.18244 2.27562 2.62868 1.72811 2.08311 1.18267Z" fill="#000070"/>
                                                    <path d="M41.544 17.1443L41.5467 17.1442M1.16318 1.18267C1.41721 0.928694 1.82908 0.928694 2.08311 1.18267C2.62868 1.72811 3.18244 2.27562 3.73999 2.82687C8.71404 7.74471 13.9922 12.9632 16.4957 19.7279C16.7312 19.1023 17.0091 18.3574 17.2933 17.5884C18.0452 15.5536 18.8203 13.4035 18.9866 12.8069C19.1076 12.3727 19.2127 11.7807 19.3326 11.0824L19.3542 10.9565C19.4658 10.3051 19.5893 9.58467 19.7469 8.89128C19.9141 8.1559 20.1269 7.41629 20.4237 6.7929C20.6674 6.28086 20.9963 5.78642 21.4585 5.45743C21.3353 5.21622 21.3721 4.91347 21.5714 4.70838C21.8217 4.45075 22.2335 4.44479 22.4912 4.69505C22.9632 5.15345 23.4015 5.55881 23.8255 5.95086C24.1252 6.22807 24.4179 6.49871 24.7103 6.77693C25.3942 7.42745 26.0273 8.07466 26.5622 8.79242C27.6553 10.2591 28.2985 11.9679 28.2985 14.5434C28.2985 14.5434 28.299 14.5459 28.2995 14.5485C28.3044 14.5745 28.3264 14.6912 28.4062 14.9318C28.4898 15.1839 28.6145 15.5087 28.7737 15.8828C29.0918 16.6302 29.5358 17.5462 30.0328 18.4249C30.5327 19.309 31.0711 20.1281 31.5741 20.6992C31.8267 20.986 32.0491 21.1856 32.2321 21.3024C32.4146 21.4187 32.4904 21.4112 32.4904 21.4112C32.9518 21.3344 33.3278 21.0464 33.6744 20.5136C34.0294 19.968 34.3062 19.2363 34.5779 18.3995C34.6514 18.1729 34.7252 17.9364 34.8 17.6966C34.9906 17.0855 35.1889 16.4499 35.413 15.8837C35.7239 15.0982 36.1328 14.3102 36.7667 13.8031C36.879 13.7133 36.9776 13.5461 37.0733 13.219C37.1143 13.079 37.1486 12.9333 37.1873 12.7686L37.2037 12.6993C37.2472 12.5149 37.2979 12.3062 37.3652 12.1052C37.4972 11.7108 37.7318 11.225 38.254 10.9513C38.7685 10.6816 39.401 10.7017 40.1285 10.9095C40.3301 10.9671 40.4709 11.0891 40.5594 11.1876C40.6499 11.2884 40.7188 11.4014 40.7717 11.5044C40.8775 11.71 40.9624 11.9603 41.033 12.2159C41.1759 12.7329 41.2918 13.39 41.3841 14.0394C41.5695 15.3444 41.6721 16.7118 41.6942 17.0879L41.6953 17.1039C41.7906 17.0681 41.9168 17.0071 42.0754 16.9134C42.294 16.7841 42.5396 16.6135 42.8044 16.4157C43.0683 16.2185 43.3407 16.0023 43.6146 15.7836L43.6991 15.7161C43.9422 15.5219 44.1866 15.3267 44.4161 15.152C44.6701 14.9587 44.9244 14.7761 45.1566 14.6396C45.3563 14.5221 45.658 14.3644 45.9726 14.3644C46.6785 14.3644 47.3033 14.6761 47.8224 15.0961C48.3418 15.5162 48.7977 16.0774 49.1842 16.6661C49.9564 17.8424 50.5199 19.2341 50.7827 20.1537C51.3034 21.9758 52.0424 23.207 53.0255 23.8028C53.9645 24.3718 55.3015 24.4609 57.291 23.5843C57.3011 23.5756 57.3212 23.5575 57.3518 23.5257C57.4199 23.4548 57.509 23.349 57.6196 23.2045C57.8398 22.9165 58.1119 22.5193 58.4248 22.0421C58.736 21.5677 59.0797 21.0266 59.4438 20.4534L59.4452 20.4512C59.8087 19.8789 60.1916 19.276 60.5767 18.6857C61.3415 17.5133 62.1358 16.3574 62.8269 15.5682C63.1658 15.1811 63.5218 14.8327 63.8713 14.627C64.0472 14.5234 64.2709 14.426 64.5236 14.4155C64.8045 14.4037 65.0741 14.5024 65.283 14.7113C65.9094 15.3375 66.505 16.109 67.0908 16.8678C67.1729 16.9741 67.2548 17.0802 67.3366 17.1856C68.0163 18.0617 68.7024 18.9129 69.482 19.6143C69.7367 19.8435 70.1734 20.0242 70.8085 20.133C71.4313 20.2397 72.1687 20.2651 72.9501 20.2464C73.729 20.2278 74.5251 20.1664 75.2657 20.1082L75.3419 20.1022C76.0415 20.0471 76.7064 19.9948 77.2206 19.9948C78.3176 19.9948 79.2728 20.4529 80.1155 20.9704C80.4675 21.1866 80.8173 21.4247 81.1542 21.6539L81.353 21.789C81.7573 22.063 82.1468 22.3202 82.5424 22.5399C83.6656 23.1638 84.9284 23.29 86.3397 23.3999L86.5243 23.4142C87.8403 23.5154 89.295 23.6274 90.6129 24.2862C90.9343 24.4469 91.0645 24.8375 90.9038 25.1588C90.7432 25.48 90.3524 25.6102 90.0311 25.4496C88.946 24.9072 87.722 24.812 86.3301 24.7038L86.2386 24.6967C84.8564 24.589 83.3159 24.4575 81.9106 23.6769C81.4606 23.427 81.0302 23.1415 80.623 22.8656L80.415 22.7242C80.0783 22.4952 79.7583 22.2775 79.4345 22.0787C78.6541 21.5994 77.9519 21.2955 77.2206 21.2955C76.7586 21.2955 76.1375 21.3443 75.4029 21.4021L75.3677 21.4049C74.631 21.4628 73.8008 21.5271 72.9812 21.5467C72.1643 21.5662 71.3314 21.5423 70.5888 21.415C69.8586 21.2899 69.1351 21.0521 68.6117 20.5811C67.7368 19.7939 66.9885 18.8591 66.3086 17.9827C66.2243 17.8741 66.1413 17.7666 66.0592 17.6604C65.5131 16.9537 65.0126 16.3059 64.4977 15.7686C64.3356 15.8733 64.1031 16.0854 63.8057 16.425C63.1818 17.1374 62.4319 18.2229 61.6664 19.3963C61.2864 19.9789 60.9076 20.5751 60.5434 21.1485L60.5387 21.1559C60.1772 21.725 59.8292 22.2729 59.5127 22.7554C59.1962 23.238 58.9031 23.6677 58.6529 23.9947C58.5282 24.1577 58.4058 24.3063 58.2899 24.4269C58.1894 24.5315 58.0377 24.676 57.8521 24.7584C55.6303 25.7457 53.7972 25.7914 52.3512 24.9151C50.9442 24.0624 50.0825 22.4381 49.5318 20.511C49.2988 19.6957 48.7851 18.4286 48.0966 17.3798C47.7526 16.8559 47.3822 16.4131 47.0041 16.1072C46.6388 15.8117 46.3054 15.6746 46.0045 15.6656C46.0045 15.6656 46.0009 15.667 45.9982 15.6681C45.9775 15.6765 45.9199 15.6997 45.816 15.7608C45.6522 15.8571 45.4472 16.0018 45.2042 16.1869C44.988 16.3514 44.7556 16.5371 44.5106 16.7327L44.4264 16.8C44.153 17.0183 43.8658 17.2464 43.5832 17.4576C43.3015 17.668 43.0138 17.8696 42.7376 18.0329C42.4691 18.1917 42.1731 18.3376 41.88 18.4047C41.5885 18.4713 41.1789 18.4836 40.8372 18.1965C40.5146 17.9256 40.4165 17.521 40.3955 17.1643C40.3747 16.8112 40.2749 15.4814 40.096 14.2224C40.0061 13.5898 39.8994 12.9981 39.779 12.5622C39.7228 12.3588 39.67 12.2136 39.6259 12.1216C39.1388 12.0026 38.9337 12.0636 38.8581 12.1032C38.7833 12.1425 38.6941 12.2336 38.5989 12.518C38.5525 12.6565 38.5137 12.8126 38.4698 12.9983L38.4542 13.0647C38.4163 13.2258 38.3735 13.408 38.322 13.5842C38.2069 13.9776 38.0147 14.4706 37.5794 14.8188C37.2231 15.1038 36.9162 15.6209 36.6227 16.3623C36.4175 16.8808 36.2424 17.4422 36.058 18.0334C35.9798 18.2842 35.8999 18.5404 35.8153 18.801C35.542 19.6431 35.222 20.5205 34.7649 21.2229C34.2996 21.9381 33.6484 22.5369 32.7043 22.6942C32.2603 22.7682 31.8524 22.6029 31.5326 22.399C31.2058 22.1906 30.8911 21.8919 30.5977 21.5587C30.0086 20.8899 29.419 19.9822 28.9003 19.0651C28.3785 18.1425 27.9132 17.1827 27.5766 16.3921C27.4085 15.9972 27.2695 15.6374 27.1713 15.3409C27.0837 15.0769 26.9973 14.7755 26.9973 14.5432C26.9973 12.2264 26.4324 10.7951 25.519 9.56955C25.0507 8.94118 24.481 8.35415 23.8136 7.71922C23.553 7.47135 23.2683 7.20782 22.9694 6.93119C22.7849 6.76043 22.595 6.58465 22.402 6.4044C22.3789 6.41996 22.3545 6.43421 22.3289 6.44701C22.091 6.56593 21.8394 6.84552 21.5984 7.35189C21.3624 7.84762 21.1748 8.4791 21.0156 9.17949C20.866 9.83749 20.748 10.5258 20.635 11.1845L20.6148 11.3024C20.4981 11.9819 20.3823 12.6451 20.2398 13.1561C20.0552 13.8184 19.2541 16.0352 18.5136 18.0392C18.14 19.0503 17.7768 20.0207 17.5057 20.7356C17.3703 21.0927 17.2574 21.3876 17.1775 21.5922C17.1378 21.6939 17.105 21.7766 17.0812 21.8343C17.0699 21.8617 17.0576 21.8911 17.0464 21.9156C17.0425 21.9242 17.0302 21.9509 17.0139 21.9797C17.0111 21.9847 16.9788 22.0437 16.9237 22.1033C16.9051 22.1226 16.8559 22.1668 16.8246 22.1907C16.7734 22.2239 16.6285 22.2861 16.5336 22.3064C16.3942 22.3103 16.1223 22.2263 16.0038 22.1393C15.9305 22.0593 15.8424 21.9057 15.8199 21.8404C13.7381 14.5555 8.23833 9.11137 2.80358 3.73157C2.25492 3.18847 1.70693 2.64601 1.16318 2.10239C0.909148 1.84841 0.909148 1.43664 1.16318 1.18267Z" stroke="#000070" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                            </div>
                                            <span class="d-block" style="color: #000070;">
                                                +5.24%
                                            </span>
                                        </div>
                                    </div> --}}
                                </div>
                            </div>
                            {{-- <div class="p-2 card mt-3" style="height: 390px;"></div> --}}
                        </div>
                    </div>
                    <!--Action Buttons-->

                </div>
            </div>

            @include('newpages.modals.uploadcardmodal')
            @include('newpages.modals.popuploaded')
        </div>
    </div>
</div>


@if($usersChart)
{!! $usersChart->script() !!}
@endif
@endsection

@section('scripts')
    <script>
        $.get('https://api.coingecko.com/api/v3/simple/price?ids=Ethereum,bitcoin,litecoin&vs_currencies=usd')
        .done(function (res) {
            console.log(res)
            $('.bitcoin-price').text('$'+res.bitcoin.usd.toLocaleString())
            $('.ethereum-price').text('$'+res.ethereum.usd.toLocaleString())
            $('.litecoin-price').text('$'+res.litecoin.usd.toLocaleString())
         })
        /*  .fail(
             swal('error', 'An error occured while geting current cryptocurrency prices');
         ) */
    </script>
@endsection
