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
                        <div> {{$segment}} Transactions
                            <div class="page-title-subheading">
                                <h6 class="d-inline">₦{{ number_format($total) }} </h6>
                                {{-- <a href="{{ route('admin.naira-transaction.create') }}"> --}}
                                <button class="btn btn-primary">Add new transaction</button>
                                {{-- </a> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-header justify-content-between ">
                            {{$segment}} Transactions
                            {{-- Search for all users --}}
                            <form action="{{route('admin.search-tnxs')}}" class="form-inline p-2"
                                method="POST">
                                @csrf
                                <div class="form-group mr-2">
                                    <label for=""> Search </label>
                                    <input type="text" required name="search" class="ml-2 form-control">
                                    <input type="hidden" name="segment" value="{{ $segment }}" class="ml-2 form-control">
                                </div>
                                <button class="btn btn-outline-primary"><i class="fa fa-search"></i></button>
                            </form>
                            <form action="{{route('admin.wallet-transactions.sort.by.date')}}" class="form-inline p-2"
                                method="POST">
                                @csrf
                                <div class="form-group mr-2">
                                    <label for="">Start date </label>
                                    <input type="date" name="start" class="ml-2 form-control">
                                </div>
                                <div class="form-group mr-2">
                                    <label for="">End date </label>
                                    <input type="date" name="end" class="ml-2 form-control">
                                </div>
                                <button class="btn btn-outline-primary"><i class="fa fa-filter"></i></button>
                            </form>
                        </div>
                        <div class="table-responsive p-3">

                            <table class="align-middle mb-4 table table-bordered table-striped transactions-table ">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Reff</th>
                                        <th>User Name</th>
                                        <th>Trans. Type</th>
                                        <th>Amount Paid</th>
                                        <th>Total Charge</th>
                                        <th>Total</th>
                                        <th>Prev. Bal </th>
                                        <th>Cur. Bal</th>
                                        <th>Cr. Acct.</th>
                                        <th>Debit Acct.</th>
                                        <th>Narration</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Extras</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transactions as $t)
                                    <tr>
                                        <td>{{$t->id}} </td>
                                        <td>{{$t->reference}} </td>
                                        <td>
                                            <a
                                                href="{{route('admin.user', [$t->user->id ?? ' ', $t->user->email ?? ' ' ] )}}">
                                                {{$t->user->first_name ?? 'A MISSING USER'}}
                                            </a>
                                        </td>
                                        <td>{{$t->transactionType->name}} </td>
                                        <td>₦{{number_format($t->amount_paid) }} </td>
                                        <td>₦{{number_format($t->charge) }} </td>
                                        <td>₦{{number_format($t->amount) }} </td>
                                        <td>₦{{number_format($t->previous_balance) }}</td>
                                        <td>₦{{number_format($t->current_balance) }} </td>
                                        <td>{{$t->cr_acct_name}} </td>
                                        <td>{{$t->dr_acct_name}} </td>
                                        <td>{{$t->narration}} </td>
                                        <td>{{$t->created_at->format('d M Y h:ia ')}} </td>
                                        <td>{{$t->status}} </td>
                                        <td>{{$t->extras}} </td>
                                        @if (in_array(Auth::user()->role, [999, 889] ) && $t->status == 'pending' )
                                        <td>
                                            <button data-toggle="modal" data-target="#refund-modal"
                                                onclick="confirmRefund({{$t->id}}, {{$t->user}}, '{{number_format($t->amount)}}' )"
                                                class="btn mb-1 btn-sm btn-outline-success">
                                                Refund
                                            </button>

                                            <button data-toggle="modal" data-target="#query-modal"
                                                onclick="queryTransaction({{$t->id}})"
                                                class="btn mb-1 btn-sm btn-outline-success">
                                                Query
                                            </button>
                                        </td>

                                        @elseif (Auth::user()->role == 777 && $t->status == 'pending' )
                                        <td>
                                            <button data-toggle="modal" data-target="#query-modal"
                                                onclick="queryTransaction({{$t->id}})"
                                                class="btn mb-1 btn-sm btn-outline-success">
                                                Query
                                            </button>
                                        </td>
                                        @else
                                        <td>..</td>
                                        @endif

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




{{-- Transaction query modal --}}
<div class="modal fade" id="query-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content c-rounded">
            <!-- Modal Header -->
            <div class="modal-header bg-custom-gradient c-rounded-top p-4">
                <h4 class="modal-title">
                    Transaction query details
                    <i class="fa fa-rotate-180 fa-paper-plane"></i>
                </h4>
                <button type="button" class="close bg-light rounded-circle" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body p-4">
                <img src="{{ asset('loader2.gif') }}" class="loader img-fluid" alt="loader"
                    style="position: absolute; top: 30px">
                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <td class="text-left"><strong>Reference code</strong></td>
                            <td class="text-right" id="q-ref">XXXXX</td>
                        </tr>
                        <tr>
                            <td class="text-left"><strong>Response Code</strong></td>
                            <td class="text-right" id="q-res-code">XXXXX</td>
                        </tr>
                        <tr>
                            <td class="text-left"><strong>Transaction status</strong></td>
                            <td class="text-right" id="q-status">XXXXX</td>
                        </tr>
                        <tr>
                            <td class="text-left"><strong>Response message</strong></td>
                            <td class="text-right" id="q-res-msg">XXXXX</td>
                        </tr>
                        <tr>
                            <td class="text-left"><strong>Amount</strong></td>
                            <td class="text-right" id="q-amount">XXXXX</td>
                        </tr>
                        <tr>
                            <td class="text-left"><strong>Cr Account</strong></td>
                            <td class="text-right" id="q-cr">XXXXX</td>
                        </tr>
                        <tr>
                            <td class="text-left"><strong>Dr Account</strong></td>
                            <td class="text-right" id="q-dr">XXXXX</td>
                        </tr>
                        <tr>
                            <td class="text-left"><strong>Request date</strong></td>
                            <td class="text-right" id="q-req-date">XXXXX</td>
                        </tr>
                    </tbody>
                </table>
                <hr>
                <form action="{{ route('admin.update-naira-transaction') }}" method="post">
                    @csrf
                    <p>Click 'refund' to perfoem a refund for failed transactions, the transaction status will be
                        changed automatically once refund is done. </p>
                    <p> For successful transactions, update status to successful and save</p>
                    <div class="form-group">
                        <input type="hidden" name="id" id="q-id">
                        <select name="status" id="" class="form-control">
                            <option value="success">Successful</option>
                        </select>
                    </div>
                    <button class="btn btn-success">Save</button>
                </form>
                <hr>
                <button class="btn btn-block c-rounded bg-custom-gradient" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Confirm refund modal --}}
<div class="modal fade " id="refund-modal">
    <div class="modal-dialog modal-dialog-centered ">
        <form action="{{route('admin.naira-refund')}} " method="post" class="txn-form">
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
