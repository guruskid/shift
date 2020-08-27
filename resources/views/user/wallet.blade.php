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
                            <div class="media ">
                                <img src="{{asset('svg/ethereum.svg')}}">
                                <div class="media-body ml-2 ">
                                    <strong>ETH 233200.00</strong>
                                    <p>NGN 0.00</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-custom-gradient p-4 ">
                        </div>
                        <div class="container-fluid">
                            <div class="row my-3">
                                <div class="col-md-6 mb-3">
                                    <div class="card wallet">
                                        <div
                                            class="card-header wallet-balance bg-custom c-rounded-top justify-content-between">
                                            <h5>Balance</h5>
                                            <div class="">
                                                <h5 class="mb-0">ETH 0.00</h5>
                                                <p>NGN 0.00</p>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="wallet-actions mb-4 d-flex justify-content-around">
                                                <a href="#">
                                                    <div class="wallet-action-btn">
                                                        <i class="fa fa-cart-plus "></i>
                                                        <span class="d-block">Buy</span>
                                                    </div>
                                                </a>
                                                <a href="#">
                                                    <div class="wallet-action-btn">
                                                        <i class="fa fa-cart-arrow-down "></i>
                                                        <span class="d-block">Sell</span>
                                                    </div>
                                                </a>
                                                <a href="#" data-toggle="modal" data-target="#send-modal">
                                                    <div class="wallet-action-btn">
                                                        <i class="fa fa-paper-plane "></i>
                                                        <span class="d-block">Send</span>
                                                    </div>
                                                </a>
                                                <a href="#" data-toggle="modal" data-target="#recieve-modal">
                                                    <div class="wallet-action-btn">
                                                        <i class="fa fa-paper-plane fa-rotate-90 "></i>
                                                        <span class="d-block">Recieve</span>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="d-flex justify-content-between wallet-buttons">
                                                <button class="btn bg-custom-gradient px-3 ">Withdraw</button>
                                                <button class="btn bg-custom-gradient px-3">Deposit</button>
                                                <button class="btn bg-custom-gradient px-3">Transfer</button>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-custom-accent p-4 c-rounded-bottom ">
                                            <strong>Pending Transc.:</strong> <span>No pending transactions in your
                                                wallet</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="card card-body p-5 mb-3">
                                        <strong class="text-accent">Guide</strong>
                                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Similique eaque,
                                            e aliquid obcaecati numquam! Harumssimus aliquam adipisci facere vitae quis
                                            commodi! Facilis.</p>
                                        <p>Lorem ipsum dolor sit amet dd sad dr s consectetur adipisicing el oribus quam
                                            vero reprehenderit enim nisi.
                                        </p>
                                    </div>
                                    <button class="btn btn-secondary btn-block mt-2 ">Back</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-1">
                <div class="col-md-12">
                    <div class="main-card mb-3 card bg-custom-accent">
                        <div class="card-header c-rounded-top bg-custom-accent">Transactions </div>
                        <div class="table-responsive">
                            <table class="align-middle mb-2 table table-striped ">
                                <thead>
                                    <tr>
                                        <th class="text-center">ID</th>
                                        <th class="text-center">Asset type</th>
                                        <th class="text-center">Tran. type</th>
                                        <th class="text-center">Asset value</th>
                                        <th class="text-center">Cash value</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            @include('layouts.partials.live-feeds')
        </div>
    </div>
</div>

<div class="modal fade " id="send-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content  c-rounded">

            <!-- Modal Header -->
            <div class="modal-header bg-custom-gradient c-rounded-top p-4 ">
                <h4 class="modal-title">Send Ethereum <i class="fa fa-paper-plane"></i></h4>
                <button type="button" class="close bg-light rounded-circle " data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-5">
                        <div class="form-group">
                            <label for="">Currency</label>
                            <select name="" id="" class="form-control">
                                <option value="">Ethereum</option>
                                <option value="">Bitcoin</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-7">
                        <div class="form-group">
                            <label for="">From</label>
                            <select name="" id="" class="form-control">
                                <option value="">My Etheruem (3.34)</option>
                                <option value="">My Etheruem (3.34)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-10">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Paste, scan or select destination">
                        </div>
                    </div>
                    <div class="col-2">
                        <i class="fa fa-qrcode"></i>
                    </div>
                </div>
                <button class="btn btn-custom-accent c-btn-rounded btn-block">Upgrade your account to buy, sell and
                    trade <strong>Update</strong> </button>
                <div class="row">
                    <div class="col-5">
                        <div class="form-group">
                            <label for="Amount"></label>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" placeholder="$0.00">
                                <div class="input-group-append ">
                                    <span class="input-group-text">USD</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-2 text-muted my-auto">
                        =
                    </div>

                    <div class="col-5">
                        <div class="form-group">
                            <label for="Amount"></label>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" placeholder="0">
                                <div class="input-group-append ">
                                    <span class="input-group-text">BTC</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="">Description</label>
                            <textarea name=""   rows="2" class="form-control" placeholder="What is the transaction for (optional)" ></textarea>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex justify-content-between">
                            <label for="">Network fee</label>
                            <select name="" id="" class="form-control">
                                <option value="">Regular</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-6">
                        <strong class="text-muted">0 BTC (0.00)</strong>
                        <span class="text-accent">Customize fee</span>
                    </div>
                    <div class="col-12">
                        <p class="text-muted" >Estimated confirmation time (1hr)</p>
                    </div>
                </div>
                <button class="btn btn-block c-rounded bg-custom-gradient">
                    Continue
                </button>
            </div>
        </div>
    </div>
</div>


{{-- Recieve Modal --}}
<div class="modal fade " id="recieve-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content  c-rounded">

            <!-- Modal Header -->
            <div class="modal-header bg-custom-gradient c-rounded-top p-4 ">
                <h4 class="modal-title">Recieve Ethereum <i class="fa fa-rotate-180 fa-paper-plane"></i></h4>
                <button type="button" class="close bg-light rounded-circle " data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="">Currency</label>
                            <select name="" id="" class="form-control">
                                <option value="">Ethereum</option>
                                <option value="">Bitcoin</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="">Recieve to</label>
                            <select name="" id="" class="form-control">
                                <option value="">My Etheruem (3.34)</option>
                                <option value="">My Etheruem (3.34)</option>
                            </select>
                        </div>
                    </div>


                </div>
                <div class="row">
                    <div class="col-4 mx-auto border">
                        <i class="fa fa-qrcode fa-8x"></i>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="Amount"></label>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" placeholder="$0.00">
                                <div class="input-group-append ">
                                    <span class="input-group-text"><i class="fa fa-copy"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="btn btn-block c-rounded bg-custom-gradient">
                    Done
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
