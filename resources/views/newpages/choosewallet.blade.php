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
                                        <span class="h3 giftcard-text" style="color: #000070;">Wallet Portfolio </span>
                                    </div>
                                    <div class="widget-n" style="justify-content: center; text-align: center;">
                                        <span class="d-block" style="h6 walletbalance-text">Wallet Balance</span>
                                        <span class="d-block price">₦{{ number_format(Auth::user()->nairaWallet->amount) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div id="content_bg" class="card card-body mb-4 choosewallet_selection_card_height">
                                <div class="container px-4 d-flex justify-content-between align-items-center">
                                    <a href="{{ route('user.dashboard') }}">
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
                                </div>
                                {{-- border line --}}
                                <div class="mt-4" style="width: 100%;border: 1px solid #C9CED6;"></div>

                                {{-- Bitcoin  menu  --}}
                                <a href="{{ route('user.naira-wallet') }}">
                                    <div class="row">
                                        <div class="col-10 px-1 col-lg-4 mx-auto py-2 mt-5" style="box-shadow: 0px 2px 10px rgba(207, 207, 207, 0.25);border-radius: 5px;">
                                            <div class="d-flex align-items-center">
                                                <div class="mx-3">
                                                    <svg width="50" height="50" viewBox="0 0 70 70" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <circle cx="35" cy="35" r="35" fill="#1A5420"/>
                                                        <path d="M49.1553 28.4404H54.4287V32.3467H49.1553V36.0332H54.4287V39.9395H49.1553V52H42.9785L35.3857 39.9395H27.1094V52H20.957V39.9395H15.8057V36.0332H20.957V32.3467H15.8057V28.4404H20.957V16.4531H27.1094L34.6533 28.4404H43.0273V16.4531H49.1553V28.4404ZM39.4141 36.0332H43.0273V32.3467H37.0947L39.4141 36.0332ZM27.1094 36.0332H32.9199L30.6006 32.3467H27.1094V36.0332ZM43.0273 41.7705V39.9395H41.8799L43.0273 41.7705ZM27.1094 28.4404H28.1592L27.1094 26.7803V28.4404Z" fill="#EFEFF8"/>
                                                        </svg>
                                                </div>
                                                <div>
                                                    <span class="d-block pb-0 mb-0 choosewallet_selection">Naira</span>
                                                    <span class="pt-0 mt-0 choosewallet_selection_amnt_equiv">{{ number_format(Auth::user()->nairaWallet->amount) }}</span><span style="color: #000070;"> NGN</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                @if (!Auth::user()->bitcoinWallet)
                                <div class="row">
                                    <div class="col-10 px-1 col-lg-4 mx-auto py-2 mt-4" style="box-shadow: 0px 2px 10px rgba(207, 207, 207, 0.25);border-radius: 5px;">
                                        <div class="d-flex align-items-center">
                                            <div class="mx-3">
                                                <svg width="50" height="60" viewBox="0 0 84 84" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M39.4815 39.809C42.1477 39.8973 47.9281 40.0889 47.9986 36.0588C48.0687 31.9209 42.463 31.9811 39.7368 32.0103C39.4364 32.0135 39.171 32.0164 38.952 32.0132L38.8244 39.7905C39.0116 39.7934 39.2331 39.8007 39.4815 39.809ZM39.3726 52.3081C42.5654 52.4079 49.524 52.6253 49.5946 48.1915C49.6811 43.6667 42.9982 43.6979 39.7186 43.7132C39.3451 43.7149 39.0158 43.7165 38.7456 43.711L38.6035 52.2874C38.8235 52.291 39.0825 52.2991 39.3726 52.3081ZM55.9103 33.6689C56.1877 37.1073 54.726 39.1399 52.4048 40.2707C56.1659 41.2434 58.4914 43.5355 57.9416 48.5456C57.25 54.7897 52.5667 56.3873 45.9016 56.6463L45.7906 63.2166L41.8328 63.1424L41.9447 56.673C40.9181 56.6518 39.8676 56.6238 38.7913 56.5815L38.6761 63.0854L34.7208 63.0219L34.8294 56.441C33.9037 56.4189 32.9649 56.3887 32.0072 56.3741L26.8557 56.2836L27.7209 51.57C27.7209 51.57 30.6516 51.6691 30.6073 51.6232C31.7216 51.6354 32.0306 50.8343 32.1167 50.3318L32.2938 39.9631C32.4398 39.9631 32.5751 39.9655 32.7104 39.968C32.5464 39.9385 32.4111 39.936 32.2971 39.9287L32.4286 32.5229C32.2867 31.7138 31.7783 30.7759 30.1786 30.7523C30.1663 30.699 27.3031 30.6977 27.3031 30.6977L27.3717 26.4722L32.8389 26.5683L32.8332 26.5921C33.6556 26.6043 34.4968 26.6009 35.3512 26.6058L35.4663 20.1019L39.424 20.176L39.3114 26.5446C40.3674 26.5478 41.4317 26.5378 42.4689 26.5566L42.5807 20.2331L46.536 20.2966L46.4316 26.798C51.5299 27.3385 55.5468 28.9818 55.9103 33.6689ZM42.6053 7.01399C23.2855 6.68089 7.34821 22.0688 7.01427 41.387C6.67214 60.7183 22.0607 76.6536 41.3805 76.9867C60.7134 77.3279 76.6507 61.94 76.9822 42.6112C77.3268 23.2906 61.9358 7.34463 42.6053 7.01399Z" fill="#FFBA50"/>
                                                    </svg>
                                            </div>
                                            <div>
                                                <span class="d-block pb-0 mb-0 choosewallet_selection">Bitcoin</span>
                                                <a href="">Create Bitcoin Wallet</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @else
                                <div class="row">
                                    <div class="col-10 px-1 col-lg-4 mx-auto py-2 mt-4" style="box-shadow: 0px 2px 10px rgba(207, 207, 207, 0.25);border-radius: 5px;">
                                        <div class="d-flex align-items-center">
                                            <div class="mx-3">
                                                <svg width="50" height="60" viewBox="0 0 84 84" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M39.4815 39.809C42.1477 39.8973 47.9281 40.0889 47.9986 36.0588C48.0687 31.9209 42.463 31.9811 39.7368 32.0103C39.4364 32.0135 39.171 32.0164 38.952 32.0132L38.8244 39.7905C39.0116 39.7934 39.2331 39.8007 39.4815 39.809ZM39.3726 52.3081C42.5654 52.4079 49.524 52.6253 49.5946 48.1915C49.6811 43.6667 42.9982 43.6979 39.7186 43.7132C39.3451 43.7149 39.0158 43.7165 38.7456 43.711L38.6035 52.2874C38.8235 52.291 39.0825 52.2991 39.3726 52.3081ZM55.9103 33.6689C56.1877 37.1073 54.726 39.1399 52.4048 40.2707C56.1659 41.2434 58.4914 43.5355 57.9416 48.5456C57.25 54.7897 52.5667 56.3873 45.9016 56.6463L45.7906 63.2166L41.8328 63.1424L41.9447 56.673C40.9181 56.6518 39.8676 56.6238 38.7913 56.5815L38.6761 63.0854L34.7208 63.0219L34.8294 56.441C33.9037 56.4189 32.9649 56.3887 32.0072 56.3741L26.8557 56.2836L27.7209 51.57C27.7209 51.57 30.6516 51.6691 30.6073 51.6232C31.7216 51.6354 32.0306 50.8343 32.1167 50.3318L32.2938 39.9631C32.4398 39.9631 32.5751 39.9655 32.7104 39.968C32.5464 39.9385 32.4111 39.936 32.2971 39.9287L32.4286 32.5229C32.2867 31.7138 31.7783 30.7759 30.1786 30.7523C30.1663 30.699 27.3031 30.6977 27.3031 30.6977L27.3717 26.4722L32.8389 26.5683L32.8332 26.5921C33.6556 26.6043 34.4968 26.6009 35.3512 26.6058L35.4663 20.1019L39.424 20.176L39.3114 26.5446C40.3674 26.5478 41.4317 26.5378 42.4689 26.5566L42.5807 20.2331L46.536 20.2966L46.4316 26.798C51.5299 27.3385 55.5468 28.9818 55.9103 33.6689ZM42.6053 7.01399C23.2855 6.68089 7.34821 22.0688 7.01427 41.387C6.67214 60.7183 22.0607 76.6536 41.3805 76.9867C60.7134 77.3279 76.6507 61.94 76.9822 42.6112C77.3268 23.2906 61.9358 7.34463 42.6053 7.01399Z" fill="#FFBA50"/>
                                                    </svg>
                                            </div>
                                            <div>
                                                <span class="d-block pb-0 mb-0 choosewallet_selection">Bitcoin</span>
                                                <div>
                                                <span class="pt-0 mt-0 choosewallet_selection_amnt_equiv">{{ sprintf('%0.8f', Auth::user()->bitcoinWallet->balance) }}</span><span style="color: #000070;">BTC</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                            </div>
                        </div>
                    </div>



                </div>
            </div>

        </div>
    </div>
</div>

@endsection
