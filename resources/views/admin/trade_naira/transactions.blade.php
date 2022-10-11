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
                        <div>@if (isset($start_date))
                            @php
                            $input = $start_date;
                            $date = strtotime($input);
                            echo date('d/M/Y h:ia', $date);
                            @endphp
                            -
                            @php
                            $input = $end_date;
                            $date = strtotime($input);
                            echo date('d/M/Y h:ia', $date);
                            @endphp
                        @endif
                            PayBridge Transactions</div>
                    </div>
                </div>
            </div>
            <div class="card-header justify-content-between">
                <form action="{{route('admin.naira-p2p.sort')}}" class="form-inline p-2"
                    method="POST">
                    @csrf
                    <div class="form-group mr-1">
                        <label for="">Start</label>
                        <input type="datetime-local" required name="start" class="ml-1 form-control">
                    </div>
                    <div class="form-group mr-1">
                        <label for="">End</label>
                        <input type="datetime-local" required name="end" class="ml-1 form-control">
                    </div>
                    <input type="hidden" name="type" value="{{ $type }}">
                    <input type="hidden" name="status" value="{{ $status }}">
                    <button class="btn btn-outline-primary"><i class="fa fa-filter"></i></button>
                </form>

                <form action="{{route('admin.naira-p2p.search')}}" class="form-inline p-2"
                    method="POST">
                    @csrf
                    <div class="form-group mr-1">
                        <input type="text" required name="search" class="ml-1 form-control" placeholder="Search">
                    </div>
                    <input type="hidden" name="type" value="{{ $type }}">
                    <input type="hidden" name="status" value="{{ $status }}">
                    <button class="btn btn-outline-primary"><i class="fa fa-search"></i></button>
                </form>
            </div>
            <div class="row mb-2">
                <div class="col-md-3">
                    {{-- bg-primary text-white --}}
                    <a href="{{ route('admin.naira-p2p.type',['type' => 'deposit']) }}">
                        <div class="card mb-1 widget-content @if ($type == 'deposit' AND $status == null)
                        bg-primary
                         @endif">
                            <div class="widget-content-wrapper">
                                <div class="widget-heading ">
                                    <h5 class="text-center @if ($type == 'deposit' AND $status == null)
                                    text-white
                                    @endif">Total Deposit [ {{ $deposit_all_tnx }} ]</h5>
                                    <p class="text-center @if ($type == 'deposit' AND $status == null)
                                    text-white
                                    @endif">Open Transactions</p>
                                </div>

                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    {{-- bg-primary text-white --}}
                    <a href="{{ route('admin.naira-p2p.type',['type' => 'withdrawal']) }}">
                        <div class="card mb-1 widget-content @if ($type == 'withdrawal' AND $status == null)
                            bg-primary
                        @endif">
                            <div class="widget-content-wrapper ">
                                <div class="widget-heading">
                                    <h5 class="text-center @if ($type == 'withdrawal' AND $status == null)
                                    text-white
                                    @endif">Total Withdrawal [ {{ $withdrawal_all_tnx }} ]</h5>
                                    <p class="text-center @if ($type == 'withdrawal' AND $status == null)
                                    text-white
                                    @endif">Open Transactions</p>
                                </div>

                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    {{-- bg-primary text-white --}}
                    <a href="{{ route('admin.naira-p2p.type',['type' => 'deposit','status'=>'success']) }}">
                        <div class="card mb-1 widget-content @if ($type == 'deposit' AND $status == "success")
                        bg-primary
                    @endif">
                            <div class="widget-content-wrapper">
                                <div class="widget-heading">
                                    <h5 class="text-center @if ($type == 'deposit' AND $status == "success")
                                        text-white
                                    @endif">Successful Deposit [ {{ $deposit_success_tnx }} ]</h5>
                                    <p class="text-center @if ($type == 'deposit' AND $status == "success")
                                        text-white
                                    @endif">

                                    @if (Auth::user()->role != 777)

                                    ₦ {{ number_format($deposit_success_amount) }}</p>
                                    @endif
                                </div>

                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    {{-- bg-primary text-white --}}
                    <a href="{{ route('admin.naira-p2p.type',['type' => 'withdrawal','status'=>'success']) }}">
                        <div class="card mb-1 widget-content
                        @if ($type == 'withdrawal' AND $status == "success")
                         bg-primary
                        @endif">
                            <div class="widget-content-wrapper">
                                <div class="widget-heading">
                                    <h5 class="text-center
                                    @if ($type == 'withdrawal' AND $status == "success")
                                    text-white
                                    @endif">Successful Withdrawal <br>[ {{ $withdrawal_success_tnx }} ]</h5>
                                    <p class="text-center
                                    @if ($type == 'withdrawal' AND $status == "success")
                                    text-white
                                    @endif">

                                    @if (Auth::user()->role != 777)
                                        ₦ {{ number_format($withdrawal_success_amount) }}</p>
                                    @endif
                                </div>

                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    {{-- bg-primary text-white --}}
                    <a href="{{ route('admin.naira-p2p.type',['type' => 'deposit','status'=>'waiting']) }}">
                        <div class="card mb-1 widget-content
                        @if ($type == 'deposit' AND $status == "waiting")
                         bg-primary
                        @endif">
                            <div class="widget-content-wrapper">
                                <div class="widget-heading">
                                    <h5 class="text-center @if ($type == 'deposit' AND $status == "waiting")
                                        text-white
                                    @endif">Waiting Deposit [ {{ $deposit_waiting_tnx }} ]</h5>
                                    <p class="text-center
                                    @if ($type == 'deposit' AND $status == "waiting")
                                        text-white
                                    @endif">₦ {{ number_format($deposit_waiting_amount) }}</p>
                                </div>

                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    {{-- bg-primary text-white --}}
                    <a href="{{ route('admin.naira-p2p.type',['type' => 'withdrawal','status'=>'waiting']) }}">
                        <div class="card mb-1 widget-content @if ($type == 'withdrawal' AND $status == "waiting")
                        bg-primary
                       @endif">
                            <div class="widget-content-wrapper">
                                <div class="widget-heading">
                                    <h5 class="text-center @if ($type == 'withdrawal' AND $status == "waiting")
                                    text-white
                                   @endif">Waiting Withdrawal [ {{ $withdrawal_waiting_tnx }} ]</h5>
                                    <p class="text-center @if ($type == 'withdrawal' AND $status == "waiting")
                                    text-white
                                   @endif">₦ {{ number_format($withdrawal_waiting_amount) }}</p>
                                </div>

                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    {{-- bg-primary text-white --}}
                    <a href="{{ route('admin.naira-p2p.type',['type' => 'deposit','status'=>'cancelled']) }}">
                        <div class="card mb-1 widget-content
                        @if ($type == 'deposit' AND $status == "cancelled")
                         bg-primary
                        @endif">
                            <div class="widget-content-wrapper">
                                <div class="widget-heading">
                                    <h5 class="text-center @if ($type == 'deposit' AND $status == "cancelled")
                                    text-white
                                     @endif">Declined Deposit [ {{ $deposit_denied_tnx }} ]</h5>
                                    <p class="text-center @if ($type == 'deposit' AND $status == "cancelled")
                                    text-white
                                    @endif">₦ {{ number_format($deposit_denied_amount) }}</p>
                                </div>

                            </div>
                        </div>
                    </a>
                </div>


                <div class="col-md-3">
                    {{-- bg-primary text-white --}}
                    <a href="{{ route('admin.naira-p2p.type',['type' => 'withdrawal','status'=>'cancelled']) }}">
                        <div class="card mb-1 widget-content @if ($type == 'withdrawal' AND $status == "cancelled")
                        bg-primary
                       @endif">
                            <div class="widget-content-wrapper">
                                <div class="widget-heading">
                                    <h5 class="text-center @if ($type == 'withdrawal' AND $status == "cancelled")
                                    text-white
                                   @endif">Declined Withdrawal <br>[ {{ $withdrawal_denied_tnx }} ]</h5>
                                    <p class="text-center @if ($type == 'withdrawal' AND $status == "cancelled")
                                    text-white
                                   @endif">₦ {{ number_format($withdrawal_denied_amount) }}</p>
                                </div>

                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 pb-3 card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="">
                                {{ $segment }} Transactions
                            </div>
                            @if ($show_limit)
                            @if (in_array(Auth::user()->role,[999, 889,777]))
                                    <div>
                                        @if (in_array(Auth::user()->role,[999, 889,777]))
                                        <form class="btn btn-md-primary"
                                            method="GET">
                                            <input type="hidden" name="downloader" value="csv">
                                            <button class="btn btn-primary">Download Table</button>
                                        </form>
                                        @endif
                                        @if (in_array(Auth::user()->role,[999, 889]))
                                        <a href="{{ route('admin.naira-p2p.withdrawal-queue')}}" class="btn btn-primary">Withdrawal Queue Range</a>
                                        <button data-toggle="modal" data-target="#limits-modal" class="btn btn-primary">Set Trade Limits</button>
                                        <button data-toggle="modal" data-target="#account-modal" class="btn btn-primary">Set account details</button>
                                        @endif
                                    </div>
                                @endif
                            @endif
                        </div>
                        <div class="table-responsive p-3">
                            <table
                                class="align-middle mb-0 table table-borderless table-striped table-hover transactons-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Amount</th>
                                        <th>Reference</th>
                                        <th>Type</th>
                                        <th>Prev Balance</th>
                                        <th>Current Balance</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transactions as $key => $t)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>
                                            @if ($t->user->total_trx > 10000000)
                                                <span><i class="fa fa-star" style="color: green;"></i></span>
                                            @endif
                                            {{ $t->user->first_name .' '. $t->user->last_name }}</td>
                                        <td>{{ $t->user->phone }}</td>
                                        @if($t->type == 'withdrawal' AND $t->status == 'waiting')
                                            <td class="text-danger">₦{{ number_format($t->amount) }}</td>
                                        @elseif($t->type == 'deposit' AND $t->status == 'waiting')
                                            <td class="text-success">₦{{ number_format($t->amount) }}</td>
                                        @else

                                        <td>₦{{ number_format($t->amount) }}</td>
                                        @endif
                                        <td>{{ $t->reference }}</td>
                                        <td>{{ $t->type }}
                                        @if($t->type == 'withdrawal' OR isset($t->acct_details))
                                            <br><br>
                                            {{ $t->acct_details }}
                                        @endif

                                        </td>
                                        <td>₦{{ number_format($t->prev_bal) }}</td>
                                        <td>₦{{ number_format($t->current_bal) }}</td>
                                        <td>{{ $t->created_at->format('d m y, h:ia') }}</td>
                                        <td>{{ $t->status }}</td>
                                        <td>
                                            <div class="btn-group">
                                                @if ($t->status == 'waiting')
                                                    <button data-toggle="modal" data-target="#confirm-modal-{{ $t->id }}" class="btn btn-primary">Approve</button>
                                                {{-- @if (in_array(Auth::user()->role, [999, 889])) --}}
                                                    <button class="btn btn-danger" data-toggle="modal" data-target="#cancel-modal-{{ $t->id }}">Cancel</button>
                                                {{-- @endif --}}
                                                @elseif($t->status == 'success' && in_array(Auth::user()->role, [999, 889]) )
                                                {{-- @else --}}
                                                    <button class="btn btn-danger" data-toggle="modal" data-target="#refund-modal-{{ $t->id }}">Refund</button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if (isset($paginate) && $paginate != false)

                            {{$transactions->links()}}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(!empty($transactions))
    {{-- Confirm trade approval modal --}}
    @foreach ($transactions as $t)
    <div class="modal fade " id="confirm-modal-{{ $t->id }}">
        <div class="modal-dialog  ">
            @if($t->type == 'withdrawal')
                <form action="{{route('admin.naira-p2p.confirm-sell', $t)}}" id="freeze-form" method="post"> @method('put')
            @else
                <form action="{{route('admin.naira-p2p.confirm', $t)}}" id="freeze-form" method="post"> @method('put')
            @endif
                @csrf
                <div class="modal-content  c-rounded">
                    <!-- Modal Header -->
                    <div class="modal-header bg-custom-gradient c-rounded-top p-4 ">
                        <h4 class="modal-title">Confirm Trade <i class="fa fa-paper-plane"></i></h4>
                        <button type="button" class="close bg-light rounded-circle " data-dismiss="modal">&times;</button>
                    </div>
                    <!-- Modal body -->

                    <div class="modal-body p-4">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Wallet pin </label>
                                    <input type="password" name="pin" required class="form-control">
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-block c-rounded bg-custom-gradient txn-btn">
                            Confirm
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endforeach
@endif

