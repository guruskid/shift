@extends('layouts.app')
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
        @include('layouts.partials.admin')
    </div>

    {{-- Content Starts here --}}
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="app-page-title">
                <div class="page-title-wrapper">
                    <div class="page-title-heading">
                        <div class="page-title-icon">
                            <i class="pe-7s-wallet icon-gradient bg-warm-flame">
                            </i>
                        </div>
                        <div>Tether Wallet Contracts </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <a href="#">
                        <div class="card mb-3 widget-content ">
                            <div class="widget-content-wrapper ">
                                <div class="widget-content-left">
                                    <div class="widget-heading">
                                        <h5>Fee Wallets Balance</h5>
                                        <span>{{number_format((float)$fees_wallet->balance, 8) }}TRX</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="#">
                        <div class="card mb-3 widget-content ">
                            <div class="widget-content-wrapper ">
                                <div class="widget-content-left">
                                    <div class="widget-heading">
                                        <h5>Available Addresses</h5>
                                        <span>{{$addresses }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="row mb-5">
                <div class="col-md-6 mb-5">
                    <form action="{{ route('admin.tether.deploy-contract') }}" method="POST" class="form-inline mb-3">@csrf
                        <select name="count" id="" class="form-control mr-2">
                            <option value="2">2 (Free) </option>
                            <option value="500">500 (Free) </option>
                        </select>
                        <button class="btn btn-primary ">Generate addresses</button>
                    </form>
                    <div class="card card-body">
                        <h5 class="mb-2">Pending Contracts</h5>
                        <ul class="list-group">
                            @foreach ($pending_transactions as $transaction)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ \Str::limit($transaction->hash, 30, '...') }}
                                <div class="btn-group">
                                    <a target="_blank" href="https://tronscan.org/#/transaction/{{ $transaction->hash }}"><button class="btn btn-info">Explorer</button></a>
                                    <a href="{{ route('admin.tether.activate-contract', $transaction->id) }}"><button class="btn btn-primary">Activate</button></a>
                                    {{-- <button class="btn btn-danger">Delete</button> --}}
                                </div>
                              </li>
                            @endforeach
                          </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection
