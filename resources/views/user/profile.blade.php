@php
$banks = App\Bank::all();
$nots = Auth::user()->notifications->take(3);
$naira_balance = 0;
if (Auth::user()->nairaWallet) {
    $naira_balance = Auth::user()->nairaWallet->amount;
}
@endphp

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

            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="m-0 p-4 c-rounded-top  bg-custom text-white">
                        </div>
                        <div class="bg-custom-gradient p-4 ">
                        </div>
                        <div class="card-body pt-0 px-md-5">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="user-balance-box">
                                        <div class="profile-img">
                                            <center>
                                                <img width="150" class="rounded-circle mb-2"
                                                    src="{{asset('storage/avatar/'.Auth::user()->dp)}} " alt="">
                                            </center>
                                            <center>
                                                <button
                                                    class="btn">{{Auth::user()->first_name.' '.Auth::user()->last_name}}</button>
                                            </center>
                                        </div>
                                        <div class="box-1">
                                            <span>WALLET BALANCE</span>
                                            <h4>₦{{number_format($naira_balance)}} </h4>
                                        </div>
                                        {{-- <div class="box-2">
                                            <div class="balances">
                                                <button class="btn btn-success" style="margin-bottom: 30px">
                                                    <div class="media align-items-center ">
                                                        <img src="{{asset('svg/bitcoin.svg')}}">
                                        <div class="media-body ml-3">
                                            BTC
                                            <span class="d-block">$50</span>
                                        </div>
                                    </div>
                                    </button>

                                    <button class="btn btn-success ">
                                        <div class="media align-items-center ">
                                            <img src="{{asset('svg/ethereum.svg')}}">
                                            <div class="media-body ml-3">
                                                ETH
                                                <span class="d-block">$50</span>
                                            </div>
                                        </div>
                                    </button>
                                </div>
                            </div> --}}
                            <div class="box-3">
                                <button class="btn btn-block btn-secondary" data-toggle="modal"
                                    data-target="#update-dp"><i class="fa fa-camera"></i> Update dp
                                </button>
                                <button class="btn btn-block btn-secondary" data-toggle="modal"
                                    data-target="#add-bank"><i class="fa fa-plus"></i> Add Bank Account
                                </button>
                                <button class="btn btn-block btn-secondary" data-toggle="modal"
                                    data-target="#change-password-modal"><i class="fa fa-money-bill"></i> Wallet
                                    password</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9 " style="margin-top: 2rem !important;">
                        <div class="main-card mb-3 card">
                            <div class="card-body">
                                <ul class="nav nav-tabs nav-justified text-uppercase text-white ">
                                    <li class="nav-item"><a data-toggle="tab" href="#tab-eg11-0"
                                            class="active nav-link">Profile</a></li>
                                    <li class="nav-item"><a data-toggle="tab" href="#tab-eg11-1"
                                            class="nav-link">Notifications</a></li>
                                    <li class="nav-item"><a data-toggle="tab" href="#tab-eg11-2"
                                            class="nav-link">Security</a></li>
                                    <li class="nav-item"><a data-toggle="tab" href="#tab-eg11-3" class="nav-link">Verification
                                        </a></li>
                                    <li class="nav-item"><a data-toggle="tab" href="#tab-eg11-4" class="nav-link">Limits
                                        </a></li>
                                </ul>
                                <div class="tab-content">
                                    {{-- Profile --}}
                                    <div class="tab-pane active" id="tab-eg11-0" role="tabpanel">
                                        <div class="tab-pane active" id="tab-eg115-0" role="tabpanel">
                                            <form class="profile-details" id="user-profile-form">
                                                {{ csrf_field() }}
                                                <div class="card-body p-0">
                                                    <table class="table table-responsive-md table-striped">
                                                        <tbody>
                                                            <tr>
                                                                <td>First Name</td>
                                                                <td class="ml-5"><input type="text" name="first_name"
                                                                        value="{{Auth::user()->first_name}}">

                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Last Name</td>
                                                                <td class="ml-5"><input type="text" name="last_name"
                                                                        value="{{Auth::user()->last_name}}">

                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Email</td>
                                                                <td class="ml-5">{{Auth::user()->email}}
                                                                </td>
                                                            </tr>
                                                            @if (Auth::user()->accounts->count() > 0)
                                                            @foreach (Auth::user()->accounts as $a)
                                                            <tr>
                                                                <td>{{$a->bank_name}}</td>
                                                                <td class="ml-5">{{$a->account_name}} ->
                                                                    {{$a->account_number}}
                                                                   {{--  <a href="#" data-toggle="modal"
                                                                        data-target="#edit-bank"
                                                                        onclick="editBank({{$a->id}})">
                                                                        <span class="fa fa-pen"></span>
                                                                    </a> --}}
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                            @else
                                                            <tr>
                                                                <td>Bank Name</td>
                                                                <td class="ml-5">____________>
                                                                    Acc. No. XXXXXXXXXX
                                                                    <a href="#" data-toggle="modal"
                                                                        data-target="#add-bank">
                                                                        <span class="fa fa-plus"></span>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                            @endif
                                                            <tr>
                                                                <td>Phone</td>
                                                                <td class="ml-5"><input type="text" name="phone"
                                                                        value="{{Auth::user()->phone}}">
                                                                    <span class="fa fa-pen"></span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Status</td>
                                                                <td class="ml-5"><input type="text"
                                                                        value="{{ucwords(Auth::user()->status)}}">
                                                                </td>
                                                            </tr>

                                                        </tbody>
                                                    </table>
                                                </div>

                                                <div class="card-footer bg-custom-accent c-rounded-bottom">
                                                    <button class="btn bg-white c-rounded">
                                                        <i class="spinner-border spinner-border-sm" id="s-p"
                                                            style="display: none;"></i>Save</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    {{-- Notifications --}}
                                    <div class="tab-pane" id="tab-eg11-1" role="tabpanel">
                                        <table class="table text-center ">
                                            <thead>
                                                <tr>
                                                    <th>Notification type</th>
                                                    <th>SMS notification</th>
                                                    <th>Email Alert</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Wallet Transactions</td>
                                                    <td>
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox"   {{Auth::user()->notificationSetting->wallet_sms ? 'checked' : '' }} onclick="notSw('w-s')" class="custom-control-input"
                                                                id="w-s">
                                                            <label class="custom-control-label" for="w-s"></label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" {{Auth::user()->notificationSetting->wallet_email ? 'checked' : '' }} onclick="notSw('w-e')" class="custom-control-input"
                                                                id="w-e">
                                                            <label class="custom-control-label" for="w-e"></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Trade transactions</td>
                                                    <td>
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" {{Auth::user()->notificationSetting->trade_sms ? 'checked' : '' }} onclick="notSw('t-s')" class="custom-control-input"
                                                                id="t-s">
                                                            <label class="custom-control-label" for="t-s"></label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" {{Auth::user()->notificationSetting->trade_email ? 'checked' : '' }} onclick="notSw('t-e')" class="custom-control-input"
                                                                id="t-e">
                                                            <label class="custom-control-label" for="t-e"></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    {{-- Password --}}
                                    <div class="tab-pane" id="tab-eg11-2" role="tabpanel">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="card card-body">
                                                    <h4>Reset Account Password</h4>
                                                    <button class="btn btn-custom-accent" data-toggle="modal"
                                                        data-target="#account-password-modal">Reset</button>
                                                </div>
                                            </div>
                                            @if (Auth::user()->nairaWallet)
                                            <div class="col-md-4">
                                                <div class="card card-body">
                                                    <h4>Reset Wallet Password</h4>
                                                    <button class="btn btn-custom-accent" data-toggle="modal"
                                                        data-target="#wallet-password-modal">Reset</button>
                                                </div>
                                            </div>
                                            @endif
                                            <div class="col-md-4">
                                                <div class="card card-body">
                                                    <h4>2FA (comming soon) </h4>
                                                    <button class="btn btn-custom-accent">Set up</button>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    {{-- Verification --}}
                                    <div class="tab-pane" id="tab-eg11-3" role="tabpanel">
                                        <form method="POST" action=" {{route('user.idcard')}}"
                                            enctype="multipart/form-data">
                                            {{ csrf_field() }}
                                            <div class="row ">
                                                <div class="col-md-6">
                                                    <img src=" {{asset('storage/idcards/'.Auth::user()->id_card)}} "
                                                        alt="" class="img-fluid">
                                                </div>
                                                <div class="col-md-6 ">
                                                    <strong>Account Status:
                                                    </strong><span>{{Auth::user()->status}}</span><br>
                                                    <label>Upload valid ID card</label>
                                                    <input type="file" name="id_card" required class="form-control"
                                                        accept="images/*">
                                                </div>
                                            </div>
                                            <div class="card-footer bg-custom-accent mt-3 c-rounded-bottom">
                                                <button class="btn bg-white c-rounded">Upload</button>
                                            </div>
                                        </form>
                                    </div>

                                    {{-- Limits --}}
                                    <div class="tab-pane" id="tab-eg11-4" role="tabpanel">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="card card-body p-5">
                                                    i.
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
    </div>
