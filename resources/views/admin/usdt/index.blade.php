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
                        <div>TRC20 USDT Wallet <br>
                            <a class="btn btn-primary" href="{{ route('admin.tether.settings') }}">Settings</a>
                            <a class="btn btn-warning" href="{{ route('admin.crypto-summary', 7) }}">Summary</a>
                            <a class="btn btn-secondary" href="{{ route('admin.tether.contracts') }}">Smart Contracts
                                ({{ $addresses }})</a>
                        </div>
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
                                        <h5>HD Wallets Balance</h5>
                                        <span>{{number_format((float)$hd_wallet->balance, 4) }}USDT</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <div class="card mb-3 widget-content">
                        <a href="#">
                            <div class="widget-content-wrapper ">
                                <div class="widget-content-left">
                                    <div class="widget-heading">
                                        <h5>Charges</h5>
                                        <span>{{number_format((float)$charges_wallet->balance, 4) }}USDT</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card mb-3 widget-content">
                        <a href="#">
                            <div class="widget-content-wrapper ">
                                <div class="widget-content-left">
                                    <div class="widget-heading">
                                        <h5>Service fee</h5>
                                        <span>{{number_format((float)$service_wallet->balance, 4) }}USDT</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card mb-3 widget-content">
                        <a href="#">
                            <div class="widget-content-wrapper ">
                                <div class="widget-content-left">
                                    <div class="widget-heading">
                                        <h5>Blockchain fee</h5>
                                        <span>{{number_format((float)$blockchain_fee_wallet->balance, 4) }}TRX</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <div class="row mb-5">
                <div class="col-md-6 mb-5">
                    <div class="card card-body">
                        @foreach ($errors->all() as $err)
                        <p class="text-danger text-center">{{ $err }}</p>
                        @endforeach
                       <div class="d-flex justify-content-between">
                        <h5>Send</h5>
                        <button data-toggle="modal" data-target="#add-address" class="btn btn-primary">Add address</button>
                       </div>
                        <form action="{{ route('admin.usdt.send') }}" method="post"> @csrf
                            <div class="form-group">
                                <label for="">Choose wallet </label>
                                <select name="wallet" class="form-control">
                                    <option value="hd">{{ $hd_wallet->name }}</option>
                                    <option value="{{ $charges_wallet->id }}">{{ $charges_wallet->name }}</option>
                                    <option value="{{ $service_wallet->id }}">{{ $service_wallet->name }}</option>
                                    {{-- <option value="{{ $fees_wallet->id }}">{{ $fees_wallet->name }}</option> --}}
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="">Address</label>
                                <select name="address" class="form-control">
                                    <option value="">Select Address</option>
                                    @foreach ($address as $a)
                                    <option value="{{ $a->address }}">{{ $a->address}}</option>
                                    @endforeach
                                </select>
                                {{-- <input type="text" name="address" id="address" required class="form-control"> --}}
                            </div>
                            <div class="form-group">
                                <label for="">Amount (USDT)</label>
                                <input type="number" step="any" name="amount" id="amount" required class="form-control">
                                <small class="text-primary"><strong>Txn Fee</strong> <span id="fee">0</span> </small>
                            </div>
                            <div class="form-group">
                                <label for="">Wallet Pin </label>
                                <input type="password" name="pin" required class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="">Account Login Password </label>
                                <input type="password" name="password" required class="form-control">
                            </div>
                            <button class="btn btn-block c-rounded bg-custom-gradient txn-btn" id="send-btn">
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
                        <h5>Fees Wallet</h5>
                        <p>{{ $blockchain_fee_wallet->address }}</p>
                    </div>
                </div>
            </div>

            {{-- //Transactions --}}
            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-header justify-content-between ">
                            All Transactions

                        </div>
                        <div class="table-responsive p-3">

                            <table class="align-middle mb-4 table table-bordered table-striped transactions-tabl ">
                                <thead>
                                    <tr>
                                        <th>ID</th>
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
                                        <td>{{$key += 1}} </td>
                                        <td>{{ $t->transactionType }}</td>
                                        <td>{{ number_format((float) $t->amount, 8) }}</td>
                                        <td>{{ number_format($t->marketValue->amount, 2) }}</td>
                                        <td>{{ $t->created->format('d M Y h:ia') }}</td>
                                        <td>Completed</td>
                                        <td class="transaction_content">
                                            @if (isset($t->txId))
                                            <a target="_blank"
                                                href="https://tronscan.org/#/transaction/{{ $t->txId }}"
                                                class="">Explorer</a>

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

<div class="modal fade" id="add-address">
    <div class="modal-dialog  ">
        <form action="{{route('admin.address')}}" id="freeze-form" method="post">
            @csrf
            <div class="modal-content  c-rounded">
                <!-- Modal Header -->
                <div class="modal-header bg-custom-gradient c-rounded-top p-4 ">
                    <h4 class="modal-title">Add Address <i class="fa fa-paper-plane"></i></h4>
                    <button type="button" class="close bg-light rounded-circle " data-dismiss="modal">&times;</button>
                </div>
                <!-- Modal body -->

                <div class="modal-body p-4">
                    {{-- <p class="text-success">Add Address</p> --}}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Crypto Currency</label>
                                <select name="crypto" class="form-control">
                                    <option value="">Select Address</option>
                                    @foreach ($crypto_currencies as $a)
                                    <option value="{{ $a->id }}">{{ $a->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="">Address </label>
                                <input type="text" name="address" id="addressModal" required class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="">Pin </label>
                                <input type="password" name="pin" required class="form-control">
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-block c-rounded bg-custom-gradient txn-btn" id="buttonModal">
                        Confirm
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
