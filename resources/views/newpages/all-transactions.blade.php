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
                                        <span class="h3 giftcard-text" style="color: #000070;">Transaction History
                                        </span>
                                    </div>
                                    <div class="widget-n" style="justify-content: center; text-align: center;">
                                        <span class="d-block" style="h6 walletbalance-text">Wallet Balance</span>
                                        <span
                                            class="d-block price">{{ Auth::user()->nairaWallet ? '₦'. number_format(Auth::user()->nairaWallet->amount) : 'No naira wallet' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="container">
                        <!-- Card Contents -->
                        <div class="row">
                            <div class="col-12 card mb-5">
                                <div
                                    class="d-flex flex-column flex-lg-row justify-content-between align-items-center my-3 mb-5 mb-lg-0">
                                    <div class="mb-4 mb-lg-0">
                                        <span class="recent_trx_text">Recent Transactions</span>
                                    </div>
                                    <div class="">
                                        <form action="{{ route('user.transactions') }}" method="get">
                                            <div class="d-flex flex-column flex-lg-row justify-content-center align-items-center"
                                                style="width: auto;">
                                                <div class="form-group row">
                                                    <label for="start-date" class="col-form-label mr-1"
                                                        style="color: #000070;font-size: 14px;">Start date</label>
                                                    <div class="">
                                                        <input type="date" class="form-control col-10" name="start_date"
                                                            id="start-date" />
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="start-date" class="col-form-label mr-1"
                                                        style="color: #000070;font-size: 14px;">End date</label>
                                                    <div class="">
                                                        <input type="date" class="form-control col-10" name="end_date"
                                                            id="start-date" />
                                                    </div>
                                                </div>
                                                {{-- <div class="form-group row">
                                                    <label for="start-date" class="col-form-label mr-1"
                                                        style="color: #000070;font-size: 14px;">Asset type</label>
                                                    <div class="">
                                                        <select class="custom-select col-9">
                                                            <option selected>Open this select menu</option>
                                                            <option value="2">Two</option>
                                                            <option value="3">Three</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="start-date" class="col-form-label mr-1"
                                                        style="color: #000070;font-size: 14px;">Start</label>
                                                    <div class="">
                                                        <select class="custom-select col-9">
                                                            <option selected>Open this select menu</option>
                                                            <option value="2">Two</option>
                                                            <option value="3">Three</option>
                                                        </select>
                                                    </div>
                                                </div> --}}
                                                <div class="form-group row">
                                                   <div class="col">
                                                    <button type="submit" class="btn text-white"
                                                    style="background-color: #000070;font-size:12px">Search</button>
                                                   </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="table-responsive ">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr
                                                        style="color: #000070;background: rgba(0, 0, 112, 0.05) !important;">
                                                        <th style="padding:20px;" scope="col">ID</th>
                                                        <th style="padding:20px;" scope="col">ASSET</th>
                                                        <th style="padding:20px;" scope="col">TRADE TYPE</th>
                                                        <th style="padding:20px;" scope="col">VALUE</th>
                                                        <th style="padding:20px;" scope="col">DATE</th>
                                                        <th style="padding:20px;" scope="col">TIME</th>
                                                        <th style="padding:20px;" scope="col">STATUS</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($transactions as $transaction)
                                                    <tr>
                                                        <th scope="row">{{ $transaction->uid }}</th>
                                                        <td>{{ $transaction->asset->name }}</td>
                                                        <td class="trade_type-sell">{{ $transaction->type }}</td>
                                                        <td>
                                                            <span
                                                                class="d-block my-0 py-0">${{ $transaction->amount }}</span>
                                                            <span class="d-block my-0 py-0"
                                                                style="color: #676B87;font-size:12px;">N{{ number_format($transaction->amount_paid) }}</span>
                                                        </td>
                                                        <td>{{ $transaction->created_at->format('M.d Y') }}</td>
                                                        <td>{{ $transaction->created_at->format('h:ia') }}</td>
                                                        <td>
                                                            @switch($transaction->status)
                                                            @case('success')
                                                            <span
                                                                class="status_success">{{ $transaction->status }}</span>
                                                            @break
                                                            @case('waiting')
                                                            <span
                                                                class="status_waiting">{{ $transaction->status }}</span>
                                                            @break
                                                            @case('in progress')
                                                            <span
                                                                class="status_inprogress">{{ $transaction->status }}</span>
                                                            @break
                                                            @case('declined')
                                                            <span
                                                                class="status_declined">{{ $transaction->status }}</span>
                                                            @break
                                                            @case('failed')
                                                            <span
                                                                class="status_declined">{{ $transaction->status }}</span>
                                                            @break
                                                            @default
                                                            <span
                                                                class="status_waiting">{{ $transaction->status }}</span>
                                                            @endswitch
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
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

@endsection
