@php
$cards = App\Card::orderBy('name', 'asc')->get(['name']);
$emails = App\User::orderBy('email', 'asc' )->pluck('email');
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
                        <div class="text-capitalize"> {{$segment}} Transactions
                            <div class="page-title-subheading">
                                <button class="btn btn-primary" onclick="location.reload()">Refresh Page</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-header justify-content-between">{{$segment}} Transactions
                            <form action="{{route('admin.transactions-by-date')}}" class="form-inline p-2"
                                method="POST">
                                @csrf
                                <div class="form-group mr-2">
                                    <label for="">Start date </label>
                                    <input type="date" required name="start" class="ml-2 form-control">
                                </div>
                                <div class="form-group mr-2">
                                    <label for="">End date </label>
                                    <input type="date" required name="end" class="ml-2 form-control">
                                </div>
                                <button class="btn btn-outline-primary"><i class="fa fa-filter"></i></button>
                            </form>
                        </div>
                        <div class="table-responsive p-3">
                            <table class="align-middle mb-4 table table-bordered table-striped transactions-table ">
                                <thead>
                                    <tr>
                                        <th class="text-center">ID</th>
                                        <th class="text-center">Asset</th>
                                        <th class="text-center">Trade type</th>
                                        <th class="text-center">Currency</th>
                                        <th class="text-center">Card type</th>
                                        <th class="text-center">Asset value</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-center">Card price</th>
                                        <th class="text-center">Cash value</th>
                                        <th class="text-center">Wallet ID</th>
                                        <th class="text-center">User</th>
                                        <th class="text-center">Date</th>
                                        <th class="text-center">Status</th>
                                        @if (Auth::user()->role == 999)
                                        <th class="text-center">Last Edit</th>
                                        <th class="text-center">Agent</th>
                                        <th class="text-center">Accountant</th>
                                        @endif
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transactions as $t)
                                    @php
                                    $c = $t->card;
                                    @endphp

                                    <tr>
                                        <td class="text-center text-muted">{{$t->uid}}</td>
                                        <td
                                            class="text-center  {{$c == 'perfect money' || $c == 'bitcoins' || $c == 'etherum' ? 'text-info   ': '' }} ">
                                            {{ucwords($t->card)}}</td>
                                        <td class="text-center text-capitalize">{{$t->type}}</td>
                                        <td class="text-center">{{$t->country}}</td>
                                        <td class="text-center">{{$t->card_type}}</td>
                                        <td class="text-center">{{$t->amount}}</td>
                                        <td class="text-center">{{$t->quantity}}</td>
                                        <td class="text-center">{{$t->card_price}}</td>
                                        <td class="text-center">N{{number_format($t->amount_paid)}}</td>
                                        <td class="text-center">{{$t->wallet_id}}</td>
                                        <td class="text-center"><a
                                                href=" {{route('admin.user', [$t->user->id, $t->user->email] )}}">
                                                {{$t->user->first_name." ".$t->user->last_name}}</a> </td>
                                        <td class="text-center">{{$t->created_at->format('d M, H:ia')}} </td>
                                        <td class="text-center">
                                            @switch($t->status)
                                            @case('success')
                                            <div class="text-success">{{$t->status}}</div>
                                            @break
                                            @case("failed")
                                            <div class="text-danger">{{$t->status}}</div>
                                            @break
                                            @case('declined')
                                            <div class="text-warning">{{$t->status}}</div>
                                            @break
                                            @case('waiting')
                                            <div class="text-info">{{$t->status}}</div>
                                            @break
                                            @default
                                            <div class="text-success">{{$t->status}}</div>

                                            @endswitch
                                        </td>
                                        @if (Auth::user()->role == 999)
                                        <td class="text-center"> {{$t->last_edited}} </td>
                                        <td class="text-center"> {{$t->agent->first_name}} </td>
                                        <td class="text-center"> {{$t->accountant->first_name ?? 'None' }} </td>
                                        @endif

                                        <td>
                                            <a href="{{route('admin.view-transaction', [$t->id, $t->uid] )}} ">
                                                <span class="btn btn-sm btn-success">View</span>
                                            </a>

                                            @if (Auth::user()->role == 889 ) {{-- super accountant options --}}

                                                <a href="#" data-toggle="modal" data-target="#edit-transac"
                                                    onclick="editTransac({{$t->id}})"><span
                                                        class="btn btn-sm btn-info">Edit</span></a>

                                                @if ($t->status == 'approved')
                                                <button data-toggle="modal" data-target="#confirm-modal"
                                                    onclick="confirmTransfer({{$t->id}}, {{$t->user}}, '{{number_format($t->amount_paid)}}' )"
                                                    class="btn btn-sm btn-outline-success">Pay</button>
                                                @elseif($t->status == 'success' || ($t->type == 'buy' && $t->status ==
                                                'declined' ) )
                                                <button data-toggle="modal" data-target="#refund-modal"
                                                    onclick="confirmRefund({{$t->id}}, {{$t->user}}, '{{number_format($t->amount_paid)}}' )"
                                                    class="btn btn-sm btn-outline-success">Refund</button>
                                                @endif

                                            @endif

                                            @if (Auth::user()->role == 999) {{-- Super Admin --}}
                                            <a href="#" data-toggle="modal" data-target="#edit-transac" onclick="editTransac({{$t->id}})">
                                                <span class="btn btn-sm btn-info">Edit</span>
                                            </a>

                                            @if ($t->status == 'approved')
                                            <button data-toggle="modal" data-target="#confirm-modal"
                                                onclick="confirmTransfer({{$t->id}}, {{$t->user}}, '{{number_format($t->amount_paid)}}' )"
                                                class="btn btn-sm btn-outline-success">Pay</button>
                                            @elseif($t->status == 'success')
                                            <button data-toggle="modal" data-target="#refund-modal"
                                                onclick="confirmRefund({{$t->id}}, {{$t->user}}, '{{number_format($t->amount_paid)}}' )"
                                                class="btn btn-sm btn-outline-success">Refund</button>
                                            @endif

                                            @endif

                                            @if (Auth::user()->role == 777) {{-- Junior Accountant --}}
                                            @if ($t->status != 'success' && $t->status != 'failed' && $t->status != 'declined')
                                            <a href="#" data-toggle="modal" data-target="#edit-transac"
                                                onclick="editTransac({{$t->id}})"><span
                                                    class="btn btn-sm btn-info">Edit</span></a>
                                            @endif

                                            @if ($t->status == 'approved')
                                            <button data-toggle="modal" data-target="#confirm-modal"
                                                onclick="confirmTransfer({{$t->id}}, {{$t->user}}, '{{number_format($t->amount_paid)}}' )"
                                                class="btn btn-sm btn-outline-success">Pay</button>
                                            @elseif($t->status == 'success')
                                            <button data-toggle="modal" data-target="#refund-modal"
                                                onclick="confirmRefund({{$t->id}}, {{$t->user}}, '{{number_format($t->amount_paid)}}' )"
                                                class="btn btn-sm btn-outline-success">Refund</button>
                                            @endif

                                            @endif

                                            @if (Auth::user()->role == 888) {{-- Sales rep --}}
                                            @if ($t->status != 'success' && $t->status != 'failed' && $t->status != 'declined')
                                            <a href="#" data-toggle="modal" data-target="#edit-transac"
                                                onclick="editTransac({{$t->id}})"><span
                                                    class="btn btn-sm btn-info">Edit</span></a>
                                            @endif
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
                                <select name="card" class="form-control">
                                    <option value="" id="e_card"></option>
                                    @foreach ($cards as $card)
                                    <option value=" {{$card->name}} "> {{ ucfirst($card->name) }} </option>
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
                            <div class="form-group">
                                <label for="">Status</label>
                                <select name="status" class="form-control">
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
