@extends('layouts.user')
@section('title', 'Recharge | ' )
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
                            <div class="m-0 p-3 c-rounded-top  bg-custom text-white"><strong>Mobile Recharge</strong></div>
                            <div class="bg-custom-gradient p-4 "></div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-12 ">
                                @if (count($errors) > 0)
                                <ul>
                                    @foreach ($errors->all() as $error)
                                    <li class="text-warning">{{ $error }}</li>
                                    @endforeach
                                </ul>
                                @endif
                                <div class="bills-box ">
                                    <ul class="tabs-animated-shadow nav-justified tabs-animated nav mb-3">
                                        <li class="nav-item"><a role="tab" onclick="getAirtimedetails()" id="tab-c1-1"
                                                data-toggle="tab" href="#tab-animated1-0" class="nav-link active"><span
                                                    class="nav-text">Airtime</span></a>
                                        </li>
                                        <li class="nav-item"><a role="tab" id="tab-c1-0" data-toggle="tab"
                                                href="#tab-animated1-1" class="nav-link "><span class="nav-text">Mobile
                                                    data</span></a></li>
                                    </ul>
                                    <div class="tab-content">
                                        <div id="tab-animated1-0" role="tabpanel" class="tab-pane active">
                                            <form method="post" action="{{route('user.airtime')}}">
                                                @csrf
                                                <div class="form-group mx-4">
                                                    <label for="asset">Network Provider</label>
                                                    <select id="a-network-provider" name="network"
                                                        onchange="getAirtimedetails()"
                                                        class="form-control form-control-sm">
                                                        <option value="mtn">MTN</option>
                                                        <option value="glo">Glo</option>
                                                        <option value="airtel">Airtel</option>
                                                        <option value="9mobile">9Mobile</option>
                                                    </select>
                                                </div>
                                                <div class="form-group mx-4">
                                                    <label for="Currency">Phone number</label>
                                                    <input type="tel" required="required" id="a-phone"
                                                        onchange="getAirtimedetails()" name="phone"
                                                        class="form-control form-control-sm">
                                                </div>
                                                <div class="form-group mx-4">
                                                    <label for="">Amount</label>
                                                    <input type="number" required="required"
                                                        onchange="getAirtimedetails()" name="amount" id="a-amount"
                                                        class="form-control form-control-sm">
                                                </div>
                                                <div class="form-group mx-4">
                                                    <label for="">Password</label>
                                                    <input type="password" required="required" placeholder="- - - -"
                                                        maxlength="4" name="password"
                                                        class="form-control form-control-sm">
                                                </div>


                                                <button type="submit" class="btn bg-custom c-rounded btn-block" style="font-size: unset;">
                                                    Recharge</button>
                                            </form>
                                        </div>
                                        {{-- Data --}}
                                        <div id="tab-animated1-1" role="tabpanel" class="tab-pane">
                                            <div class="form-group mx-4">
                                                <label for="asset">Data Network Carrier</label>
                                                <select class="form-control form-control-sm">
                                                    <option value="mtn">MTN</option>
                                                    <option value="glo">Glo</option>
                                                    <option value="airtel">Airtel</option>
                                                    <option value="9mobile">9Mobile</option>
                                                </select>
                                            </div>
                                            <div class="form-group mx-4">
                                                <label for="Currency">Phone number</label>
                                                <input type="number" class="form-control form-control-sm">
                                            </div>
                                            <div class="form-group mx-4">
                                                <label for="">Amount</label>
                                                <input type="number" name="amount" class="form-control form-control-sm">
                                            </div>
                                            <div class="form-group mx-4">
                                                <label for="">Password</label>
                                                <input type="password" placeholder="- - - -" name="password"
                                                    class="form-control form-control-sm">
                                            </div>
                                            <button type="submit" class="btn bg-custom c-rounded btn-block" style="font-size: unset;">
                                                Pay</button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                <div class="trade-details-box card">
                                    <div class="card-header bg-custom-accent ">Transactioin Details </div>
                                    <div class="card-body ">
                                        <p class="mb-3  mt-3"><span>Purchase Type</span> <span id="d-txn-type"
                                                class="float-right">Airtime</span></p>
                                        <p class="mb-3"><span>Network provider</span> <span id="d-network-provider"
                                                class="float-right" style="text-transform: uppercase;"></span></p>
                                        <p class="mb-3"><span>Phone number</span> <span id="d-phone"
                                                class="float-right"></span></p>
                                        <p class="mb-3"><span>Amount</span> <span id="d-amount"
                                                class="float-right"></span></p>
                                        <p class="mb-3"><span>Amount payable</span> <span id="d-amount-payable"
                                                class="float-right"></span></p>
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
