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
                        <div>{{ $cur }} Transactions
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card card-body mb-3">
                        <table class="table ">
                            <thead>
                                <tr>
                                    <th>No. Trades</th>
                                    <th>Total BTC</th>
                                    <th>Total USD</th>
                                    <th>Total Naira</th>
                                    <th>AVG. BTC Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $sell_transactions->count() }} </td>
                                    <td>{{ number_format((float)$sell_btc, 8) }} BTC</td>
                                    <td>${{ number_format($sell_usd) }}</td>
                                    <td>₦{{ number_format($sell_transactions->sum('amount_paid')) }}</td>
                                    <td>${{ number_format($sell_average) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                           <form action="{{ route('admin.crypto-summary-txns.sort', $card_id) }}" method="post">@csrf
                                <div class="form-inline mb-3">
                                    <label class="mr-2">Start</label>
                                    <input type="datetime-local" name="start" class="form-control mr-4">

                                    <label class="mr-2">End</label>
                                    <input type="datetime-local" name="end" class="form-control mr-4">

                                    <button class="btn btn-primary">Sort</button>
                                </div>
                           </form>
                            <ul class="nav nav-tabs nav-justified">
                                <li class="nav-item"><a data-toggle="tab" href="#tab-eg11-0"
                                        class="active nav-link">Sell Transactions</a></li>
                                <li class="nav-item"><a data-toggle="tab" href="#tab-eg11-1" class="nav-link">Buy Transactions</a>
                                </li>
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
                                                    <th>Type</th>
                                                    <th>{{ $cur }}</th>
                                                    <th>USD</th>
                                                    <th>NGN</th>
                                                    <th>Rate</th>
                                                    <th>Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($sell_transactions as $transaction)
                                                <tr>
                                                    <td>{{ $transaction->id }}</td>
                                                    <td>{{ $transaction->user->first_name }}</td>
                                                    <td>{{ $transaction->type }}</td>
                                                    <td>{{ number_format((float)$transaction->quantity, 5) }}</td>
                                                    <td>${{ number_format($transaction->amount, 2) }}</td>
                                                    <td>₦{{ number_format($transaction->amount_paid) }}</td>
                                                    <td>${{ number_format($transaction->card_price, 3) }}</td>
                                                    <td>{{ $transaction->created_at->format('d m y, h:ia') }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="10" ></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Total {{ $cur }}</strong></td>
                                                    <td>{{ number_format((float)$sell_btc, 8) }} {{ $cur }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Total USD</strong></td>
                                                    <td>${{ number_format($sell_usd, 4) }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Average {{ $cur }} Price</strong></td>
                                                    <td>${{ number_format($sell_average, 4) }}</td>
                                                </tr>
                                            </tfoot>


                                        </table>
                                    </div>
                                </div>
                                {{-- Buy TXNS --}}
                                <div class="tab-pane" id="tab-eg11-1" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="mb-2 transactions-table table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Id</th>
                                                    <th>User</th>
                                                    <th>Type</th>
                                                    <th>{{ $cur }}</th>
                                                    <th>USD</th>
                                                    <th>NGN</th>
                                                    <th>Rate</th>
                                                    <th>Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($buy_transactions as $transaction)
                                                <tr>
                                                    <td>{{ $transaction->id }}</td>
                                                    <td>{{ $transaction->user->first_name }}</td>
                                                    <td>{{ $transaction->type }}</td>
                                                    <td>{{ number_format((float)$transaction->quantity, 8) }}</td>
                                                    <td>${{ number_format($transaction->amount) }}</td>
                                                    <td>₦{{ number_format($transaction->amount_paid) }}</td>
                                                    <td>${{ number_format($transaction->card_price) }}</td>
                                                    <td>{{ $transaction->created_at->format('d m y, h:ia') }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="10" ></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Total {{ $cur }}</strong></td>
                                                    <td>{{ number_format((float)$buy_btc, 8) }} {{ $cur }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Total USD</strong></td>
                                                    <td>${{ number_format($buy_usd) }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Average {{ $cur }} Price</strong></td>
                                                    <td>${{ number_format($buy_average) }}</td>
                                                </tr>
                                            </tfoot>


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