</div>

@include('layouts.partials.live-feeds')
</div>
</div>
</div>


{{-- Edit Bank details --}}
<div class="modal fade  item-badge-rightm" id="edit-bank" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{route('user.update_bank_details')}} " method="POST" class="mb-3">
                {{ csrf_field() }}
                <div class="modal-body">
                    <div class="form-group">
                        <input type="hidden" readonly name="id" id="e-account-id">
                    </div>
                    <div class="form-group">
                        <label for="">Bank Name</label>
                        <select name="bank_name" class="form-control">
                            <option value="" id="e-bank-name"></option>
                            @foreach ($banks as $b)
                            <option value="{{$b->name}}">{{$b->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Account Name</label>
                        <input type="text" class="form-control" name="account_name" id="e-account-name">
                    </div>
                    <div class="form-group">
                        <label>Account Number</label>
                        <input type="number" class="form-control" name="account_number" id="e-account-number">
                    </div>

                    <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- Add bank account --}}
<div class="modal fade  item-badge-rightm" id="add-bank" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <form id="user-bank-details" class="mb-4">
                    {{ csrf_field() }}
                    <div class="form-row ">
                        <div class="col-md-12">
                            <div class="position-relative form-group">
                                <label>Bank Name</label>
                                <select name="bank_name" id="" class="form-control">
                                    @foreach ($banks as $b)
                                    <option value="{{$b->name}}">{{$b->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="position-relative form-group">
                                <label>Account Name</label>
                                <input type="text" class="form-control" name="account_name">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="position-relative form-group">
                                <label>Account Number</label>
                                <input type="text" class="form-control" name="account_number">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="mt-2 btn btn-outline-primary">
                        <i class="spinner-border spinner-border-sm" id="s-b" style="display: none;"></i>
                        Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Add image --}}
