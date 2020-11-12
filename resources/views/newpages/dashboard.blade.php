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
                        <div class="col-sm-12 mb-3">
                            <div class="card card-body">
                                <div class="welcomeText" style="color: #000070;font-weight: 500;font-size: 18px;">
                                    {{$item->title}}</div>
                                <div class="welcomeText" style="color: #000070;font-size: 16px;">{{$item->body}} </div>
                                <span style="position: relative;left:97%;top:-70px;cursor: pointer;">
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
                        {{-- <div class="col-sm-4 mb-3">
                            <div class="card card-body" style="height: 118px;">
                                <div class="welcomeText" style="color: #000070;font-weight: 500;font-size: 24px;">Hi, Buhari,</div>
                            </div>
                        </div> --}}
                        <div class="col-sm-4">
                            <div class="card mb-3" style="margin: 0px">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-6 col-8 col-md-8">
                                            <h5 class="card-title">{{ number_format($p) }}</h5>
                                            <p class="card-text">Pending Transactions</p>
                                        </div>
                                        <div class="col-sm-3 col-4 col-md-4" style="justify-content: right">
                                            <span style="width: 800px;">
                                                <svg width="90" height="90" viewBox="0 0 120 120" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M38.4408 116.007C60.5908 109.61 79.1175 116.693 89.7908 113.61C100.464 110.527 121.111 97.9165 106.931 48.8098C92.7508 -0.296839 60.5175 1.20983 48.7742 4.59983C-6.20249 20.4765 -0.78916 127.337 38.4408 116.007Z"
                                                        fill="#EFEFEF" />
                                                    <path
                                                        d="M19.166 20.4063C16.4093 20.4063 14.166 18.1629 14.166 15.4063C14.166 12.6496 16.4093 10.4062 19.166 10.4062C21.9227 10.4062 24.166 12.6496 24.166 15.4063C24.166 18.1629 21.9227 20.4063 19.166 20.4063ZM19.166 13.7396C18.2493 13.7396 17.4993 14.4863 17.4993 15.4063C17.4993 16.3263 18.2493 17.0729 19.166 17.0729C20.0827 17.0729 20.8327 16.3263 20.8327 15.4063C20.8327 14.4863 20.0827 13.7396 19.166 13.7396Z"
                                                        fill="#A4AFC1" />
                                                    <path
                                                        d="M89.9629 12.0391L92.3199 9.68204L97.034 14.3961L94.6769 16.7531L89.9629 12.0391Z"
                                                        fill="#A4AFC1" />
                                                    <path
                                                        d="M103.51 25.5898L105.867 23.2328L110.581 27.9469L108.224 30.3039L103.51 25.5898Z"
                                                        fill="#A4AFC1" />
                                                    <path
                                                        d="M102.932 14.4121L107.646 9.69807L110.003 12.0551L105.289 16.7691L102.932 14.4121Z"
                                                        fill="#A4AFC1" />
                                                    <path
                                                        d="M90.8333 34.0664V60.8331C90.8333 64.4997 87.8333 67.4997 84.1667 67.4997H27.5C24.7333 67.4997 22.5 65.2664 22.5 62.4997V34.0664L53.8667 49.5997C55.6 50.4664 57.7333 50.4664 59.4667 49.5997L90.8333 34.0664Z"
                                                        fill="#F3F3F1" />
                                                    <path
                                                        d="M90.8333 30.832V34.0654L59.4667 49.5987C57.7333 50.4654 55.6 50.4654 53.8667 49.5987L22.5 34.0654V30.832C22.5 28.0654 24.7333 25.832 27.5 25.832H85.8333C88.6 25.832 90.8333 28.0654 90.8333 30.832Z"
                                                        fill="#FFD088" />
                                                    <path
                                                        d="M30 62.4997V37.7797L22.5 34.0664V62.4997C22.5 65.2664 24.7333 67.4997 27.5 67.4997H35C32.2333 67.4997 30 65.2664 30 62.4997Z"
                                                        fill="#D5DBE1" />
                                                    <path
                                                        d="M30 32.4354V30.832C30 28.0654 32.2333 25.832 35 25.832H27.5C24.7333 25.832 22.5 28.0654 22.5 30.832V34.0654L53.8667 49.5987C55.6 50.4654 57.7333 50.4654 59.4667 49.5987L60.4167 47.4987L30 32.4354Z"
                                                        fill="#FFB800" />
                                                    <path
                                                        d="M78.3327 97.4994C88.9181 97.4994 97.4994 88.9182 97.4994 78.3327C97.4994 67.7472 88.9181 59.166 78.3327 59.166C67.7472 59.166 59.166 67.7472 59.166 78.3327C59.166 88.9182 67.7472 97.4994 78.3327 97.4994Z"
                                                        fill="#FFD088" />
                                                    <path
                                                        d="M66.666 78.3327C66.666 69.0327 73.2927 61.2827 82.0827 59.5393C80.8694 59.2993 79.616 59.166 78.3327 59.166C67.746 59.166 59.166 67.746 59.166 78.3327C59.166 88.9194 67.746 97.4994 78.3327 97.4994C79.616 97.4994 80.8694 97.3694 82.0827 97.126C73.2927 95.3827 66.666 87.6327 66.666 78.3327Z"
                                                        fill="#FFB800" />
                                                    <path
                                                        d="M87.5007 80.8327H78.334C76.954 80.8327 75.834 79.7127 75.834 78.3327V69.166H80.834V75.8327H87.5007V80.8327Z"
                                                        fill="black" />
                                                    <path
                                                        d="M78.3327 100.001C66.386 100.001 56.666 90.2813 56.666 78.3346C56.666 66.388 66.386 56.668 78.3327 56.668C90.2794 56.668 99.9993 66.388 99.9993 78.3346C99.9993 90.2813 90.2794 100.001 78.3327 100.001ZM78.3327 61.668C69.1427 61.668 61.666 69.1446 61.666 78.3346C61.666 87.5246 69.1427 95.0013 78.3327 95.0013C87.5227 95.0013 94.9993 87.5246 94.9993 78.3346C94.9993 69.1446 87.5227 61.668 78.3327 61.668Z"
                                                        fill="black" />
                                                    <path
                                                        d="M50.8333 70.0006H27.5C23.3667 70.0006 20 66.6373 20 62.5006V30.834C20 26.6973 23.3667 23.334 27.5 23.334H85.8333C89.9667 23.334 93.3333 26.6973 93.3333 30.834V50.834H88.3333V30.834C88.3333 29.454 87.21 28.334 85.8333 28.334H27.5C26.1233 28.334 25 29.454 25 30.834V62.5006C25 63.8806 26.1233 65.0006 27.5 65.0006H50.8333V70.0006Z"
                                                        fill="black" />
                                                    <path
                                                        d="M56.6706 52.7581C55.324 52.7581 53.974 52.4514 52.7473 51.8348L21.3906 36.3081L23.6106 31.8281L54.9773 47.3615C56.014 47.8781 57.3306 47.8781 58.3473 47.3648L89.724 31.8281L91.944 36.3081L60.5773 51.8414C59.3573 52.4514 58.014 52.7581 56.6706 52.7581Z"
                                                        fill="black" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-6 col-8 col-md-8">
                                            <h5 class="card-title">{{ number_format($s) }}</h5>
                                            <p class="card-text">Successful Transactions</p>
                                        </div>
                                        <div class="col-sm-2 col-4 col-md-4">
                                            <span>
                                                <svg width="90" height="90" viewBox="0 0 120 120" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M91.3905 13.3239C68.3405 13.7539 52.2838 2.10726 41.1772 2.31726C30.0705 2.52726 6.85718 9.35059 7.81384 60.4506C8.77051 111.551 40.2938 118.464 52.5138 118.237C109.724 117.167 132.217 12.5606 91.3905 13.3239Z"
                                                        fill="#EFEFEF" />
                                                    <path
                                                        d="M56.6667 97.5013C75.5364 97.5013 90.8333 82.2044 90.8333 63.3346C90.8333 44.4649 75.5364 29.168 56.6667 29.168C37.7969 29.168 22.5 44.4649 22.5 63.3346C22.5 82.2044 37.7969 97.5013 56.6667 97.5013Z"
                                                        fill="#2FDF84" />
                                                    <path
                                                        d="M29.3333 63.3346C29.3333 45.618 42.82 31.0546 60.0833 29.3413C58.96 29.228 57.82 29.168 56.6667 29.168C37.7967 29.168 22.5 44.4646 22.5 63.3346C22.5 82.2046 37.7967 97.5013 56.6667 97.5013C57.82 97.5013 58.96 97.4413 60.0833 97.328C42.82 95.6146 29.3333 81.0513 29.3333 63.3346Z"
                                                        fill="#00B871" />
                                                    <path
                                                        d="M95.9629 100.166L98.3199 97.809L103.034 102.523L100.677 104.88L95.9629 100.166Z"
                                                        fill="#A4AFC1" />
                                                    <path
                                                        d="M96.5547 88.9434L101.269 84.2293L103.626 86.5863L98.9117 91.3004L96.5547 88.9434Z"
                                                        fill="#A4AFC1" />
                                                    <path
                                                        d="M83.0039 102.496L87.718 97.7821L90.075 100.139L85.3609 104.853L83.0039 102.496Z"
                                                        fill="#A4AFC1" />
                                                    <path
                                                        d="M90 23.3906C87.2433 23.3906 85 21.1473 85 18.3906C85 15.634 87.2433 13.3906 90 13.3906C92.7567 13.3906 95 15.634 95 18.3906C95 21.1473 92.7567 23.3906 90 23.3906ZM90 16.724C89.08 16.724 88.3333 17.474 88.3333 18.3906C88.3333 19.3073 89.08 20.0573 90 20.0573C90.92 20.0573 91.6667 19.3073 91.6667 18.3906C91.6667 17.474 90.92 16.724 90 16.724Z"
                                                        fill="#A4AFC1" />
                                                    <path
                                                        d="M56.6667 99.9993C36.45 99.9993 20 83.5493 20 63.3327C20 43.116 36.45 26.666 56.6667 26.666C63.7 26.666 70.54 28.6727 76.4467 32.4627L73.7433 36.6693C68.6467 33.3993 62.74 31.666 56.6667 31.666C39.2067 31.666 25 45.8727 25 63.3327C25 80.7927 39.2067 94.9993 56.6667 94.9993C74.1267 94.9993 88.3333 80.7927 88.3333 63.3327C88.3333 62.2893 88.2833 61.256 88.1867 60.2393L93.1633 59.756C93.2767 60.936 93.3333 62.126 93.3333 63.3327C93.3333 83.5493 76.8833 99.9993 56.6667 99.9993Z"
                                                        fill="black" />
                                                    <path
                                                        d="M60.8331 71.6658C60.1931 71.6658 59.5531 71.4224 59.0664 70.9324L44.0664 55.9324L47.6031 52.3958L60.8364 65.6291L95.7331 30.7324L99.2697 34.2691L62.6031 70.9358C62.1131 71.4224 61.4731 71.6658 60.8331 71.6658Z"
                                                        fill="black" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-6 col-8 col-md-8">
                                            <h5 class="card-title">{{ number_format($d) }}</h5>
                                            <p class="card-text">Declined Transactions</p>
                                        </div>
                                        <div class="col-sm-2 col-4 col-md-4">
                                            <span>
                                                <svg width="90" height="90" viewBox="0 0 120 120" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M91.3905 13.3239C68.3405 13.7539 52.2838 2.10726 41.1772 2.31726C30.0705 2.52726 6.85718 9.35059 7.81384 60.4506C8.77051 111.551 40.2938 118.464 52.5138 118.237C109.724 117.167 132.217 12.5606 91.3905 13.3239Z"
                                                        fill="#EFEFEF" />
                                                    <path
                                                        d="M59.3092 96.9036C78.179 96.9036 93.4759 81.6067 93.4759 62.737C93.4759 43.8672 78.179 28.5703 59.3092 28.5703C40.4395 28.5703 25.1426 43.8672 25.1426 62.737C25.1426 81.6067 40.4395 96.9036 59.3092 96.9036Z"
                                                        fill="#FF6B6B" />
                                                    <path
                                                        d="M29.3333 63.3327C29.3333 45.616 42.82 31.0527 60.0833 29.3393C58.96 29.226 57.82 29.166 56.6667 29.166C37.7967 29.166 22.5 44.4627 22.5 63.3327C22.5 82.2027 37.7967 97.4994 56.6667 97.4994C57.82 97.4994 58.96 97.4394 60.0833 97.326C42.82 95.6127 29.3333 81.0494 29.3333 63.3327Z"
                                                        fill="#FF001F" />
                                                    <path
                                                        d="M95.9629 100.166L98.3199 97.809L103.034 102.523L100.677 104.88L95.9629 100.166Z"
                                                        fill="#A4AFC1" />
                                                    <path
                                                        d="M96.5547 88.9434L101.269 84.2293L103.626 86.5863L98.9117 91.3004L96.5547 88.9434Z"
                                                        fill="#A4AFC1" />
                                                    <path
                                                        d="M83.0039 102.496L87.718 97.7821L90.075 100.139L85.3609 104.853L83.0039 102.496Z"
                                                        fill="#A4AFC1" />
                                                    <path
                                                        d="M90 23.3906C87.2433 23.3906 85 21.1473 85 18.3906C85 15.634 87.2433 13.3906 90 13.3906C92.7567 13.3906 95 15.634 95 18.3906C95 21.1473 92.7567 23.3906 90 23.3906ZM90 16.724C89.08 16.724 88.3333 17.474 88.3333 18.3906C88.3333 19.3073 89.08 20.0573 90 20.0573C90.92 20.0573 91.6667 19.3073 91.6667 18.3906C91.6667 17.474 90.92 16.724 90 16.724Z"
                                                        fill="#A4AFC1" />
                                                    <path
                                                        d="M56.6667 99.9993C36.45 99.9993 20 83.5493 20 63.3327C20 43.116 36.45 26.666 56.6667 26.666C63.7 26.666 70.54 28.6727 76.4467 32.4627L73.7433 36.6693C68.6467 33.3993 62.74 31.666 56.6667 31.666C39.2067 31.666 25 45.8727 25 63.3327C25 80.7927 39.2067 94.9993 56.6667 94.9993C74.1267 94.9993 88.3333 80.7927 88.3333 63.3327C88.3333 62.2893 88.2833 61.256 88.1867 60.2393L93.1633 59.756C93.2767 60.936 93.3333 62.126 93.3333 63.3327C93.3333 83.5493 76.8833 99.9993 56.6667 99.9993Z"
                                                        fill="black" />
                                                    <path
                                                        d="M54.2452 67.8677H57.6652L58.1332 50.4077H53.7772L54.2452 67.8677ZM58.6732 73.0517C58.6732 71.5757 57.5212 70.4237 56.0812 70.4237C54.6052 70.4237 53.4532 71.5757 53.4532 73.0517C53.4532 74.5277 54.6052 75.6797 56.0812 75.6797C57.5212 75.6797 58.6732 74.5277 58.6732 73.0517Z"
                                                        fill="black" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row flex-column flex-lg-row {{-- justify-content-between --}}">
                        <div class="d-flex flex-column list_assets col-md-8">
                            <div class="card list_assets-card">
                                <div class="card-body d-flex flex-column flex-lg-row align-items-center px-2">
                                    <a href="{{ route('user.asset.rate', ['sell', 102, 'bitcoins']) }}">
                                        <div
                                            class="mx-2 asset_card_container d-flex flex-column justify-content-center align-items-center">
                                            <span class="d-block text-center mb-3">
                                                <img src="{{asset('newpages/svg/bitcoin.svg')}}">
                                            </span>
                                            <span class="d-block text-center asset_card_title">Bitcoin</span>
                                        </div>
                                    </a>
                                    <a href="{{ route('user.assets', 'digital assets') }}">
                                        <div class="mx-2 asset_card_container d-flex flex-column justify-content-center align-items-center">
                                            <span class="d-block text-center mb-4">
                                                <img src="{{asset('newpages/svg/assets.svg')}}">
                                            </span>
                                            <span class="d-block text-center asset_card_title">Digital Assets</span>
                                        </div>
                                    </a>

                                    <a href="{{ route('user.assets', 'gift cards') }}">
                                        <div class="mx-2 asset_card_container d-flex flex-column justify-content-center align-items-center">
                                            <span class="d-block text-center mb-4">
                                                <img src="{{asset('newpages/svg/cards.svg')}}">
                                            </span>
                                            <span class="d-block text-center asset_card_title">Gift cards</span>
                                            <span class="d-block text-center asset_card_description">Buy & Sell your
                                                cards</span>
                                        </div>
                                    </a>
                                    <div
                                        class="mx-2 asset_card_container d-flex flex-column justify-content-center align-items-center">
                                        <span class="d-block text-center mb-4">
                                            <img src="{{asset('newpages/svg/airtime.svg')}}">
                                        </span>
                                        <span class="d-block text-center asset_card_title">Airtime</span>
                                        <span class="d-block text-center asset_card_description">Buy & Convert your
                                            airtime to cash</span>
                                    </div>
                                    <div
                                        class="mx-2 asset_card_container d-flex flex-column justify-content-center align-items-center">
                                        <span class="d-block text-center mb-4">
                                            <img src="{{asset('newpages/svg/bills.svg')}}">
                                        </span>
                                        <span class="d-block text-center asset_card_title">Pay bills</span>
                                        <span class="d-block text-center asset_card_description px-1">DSTV, GoTV, PHCN
                                            and more</span>
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

                        <div class="d-flex flex-column budget_card col-md-4">
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
