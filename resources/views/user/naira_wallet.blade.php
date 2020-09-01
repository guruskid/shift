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
                            <div class="media ">
                                <img src="{{asset('svg/naira.svg')}}" style="height: 40px">
                                <div class="media-body ml-2 ">
                                    <strong>{{$n->account_name}}</strong>
                                    <p>{{$n->account_number}}</p>
                                </div>
                            </div>
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
                            @if (Auth::user()->nairaWallet->password == null)
                            <p class="text-info">Please visit <a href="{{route('user.profile')}}">account settings </a>to reset your wallet password before initiating a wallet transaction </p>
                            @endif
                            <div class="row my-3">
                                <div class="col-md-6 mb-3">
                                    <div class="card wallet">
                                        <div
                                            class="card-header wallet-balance bg-custom c-rounded-top justify-content-between">
                                            <h5>Dantown Wallet</h5>
                                            <div class="">
                                                <h5 class="mb-0">Balance</h5>
                                                <p>₦{{number_format($n->amount)}}</p>
                                            </div>
                                        </div>
                                        <div class="card-body px-2 py-3" style="position: unset;">

                                            <div class="d-flex justify-content-between wallet-buttons">
                                                <button class="btn bg-custom px-3" data-toggle="modal"
                                                    data-target="#send-modal" onclick="tnsType(1)">Transfer</button>
                                                <button class="btn bg-custom px-3" data-toggle="modal"
                                                    data-target="#send-modal" onclick="tnsType(2)">Withdraw</button>
                                                <button class="btn bg-custom px-3" data-toggle="modal"
                                                    data-target="#recieve-modal">Deposit</button>

                                            </div>
                                        </div>
                                        <div class="card-footer bg-custom-accent p-4 c-rounded-bottom ">
                                            <strong>Pending Transc.:</strong> <span>No pending transactions in your
                                                wallet</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="card card-body p-5 mb-3">
                                        <strong class="text-accent">Guide</strong>
                                        <p>Withdraw or make transfers to an account on Dantown or to other banks from
                                            your Dantown
                                            wallet by clicking on the withdraw or transfer box, fill in the account details and
                                            authenticate the transactiton with your 4-digit wallet pin created when
                                            opening a new wallet.</p>
                                        <p>To deposit in to your wallet, click non the deposit box to view your account
                                            details. You can pay at the bank to the details or through your favourite
                                            mode of transfers</p>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-1">
                <div class="col-md-12">
                    <div class="main-card mb-3 card bg-custom-accent">
                        <div class="card-header c-rounded-top bg-custom-accent">Transactions </div>
                        <div class="table-responsive">
                            <table class="mb-2 transactions-table table " id="nt-table">
                                <thead>
                                    <tr>
                                        <th class="text-center">Reference id</th>
                                        <th class="text-center">Cr. Account</th>
                                        <th class="text-center">Dr. Account</th>
                                        <th class="text-center">Amount</th>
                                        <th class="text-center">Narration</th>
                                        <th class="text-center">Type</th>
                                        <th class="text-center">Trans. Type</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-custom-accent">
                                    @foreach ($nts as $t)
                                    <tr class="bg-custom-accent">
                                        <td class="text-center">{{$t->reference}}</td>
                                        <td class="text-center">{{$t->cr_acct_name}}</td>
                                        <td class="text-center">{{$t->dr_acct_name}}</td>
                                        <td class="text-center">₦{{number_format($t->amount)}}</td>
                                        <td class="text-center">{{$t->narration}}</td>
                                        <td class="text-center">{{ucwords($t->transactionType->name)}}</td>
                                        <td class="text-center">{{ucwords($t->trans_type)}}</td>
                                        <td class="text-center">{{ucwords($t->status)}} </td>
                                        <td class="text-center">{{$t->created_at->format('d M y ')}}</td>
                                    </tr>
                                    @endforeach
                                    <tr class="bg-custom-accent">
                                        <td class="text-center"><strong>Debit Total</strong></td>
                                        <td class="text-center"><strong>₦{{number_format($dr_total)}} </strong></td>
                                    </tr>
                                    <tr class="bg-custom-accent">
                                        <td class="text-center"><strong>Credit Total</strong></td>
                                        <td class="text-center"><strong>₦{{number_format($cr_total)}} </strong></td>
                                    </tr>
                                </tbody>
                                {{$nts->links()}}
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            @include('layouts.partials.live-feeds')
        </div>
    </div>
