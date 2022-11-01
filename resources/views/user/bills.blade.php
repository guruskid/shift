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
            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="m-0 p-3 c-rounded-top  bg-custom text-white">
                            <strong>Recharge</strong>
                        </div>
                        <div class="bg-custom-gradient p-4 ">
                        </div>
                        <div class="container-fluid">
                            @if (count($errors) > 0)
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li class="text-warning">{{ $error }}</li>
                                @endforeach
                            </ul>
                            @endif
                            <div class="row">
                                <div class="col-md-4 col-lg-4 col-6 col-sm-6 my-3">
                                    <div class="card card-body text-center text-custom">
                                        <img src="{{asset('svg/airtime-data.svg')}}" style="height: 40px; width: 40px"
                                            class="align-self-center">
                                        <h6 class=" mt-3">Airtime & Data Subscription</h6>
                                        <a href="#" data-toggle="modal" data-target="#airtime-recharge-modal"><button
                                                class="btn portfolio-btn">Buy now</button></a>
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-4 col-6 col-sm-6 my-3">
                                    <div class="card card-body text-center text-custom">
                                        <img src="{{asset('svg/airtime-cash.svg')}}" style="height: 40px; width: 40px"
                                            class="align-self-center">
                                        <h6 class=" mt-3">Airtime to cash </h6>
                                        <a href="#" data-toggle="modal" data-target="#airtime-to-cash-modal">
                                            <button class="btn portfolio-btn">Trade now</button></a>
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-4 col-6 col-sm-6 my-3">
                                    <div class="card card-body text-center text-custom">
                                        <img src="{{asset('svg/discount-airtime.svg')}}"
                                            style="height: 40px; width: 40px" class="align-self-center">
                                        <h6 class=" mt-3">Buy Discounted Airtime</h6>
                                        <a href="#" data-toggle="modal" data-target="#discount-airtime-modal">
                                            <button class="btn portfolio-btn">Buy now</button></a>
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-4 col-6 col-sm-6 my-3">
                                    <div class="card card-body text-center text-custom">
                                        <img src="{{asset('svg/cable.svg')}}" style="height: 40px; width: 40px"
                                            class="align-self-center">
                                        <h6 class=" mt-3">Cable Subscription and TV</h6>
                                        <a  href="#" data-toggle="modal" data-target="#paytv-modal">
                                            <button class="btn portfolio-btn">Pay now</button></a>
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-4 col-6 col-sm-6 my-3">
                                    <div class="card card-body text-center text-custom">
                                        <img src="{{asset('svg/electricity.svg')}}" style="height: 40px; width: 40px"
                                            class="align-self-center">
                                        <h6 class=" mt-3">Electricity Bills</h6>
                                        <a href="#" data-toggle="modal" data-target="#electricity-modal">
                                            <button class="btn portfolio-btn">Buy now</button></a>
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



{{-- Airtime modal --}}
<div class="modal fade " id="airtime-recharge-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content  c-rounded">

            <!-- Modal Header -->
            <div class="modal-header bg-custom-gradient c-rounded-top p-4 ">
                <h4 class="modal-title">Buy Airtime </h4>
                <button type="button" class="close bg-light rounded-circle " data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <ul class="tabs-animated-shadow nav-justified tabs-animated nav mb-3">
                    <li class="nav-item"><a role="tab" onclick="getAirtimedetails()" id="tab-c1-1" data-toggle="tab"
                            href="#tab-animated1-0" class="nav-link active"><span class="nav-text">Airtime</span></a>
                    </li>
                    <li class="nav-item"><a role="tab" id="tab-c1-0" data-toggle="tab" href="#tab-animated1-1"
                            class="nav-link "><span class="nav-text">Mobile
                                data</span></a></li>
                </ul>
                <div class="tab-content">
                    <div id="tab-animated1-0" role="tabpanel" class="tab-pane active">
                        <form method="post" action="{{route('user.airtime')}}" id="airtime-form" >
                            <input type="hidden" name="ref" value="{{$ref}}">
                            @csrf
                            <div class="form-group mx-4">
                                <label for="asset">Network Provider</label>
                                <select id="a-network-provider" name="network" onchange="getAirtimedetails()"
                                    class="form-control form-control-sm">
                                    <option value="mtn">MTN</option>
                                    <option value="glo">Glo</option>
                                    <option value="airtel">Airtel</option>
                                    <option value="9mobile">9Mobile</option>
                                </select>
                            </div>
                            <div class="form-group mx-4">
                                <label for="Currency">Phone number</label>
                                <input type="tel" required="required" id="a-phone" onchange="getAirtimedetails()"
                                    name="phone" class="form-control form-control-sm">
                            </div>
                            <div class="form-group mx-4">
                                <label for="">Amount</label>
                                <input type="number" required="required" onchange="getAirtimedetails()" name="amount"
                                    id="a-amount" class="form-control form-control-sm">
                            </div>
                            <div class="form-group mx-4">
                                <label for="">Password</label>
                                <input type="password" required="required" placeholder="- - - -" maxlength="4"
                                    name="password" class="form-control form-control-sm">
                            </div>


                            <button id="recharge-btn" type="submit" class="btn bg-custom c-rounded btn-block" style="font-size: unset;">
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
    </div>
