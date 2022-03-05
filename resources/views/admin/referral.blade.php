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
                                {{-- <h5 class="d-inline">₦{{ number_format($total) }} </h5> --}}
                                 <button class="btn btn-primary" onclick="location.reload()">Refresh Page</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-header justify-content-between">Referral settings</div>
                        <div class="card-body">
                            <div class="form">
                                    <div class="row">
                                        @if(isset($referralSettings[0]))
                                        <div class="col-md-4">
                                           <form method="POST" action="{{route('admin.change_referral_status')}}">@csrf
                                                <div class="form-group">
                                                    <label>Referral status <badge class="badge {{($referralSettings[0]->ref_status == 1) ? 'badge-danger' : 'badge-secondary'}} badge-sm">{{($referralSettings[0]->ref_status == 1) ? 'ON' : 'OFF'}}</badge> </label><br>
                                                    <input type="hidden" value="{{$referralSettings[0]->id}}" name="id">
                                                    <input type="hidden" value="{{($referralSettings[0]->ref_status == 1) ? '0' : '1'}} " name="ref_status" class="form-control">
                                                    <hr>
                                                    <button class="btn  {{($referralSettings[0]->ref_status == 1) ? 'btn-secondary' : 'btn-danger'}}  btn-md"> {{($referralSettings[0]->ref_status == 1) ? 'Turn Off' : 'Turn On'}} </button>
                                                </div>
                                           </form>
                                        </div>

                                        <div class="col-md-4 border-primary">
                                            <form action="{{route('admin.set_referral_percentage')}}" method="POST">@csrf
                                                <div class="form-group">
                                                    <label>Referral percentage</label><br>
                                                    <input type="hidden" value="{{$referralSettings[0]->id}}" name="id">
                                                    <input type="text" value="{{$referralSettings[0]->ref_percent}}" name="percent" class="form-control">
                                                    <input type="date"  name="date" class="form-control">
                                                </div>
                                                <div class="form-group">
                                                    <button class="btn btn-primary btn-md">Update</button>
                                                </div>
                                            </form>
                                        </div>
                                        @else
                                        <div class="col-md-4">
                                            <form method="POST" action="{{route('admin.set_referral')}}">@csrf
                                                 <div class="form-group">
                                                     <label>Referral status <badge class="badge badge-secondary badge-sm">OFF</badge> </label><br>
                                                     <input type="hidden" required value="1" name="ref_status" class="form-control">
                                                 </div>
                                         </div>
                                         <div class="col-md-4 border-primary">
                                                 <div class="form-group">
                                                     <label>Referral percentage</label><br>
                                                     <input type="text" required name="percent" class="form-control">
                                                 </div>
                                                 <div class="form-group">
                                                     <button class="btn btn-danger btn-md">Turn On</button>
                                                 </div>
                                             </form>
                                         </div>
                                        @endif
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>
<!-- MVP -->
{{-- Edit transactions Modal --}}
<div class="modal fade  item-badge-rightm" id="edit-transac" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{route('admin.edit_transaction')}} " method="POST" class="mb-3">
                {{ csrf_field() }}
                <div class="modal-header">
                    <div class="media">
                        <div class="media-body">
                            <h4 class="media-heading" id="e_email">User Email</h4>
                        </div>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <input type="hidden" readonly name="id" id="e_id">
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="">Card</label>
                                <select name="card_id" class="form-control">
                                    <option value="" id="e_card"></option>
                                    @foreach ($cards as $card)
                                    <option value="{{$card->id}}"> {{ ucfirst($card->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="form-group">
                                <label for="">Country</label>
                                <select name="country" class="form-control">
                                    <option value="" id="e_country"></option>
                                    <option value="USD">USD</option>
                                    <option value="EUR">EUR</option>
                                    <option value="GBP">GBP</option>
                                    <option value="AUD">AUD</option>
                                    <option value="CAD">CAD</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="">Unit</label>
                                <input type="text" placeholder="Value" id="e_amount" class="form-control" name="amount">
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="form-group">
                                <label for="">Cash Value</label>
                                <input type="text" placeholder="Amount paid" id="e_amount_paid" class="form-control"
                                    name="amount_paid">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
<!-- ///////////// WORK IN PROGRESS ////////////// -->
                            <div class="form-group">
                                <label for="">Status</label>
                                <select onchange="feedback_status()" id="f_status" name="status" class="form-control">
                                    <option value="" id="e_status"></option>
                                    @if (in_array(Auth::user()->role, [889, 777, 999]))
                                    <option value="success">Success</option>
                                    @endif
                                    <option value="approved">Approved (cleared to pay)</option>
                                    <option value="waiting">Waiting</option>
                                    <option value="in progress">In progress</option>
                                    <option value="failed">Failed</option>
                                    <option value="declined">Declined</option>
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="">Transac Type</label>
                                <select name="trade_type" class="form-control">
                                    <option value="" id="e_trade_type"></option>
                                    <option value="buy">Buy</option>
                                    <option value="sell">Sell</option>
                                </select>
                            </div>
                        </div>
<!-- //////////////////////////////////// -->
                        <div class="d-none col-12" id="yfailed">
                            <div class="form-group">
                            <label for="feedback">Feedback</label>
                                <select name="failfeedbackstatus" class="form-control">
                                    <option value="Your card was used">Your card was used</option>
                                </select>
                            </div>
                        </div>
                        <div class="d-none col-12" id="ydeclined">
                            <div class="form-group">
                            <label for="feedback">Feedback</label>
                                <select name="declinefeedbackstatus" class="form-control">
                                    <option value="Your card/code was invalid">Your card/code was invalid</option>
                                    <option value="The card/code was not clear"> The card/code was not clear  </option>
                                    <option value="Your card/code needed more info"> Your card/code needed more info </option>
                                    <option value="Multiple transaction was opened"> Multiple transaction was opened </option>
                                    <option value="No image was uploaded">No image was uploaded</option>
                                </select>
                            </div>
                        </div>
<!-- /////////////////////////////////////// -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Confirm Transfer of funds --}}
<div class="modal fade " id="confirm-modal">
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
</div>

{{-- Confirm Btc transfer payment --}}
<div class="modal fade " id="confirm-btc-modal">
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
</div>


{{-- Confirm refund modal --}}
<div class="modal fade " id="refund-modal">
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
</div>
@endsection