@if(!empty($transactions))
    {{-- Confirm trade approval modal --}}
    @foreach ($transactions as $t)
    <div class="modal fade " id="cancel-modal-{{ $t->id }}">
        <div class="modal-dialog">
            <form action="{{route('admin.naira-p2p.cancel-trade', $t)}}" id="freeze-form" method="post"> @method('put')
                @csrf
                <div class="modal-content  c-rounded">
                    <!-- Modal Header -->
                    <div class="modal-header bg-custom-gradient c-rounded-top p-4 ">
                        <h4 class="modal-title">Decline Trade <i class="fa fa-paper-plane"></i></h4>
                        <button type="button" class="close bg-light rounded-circle " data-dismiss="modal">&times;</button>
                    </div>
                    <!-- Modal body -->

                    <div class="modal-body p-4">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Wallet pin </label>
                                    <input type="password" name="pin" required class="form-control">
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-block c-rounded bg-custom-gradient txn-btn">
                            Decline
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endforeach
@endif

@if(!empty($transactions))
    {{-- Confirm trade approval modal --}}
    @foreach ($transactions as $t)
    <div class="modal fade " id="refund-modal-{{ $t->id }}">
        <div class="modal-dialog">
            <form action="{{route('admin.naira-p2p.refund-trade', $t)}}" id="freeze-form" method="post"> @method('put')
                @csrf
                <div class="modal-content  c-rounded">
                    <!-- Modal Header -->
                    <div class="modal-header bg-custom-gradient c-rounded-top p-4 ">
                        <h4 class="modal-title">Refund<i class="fa fa-paper-plane"></i></h4>
                        <button type="button" class="close bg-light rounded-circle " data-dismiss="modal">&times;</button>
                    </div>
                    <!-- Modal body -->

                    <div class="modal-body p-4">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Wallet pin </label>
                                    <input type="password" name="pin" required class="form-control">
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-block c-rounded bg-custom-gradient txn-btn">
                            Refund
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endforeach
@endif

