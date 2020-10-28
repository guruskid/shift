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
                                        <span class="h3 giftcard-text">Giftcards</span>
                                    </div>
                                    <div class="widget-n" style="justify-content: center; text-align: center;">
                                        <span class="d-block" style="h6 walletbalance-text">Wallet Balance</span>
                                        <span class="d-block price">₦56,758</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-12">
                            <div class="widget widget-chart-one">
                                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-center pb-4 mb-3"
                                    style="border-bottom: 1px solid #C9CED6;">
                                    <div class="list-cards-title primary-color">Buy/Sell Card</div>
                                    <div class="list-cards-search primary-color mt-3 mt-lg-0">
                                        <form action="" method="post">
                                            <div class="form-group p-0 m-0">
                                                <span class="search-icon">
                                                    <svg width="0.9em" height="1em" viewBox="0 0 16 16" class="bi bi-search"
                                                        fill="#000070" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd"
                                                            d="M10.442 10.442a1 1 0 0 1 1.415 0l3.85 3.85a1 1 0 0 1-1.414 1.415l-3.85-3.85a1 1 0 0 1 0-1.415z" />
                                                        <path fill-rule="evenodd"
                                                            d="M6.5 12a5.5 5.5 0 1 0 0-11 5.5 5.5 0 0 0 0 11zM13 6.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0z" />
                                                    </svg>
                                                </span>
                                                <input type="text" class="form-control search-giftcard pl-4"
                                                    placeholder="Search for giftcard" />
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-6 col-lg-4 my-3 my-3">
                                        <div class="card" style="box-shadow: 0px 2px 10px rgba(166, 166, 166, 0.25);">
                                            <div class="card-body d-flex flex-wrap justify-content-around align-items-center">
                                                <span class="d-block">
                                                    <svg width="80" height="40" viewBox="0 0 130 130" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink">
                                                        <rect width="130" height="130" fill="url(#pattern0)" />
                                                        <defs>
                                                            <pattern id="pattern0" patternContentUnits="objectBoundingBox"
                                                                width="1" height="1">
                                                                <use xlink:href="#image0" transform="scale(0.002)" />
                                                            </pattern>
                                                            <image id="image0" width="500" height="500"
                                                                xlink:href="data:image/png;base64,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" />
                                                        </defs>
                                                    </svg>
                                                </span>
                                                <div
                                                    class="card-type d-flex flex-column justify-content-center align-items-center">
                                                    <span class="d-block primary-color" style="font-size: 22px;">Steam gift
                                                        card</span>
                                                    <div class="d-flex justify-content-around align-items-center mt-2">
                                                        <a class="card-type__sell-card mx-2" href="#">Sell</a>
                                                        <a class="card-type__buy-card mx-2" href="#">Buy</a>
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

            @include('layouts.partials.live-feeds')
        </div>
    </div>
</div>


@endsection