</div>
<form action="{{route('user.transfer')}}" method="POST" id="transfer-form">
    @csrf
    {{-- Send Modal --}}
    <div class="modal fade " id="send-modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content  c-rounded">
                <!-- Modal Header -->
                <div class="modal-header bg-custom-gradient c-rounded-top p-4 ">
                    <h4 class="modal-title tns-title ">Send NGN<i class="fa fa-paper-plane"></i></h4>
                    <button type="button" class="close bg-light rounded-circle " data-dismiss="modal">&times;</button>
                </div>
                <!-- Modal body -->

                <div class="modal-body p-4">
                    <div id="transfer-dest">
                        <div class="d-flex p-0 my-2 bg-dark  rounded">
                            <div class="col-6 p-1 text-center bg-custom-accent rounded-left" id="dantown-transfer"
                                onclick="changeTransferType('dantown')">
                                <a href="#" class="text-white">Dantown to Dantown </a>
                            </div>
                            <div class="col-6 p-1 text-center rounded-right " id="other-transfer"
                                onclick="changeTransferType('other')">
                                <a href="#" class="text-white">To other banks </a>
                            </div>
                        </div>
                    </div>

                    <div class="row user-accts">
                        @foreach (Auth::user()->accounts as $a)
                        <div class="col-md-6">
                            <div class="custom-control  custom-radio border-custom border px-5 py-3 my-1 form-group ">
                                <input type="radio" class="custom-control-input" name="account_id" value="{{$a->id}}"
                                    id="{{'acct-'.$a->id}}">
                                <label for="{{'acct-'.$a->id}}"
                                    class="custom-control-label"><strong>{{$a->bank_name}}</strong>,
                                    {{$a->account_number}}</label>
                            </div>
                        </div>
                        @endforeach
                        <div class="col-md-6">
                            <button class="btn btn-outline-success p-3 btn-block" onclick="addAcct()" type="button">
                                <i class="fa fa-plus"></i>
                                Add new bank account
                            </button>
                        </div>
                    </div>
                    <input type="hidden" name="trans_type" id="trns-type">
                    <div id="add-acct-details">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="">Bank name </label>
                                    <select name="bank_code" id="bank-name" class="form-control">
                                        <option id="dantown-bank" value="">Select bank name</option>
                                        @foreach ($banks as $b)
                                        <option class="other-banks" value="{{$b->code}}">{{$b->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="">Account number </label>
                                    <input type="number" name="acct_num" id="account-number" class="form-control">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="">Account name </label>
                                    <input type="text" name="acct_name" id="" readonly class="form-control acct-name">
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Amount (₦)</label>
                                <input type="number" name="amount" id="amount" required class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Narration</label>
                                <input type="text" name="narration" required class="form-control"
                                    placeholder="Description">
                            </div>
                        </div>
                    </div>
                    <button type="button" id="sign-up-btn" onclick="postAmount()" data-toggle="modal" data-target="#confirm-modal"
                        class="btn btn-block c-rounded bg-custom-gradient">
                        Confirm
                    </button>
                    <p class="text-custom t-info "></p>
                </div>
            </div>
        </div>
    </div>

    {{-- Confirm transfer modal --}}
    <div class="modal fade " id="confirm-modal">
        <div class="modal-dialog ">
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
                                <input type="password" name="pin" minlength="4" maxlength="4" required class="form-control wallet-pin"
                                    placeholder="- - - -">
                            </div>
                        </div>
                    </div>
                    <button id="transfer-btn" class="btn btn-block c-rounded bg-custom-gradient">
                        Send
                    </button>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="ref" value="{{$ref}}">
</form>

{{-- Recieve Modal --}}
<div class="modal fade " id="recieve-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content  c-rounded">

            <!-- Modal Header -->
            <div class="modal-header bg-custom-gradient c-rounded-top p-4 ">
                <h4 class="modal-title">Account details <i class="fa fa-rotate-180 fa-paper-plane"></i></h4>
                <button type="button" class="close bg-light rounded-circle " data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="">Bank name </label>
                            <input type="disabled" readonly class="form-control" value="{{$n->bank_name}}">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="">Account name </label>
                            <input type="disabled" readonly class="form-control" value="{{$n->account_name}}">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="">Account number </label>
                            <input type="disabled" readonly class="form-control" value="{{$n->account_number}}">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="">Bank code </label>
                            <input type="disabled" readonly class="form-control" value="{{$n->bank_code}}">
                        </div>
                    </div>
                </div>
                <button class="btn btn-block c-rounded bg-custom-gradient" data-dismiss="modal">
                    Done
                </button>
            </div>
        </div>
    </div>
</div>


@endsection
