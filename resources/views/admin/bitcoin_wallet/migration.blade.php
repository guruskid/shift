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
                        <div>Migration Wallet
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                            <ul class="nav nav-tabs nav-justified">
                                <li class="nav-item"><a data-toggle="tab" href="#tab-eg11-0" class="active nav-link">Pending</a></li>
                                <li class="nav-item"><a data-toggle="tab" href="#tab-eg11-1" class="nav-link">Transactions</a> </li>
                            </ul>
                            <div class="tab-content">
                                {{-- Sell TXNS --}}
                                <div class="tab-pane active" id="tab-eg11-0" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="mb-2 transactions-table table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Id</th>
                                                    <th>User</th>
                                                    <th>BTC</th>
                                                    <th>Status</th>
                                                    <th>Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($pending as $transaction)
                                                <tr>
                                                    <td>{{ $transaction->id }}</td>
                                                    <td>{{ $transaction->user->first_name }}</td>
                                                    <td>{{ number_format((float)$transaction->amount, 8) }}</td>
                                                    <td>{{ $transaction->status }}</td>
                                                    <td>{{ $transaction->created_at->format('d m y, h:ia') }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                {{-- Buy TXNS --}}
                                <div class="tab-pane" id="tab-eg11-1" role="tabpanel">
                                    <div class="table-responsive">
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
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-body">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
