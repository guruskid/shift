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
                            <i class="pe-7s-graph1 icon-gradient bg-warm-flame">
                            </i>
                        </div>
                        <div>Charges
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card mb-2 widget-content ">
                        <div class="widget-content-wrapper">
                            <div class="widget-content-left">
                                <div class="widget-heading">
                                    <h4>Charges</h4>
                                    <h5>{{number_format((float)$charges, 8) }}BTC</h5>{{--
                                    <button class="btn btn-danger" data-toggle="modal"
                                        data-target="#confirm-transfer-modal">Transfer</button> --}}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card card-body">
                        <form action="{{ route('admin.bitcoin.transfer-charges') }}" method="POST">@csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Txn Fee</label>
                                        <input type="hidden" value="bitcoin charges" name="wallet">
                                        <input type="number" step="any" name="fees"
                                            value="{{ $fees ?? '' }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Amount</label>
                                        <input type="number" step="any" name="amount" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Address</label>
                                        <input type="text"  name="address" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Your Wallet Pin</label>
                                        <input type="password" name="pin" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <button class="btn btn-primary">Transfer</button>
                        </form>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card card-body py-3">
                        <h5>Bitcoin Settings</h5>
                        <form action="{{ route('admin.set-bitcoin-charge') }}" method="POST">@csrf
                            <div class="form-group">
                                <label for="">Send transaction charge</label>
                                <input type="number" step="any" name="bitcoin_charge"
                                    value="{{ $bitcoin_charge->value ?? '' }}" class="form-control">
                            </div>

                            <div class="form-group">
                                <label for="">Buy bitcoin charge (%)</label>
                                <input type="number" step="any" name="bitcoin_buy_charge"
                                    value="{{ $bitcoin_buy_charge->value ?? '' }}" class="form-control">
                            </div>

                            <div class="form-group">
                                <label for="">Sell bitcoin charge (%)</label>
                                <input type="number" step="any" name="bitcoin_sell_charge"
                                    value="{{ $bitcoin_sell_charge->value ?? '' }}" class="form-control">
                            </div>
                            <button class="btn btn-primary">Save</button>
                        </form>
                    </div>
                </div>

            </div>
            <div class="row">

                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                            <table class="mb-2 transactions-table table">
                                <thead>
                                    <tr>
                                        <th class="text-center">Id</th>
                                        <th class="text-center">Type</th>
                                        <th class="text-center">Amount</th>
                                        <th class="text-center">Charge (BTC)</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transactions as $t)
                                    <tr>
                                        <td class="text-center">{{$t->id}}</td>
                                        <td class="text-center">{{$t->type->name}}</td>
                                        <td class="text-center">
                                            {{$t->debit == 0 ? number_format((float)$t->credit, 8) : number_format((float)$t->debit, 8)}}
                                        </td>
                                        <td class="text-center">{{number_format((float)$t->charge, 8)}}</td>
                                        <td class="text-center">{{ucwords($t->status)}} </td>
                                        <td class="text-center">{{$t->created_at->format('d M y ')}}</td>
                                    </tr>
                                    @endforeach

                                </tbody>
                                {{$transactions->links()}}
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
