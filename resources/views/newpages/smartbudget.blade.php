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
                                        <span class="h3 giftcard-text" style="color: #000070;">Smart Budget</span>
                                    </div>
                                    <div class="widget-n" style="justify-content: center; text-align: center;">
                                        <span class="d-block" style="h6 walletbalance-text">Wallet Balance</span>
                                        <span class="d-block price realtime-wallet-balance"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mb-4">
                            <div class="card card-body px-2 px-lg-auto">
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
                                <div class="mt-4" style="width: 100%;border: 1px solid #C9CED6;"></div>
                                <div class="col-12 col-lg-8 mx-auto mt-4 py-4 px-0 px-lg-auto smartbudget_container">
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <div class="d-flex justify-content-center flex-wrap flex-lg-nowrap">
                                                <div class="dailysmartbudgetamnt mx-4">
                                                    <span class="bugdetdailytext">Daily:</span>
                                                    <span class="bugdetdailyamount">₦0.00</span>
                                                </div>
                                                <div class="dailysmartbudgetamnt mx-4">
                                                    <span class="bugdetdailytext">Weekly:</span>
                                                    <span class="bugdetdailyamount">₦100,000,000</span>
                                                </div>
                                                <div class="dailysmartbudgetamnt mx-4">
                                                    <span class="bugdetdailytext">Monthly:</span>
                                                    <span class="bugdetdailyamount">₦100,000</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-lg-6 my-3 smartbudget_cardmenu">
                                            <div class="card card-body smartbudget_item_container" style="cursor: pointer;">
                                                <div class="row">
                                                    <div class="col-9">
                                                        <div class="d-flex align-items-center">
                                                            <div>
                                                                <svg width="60" height="60" viewBox="0 0 70 70" fill="none"
                                                                    xmlns="http://www.w3.org/2000/svg">
                                                                    <path
                                                                        d="M46.666 11.666H23.3327C21.7219 11.666 20.416 12.9719 20.416 14.5827V55.416C20.416 57.0268 21.7219 58.3327 23.3327 58.3327H46.666C48.2768 58.3327 49.5827 57.0268 49.5827 55.416V14.5827C49.5827 12.9719 48.2768 11.666 46.666 11.666Z"
                                                                        stroke="#8484BF" stroke-width="1.5"
                                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                                    <path d="M32.084 15.584H37.9173" stroke="#8484BF"
                                                                        stroke-width="1.5" stroke-linecap="round"
                                                                        stroke-linejoin="round" />
                                                                    <path d="M35 52.084V52.1132" stroke="#8484BF"
                                                                        stroke-width="2.75" stroke-linecap="round"
                                                                        stroke-linejoin="round" />
                                                                </svg>
                                                            </div>
                                                            <div>
                                                                <div class="d-flex flex-column">
                                                                    <span class="titlename">Buy Airtime</span>

                                                                    {{-- If there is a budget --}}
                                                                    <span class="phoneNumber my-2">08141894420</span>
                                                                    <span class="duration">₦1000 - Weekly</span>

                                                                    {{-- If there is no budget --}}
                                                                    <span class="" style="font-size: 12px;color: #676B87;">No Budget made yet!</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-3 p-0">
                                                        <a onclick="toggleTab('airtime_tab')" class="btn text-white" style="background: #000070;border-radius: 3px;">Manage</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @include('newpages.tabs.smartbudget-airtime_tab')


                                        <div class="col-12 col-lg-6 my-3 smartbudget_cardmenu">
                                            <div class="card card-body smartbudget_item_container">
                                                <div class="row">
                                                    <div class="col-9">
                                                        <div class="d-flex alin-items-center">
                                                            <div>
                                                                <svg width="60" height="60" viewBox="0 0 70 70" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M55.4167 20.416H14.5833C11.3617 20.416 8.75 23.0277 8.75 26.2493V52.4993C8.75 55.721 11.3617 58.3327 14.5833 58.3327H55.4167C58.6383 58.3327 61.25 55.721 61.25 52.4993V26.2493C61.25 23.0277 58.6383 20.416 55.4167 20.416Z" stroke="#8484BF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                                    <path d="M46.6673 8.75L35.0007 20.4167L23.334 8.75" stroke="#8484BF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                                    <line x1="16" y1="35.5" x2="34" y2="35.5" stroke="#8484BF" stroke-width="2" stroke-linecap="round"/>
                                                                    <line x1="16" y1="43.5" x2="41" y2="43.5" stroke="#8484BF" stroke-width="2" stroke-linecap="round"/>
                                                                </svg>                                                                
                                                            </div>
                                                            <div>
                                                                <div class="d-flex flex-column">
                                                                    <span class="titlename">Cable Subscription and TV</span>
                                                                    <span class="phoneNumber my-2" style="font-size: 12px;">0000 0000 0000 0000</span>
                                                                    <span class="duration">GoTV Max ( ₦3,880 ) - Monthly</span>

                                                                    {{-- If there is no budget --}}
                                                                    <span class="" style="font-size: 12px;color: #676B87;">No Budget made yet!</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-3 p-0">
                                                        <a onclick="toggleTab('cablesubscription_tab')" class="btn text-white" style="background: #000070;border-radius: 3px;">Manage</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @include('newpages.tabs.cablesubscription_tab')

                                        <div class="col-12 col-lg-6 my-3 smartbudget_cardmenu">
                                            <div class="card card-body smartbudget_item_container">
                                                <div class="row">
                                                    <div class="col-9">
                                                        <div class="d-flex alin-items-center">
                                                            <div>
                                                                <svg width="60" height="60" viewBox="0 0 70 70" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M46.666 11.666H23.3327C21.7219 11.666 20.416 12.9719 20.416 14.5827V55.416C20.416 57.0268 21.7219 58.3327 23.3327 58.3327H46.666C48.2768 58.3327 49.5827 57.0268 49.5827 55.416V14.5827C49.5827 12.9719 48.2768 11.666 46.666 11.666Z" stroke="#8484BF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                                    <path d="M32.084 15.584H37.9173" stroke="#8484BF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                                    <path d="M35 52.084V52.1132" stroke="#8484BF" stroke-width="2.75" stroke-linecap="round" stroke-linejoin="round"/>
                                                                    <path d="M36.27 36.6863C37.2539 37.6703 36.5474 39.3528 35.1655 39.3528C33.7845 39.3528 33.0763 37.6711 34.061 36.6863C34.6714 36.0758 35.6595 36.0757 36.27 36.6863ZM31.3365 33.9619C31.0112 34.2872 31.0112 34.8146 31.3365 35.14C31.6619 35.4654 32.1893 35.4654 32.5147 35.14C33.9796 33.6749 36.3511 33.6748 37.8162 35.14C37.9789 35.3027 38.192 35.3839 38.4053 35.3839C39.1407 35.3839 39.5207 34.4882 38.9943 33.9618C36.8784 31.8457 33.4531 31.8454 31.3365 33.9619ZM42.0871 30.8693C38.2615 27.0437 32.07 27.0434 28.244 30.8693C27.9187 31.1946 27.9187 31.722 28.244 32.0473C28.5694 32.3726 29.0967 32.3726 29.4221 32.0473C32.589 28.8803 37.7419 28.8805 40.9088 32.0473C41.2343 32.3727 41.7616 32.3726 42.087 32.0473C42.4124 31.722 42.4124 31.1946 42.0871 30.8693Z" fill="#8484BF"/>
                                                                </svg>                                                                                                                                   
                                                            </div>
                                                            <div>
                                                                <div class="d-flex flex-column">
                                                                    <span class="titlename">Data Subscription</span>
                                                                    <span class="phoneNumber my-2" style="font-size: 12px;">0000 0000 0000 0000</span>
                                                                    <span class="duration">10gb ( ₦10,000 ) - Monthly</span>

                                                                    {{-- If there is no budget --}}
                                                                    <span class="" style="font-size: 12px;color: #676B87;">No Budget made yet!</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-3 p-0">
                                                        <a onclick="toggleTab('datasubscription_tab')" class="btn text-white" style="background: #000070;border-radius: 3px;">Manage</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @include('newpages.tabs.datasubscription_tab')

                                        
                                        <div class="col-12 col-lg-6 my-3 smartbudget_cardmenu">
                                            <div class="card card-body smartbudget_item_container">
                                                <div class="row">
                                                    <div class="col-9">
                                                        <div class="d-flex alin-items-center">
                                                            <div>
                                                                <svg width="60" height="60" viewBox="0 0 70 70" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M37.9173 8.75V29.1667H55.4173L32.084 61.25V40.8333H14.584L37.9173 8.75Z" stroke="#8484BF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                                    </svg>                                                                                                                                                                                                       
                                                            </div>
                                                            <div>
                                                                <div class="d-flex flex-column">
                                                                    <span class="titlename">Electricity Bills</span>
                                                                    <span class="phoneNumber my-2" style="font-size: 12px;">0000 0000 0000 0000</span>
                                                                    <span class="duration">10gb ( ₦10,000 ) - Monthly</span>

                                                                    {{-- If there is no budget --}}
                                                                    <span class="" style="font-size: 12px;color: #676B87;">No Budget made yet!</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-3 p-0">
                                                        <a onclick="toggleTab('electricitybills_tab')" class="btn text-white" style="background: #000070;border-radius: 3px;">Manage</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @include('newpages.tabs.electricitybills_tab')

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

@endsection