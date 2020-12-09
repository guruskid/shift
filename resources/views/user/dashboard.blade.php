@extends('layouts.user')
@section('content')
@php
$not = $notifications->last();
@endphp
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
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="row">
                @if (Auth::user()->nairaWallet->status == 'paused')
                <div class="col-md-12 mb-3">
                    <div class="alert text-white alert-dismissible" style="background: #f51109 url('/user_assets/images/goup.png') repeat center; background-size: full">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <strong>Froozen account</strong>
                         <p>Sorry, your Naira wallet is currently froozen, please contact the support for more info about yout account</p>
                    </div>
                </div>
                @endif
                @foreach ($notifications as $item)
                <div class="col-md-12 mb-3">
                    <div class="alert text-white alert-dismissible" style="background: #000070 url('/user_assets/images/group.png') repeat center; background-size: full">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <strong>{{$item->title}}</strong>
                         <p>{{$item->body}}</p>
                    </div>
                </div>
                @endforeach
            </div>


            <div class="row">
                <div class="col-md-4 col-lg-4">
                    <div class="row">
                        <div class="col-6">
                            <a href=" {{route('user.calcCrypto')}} " class="text-white">
                                <div class="card mb-2 widget-content">
                                    <div class="widget-content-wrapper text-white">
                                        <div class="widget-content- mx-auto">
                                            <center>
                                                <img src="{{asset('user_assets/images/crypto.png')}}" style="height: 4.5em;" class="img-fluid" alt="">
                                                <h5 class="text-custom">Trade Assets</h5>
                                            </center>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href=" {{route('user.calcCard')}} " class="text-white">
                                <div class="card mb-2 widget-content">
                                    <div class="widget-content-wrapper py-0 text-white">
                                        <div class="widget-content- mx-auto">
                                            <center>
                                                <img src="{{asset('user_assets/images/gift-cards.png')}}" style="height: 4.5em;" class="img-fluid" alt="">
                                                <h5 class="text-custom" style="font-size: 17px">Trade Gift Cards</h5>
                                            </center>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                    <a href="{{route('user.calcCrypto')}}" class="text-white">
                        <div class="card mb-2 widget-content">
                            <div class="widget-content-wrapper py-0 text-white">
                                <div class="widget-content- mx-auto">
                                    <center>
                                        <img src=" {{asset('svg/bitcoin.svg')}}" class="img-fluid" alt="">
                                        <h5 class="text-custom">Bitcoin</h5>
                                    </center>
                                </div>
                            </div>
                        </div>
                    </a>
                    <div class="row">
                        <div class="col-6">
                            <a href=" {{route('user.bills')}} " class="text-white">
                                <div class="card mb-2 widget-content">
                                    <div class="widget-content-wrapper py-0 text-white">
                                        <div class="widget-content- mx-auto">
                                            <center>
                                                <img src="{{asset('user_assets/images/recharge.png')}}" style="height: 4.5em;" class="img-fluid" alt="">
                                                <h5 class="text-custom">Recharge</h5>
                                            </center>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href=" {{route('user.bills')}} " class="text-white">
                                <div class="card mb-2 widget-content">
                                    <div class="widget-content-wrapper py-0 text-white">
                                        <div class="widget-content- mx-auto">
                                            <center>
                                                <img src="{{asset('user_assets/images/airtime.png')}}" class="img-fluid" style="height: 4.5em;" alt="">
                                                <h5 class="text-custom" style="font-size: 18px" >Airtime to Cash</h5>
                                            </center>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 col-lg-5 mb-3">
                    <a href="{{route('user.naira-wallet')}} ">
                        <div class="px-4 py-4 text-center text-white bg-custom c-rounded-top">
                            <strong>Wallet Balance</strong>
                            <h4>₦{{number_format($naira_balance)}} </h4>
                        </div>
                    </a>
                    <div class="m-0 px-2 pt-1 dashboard-wallets">
                        <div class="card card-body mx-md-5 mt-4 mb-5">
                            <div class="d-flex justify-content-between">
                                <div class="media">
                                    <img src="{{asset('svg/naira.svg')}}" style="height: 50px; margin-right: 5px"  alt="naira wallet">
                                    <a href="{{route('user.naira-wallet')}} ">
                                        <div class="media-body text-custom">
                                            <h6 class="mb-0" >NGN</h6>
                                            <strong>₦{{number_format($naira_balance)}}</strong>
                                        </div>
                                    </a>
                                </div>
                                <a href="#">
                                    <button class="btn opacity-7 c-btn-rounded bg-custom ">
                                        Default Wallet
                                    </button>
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-10 mx-auto bg-custom-accent p-3 balance-box c-rounded-top ">
                                <div class="row ongoing-txn">
                                    <div class="col-md-9 col-10 p-2 mx-auto">
                                        <div class="c-rounded py-2 d-flex bg-white shadow justify-content-center align-items-center ">
                                            <svg class="mr-2" width="47" height="48" viewBox="0 0 47 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="23.3379" cy="24.1143" r="23.3379" fill="#00B9CD"/>
                                                <line x1="14" y1="19" x2="33" y2="19" stroke="white" stroke-width="2"/>
                                                <line x1="14" y1="25" x2="33" y2="25" stroke="white" stroke-width="2"/>
                                                <line x1="14" y1="31" x2="33" y2="31" stroke="white" stroke-width="2"/>
                                            </svg>

                                            <strong class="text-custom">Transaction on going</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between ">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="main-card mb-3 card">
                        <div class="card-header">
                            <h5 class="card-title">Transactions Summary</h5>
                        </div>
                        <div class="card-body">
                            <div style="max-height: 195px">{!! $usersChart->container() !!}</div>
                            <div class="m-1">
                                <span class="text-muted"> <i class="badge badge-dot badge-success"></i> Successfull
                                    Transactions </span><strong class="float-right ">{{$s}}</strong>
                            </div>
                            <div class="m-1">
                                <span class="text-muted">Waiting Transactions </span><strong
                                    class="float-right ">{{$w}}</strong>
                            </div>
                            <div class="m-1">
                                <span class="text-muted">Declined Transactions </span><strong
                                    class="float-right ">{{$d}}</strong>
                            </div>
                            <div class="m-1">
                                <span class="text-muted">Failed Transactions </strong><strong
                                        class="float-right ">{{$f}}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    {{-- <div class="d-md-none text-center">
                        <h5 class="mb-0" >₦{{number_format($naira_balance)}}</h5>
                        <p>Balance</p>
                    </div> --}}
                    <div class="main-card mb-3 c-rounded card bg-custom-accent">
                        <div class="card-header c-rounded-top bg-custom-accent">Transactions </div>
                        <div class="table-responsive px-3 py-2">
                            <transactions-component :trans="{{$transactions}}" :val="{{-1}}" >
                            </transactions-component>
                        </div>
                    </div>
                </div>
            </div>
            @include('layouts.partials.live-feeds')

        </div>
    </div>
</div>


@if($usersChart)
{!! $usersChart->script() !!}
@endif
@endsection
