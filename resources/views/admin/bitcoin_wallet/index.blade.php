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
                        <div>Bitcoin Wallet
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- <div class="col-md-3">
                    <div class="card mb-3 widget-content ">
                        <a href="{{ route('live-balance.transactions') }}">
                <div class="widget-content-wrapper ">
                    <div class="widget-content-left">
                        <div class="widget-heading">
                            <h5>Live Balance</h5>
                            <span>{{number_format((float)$live_balance, 8) }}BTC</span>
                        </div>
                    </div>
                </div>
                </a>
            </div>
        </div> --}}
        <div class="col-md-3">
            <a href="{{route('admin.bitcoin.hd-wallets')}}">
                <div class="card mb-3 widget-content ">
                    <div class="widget-content-wrapper ">
                        <div class="widget-content-left">
                            <div class="widget-heading">
                                <h5>HD Wallets Balance</h5>
                                <span>{{number_format((float)$hd_wallet->balance, 8) }}BTC</span>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <div class="card mb-3 widget-content">
                <a href="{{ route('admin.bitcoin.charges') }}">
                    <div class="widget-content-wrapper ">
                        <div class="widget-content-left">
                            <div class="widget-heading">
                                <h5>Charges</h5>
                                <span>{{number_format((float)$charges_wallet->balance, 8) }}BTC</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card mb-3 widget-content">
                <a href="{{ route('admin.service-fee') }}">
                    <div class="widget-content-wrapper ">
                        <div class="widget-content-left">
                            <div class="widget-heading">
                                <h5>Service fee</h5>
                                <span>{{number_format((float)$service_wallet->balance, 8) }}BTC</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card mb-3 widget-content">
                <a href="{{ route('admin.btc.migration') }}">
                    <div class="widget-content-wrapper ">
                        <div class="widget-content-left">
                            <div class="widget-heading">
                                <h5>Migration Wallet</h5>
                                <span>{{number_format((float)$migration_wallet->balance, 8) }}BTC</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-md-6">
            <div class="card card-body">
                <h5>Send</h5>
                <form action="{{ route('admin.btc.send') }}" method="post">
                    @csrf
                    <div class="form-group">
                        <label for="">Choose wallet </label>
                        <select name="wallet" class="form-control">
                            <option value="hd">{{ $hd_wallet->name }}</option>
                            <option value="{{ $charges_wallet->id }}">{{ $charges_wallet->name }}</option>
                            <option value="{{ $service_wallet->id }}">{{ $service_wallet->name }}</option>
                            <option value="{{ $migration_wallet->id }}">{{ $migration_wallet->name }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Address </label>
                        <select name="address" class="form-control">
                            <option value="">Select Address</option>
                            @foreach ($address as $a)
                                <option value="{{ $a->address }}">{{ $a->address}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Amount (BTC)</label>
                        <input type="number" step="any" name="btc" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">Wallet Pin </label>
                        <input type="password" name="pin" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">Account Login Password </label>
                        <input type="password" name="password" required class="form-control">
                    </div>
                    <button class="btn btn-block c-rounded bg-custom-gradient txn-btn">
                        Send
                    </button>
                </form>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-body mb-3">
                <h5>Hd Wallet</h5>
                <p>{{ $hd_wallet->address }}</p>
            </div>

            <div class="card card-body mb-3">
                <h5>Charges Wallet</h5>
                <p>{{ $charges_wallet->address }}</p>
            </div>

            <div class="card card-body mb-3">
                <h5>Service Wallet</h5>
                <p>{{ $service_wallet->address }}</p>
            </div>

            <div class="card card-body mb-3">
                <h5>Migration Wallet</h5>
                <p>{{ $migration_wallet->address }}</p>
            </div>
        </div>
    </div>

    {{-- //Transactions --}}
    <div class="row">
        <div class="col-md-12">
            <div class="main-card mb-3 card">
                <div class="card-header justify-content-between ">
                    All Transactions
                   {{--  <form action="{{route('admin.wallet-transactions.sort.by.date')}}" class="form-inline p-2"
                        method="POST">
                        @csrf
                        <div class="form-group mr-2">
                            <label for="">Start date </label>
                            <input type="date" name="start" class="ml-2 form-control">
                        </div>
                        <div class="form-group mr-2">
                            <label for="">End date </label>
                            <input type="date" name="end" class="ml-2 form-control">
                        </div>
                        <button class="btn btn-outline-primary"><i class="fa fa-filter"></i></button>
                    </form> --}}
                </div>
                <div class="table-responsive p-3">

                    <table class="align-middle mb-4 table table-bordered table-striped transactions-table ">
                        <thead>
                            <tr>
                                {{-- <th>ID</th> --}}
                                <th>Trans. Type</th>
                                <th>Amount</th>
                                <th>USD</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transactions as $key => $t)
                            <tr>
                                {{-- <td>{{$key += 1}} </td> --}}
                                <td>{{ $t->transactionType }}</td>
                                <td>{{ number_format((float) $t->amount, 8) }}</td>
                                <td>{{ number_format($t->marketValue->amount, 2) }}</td>
                                <td>{{ $t->created->format('d M Y h:ia') }}</td>
                                <td>Completed</td>
                                <td class="transaction_content">
                                    @if (isset($t->txId))
                                        <a target="_blank" href="https://blockexplorer.one/btc/mainnet/tx/{{ $t->txId }}" class="">Explorer</a>

                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>

                    </table>
                    {{-- {{$transactions->links()}} --}}
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
@endsection
