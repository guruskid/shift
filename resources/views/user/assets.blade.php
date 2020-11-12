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
                                        <span class="h3 giftcard-text text-capitalize">{{ $asset_type }}</span>
                                    </div>
                                    <div class="widget-n text-center" style="justify-content: center;">
                                        <span class="d-block" style="h6 walletbalance-text">Wallet Balance</span>
                                        <span
                                            class="d-block price">₦{{ number_format(Auth::user()->nairaWallet->amount) }}</span>
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
                                    <div class="list-cards-title primary-color">
                                        <a href="#">
                                            <span class="ml-1"
                                                style="color: rgba(0, 0, 112, 0.75);font-size:16px;">Buy/Sell
                                                card</span>
                                        </a>
                                    </div>
                                    <div class="list-cards-search primary-color mt-3 mt-lg-0">
                                        <form action="" method="post">
                                            <div class="form-group p-0 m-0">
                                                <span class="giftcard search-icon">
                                                    <svg width="1em" height="1em" viewBox="0 0 16 16"
                                                        class="bi bi-search" fill="#000070"
                                                        xmlns="http://www.w3.org/2000/svg">
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
                                <div class="container">
                                    <div class="row">
                                        @foreach ($assets as $asset)
                                        <div class="col-lg-4 col-md-6 col-12 mb-3">
                                            <div class="wallcards-container py-lg-5 px-lg-3 px-1 list_all_cards d-flex align-items-center">
                                                <div style="width:40%;">
                                                    <img src="{{ asset('storage/assets/'.$asset->image) }}" style="width: 100px" alt="{{ $asset->name }}">
                                                </div>
                                                <div class="d-flex flex-column flex-start ml-4" style="width:60%;">
                                                    <span class="d-block text-primary mb-2 ml-2" style="font-weight: normal;font-style: normal;">{{ $asset->name }}</span>
                                                        <div class="d-flex justify-content-around">
                                                            <a href="{{ route('user.asset.rate', ['buy', $asset->id, str_slug($asset->name)]) }}">
                                                                <button class="btn btn-primary btn-c-rounded"  {{ $asset->buyable != 1 ? 'disabled' : '' }} >Buy</button>
                                                            </a>
                                                            <a href="{{ route('user.asset.rate', ['sell', $asset->id, str_slug($asset->name)]) }}">
                                                                <button class="btn btn-outline-primary btn-c-rounded" {{ $asset->sellable != 1 ? 'disabled' : '' }}>Sell</button>
                                                            </a>
                                                        </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- @include('newpages.modals.uploadcardmodal') --}}
        </div>
    </div>
</div>

@endsection
