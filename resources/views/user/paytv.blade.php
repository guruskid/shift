@php
$s = Auth::user()->transactions->where('status', 'success')->count();
$w = Auth::user()->transactions->where('status', 'waiting')->count();
$d = Auth::user()->transactions->where('status', 'declined')->count();
$f = Auth::user()->transactions->where('status', 'failed')->count();
@endphp


@extends('layouts.user')
@section('title', 'Assets Calculator | ' )
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
            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="col-md-12  px-0">
                            <div class="m-0 p-3 c-rounded-top  bg-custom text-white">
                                <strong>Cable Subscription</strong>
                            </div>
                            <div class="bg-custom-gradient p-4 ">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                <div class="bills-box ">
                                    <form method="post" action="{{route('user.paytv')}}">
                                        @csrf
                                        <div class="d-flex p-0 my-2 rounded">
                                            <div class="col-12 p-1 text-center bg-custom c-rounded " id="sell-trade"> 
                                                Pay Tv
                                            </div>
                                        </div>
                                        <div class="form-group mx-4">
                                            <label for="asset">TV Biller</label>
                                            <select class="form-control form-control-sm" id="biller" name="service_name" onchange="getDecoderUser()" >
                                                <option value=""></option>
                                                @foreach ($billers as $b)
                                                <option value="{{$b->billername}}">{{ ucfirst($b->billername) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group mx-4" >
                                            <label for="Currency">Smartcard no.</label>
                                            <input type="text" class="form-control form-control-sm" name="card_num" id="dec-num" onchange="getDecoderUser()" >
                                        </div>

                                        <div class="form-group mx-4">
                                            <label for="">Associated name</label>
                                            <input type="text" class="form-control form-control-sm" id="acct-name" name="name" readonly >
                                        </div>

                                        <div class="form-group mx-4" id="card-type-div">
                                            <label for="type">Package</label>
                                            <select class="form-control form-control-sm" name="package" id="packages">
                                                <option value="" id="package-loader" ></option>
                                            </select>
                                        </div>
                                        <input type="hidden" name="amount" id="amount" >
                                        <div class="form-group mx-4">
                                            <label for="">Password</label>
                                            <input type="password" class="form-control form-control-sm" name="password">
                                        </div>
                                        <button type="submit" class="btn bg-custom c-rounded btn-block"
                                            style="font-size: unset">
                                            <img src="{{asset('loader.gif')}}" height="20px" id="loader"
                                                style="display: none;" alt="">
                                            Pay</button>
                                    </form>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                <div class="trade-details-box card">
                                    <div class="card-header bg-custom-accent "> Transaction Details </div>
                                    <div class="card-body ">
                                        <p class="mb-3  mt-3">
                                            <span>Smartcard No.</span>
                                            <span id="d-dec-num" class="float-right">xxxxx</span>
                                        </p>
                                        <p class="mb-3">
                                            <span>Associated name</span>
                                            <span id="d-acct-name" class="float-right">xxxxx</span>
                                        </p>
                                        <p class="mb-3">
                                            <span>Package</span>
                                            <span id="d-package" class="float-right">xxxxx</span>
                                        </p>
                                        <p class="mb-3">
                                            <span>Amount payable</span>
                                            <span id="d-amount" class="float-right">xxxxx</span>
                                        </p>
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