</div>


{{-- Cable TV modal --}}
<div class="modal fade " id="paytv-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content  c-rounded">

            <!-- Modal Header -->
            <div class="modal-header bg-custom-gradient c-rounded-top p-4 ">
                <h4 class="modal-title">Cable Subscription</h4>
                <button type="button" class="close bg-light rounded-circle " data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <form method="post" action="{{route('user.paytv')}}" id="cable-form">
                    <input type="hidden" name="ref" value="{{$ref}}">
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
                    <button type="submit" id="cable-btn" class="btn bg-custom c-rounded btn-block"
                        style="font-size: unset">
                        Pay</button>
                </form>
            </div>
        </div>
    </div>
</div>


{{-- Electricity payment --}}
<div class="modal fade " id="electricity-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content  c-rounded">

            <!-- Modal Header -->
            <div class="modal-header bg-custom-gradient c-rounded-top p-4 ">
                <h4 class="modal-title">Electricity bill payment</h4>
                <button type="button" class="close bg-light rounded-circle " data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <form method="post" action="{{route('user.electricity')}}" id="electricity-form" >
                    <input type="hidden" name="ref" value="{{$ref}}">
                    @csrf
                    <div class="form-group mx-4">
                        <label for="asset">Network Provider</label>
                        <select class="form-control form-control-sm" name="provider" id="provider" onchange="getElectUser()" >
                            <option value=""><--Select provider--></option>
                            @foreach ($providers as $p)
                            <option value="{{$p->billername}}" data-scid="{{$p->service_category_id}}">{{$p->billername}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mx-4">
                        <label for="Currency">Meter number</label>
                        <input type="number" required="required" id="dec-acct-num" name="account" onchange="getElectUser()" class="form-control form-control-sm">
                    </div>
                    <div class="form-group mx-4">
                        <label for="">Account</label>
                        <input type="text" readonly id="dec-acct-name" class="form-control form-control-sm">
                    </div>
                    <div class="form-group mx-4">
                        <label for="">Amount</label>
                        <input type="number" required="required" name="amount" id="amount" onchange="getElectPrice()" class="form-control form-control-sm">
                    </div>
                    <div class="form-group mx-4">
                        <label for="">Password</label>
                        <input type="password" required="required" placeholder="- - - -"
                            maxlength="4" name="password"
                            class="form-control form-control-sm">
                    </div>
                    <input type="hidden" id="scid" name="scid" >
                    <button type="submit" id="electricity-btn" class="btn bg-custom c-rounded btn-block" style="font-size: unset;">
                        Recharge</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Discounted Airtime --}}
<div class="modal fade " id="discount-airtime-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content  c-rounded">

            <!-- Modal Header -->
            <div class="modal-header bg-custom-gradient c-rounded-top p-4 ">
                <h4 class="modal-title">Discounted Airtime</h4>
                <button type="button" class="close bg-light rounded-circle " data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <form method="post" action="#">
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
        </div>
    </div>
</div>


{{-- Airtime to cash --}}
<div class="modal fade " id="airtime-to-cash-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content  c-rounded">

            <!-- Modal Header -->
            <div class="modal-header bg-custom-gradient c-rounded-top p-4 ">
                <h4 class="modal-title">Airtime to Cash</h4>
                <button type="button" class="close bg-light rounded-circle " data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <form method="post" action="#">
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
                        <label for="Currency">Amount</label>
                        <input type="number" required="required" id="a-phone"
                            onchange="getAirtimedetails()" name="phone"
                            class="form-control form-control-sm">
                    </div>
                    <div class="form-group mx-4">
                        <label for="Currency">Sending from</label>
                        <input type="tel" required="required" name="from" class="form-control form-control-sm">
                    </div>
                    <div class="form-group mx-4">
                        <label for="">Payable</label>
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
        </div>
    </div>
</div>

@endsection
