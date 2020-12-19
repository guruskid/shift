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
            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="m-0 p-3 c-rounded-top  bg-custom text-white">
                            <strong>Wallets</strong>
                        </div>
                        <div class="bg-custom-gradient p-4 ">
                        </div>
                        <div class="container-fluid">
                            @if (count($errors) > 0)
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li class="text-warning">{{ $error }}</li>
                                @endforeach
                            </ul>
                            @endif
                            <div class="row">
                                @if ($naira == 0)
                                <div class="col-md-4 col-lg-3 col-6 col-sm-6 my-3">
                                    <div class="card card-body text-center text-custom py-5 ">
                                        <p>You dont have a Naira wallet yet.</p>
                                        <button data-toggle="modal" data-target="#new-naira-wallet"
                                            class="btn portfolio-btn">Create Naira Wallet</button>
                                    </div>
                                </div>
                                @else

                                <div class="col-md-4 col-lg-3 col-6 col-sm-6 my-3">
                                    <div class="card card-body text-center text-custom">
                                        <img src="{{asset('svg/naira.svg')}}" style="height: 40px; width: 40px"
                                            class="align-self-center">
                                        <h4 class="mb-0 mt-3"><strong>Dantown Wallet</strong></h4>
                                        <p>₦{{number_format($nw->amount)}} </p>
                                        <a href=" {{route('user.naira-wallet')}}"><button class="btn portfolio-btn">Go
                                                to wallet</button></a>
                                    </div>
                                </div>
                                @endif

                                @if (!Auth::user()->bitcoinWallet)
                                <div class="col-md-4 col-lg-3 col-6 col-sm-6 my-3">
                                    <div class="card card-body text-center text-custom py-5 ">
                                        <p>You dont have a Bitcoin wallet yet.</p>
                                        <button data-toggle="modal" data-target="#new-bitcoin-wallet"
                                            class="btn portfolio-btn">Create Bitcoin Wallet</button>
                                    </div>
                                </div>
                                @else
                                <div class="col-md-4 col-lg-3 col-6 col-sm-6 my-3">
                                    <div class="card card-body text-center text-custom">
                                        <img src="{{asset('svg/naira.svg')}}" style="height: 40px; width: 40px"
                                            class="align-self-center">
                                        <h4 class="mb-0 mt-3"><strong>{{ Auth::user()->bitcoinWallet->name }}</strong></h4>
                                        <p>BTC {{Auth::user()->bitcoinWallet->balance}} </p>
                                        <a href=" {{route('user.bitcoin-wallet')}}"><button class="btn portfolio-btn">Go
                                                to wallet</button></a>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @include('layouts.partials.live-feeds')
        </div>
    </div>
</div>


{{-- Naira wallet password --}}
<div class="modal fade " id="new-naira-wallet">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content  c-rounded">

            <!-- Modal Header -->
            <div class="modal-header bg-custom-gradient c-rounded-top p-4 ">
                <h4 class="modal-title">New Naira Wallet </h4>
                <button type="button" class="close bg-light rounded-circle " data-dismiss="modal">&times;</button>
            </div>

            <form action="{{route('user.create-naira')}}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="">Wallet Password (4 digits)</label>
                                <input type="password" class="form-control wallet-pin" required name="password" minlength="4" maxlength="4" placeholder="- - - -" >
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="">Confirm password</label>
                                <input type="password" class="form-control wallet-pin" required name="password_confirmation" placeholder="- - - -" >
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-block c-rounded bg-custom-gradient">
                        Create wallet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Bitcoin Wallet --}}
<div class="modal fade " id="new-bitcoin-wallet">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content  c-rounded">

            <!-- Modal Header -->
            <div class="modal-header bg-custom-gradient c-rounded-top p-4 ">
                <h4 class="modal-title">New Naira Wallet </h4>
                <button type="button" class="close bg-light rounded-circle " data-dismiss="modal">&times;</button>
            </div>

            <form action="{{route('user.bitcoin-wallet.create')}}" method="POST">@csrf
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="">Wallet Password (4) </label>
                                <input type="password" class="form-control" required name="wallet_password" minlength="4" maxlength="4"  >
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="">Confirm password</label>
                                <input type="password" class="form-control" required name="wallet_password_confirmation" >
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-block c-rounded bg-custom-gradient">
                        Create wallet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
