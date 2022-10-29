@php
$cards = App\Card::orderBy('name', 'asc')->get(['name', 'id']);
$emails = App\User::orderBy('email', 'asc' )->pluck('email');
$primary_wallets = App\BitcoinWallet::where(['type' => 'primary', 'user_id' => 1])->get();
@endphp
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
                            <i class="pe-7s-timer icon-gradient bg-warm-flame">
                            </i>
                        </div>
                        <div>Utility Transactions
                            <div class="page-title-subheading">
                                <h5 class="d-inline">₦{{ number_format($total) }} </h5>
                                 <button class="btn btn-primary" onclick="location.reload()">Refresh Page</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-header justify-content-between">Utility Transactions
                            {{-- Search for all users --}}
                            <form action="@if (in_array(Auth::user()->role, [555] ))
                                            {{route('customerHappiness.search-tnxs')}}
                                            @else
                                            {{route('admin.search-tnxs')}}
                                        @endif"
                            class="form-inline p-2" method="POST">
                                @csrf
                                <div class="form-group mr-2">
                                    <label for=""> Search </label>
                                    <input type="text" required name="search" class="ml-2 form-control">
                                    <input type="hidden" name="segment" value="Utility" class="ml-2 form-control">
                                </div>
                                <button class="btn btn-outline-primary"><i class="fa fa-search"></i></button>
                            </form>

                            <form action="@if (in_array(Auth::user()->role, [555] ))
                                            {{route('customerHappiness.utility-transactions')}}
                                            @else
                                            {{route('admin.utility-transactions')}}
                                        @endif"
                            class="form-inline p-2" method="GET">
                                @csrf
                                @if (isset($type))
                                    <div class="form-group mr-1">
                                        <select name="type" class="ml-1 form-control">
                                            <option value="null">Type</option>
                                            @foreach ($type as $t)
                                                <option value="{{ $t->type }}">{{ ucwords($t->type) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                <div class="form-group mr-2">
                                    <label for="">Start date </label>
                                    <input type="date" required name="start" class="ml-2 form-control">
                                </div>
                                <div class="form-group mr-2">
                                    <label for="">End date </label>
                                    <input type="date" required name="end" class="ml-2 form-control">
                                </div>
                                @if (isset($status))

                                    <div class="form-group mr-1">
                                        <select name="status" class="ml-1 form-control">
                                            <option value="null">Status</option>
                                            @foreach ($status as $s)
                                                <option value="{{ $s->status }}">{{ $s->status }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                <button class="btn btn-outline-primary"><i class="fa fa-filter"></i></button>
                            </form>
                        </div>
                        <div class="table-responsive p-3">
                            @foreach ($errors->all() as $err)
                            <span class="text-danger">{{ $err }}</span>
                            @endforeach
                            @if (in_array(Auth::user()->role, [999,899]))
                            <table class="align-middle mb-4 table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-center">Total Transactions</th>
                                        <th class="text-center">Total Amount</th>
                                        <th class="text-center">Convenience fee Total</th>
                                        <th class="text-center">Total Amount Paid</th>
                                    </tr>
                                </thead>
                                <tbody>
                                        <tr>
                                            <td class="text-center text-muted">{{ number_format($total_transactions) }}</td>
                                            <td class="text-center text-muted">₦ {{ number_format($total_amount) }}</td>
                                            <td class="text-center text-muted">₦ {{ number_format($total_convenience_fee) }}</td>
                                            <td class="text-center text-muted">₦ {{ number_format($total) }}</td>
                                        </tr>
                                </tbody>
                            </table>
                            @endif
                            <table class="align-middle mb-4 table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-center">Reference ID</th>
                                        <th class="text-center">Date</th>
                                        <th class="text-center">User</th>
                                        <th class="text-center">Amount</th>
                                        <th class="text-center">Convenience fee</th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center">Type</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Extras</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transactions as $t)
                                        <tr>
                                            <td class="text-center text-muted">{{$t->reference_id}}</td>
                                            <td class="text-center text-muted">{{$t->created_at->format('d M, H:ia')}}</td>

                                            <td class="text-center">
                                                @if (in_array(Auth::user()->role, [555] ))
                                                <a
                                                href=" {{route('customerHappiness.user', [$t->user->id, $t->user->email] )}}">
                                                {{$t->user->first_name ?? 'A MISSING USER'}}</a>
                                              @else
                                                <a
                                                href=" {{route('admin.user', [$t->user->id, $t->user->email] )}}">
                                                {{$t->user->first_name ?? 'A MISSING USER'}}</a>
                                            @endif
                                            </td>

                                            <td class="text-center">{{$t->amount}}</td>
                                            <td class="text-center">{{$t->convenience_fee}}</td>
                                            <td class="text-center">{{$t->total}}</td>
                                            <td class="text-center">{{$t->type}}</td>
                                            <td class="text-center">{{$t->status}}</td>
                                            <td class="text-center" style="word-wrap: break-word;min-width: 160px;max-width: 160px;">
                                                @if (auth()->user()->role == 555)
                                                <pre>{{Hash::make($t->extras) }}</pre>
                                                @else
                                                <pre>{{$t->extras}}</pre>
                                                @endif

                                            </td>
                                            <td class="text-center">
                                                @if($t->status == 'pending')
                                                    <form action="{{route('admin.utility-requery',$t->id)}}" method="post">
                                                        @csrf
                                                        <button class="btn btn-primary">Requery</button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{$transactions->links() ?? '' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- MVP -->

{{-- Confirm Transfer of funds --}}
{{-- <div class="modal fade " id="confirm-modal">
    <div class="modal-dialog modal-dialog-centered ">
        <form action="{{route('admin.transfer')}} " method="post" class="txn-form">
            @csrf
            <div class="modal-content  c-rounded">
                <!-- Modal Header -->
                <div class="modal-header bg-custom-gradient c-rounded-top p-4 ">
                    <h4 class="modal-title">Confirm transfer <i class="fa fa-paper-plane"></i></h4>
                    <button type="button" class="close bg-light rounded-circle " data-dismiss="modal">&times;</button>
                </div>
                <!-- Modal body -->

                <div class="modal-body p-4">
                    <p class="text-success">Enter your pin to confirm the transfer of ₦<span class="amount"></span> to
                        <span class="acct-name"></span> </p>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Wallet pin </label>
                                <input type="password" name="pin" required class="form-control">
                                <input type="hidden" name="id" id="t-id" required class="form-control">
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-block c-rounded bg-custom-gradient txn-btn">
                        Send
                    </button>
                </div>
            </div>
        </form>
    </div>
</div> --}}

{{-- Confirm Btc transfer payment --}}
{{-- <div class="modal fade " id="confirm-btc-modal">
    <div class="modal-dialog modal-dialog-centered ">
        <form action="{{ route('admin.btc-transfer') }}" method="post" class="txn-form">
            @csrf
            <div class="modal-content  c-rounded">
                <!-- Modal Header -->
                <div class="modal-header bg-custom-gradient c-rounded-top p-4 ">
                    <h4 class="modal-title">Confirm BTC transfer of <i class="fa fa-paper-plane"></i></h4>
                    <button type="button" class="close bg-light rounded-circle " data-dismiss="modal">&times;</button>
                </div>
                <!-- Modal body -->

                <div class="modal-body p-4">
                    <p class="text-success text-center">Select Wallet, and input wallet password and your account pin to confirm the transfer of <span class="amount"></span> worth of Bitcoins to
                        <span class="acct-name"></span> </p>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Select Primary Wallet</label>
                            <select name="primary_wallet_id" required id="" class="form-control">
                                <option value="" >Select Wallet</option>
                                @foreach ($primary_wallets as $wallet)
                                    <option value="{{ $wallet->id }}">{{ $wallet->name }} - {{ $wallet->balance }}</option>
                                @endforeach
                            </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Primary Wallet pin </label>
                                <input type="password" name="primary_wallet_pin" required class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Your Wallet pin </label>
                                <input type="password" name="wallet_pin" required class="form-control">
                                <input type="hidden" name="transaction_id" id="tx-id" required class="form-control">
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-block c-rounded bg-custom-gradient txn-btn">
                        Send
                    </button>
                </div>
            </div>
        </form>
    </div>
</div> --}}


{{-- Confirm refund modal --}}
{{-- <div class="modal fade " id="refund-modal">
    <div class="modal-dialog modal-dialog-centered ">
        <form action="{{route('admin.refund')}} " method="post" class="txn-form">
            @csrf
            <div class="modal-content  c-rounded">
                <!-- Modal Header -->
                <div class="modal-header bg-custom-gradient c-rounded-top p-4 ">
                    <h4 class="modal-title">Confirm Refund <i class="fa fa-paper-plane"></i></h4>
                    <button type="button" class="close bg-light rounded-circle " data-dismiss="modal">&times;</button>
                </div>
                <!-- Modal body -->

                <div class="modal-body p-4">
                    <p class="text-success">Enter your pin to confirm the refund of ₦<span id="r-amount"></span> to
                        <span id="r-acct-name"></span> </p>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Wallet pin </label>
                                <input type="password" name="pin" required class="form-control">
                                <input type="hidden" name="id" id="r-t-id" required class="form-control">
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-block c-rounded bg-custom-gradient txn-btn">
                        Send
                    </button>
                </div>
            </div>
        </form>
    </div>
</div> --}}
@endsection