<div class="modal fade  item-badge-rightm" id="update-dp" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <form method="POST" action="{{route('user.dp')}}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <input type="file" name="dp" class="form-control" accept="images/*">
                    </div>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Change wallet Password --}}
<div class="modal fade " id="wallet-password-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content  c-rounded">

            <!-- Modal Header -->
            <div class="modal-header bg-custom-gradient c-rounded-top p-4 ">
                <h4 class="modal-title">Wallet password <i class="fa fa-rotate-180 fa-paper-plane"></i></h4>
                <button type="button" class="close bg-light rounded-circle " data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <form action="{{route('user.update-naira-password')}}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="">Current account password</label>
                                <input type="password" class="form-control" required name="old_password"  >
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="">New wallet pin</label>
                                <input type="password" class="form-control" required name="new_password" minlength="4" maxlength="4" >
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="">Confirm wallet pin</label>
                                <input type="password" class="form-control" required name="new_password_confirmation">
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-block c-rounded bg-custom-gradient">
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- Account password modal --}}
<div class="modal fade " id="account-password-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content  c-rounded">

            <!-- Modal Header -->
            <div class="modal-header bg-custom-gradient c-rounded-top p-4 ">
                <h4 class="modal-title">Account Password <i class="fa fa-rotate-180 fa-paper-plane"></i></h4>
                <button type="button" class="close bg-light rounded-circle " data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <form action="{{route('user.change_password')}}" class="ml-3" method="POST">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label>Old Password</label>
                        <input type="password" class="form-control" name="old_password" required>
                    </div>
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" class="form-control form-control" name="new_password" required>
                    </div>

                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input name="new_password_confirmation" type="password" class="form-control" required>
                    </div>
                    <button class="btn btn-block c-rounded bg-custom-gradient">
                        Update Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
