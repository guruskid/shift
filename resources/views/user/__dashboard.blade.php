@extends('layouts.account')
@section('title', 'User dashboard |' )
@section('content')


<div class="container d-flex align-content-center flex-column px-4 welcome">
    {{-- <div class="row my-4 " id="first-row">
        <h6 class="text-white p-3">
            Hi Simeon
        </h6>
        <div class="d-flex">
            <div class="col-6 col-lg-6 col-md-6 col-sm-6 p-0">
                <p class="text-white p-3 mb-0" style="font-size: small;">
                    Welcome to DANTOWN DASHBOARD. <br>
                    Will you like us to show you aroud the dashboard?
                </p>
            </div>
            <div class="col-6 col-lg-6 col-md-6 col-sm-6 p-0 align-self-center">
                <button class="btn btn-warning mr-4">
                    YES! Please
                </button>
                <button class="btn bg-white">
                    Maybe Later
                </button>
            </div>
        </div>
    </div> --}}

    <div class="row mt-3 py-2 table" style="background: #00B9CD;">
        <p class="text-white w-100 pl-2 mb-0">
            Transaction
        </p>
        <table class="col-12 col-lg-12 col-md-12 col-sm-12 text-white mb-4">
            <thead>
                <tr>
                    <td class=" text-center">
                        ID
                    </td>
                    <td class=" text-center">
                        Asset Type
                    </td>
                    <td class=" text-center">
                        Tran. Type
                    </td>
                    <td class=" text-center">
                        Asset Value
                    </td>
                    <td class=" text-center">
                        Cash Value
                    </td>
                    <td class=" text-center">
                        Status
                    </td>
                </tr>
            </thead>
            <tbody>
                @foreach ($transactions as $t)
                <tr>
                    <td class="text-center">{{$t->uid}}</td>
                    <td class="text-center">{{ucwords($t->card)}}</td>
                    <td class="text-center">{{$t->type}}</td>
                    <td class="text-center">{{$t->amount}}</td>
                    <td class="text-center">{{number_format($t->amount_paid)}}</td>
                    <td class="text-center">
                        @switch($t->status)
                        @case('success')
                        <div class="badge badge-success">{{$t->status}}</div>
                        @break
                        @case("failed")
                        <div class="badge badge-danger">{{$t->status}}</div>
                        @break
                        @case('declined')
                        <div class="badge badge-warning">{{$t->status}}</div>
                        @break
                        @case('waiting')
                        <div class="badge badge-info">{{$t->status}}</div>
                        @break
                        @default
                        <div class="badge badge-success">{{$t->status}}</div>

                        @endswitch
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="row mt-4 mr-0" id="third-row">
        <div class="col-12 col-lg-4 col-md-12 col-sm-12 px-0">
            <div class="d-flex third-row flex-column px-0">
               <a href="{{ route('user.calculator') }} ">
                <div class="col-12 col-lg-12 col-md-12 col-sm-12 py-2 mb-3">
                    <center>
                        <img src="{{asset('main/svg/Group 367.svg')}} " alt="">
                        <p class="mb-2 mt-1" style="color: rgba(0, 0, 112, 0.75);">
                            Trade Assets/Gift Cards
                        </p>
                    </center>
                </div>
               </a>
                <div class="col-12 col-lg-12 col-md-12 col-sm-12 py-2 mb-3">
                    <center>
                        <img src="{{asset('main/svg/2015911933344.svg')}} " alt="">
                        <p class="mb-2 mt-1" style="color: rgba(0, 0, 112, 0.75);">
                            Recharge
                        </p>
                    </center>
                </div>
                <div class="col-12 col-lg-12 col-md-12 col-sm-12 py-2 mb-3">
                    <center>
                        <img src="{{asset('main/svg/Vector (6).svg')}} " alt="">
                        <p class="mb-2 mt-1" style="color: rgba(0, 0, 112, 0.75);">
                            Send Gifts
                        </p>
                    </center>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4 col-md-12 col-sm-12 ml-0 p-0">
            <div id="sales-form" class="px-4 pt-2">
                <small class="text-center d-block text-white my-3">
                    WALLET BALANCE
                </small>
                <h3 class="text-center text-white mt-3 mb-0">
                    NGN800,333.00
                </h3>
                <div class="row m-0" id="coin">
                    <form action="">
                        <div class="form-group mx-5">
                            <select class="custom-select">
                                <option value="" disabled selected>Choose your option</option>
                                <option value="1">Option 1</option>
                                <option value="2">Option 2</option>
                                <option value="3">Option 3</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
            <div class="m-0 px-2 pt-3 coinSelector">
                <div id="grids" class="mt-5">
                    <div class="grid p-2">
                        <p class="mb-0 d-flex">
                            <img src="{{asset('main/svg/Bitcoin.svg')}} " class="mr-2">
                            <span>BTC 0.00 <br><small>NGN 0.00</small></span>
                        </p>
                    </div>
                    <div class="grid p-2">
                        <p class="mb-0 d-flex  align-content-center">
                            <img src="{{asset('main/svg/ethereum.svg')}} " class="mr-2">
                            <span>ETH 0.00 <br><small>NGN 0.00</small></span>
                        </p>
                    </div>
                </div>
                <div id="add" class="mt-5 mx-5 d-flex justify-content-center pb-4">
                    <div class="grid p-2 d-flex">
                        <p class="mb-0 d-flex justify-content-center">
                            <img src="{{asset('main/svg/add new icon.svg')}} " class="mr-2">
                            <span class="align-self-center">Add a new wallet</span>
                        </p>
                    </div>
                </div>
            </div>

        </div>
        <div class="col-12 col-lg-4 col-md-12 col-sm-12 d-flex flex-column p-1 chartdiv">
            <div class="shadow rounded  ml-auto " style="width: 19em;">
                <a href="" style="border-bottom: 1px solid gray; color: black; text-decoration: none;"
                    class=" pt-1 px-1 d-block">Transaction Summary</a>
                <div id="myChart">

                </div>
                <table class="w-100 px-3" style="border-spacing: 3px 20px;border-collapse: separate;">
                    <tr>
                        <td>
                            <aside
                                style="padding:5px; background: rgba(0, 0, 112, 0.75); width: 3px;height: 3  px;border-radius: 5px;"
                                class="mt-1 mr-2 d-inline-block"></aside><span
                                style="padding: 0px; font-size: smaller;"> Waiting
                                Transactions</span>
                        </td>
                        <td style="text-align: end;">
                            <span style="font-size: smaller;">{{$w}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <aside
                                style="padding:5px; background: #FFB800; width: 3px;height: 3  px;border-radius: 5px;"
                                class="mt-1 mr-2 d-inline-block"></aside><span
                                style="padding: 0px; font-size: smaller;"> Declined
                                Transactions</span>
                        </td>
                        <td style="text-align: end;">
                            <span style="font-size: smaller;">{{$d}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <aside
                                style="padding:5px; background: #FF001F; width: 3px;height: 3  px;border-radius: 5px;"
                                class="mt-1 mr-2 d-inline-block"></aside><span
                                style="padding: 0px; font-size: smaller;"> Failed
                                Transactions</span>
                        </td>
                        <td style="text-align: end;">
                            <span style="font-size: smaller;">{{$f}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <aside
                                style="padding:5px; background: skyblue; width: 3px;height: 3  px;border-radius: 5px;"
                                class="mt-1 mr-2 d-inline-block"></aside><span
                                style="padding: 0px; font-size: smaller;"> Successful
                                Transactions</span>
                        </td>
                        <td style="text-align: end;">
                            <span style="font-size: smaller;">{{$s}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span style="padding: 0px; font-size: smaller;"> Total
                                Transactions</span>
                        </td>
                        <td>
                            <button class="btn btn-sm p-1 w-100"
                                style="background: rgba(0, 0, 112, 0.75);color: white;">
                                {{Auth::user()->transactions->count()}}
                            </button>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="row shadow p-4 mt-5 justify-content-around footer">
        <div class="col-4 col-lg-2 col-md-4 col-sm-4 text-center">
            <h6 class="mb-0" style="color: #000070;">LTC
                <span style="color: darkgray;">/NGN</span>
            </h6>
            <small class="mt-0">15,688.91NGN</small>
        </div>
        <div class="col-4 col-lg-2 col-md-4 col-sm-4 text-center">
            <h6 class="mb-0" style="color: #000070;">ETC
                <span class="text-muted" style="color: darkgray">/NGN</span>
            </h6>
            <small class="mt-0">55,038.47NGN</small>
        </div>
        <div class="col-4 col-lg-2 col-md-4 col-sm-4 text-center">
            <h6 class="mb-0" style="color: #000070;">XRP
                <span class="text-muted" style="color: darkgray;">/NGN</span>
            </h6>
            <small class="mt-0">66.3NGN</small>
        </div>
        <div class="col-4 col-lg-2 col-md-4 col-sm-4 text-center">
            <h6 class="mb-0" style="color: #000070;">USDT
                <span class="text-muted" style="color: darkgray;">/NGN</span>
            </h6>
            <small class="mt-0">416.97NGN</small>
        </div>
        <div class="col-4 col-lg-2 col-md-4 col-sm-4 text-center">
            <h6 class="mb-0" style="color: #000070;">BTC
                <span class="text-muted" style="color: darkgray;">/NGN</span>
            </h6>
            <small class="mt-0">2,593,012.00NGN</small>
        </div>
        <div class="col-4 col-lg-2 col-md-4 col-sm-4 text-center">
            <h6 class="mb-0" style="color: #000070;">DASH
                <span class="text-muted" style="color: darkgray;">/NGN</span>
            </h6>
            <small class="mt-0">27,376.15NGN</small>
        </div>
    </div>
</div>
</div>
</div>
</div>


@endsection
