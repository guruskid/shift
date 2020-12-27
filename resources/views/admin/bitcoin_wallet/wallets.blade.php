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
                            <i class="pe-7s-users icon-gradient bg-sunny-morning">
                            </i>
                        </div>
                        <div class="widget-subheading">
                            All Wallets <br>
                            @if (in_array(Auth::user()->role, [999, 889]))
                            <button data-toggle="modal" data-target="#new-wallet-modal" class="btn btn-primary">Create HD Wallet</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-5 mx-auto mb-3">
                    <div class="card card-body">
                        <h5>Bitcoin Settings</h5>
                        <form action="{{ route('admin.set-bitcoin-charge') }}" method="POST" >@csrf
                            <div class="form-group">
                                <label for="">Send transaction fees</label>
                                <input type="number" step="any" name="bitcoin_charge" value="{{ $bitcoin_charge->value ?? '' }}" class="form-control">
                            </div>
                            <button class="btn btn-primary">Save</button>
                        </form>
                    </div>
                </div>
                <div class="col-md-12">
                    @foreach ($errors->all() as $err)
                    <p class="text-danger">{{ $err }}</p>
                    @endforeach
                    <div class="main-card mb-3 pb-3 card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="">
                                Recent Bitcoin Wallets
                            </div>
                        </div>
                        <div class="table-responsive p-3">
                            <table class="align-middle mb-0 table table-borderless table-striped table-hover transactions-table">
                                <thead>
                                    <tr>
                                        <th >#</th>
                                        <th >User</th>
                                        <th >Path</th>
                                        <th >Address</th>
                                        <th >Type</th>
                                        <th >Primary Wallet</th>
                                        <th >Balance</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($wallets as $wallet)
                                    <tr>
                                        <td class="text-muted">{{$wallet->id}}</td>
                                        <td >{{ucwords($wallet->user->first_name)}}</td>
                                        <td >{{$wallet->path}}</td>
                                        <td >{{$wallet->address}}</td>
                                        <td >{{ $wallet->type }} </td>
                                        <td >{{$wallet->primaryWallet->name ?? ''}}</td>
                                        <td >{{ number_format((float)$wallet->balance, 8 ) }} </td>
                                        <td>
                                            <button class="btn btn-group">
                                                <button class="btn btn-primary">View</button>
                                                <button class="btn btn-primary">View</button>
                                            </button>
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

<div class="modal fade " id="new-wallet-modal">
    <div class="modal-dialog ">
        <form action="{{ route('admin.bitcoin-wallet.create') }}" method="post" >
            @csrf
            <div class="modal-content  c-rounded">
                <!-- Modal Header -->
                <div class="modal-header bg-custom-gradient c-rounded-top p-4 ">
                    <h4 class="modal-title">Confirm Action <i class="fa fa-paper-plane"></i></h4>
                    <button type="button" class="close bg-light rounded-circle " data-dismiss="modal">&times;</button>
                </div>
                <!-- Modal body -->

                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">New Wallet Password </label>
                                <input type="password" minlength="10" name="wallet_password" required class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="">Confirm Wallet Password </label>
                                <input type="password" name="wallet_password_confirmation" required class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="">Account Login Password </label>
                                <input type="text" name="account_password" required class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="">Wallet Name </label>
                                <input type="text" name="name" required class="form-control">
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-block c-rounded bg-custom-gradient txn-btn">
                        Create Wallet
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
