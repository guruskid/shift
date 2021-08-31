@php
$countries = App\Country::orderBy('phonecode', 'asc')->get();
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

            <div id="content" class="main-content">
                <div class="layout-px-spacing">
                    <div class="row layout-top-spacing"></div>
                    <div class="row layout-top-spacing">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                            <div class="widget widget-chart-one">
                                <div class="widget-heading">
                                    <div>
                                        <span class="h3 giftcard-text" style="color: #000070;">Naira Wallet (₦) </span>
                                    </div>
                                    <div class="widget-n" style="justify-content: center; text-align: center;">
                                        <span class="d-block" style="h6 walletbalance-text">Naira Wallet Balance</span>
                                        <span
                                            class="d-block price ">₦{{ number_format(Auth::user()->nairaWallet->amount) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div id="content_bg" class="card card-body mb-4" {{-- style="height:550px;" --}}>
                                <div class="container px-4 d-flex justify-content-between align-items-center">
                                    <a href="{{ route('user.portfolio') }}">
                                        <div class="d-flex align-items-center">
                                            <div
                                                style="background: rgba(0, 0, 112, 0.25);width:24px;height:24px;border-radius:12px;">
                                                <span style="position: relative;left:33%;top:0;">
                                                    <svg width="8" height="12" viewBox="0 0 8 12" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M7.41 1.41L6 0L0 6L6 12L7.41 10.59L2.83 6L7.41 1.41Z"
                                                            fill="#000070" />
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="ml-2" style="color: #000070;font-size: 20px;">Back</div>

                                        </div>
                                    </a>
                                    <div class="d-flex justify-content-center align-items-center"
                                        style="height:60px;box-shadow: 0px 2px 10px rgba(207, 207, 207, 0.25);">
                                        <a class="mx-1 mx-lg-2 px-1" href="#">
                                            <img src="{{asset('svg/nairawallet_logo.svg')}}" class="img-fluid" alt="">
                                        </a>
                                        <a class="mx-1 mx-lg-2 px-1" href="#">
                                            <img src="{{asset('svg/bitcoinwallet_logo.svg')}}" class="img-fluid" alt="">
                                        </a>
                                        <a class="mx-1 mx-lg-2 px-1" href="#">
                                            <img src="{{asset('svg/ethereumwallet_logo.svg')}}" class="img-fluid"
                                                alt="">
                                        </a>
                                        <a class="mx-1 mx-lg-2 px-1" href="#">
                                            <img src="{{asset('svg/tetherwallet_logo.svg')}}" class="img-fluid" alt="">
                                        </a>
                                    </div>
                                    <div class="d-flex" style="visibility: hidden !important;">
                                        <div class="mr-1 mr-lg-2" style="">$ 8,452.98
                                        </div>
                                    </div>
                                </div>
                                {{-- border line --}}
                                <div class="mt-4" style="width: 100%;border: 1px solid #C9CED6;"></div>
                                @foreach ($errors->all() as $err)
                                <p class="text-danger">{{ $err }}</p>
                                @endforeach

                                {{-- Wallet  menu  --}}
                                <div class="row">
                                    <div class="col-12 col-lg-6">
                                        <div class="mx-auto mt-4">
                                            <div
                                                class="walletpage_menu d-flex flex-column flex-lg-row justify-content-center justify-content-lg-between align-items-center flex-wrap flex-lg-nowrap">
                                                <div>
                                                    <span class="d-block" style="color: #565656;font-size: 16px;">Naira
                                                        wallet
                                                        Balance</span>
                                                    <span class="d-block">
                                                        <span
                                                            style="color: #000070;font-size: 30px;">₦{{ number_format($n->amount) }}</span>
                                                    </span>
                                                </div>

                                                <div class="d-flex mt-3 mt-md-0">
                                                    {{--  <a id="naira_transfer" class="btn  naira_menu">
                                                        <span class="d-block">
                                                        <img src="{{asset('svg/bitcoin-send-icon.svg')}}" alt="">
                                                    </span>
                                                    <span class="d-block"
                                                        style="color: #000000;font-size: 14px;">Transfer</span>
                                                    </a> --}}
                                                    <a id="naira_withdraw"
                                                        class="btn naira_menu walletpage_menu-active">
                                                        <span class="d-block">
                                                            <img src="{{asset('svg/naira-withdraw-icon.svg')}}" alt="">
                                                        </span>
                                                        <span class="d-block"
                                                            style="color: #000000;font-size: 14px;">Withdraw</span>
                                                    </a>
                                                    {{--  <a id="naira_deposit" class="btn naira_menu">
                                                        <span class="d-block">
                                                            <img src="{{asset('svg/naira-deposit-icon.svg')}}" alt="">
                                                    </span>
                                                    <span class="d-block"
                                                        style="color: #000000;font-size: 14px;">Deposit</span>
                                                    </a> --}}
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 text-center">
                                                    <p><strong class="text-primary mb-0">Daily Limit:</strong>
                                                        ₦{{ number_format(Auth::user()->daily_max) }} <strong
                                                            class="text-primary">Rem:
                                                        </strong>₦{{ number_format($daily_rem) }} </p>
                                                    <p><strong class="text-primary mb-0">Monthly Limit:</strong>
                                                        ₦{{ number_format(Auth::user()->monthly_max) }} <strong
                                                            class="text-primary">Rem:
                                                        </strong>₦{{ number_format($monthly_rem) }}</p>
                                                    <p>Please visit <a href="{{ route('user.profile') }}">Account
                                                            settings</a> to upgrade your limits</p>
                                                </div>
                                            </div>

                                            @include('newpages.tabs.naira-transfer-tab')
                                            {{-- @if($setting['data']['settings_value'] == 1) --}}
                                                @include('newpages.tabs.naira-withdraw-tab')
                                            {{-- @else
                                                <h3 class="text-center p-3 text-white" style="background-color: #000070"><i class="fas fa-info-circle"></i> {{$setting['notice']}}</h3>
                                            @endif --}}
                                            @include('newpages.tabs.naira-deposit-tab')


                                            {{-- Naira transfer modal --}}
                                            @include('newpages.modals.dantownTodantown-modal')
                                            @include('newpages.modals.dantownToOther-modal')

                                        </div>
                                    </div>
                                    <div class="col-12 col-lg-6 pt-2 nairawalletmain__transactionhistory-container">
                                        <span class="d-block txn_history_title mt-3">Transaction history</span>
                                        <div class="mt-3" style="width: 100%;border: 1px solid #EFEFF8;"></div>
                                        <div class="table-responsive">
                                            <table class="table table-borderless">
                                                <tbody class="text-center wallet_trxs">
                                                    @foreach ($nts as $t)
                                                    <tr class="my-2">
                                                        <th scope="row">
                                                            <span class="d-block"
                                                                style="font-size: 16px;">{{ $t->created_at->format('d M Y') }}</span>
                                                            <span class="d-block"
                                                                style="font-size: 14px;font-weight: normal;">{{ $t->created_at->format('h:i a') }}</span>
                                                        </th>
                                                        <td>₦{{number_format($t->amount)}}</td>
                                                        <td>
                                                            <span
                                                                style="color: #87676F;font-size: 16px;">{{ $t->transactionType->name }}</span>
                                                        </td>
                                                        <td>
                                                            <span class="px-3 py-2"
                                                                style="font-size: 12px;color: #219653;background: rgba(115, 219, 158, 0.3);border-radius: 15px;">{{ $t->status }}</span>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                    {{ $nts->links() }}
                                                </tbody>
                                            </table>
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


{{-- Add bank account --}}
<div class="modal fade  item-badge-rightm" id="add-bank-modal" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <form id="user-bank-details" class="mb-4">
                    {{ csrf_field() }}
                    <div class="form-row ">
                        <div class="col-md-12">
                            <div class="position-relative form-group">
                                <label>Bank Name</label>
                                <select name="bank_code" class="form-control">
                                    @foreach ($banks as $b)
                                    <option value="{{$b->code}}">{{$b->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="position-relative form-group">
                                <label>Account Number</label>
                                <input type="text" required class="form-control" name="account_number">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="position-relative form-group">
                                <label>Account Name</label>
                                @if (Auth::user()->accounts->count() == 0)
                                <input type="text" required class="form-control " name="account_name">
                                @else
                                <input type="text" required class="form-control" readonly value="{{ Auth::user()->first_name }}" name="account_name">
                                @endif
                            </div>
                        </div>

                        @if (Auth::user()->phone_verified_at == null)
                        <div class="col-md-12">
                            <label for="">Phone Number</label>
                            <div class="position-relative input-group mb-0 mx-auto mx-md-0" style="">
                                <div class="input-group-prepend"
                                    style="border: 1px solid rgba(0, 0, 112, 0.25);border-right:0px;border-top-left-radius:5px;border-bottom-left-radius:5px;">
                                    <select name="country_id" id="country-id" class="form-control">
                                        <option value="156">+234</option>
                                        @foreach ($countries as $country)
                                        <option value="{{ $country->id }}">+ {{ $country->phonecode }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <input type="tel" id="signup_phonenumber" min="1" maxlength="10" name="phone" value="{{ Auth::user()->phone ?? '' }}"
                                    placeholder="8141894420" class="form-control col-12" style="border-left: 0px;"
                                    pattern="[1-9]\d*" title="Number not starting with 0">
                                <div class="input-group-prepend">
                                    <button id="otp-text" type="button" onclick="sendOtp()" class="btn btn-outline-primary btn-block">Send OTP</button>
                                </div>
                            </div>
                            <small>Number must not start with '0'.</small>
                        </div>

                        <div class="col-md-12">
                            <div class="position-relative form-group">
                                <label>OTP Code</label>
                                <input type="nnumber" required class="form-control " name="otp">
                            </div>
                        </div>

                        @endif



                    </div>
                    <button type="submit" id="sign-up-btn" class="mt-2 btn btn-outline-primary">
                        <i class="spinner-border spinner-border-sm" id="s-b" style="display: none;"></i>
                        Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