@if ($show_limit)
{{-- Set Limits --}}
<div class="modal fade " id="limits-modal">
    <div class="modal-dialog modal-dialog-centered ">
        <form action="{{ route('admin.naira-p2p.set-limits') }}" id="freeze-form" method="post"> @csrf
            <div class="modal-content  c-rounded">
                <!-- Modal body -->

                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Minimum</label>
                                <input type="number" name="min" value="{{ isset(Auth::user()->agentLimits->min) ? Auth::user()->agentLimits->min : '' }}" required
                                    class="form-control">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Maximum</label>
                                <input type="number" name="max" value="{{ isset(Auth::user()->agentLimits->max) ? Auth::user()->agentLimits->max : '' }}" required
                                    class="form-control">
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-block c-rounded bg-custom-gradient txn-btn">
                        Save
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@if(!empty($account))
    <div class="modal fade " id="account-modal">
        <div class="modal-dialog modal-dialog-centered ">
            <div class="modal-content">
                <div class="modal-body">
                    <form action="{{ route('agent.update-bank') }}" method="POST" class="mb-4">@csrf
                        <div class="form-row ">
                            <div class="col-md-12">
                                <input type="hidden" value="{{ $account->id }}" name="id">
                                <div class="position-relative form-group">
                                    <label>Bank Name</label>
                                    <select name="bank_id" class="form-control">
                                        <option value="{{ $account->bank_id }}">{{ $account->bank_name }}</option>
                                        @foreach ($banks as $b)
                                        <option value="{{$b->id}}">{{$b->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="position-relative form-group">
                                    <label>Account Number</label>
                                    <input type="text" required class="form-control" value="{{ $account->account_number }}" name="account_number">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="position-relative form-group">
                                    <label>Account Name</label>
                                    <input type="text" required class="form-control" value="{{ $account->account_name }}" name="account_name">
                                </div>
                            </div>
                        </div>
                        <button type="submit" id="sign-up-btn" class="mt-2 btn btn-outline-primary">
                            <i class="spinner-border spinner-border-sm" id="s-b" style="display: none;"></i>
                            Save
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif
@endif
@endsection
