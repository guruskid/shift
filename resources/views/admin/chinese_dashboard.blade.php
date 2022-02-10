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
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="app-page-title">
                <div class="page-title-wrapper">
                    <div class="page-title-heading">
                        <div class="page-title-icon">
                            <i class="pe-7s-home icon-gradient bg-night-sky">
                            </i>
                        </div>
                        <div>Dashboard Home
                            <div class="page-title-subheading">Hi {{Auth::user()->first_name}}, good to see you again
                                Boss.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">

                <div class="col-md-4 col-xl-4">
                    <div class="card mb-3 widget-content bg-ripe-malin">
                        <div class="widget-content-wrapper py-2 text-white">
                            <div class="widget-content-actions mx-auto ">
                                <div class="widget-heading text-center">
                                    <h5>Gift cards transaction within 24hours </h5>
                                    <h6>{{$cardTwentyFourHrscount}}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="col-md-4 col-xl-4">
                    <div class="card mb-3 widget-content bg-happy-fisher">
                        <div class="widget-content-wrapper py-2 text-white">
                            <div class="widget-content-actions mx-auto ">
                                <div class="widget-heading text-center">
                                    <h5>Total card volume in Naira  within 24hours</h5>
                                    <h6>N{{$nairaTwentyFourHrs}}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-xl-4">
                    <div class="card mb-3 widget-content bg-grow-early">
                        <div class="widget-content-wrapper py-2 text-white">
                            <div class="widget-content-actions mx-auto ">
                                <div class="widget-heading text-center">
                                    <h5>Total card volume in Dollar within 24hours</h5>
                                    <h6> ${{$dollarTwentyFourHrs}}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- countWaiting
                countProgreses
                countSuccess
                countApproved --}}

                <div class="col-md-3 col-xl-3 to_trans_page" 
                onclick="window.location = '{{route('admin.transactions-status', 'success')}}'" >
                    <div class="card mb-3 widget-content bg-grow-early">
                        <div class="widget-content-wrapper py-2 text-white">
                            <div class="widget-content-actions mx-auto ">
                                <div class="widget-heading text-center">
                                    <h5>Successfull </h5>
                                    <h6>{{$countSuccess}}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-xl-3 to_trans_page"
                onclick="window.location = '{{route('admin.transactions-status', 'declined')}}'"
                >
                    <div class="card mb-3 widget-content bg-happy-fisher">
                        <div class="widget-content-wrapper py-2 text-white">
                            <div class="widget-content- mx-auto">
                                <div class="widget-heading text-center">
                                    <h5>Failed/Declined</h5>
                                    <h6>{{$failedAndDeclined}}</h6>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-xl-3 to_trans_page"
                onclick="window.location = '{{route('admin.transactions-status', 'in progress')}}'"
                >
                    <div class="card mb-3 widget-content bg-sunny-morning">
                        <div class="widget-content-wrapper py-2 text-white">
                            <div class="widget-content- mx-auto">
                                <div class="widget-heading text-center">
                                    <h5>In Progress </h5>
                                    <h6 id="in_progress_count">...</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-xl-3 to_trans_page"
                onclick="window.location = '{{route('admin.transactions-status', 'waiting')}}'"
                >
                    <div class="card mb-3 widget-content bg-ripe-malin">
                        <div class="widget-content-wrapper py-2 text-white">
                            <div class="widget-content- mx-auto">
                                <div class="widget-heading text-center">
                                    <h5>Waiting</h5>
                                    <h6 id="waiting_count">...</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- Recent Transactions --}}
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-header">Recent Transactions </div>

                        <div class="col-md-12">
                            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home"
                                        role="tab" aria-controls="pills-home" aria-selected="true">Waiting</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile"
                                        role="tab" aria-controls="pills-profile" aria-selected="false">Failed</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-contact"
                                        role="tab" aria-controls="pills-contact" aria-selected="false">In progress</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="pills-success-tab" data-toggle="pill" href="#pills-success"
                                        role="tab" aria-controls="pills-success" aria-selected="false">Success</a>
                                </li>
                            </ul>
                            <div class="tab-content" id="pills-tabContent">
                                <div class="tab-pane fade show active" id="pills-home" role="tabpanel"
                                    aria-labelledby="pills-home-tab">

                                    {{-- Waiting Transactions --}}
                                    <div class="col-md-12">
                                        <div class="main-card mb-3 card">
                                            <div class="card-header d-flex justify-content-between">
                                                <span>Waiting Transactions</span>
                                                <div class="page-title-subheading">
                                                    <button class="btn btn-primary" onclick="location.reload()"> Refresh
                                                        Page</button>
                                                </div>
                                            </div>
                                            <div class="table-responsive">
                                                <table
                                                    class="align-middle mb-4 table table-bordered table-striped transactions-table ">
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
                                                            @if (!in_array(Auth::user()->role, [449, 444] ))
                                                            <th class="text-center">Wallet ID</th>
                                                            @endif
                                                            <th class="text-center">User</th>
                                                            @if (!in_array(Auth::user()->role, [449, 444] ))
                                                            <th class="text-center">User Phone</th>
                                                            @endif
                                                            <th class="text-center">Date</th>
                                                            <th class="text-center">Status</th>
                                                            {{-- test//// --}}
                                                            @if (in_array(Auth::user()->role, [999, 889] ))
                                                            <th class="text-center">Last Edit</th>
                                                            <th class="text-center">Agent</th>
                                                            <th class="text-center">Accountant</th>
                                                            @endif
                                                            <th class="text-center">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($waiting_transactions_chinese as $t)
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

                                                            @if ($t->asset->is_crypto)
                                                            <td class="text-center">{{ sprintf('%.8f',
                                                                floatval($t->quantity))}}</td>
                                                            @else
                                                            <td class="text-center">{{ $t->quantity}}</td>
                                                            @endif
                                                            <td class="text-center">{{$t->card_price}}</td>
                                                            <td class="text-center">N{{number_format($t->amount_paid)}}
                                                            </td>
                                                            @if (!in_array(Auth::user()->role, [449, 444] ))
                                                            <td class="text-center">{{$t->wallet_id}}</td>
                                                            @endif
                                                            <td class="text-center">
                                                                @if (in_array(Auth::user()->role, [449,444] ))
                                                                {{$t->user->first_name." ".$t->user->last_name}}
                                                                @else
                                                                <a
                                                                href=" {{route('admin.user', [$t->user->id, $t->user->email] )}}">
                                                                {{$t->user->first_name." ".$t->user->last_name}}</a> 
                                                                @endif
                                                            </td>
                                                            @if (!in_array(Auth::user()->role, [449, 444] ))
                                                            <td class="text-center">
                                                               {{$t->user->phone}}
                                                            </td>
                                                            @endif
                                                            <td class="text-center">{{$t->created_at->format('d M,
                                                                H:ia')}} </td>
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
                                                            {{-- test//// --}}
                                                            @if (in_array(Auth::user()->role, [999, 889] ))
                                                            <td class="text-center"> {{$t->last_edited}} </td>
                                                            <td class="text-center"> {{$t->agent->first_name}} </td>
                                                            <td class="text-center"> {{$t->accountant->first_name ??
                                                                'None' }} </td>
                                                            @endif

                                                            <td>
                                                                <a
                                                                    href="{{route('admin.view-transaction', [$t->id, $t->uid] )}} ">
                                                                    <span class="btn btn-sm btn-success">View</span>
                                                                </a>

                                                                @if (Auth::user()->role == 444 OR Auth::user()->role == 449 ) {{--test//// super accountant
                                                                options --}}

                                                                <a href="#" data-toggle="modal"
                                                                    data-target="#edit-transac{{$t->id}}"
                                                                    {{-- onclick="editTransac({{$t}})" --}}
                                                                    ><span
                                                                        class="btn btn-sm btn-info">Edit</span></a>

                                                                @if ($t->status == 'approved')
                                                                @if (\Str::lower($t->card) == 'bitcoins')
                                                                <button data-toggle="modal"
                                                                    data-target="#confirm-btc-modal"
                                                                    onclick="confirmBtcTransfer({{$t->id}}, {{$t->user}}, '{{number_format($t->amount_paid)}}' )"
                                                                    class="btn btn-sm btn-outline-success">Pay
                                                                    BTC</button>
                                                                @else
                                                                <button data-toggle="modal" data-target="#confirm-modal"
                                                                    onclick="confirmTransfer({{$t->id}}, {{$t->user}}, '{{number_format($t->amount_paid)}}' )"
                                                                    class="btn btn-sm btn-outline-success">Pay</button>
                                                                @endif

                                                                @elseif($t->status == 'success' || ($t->type == 'buy' &&
                                                                $t->status ==
                                                                'declined' ) )
                                                                <button data-toggle="modal" data-target="#refund-modal"
                                                                    onclick="confirmRefund({{$t->id}}, {{$t->user}}, '{{number_format($t->amount_paid)}}' )"
                                                                    class="btn btn-sm btn-outline-success">Refund</button>
                                                                @endif

                                                                @endif

                                                                @if (Auth::user()->role == 999) {{-- Super Admin --}}
                                                                <a href="#" data-toggle="modal"
                                                                    data-target="#edit-transac{{$t->id}}">
                                                                    <span class="btn btn-sm btn-info">Edit</span>
                                                                </a>

                                                                @if ($t->status == 'approved')
                                                                @if (\Str::lower($t->card) == 'bitcoins')
                                                                <button data-toggle="modal"
                                                                    data-target="#confirm-btc-modal"
                                                                    onclick="confirmBtcTransfer({{$t->id}}, {{$t->user}}, '{{number_format($t->amount_paid)}}' )"
                                                                    class="btn btn-sm btn-outline-success">Pay
                                                                    BTC</button>
                                                                @else
                                                                <button data-toggle="modal" data-target="#confirm-modal"
                                                                    onclick="confirmTransfer({{$t->id}}, {{$t->user}}, '{{number_format($t->amount_paid)}}' )"
                                                                    class="btn btn-sm btn-outline-success">Pay</button>
                                                                @endif

                                                                @elseif($t->status == 'success' || ($t->type == 'buy' &&
                                                                $t->status ==
                                                                'declined' ))
                                                                <button data-toggle="modal" data-target="#refund-modal"
                                                                    onclick="confirmRefund({{$t->id}}, {{$t->user}}, '{{number_format($t->amount_paid)}}' )"
                                                                    class="btn btn-sm btn-outline-success">Refund</button>
                                                                @endif

                                                                @endif

                                                                @if (Auth::user()->role == 777) {{-- Junior Accountant
                                                                --}}
                                                                @if ($t->status != 'success' && $t->status != 'failed'
                                                                && $t->status != 'declined')
                                                                <a href="#" data-toggle="modal"
                                                                    data-target="#edit-transac"
                                                                    onclick="editTransac({{$t}})"><span
                                                                        class="btn btn-sm btn-info">Edit</span></a>
                                                                @endif

                                                                @if ($t->status == 'approved')
                                                                @if (\Str::lower($t->card) == 'bitcoins')
                                                                <button data-toggle="modal"
                                                                    data-target="#confirm-btc-modal"
                                                                    onclick="confirmBtcTransfer({{$t->id}}, {{$t->user}}, '{{number_format($t->amount_paid)}}' )"
                                                                    class="btn btn-sm btn-outline-success">Pay
                                                                    BTC</button>
                                                                @else
                                                                <button data-toggle="modal" data-target="#confirm-modal"
                                                                    onclick="confirmTransfer({{$t->id}}, {{$t->user}}, '{{number_format($t->amount_paid)}}' )"
                                                                    class="btn btn-sm btn-outline-success">Pay</button>
                                                                @endif
                                                                @elseif($t->status == 'success')
                                                                <button data-toggle="modal" data-target="#refund-modal"
                                                                    onclick="confirmRefund({{$t->id}}, {{$t->user}}, '{{number_format($t->amount_paid)}}' )"
                                                                    class="btn btn-sm btn-outline-success">Refund</button>
                                                                @endif

                                                                @endif
                                                                {{-- Junior Accountant end --}}

                                                                @if (Auth::user()->role == 888 ) {{-- Sales rep --}}
                                                                @if ($t->status != 'success' && $t->status != 'failed'
                                                                && $t->status != 'declined')
                                                                <a href="#" data-toggle="modal"
                                                                    data-target="#edit-transac"
                                                                    onclick="editTransac({{$t}})"><span
                                                                        class="btn btn-sm btn-info">Edit</span></a>
                                                                @endif
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                                <tfoot>
                                                    {{-- <a href=" {{route('admin.transactions-status', 'waiting')}} "><button
                                                            class="m-3 btn btn-outline-info">View all</button></a> --}}
                                                </tfoot>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="tab-pane fade" id="pills-profile" role="tabpanel"
                                    aria-labelledby="pills-profile-tab">
                                    {{-- //////////// Failed Transactions --}}
                                    <div class="col-md-12">
                                        <div class="main-card mb-3 card">
                                            <div class="card-header d-flex justify-content-between">
                                                <span>Failed Transactions</span>
                                                <div class="page-title-subheading">
                                                    <button class="btn btn-primary" onclick="location.reload()"> Refresh
                                                        Page</button>
                                                </div>
                                            </div>
                                            <div class="table-responsive">
                                                <table
                                                    class="align-middle mb-0 table table-borderless table-striped table-hover transactions-table">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">ID</th>
                                                            <th class="text-center">Asset type</th>
                                                            <th class="text-center">Tran. type</th>
                                                            <th class="text-center">Asset value</th>
                                                            <th class="text-center">Cash value</th>
                                                            <th class="text-center">User</th>
                                                            <th class="text-center">Agent</th>
                                                            <th class="text-center">Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($failed_transactions_chinese as $t)
                                                        <tr>
                                                            <td class="text-center text-muted">{{$t->uid}}</td>
                                                            <td class="text-center">{{ucwords($t->card)}}</td>
                                                            <td class="text-center">{{$t->type}}</td>
                                                            <td class="text-center">{{$t->amount}}</td>
                                                            <td class="text-center">{{number_format($t->amount_paid)}}
                                                            </td>
                                                            <td class="text-center"> {{$t->user->first_name}} </td>
                                                            <td class="text-center"> {{$t->agent->first_name}} </td>
                                                            <td class="text-center">
                                                                @switch($t->status)
                                                                @case('success')
                                                                <div class="badge badge-success">{{$t->status}}</div>
                                                                @break
                                                                @case("failed")
                                                                <div class="badge badge-danger">{{$t->status}}</div>
                                                                @break
                                                                @case('declined')
                                                                <div class="badge badge-warning">{{$t->status}}</div>
                                                                @break
                                                                @case('waiting')
                                                                <div class="badge badge-info">{{$t->status}}</div>
                                                                @break
                                                                @default
                                                                <div class="badge badge-success">{{$t->status}}</div>

                                                                @endswitch
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                                <tfoot>
                                                    {{-- <a href=" {{route('admin.transactions-status', 'in progress')}} "><button
                                                            class="m-3 btn btn-outline-info">View all</button></a> --}}
                                                </tfoot>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="pills-contact" role="tabpanel"
                                    aria-labelledby="pills-contact-tab">

                                    {{-- ////////////In progress Transactions --}}
                                    <div class="col-md-12">
                                        <div class="main-card mb-3 card">
                                            <div class="card-header d-flex justify-content-between">
                                                <span>In Progress Transactions</span>
                                                <div class="page-title-subheading">
                                                    <button class="btn btn-primary" onclick="location.reload()"> Refresh
                                                        Page</button>
                                                </div>
                                            </div>
                                            <div class="table-responsive">
                                                <table
                                                    class="align-middle mb-0 table table-borderless table-striped table-hover transactions-table">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">ID</th>
                                                            <th class="text-center">Asset type</th>
                                                            <th class="text-center">Tran. type</th>
                                                            <th class="text-center">Asset value</th>
                                                            <th class="text-center">Cash value</th>
                                                            <th class="text-center">User</th>
                                                            <th class="text-center">Agent</th>
                                                            <th class="text-center">Status</th>
                                                            <th class="text-center">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($in_progress_transactions_chinese as $t)
                                                        <tr>
                                                            <td class="text-center text-muted">{{$t->uid}}</td>
                                                            <td class="text-center">{{ucwords($t->card)}}</td>
                                                            <td class="text-center">{{$t->type}}</td>
                                                            <td class="text-center">{{$t->amount}}</td>
                                                            <td class="text-center">{{number_format($t->amount_paid)}}
                                                            </td>
                                                            <td class="text-center"> {{$t->user->first_name}} </td>
                                                            <td class="text-center"> {{$t->agent->first_name}} </td>
                                                            <td class="text-center">
                                                                @switch($t->status)
                                                                @case('success')
                                                                <div class="badge badge-success">{{$t->status}}</div>
                                                                @break
                                                                @case("failed")
                                                                <div class="badge badge-danger">{{$t->status}}</div>
                                                                @break
                                                                @case('declined')
                                                                <div class="badge badge-warning">{{$t->status}}</div>
                                                                @break
                                                                @case('waiting')
                                                                <div class="badge badge-info">{{$t->status}}</div>
                                                                @break
                                                                @default
                                                                <div class="badge badge-success">{{$t->status}}</div>

                                                                @endswitch
                                                            </td>
                                                            <td>
                                                                <a
                                                                    href="{{route('admin.view-transaction', [$t->id, $t->uid] )}} ">
                                                                    <span class="btn btn-sm btn-success">View</span>
                                                                </a>

                                                                @if (Auth::user()->role == 444 ) {{--test//// super accountant
                                                                options --}}

                                                                <a href="#" data-toggle="modal"
                                                                    data-target="#edit-transac{{$t->id}}"
                                                                    {{-- onclick="editTransac({{$t}})" --}}
                                                                    ><span
                                                                        class="btn btn-sm btn-info">Edit</span></a>

                                                                @if ($t->status == 'approved')
                                                                @if (\Str::lower($t->card) == 'bitcoins')
                                                                <button data-toggle="modal"
                                                                    data-target="#confirm-btc-modal"
                                                                    onclick="confirmBtcTransfer({{$t->id}}, {{$t->user}}, '{{number_format($t->amount_paid)}}' )"
                                                                    class="btn btn-sm btn-outline-success">Pay
                                                                    BTC</button>
                                                                @else
                                                                <button data-toggle="modal" data-target="#confirm-modal"
                                                                    onclick="confirmTransfer({{$t->id}}, {{$t->user}}, '{{number_format($t->amount_paid)}}' )"
                                                                    class="btn btn-sm btn-outline-success">Pay</button>
                                                                @endif

                                                                @elseif($t->status == 'success' || ($t->type == 'buy' &&
                                                                $t->status ==
                                                                'declined' ) )
                                                                <button data-toggle="modal" data-target="#refund-modal"
                                                                    onclick="confirmRefund({{$t->id}}, {{$t->user}}, '{{number_format($t->amount_paid)}}' )"
                                                                    class="btn btn-sm btn-outline-success">Refund</button>
                                                                @endif

                                                                @endif

                                                                @if (Auth::user()->role == 999) {{-- Super Admin --}}
                                                                <a href="#" data-toggle="modal"
                                                                    data-target="#edit-transac{{$t->id}}">
                                                                    <span class="btn btn-sm btn-info">Edit</span>
                                                                </a>

                                                                @if ($t->status == 'approved')
                                                                @if (\Str::lower($t->card) == 'bitcoins')
                                                                <button data-toggle="modal"
                                                                    data-target="#confirm-btc-modal"
                                                                    onclick="confirmBtcTransfer({{$t->id}}, {{$t->user}}, '{{number_format($t->amount_paid)}}' )"
                                                                    class="btn btn-sm btn-outline-success">Pay
                                                                    BTC</button>
                                                                @else
                                                                <button data-toggle="modal" data-target="#confirm-modal"
                                                                    onclick="confirmTransfer({{$t->id}}, {{$t->user}}, '{{number_format($t->amount_paid)}}' )"
                                                                    class="btn btn-sm btn-outline-success">Pay</button>
                                                                @endif

                                                                @elseif($t->status == 'success' || ($t->type == 'buy' &&
                                                                $t->status ==
                                                                'declined' ))
                                                                <button data-toggle="modal" data-target="#refund-modal"
                                                                    onclick="confirmRefund({{$t->id}}, {{$t->user}}, '{{number_format($t->amount_paid)}}' )"
                                                                    class="btn btn-sm btn-outline-success">Refund</button>
                                                                @endif

                                                                @endif

                                                                @if (Auth::user()->role == 777) {{-- Junior Accountant
                                                                --}}
                                                                @if ($t->status != 'success' && $t->status != 'failed'
                                                                && $t->status != 'declined')
                                                                <a href="#" data-toggle="modal"
                                                                    data-target="#edit-transac"
                                                                    onclick="editTransac({{$t}})"><span
                                                                        class="btn btn-sm btn-info">Edit</span></a>
                                                                @endif

                                                                @if ($t->status == 'approved')
                                                                @if (\Str::lower($t->card) == 'bitcoins')
                                                                <button data-toggle="modal"
                                                                    data-target="#confirm-btc-modal"
                                                                    onclick="confirmBtcTransfer({{$t->id}}, {{$t->user}}, '{{number_format($t->amount_paid)}}' )"
                                                                    class="btn btn-sm btn-outline-success">Pay
                                                                    BTC</button>
                                                                @else
                                                                <button data-toggle="modal" data-target="#confirm-modal"
                                                                    onclick="confirmTransfer({{$t->id}}, {{$t->user}}, '{{number_format($t->amount_paid)}}' )"
                                                                    class="btn btn-sm btn-outline-success">Pay</button>
                                                                @endif
                                                                @elseif($t->status == 'success')
                                                                <button data-toggle="modal" data-target="#refund-modal"
                                                                    onclick="confirmRefund({{$t->id}}, {{$t->user}}, '{{number_format($t->amount_paid)}}' )"
                                                                    class="btn btn-sm btn-outline-success">Refund</button>
                                                                @endif

                                                                @endif
                                                                {{-- Junior Accountant end --}}

                                                                @if (Auth::user()->role == 888 ) {{-- Sales rep --}}
                                                                @if ($t->status != 'success' && $t->status != 'failed'
                                                                && $t->status != 'declined')
                                                                <a href="#" data-toggle="modal"
                                                                    data-target="#edit-transac"
                                                                    onclick="editTransac({{$t}})"><span
                                                                        class="btn btn-sm btn-info">Edit</span></a>
                                                                @endif
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                                <tfoot>
                                                    {{-- <a href=" {{route('admin.transactions-status', 'in progress')}} "><button
                                                            class="m-3 btn btn-outline-info">View all</button></a> --}}
                                                </tfoot>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="pills-success" role="tabpanel"
                                    aria-labelledby="pills-success-tab">
                                    <div class="col-md-12">
                                        <div class="main-card mb-3 card">
                                            <div class="card-header d-flex justify-content-between">
                                                <span>Succcessful Transactions</span>
                                                <div class="page-title-subheading">
                                                    <button class="btn btn-primary" onclick="location.reload()"> Refresh
                                                        Page</button>
                                                </div>
                                            </div>
                                            <div class="table-responsive">
                                                <table
                                                    class="align-middle mb-0 table table-borderless table-striped table-hover transactions-table">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">ID</th>
                                                            <th class="text-center">Asset type</th>
                                                            <th class="text-center">Tran. type</th>
                                                            <th class="text-center">Asset value</th>
                                                            <th class="text-center">Cash value</th>
                                                            <th class="text-center">User</th>
                                                            <th class="text-center">Agent</th>
                                                            <th class="text-center">Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($success_transactions_chinese as $t)
                                                        <tr>
                                                            <td class="text-center text-muted">{{$t->uid}}</td>
                                                            <td class="text-center">{{ucwords($t->card)}}</td>
                                                            <td class="text-center">{{$t->type}}</td>
                                                            <td class="text-center">{{$t->amount}}</td>
                                                            <td class="text-center">{{number_format($t->amount_paid)}}
                                                            </td>
                                                            <td class="text-center"> {{$t->user->first_name}} </td>
                                                            <td class="text-center"> {{$t->agent->first_name}} </td>
                                                            <td class="text-center">
                                                                @switch($t->status)
                                                                @case('success')
                                                                <div class="badge badge-success">{{$t->status}}</div>
                                                                @break
                                                                @case("failed")
                                                                <div class="badge badge-danger">{{$t->status}}</div>
                                                                @break
                                                                @case('declined')
                                                                <div class="badge badge-warning">{{$t->status}}</div>
                                                                @break
                                                                @case('waiting')
                                                                <div class="badge badge-info">{{$t->status}}</div>
                                                                @break
                                                                @default
                                                                <div class="badge badge-success">{{$t->status}}</div>

                                                                @endswitch
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                                <tfoot>
                                                    {{-- <a href=" {{route('admin.transactions-status', 'in progress')}} "><button
                                                            class="m-3 btn btn-outline-info">View all</button></a> --}}
                                                </tfoot>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Transactions and Users Overview --}}
            @if (Auth::user()->role == 888)
            <div class="row">
                <div class="main-card mb-3 card col-md-12">
                    <div class="no-gutters row">
                        <div class="col-md-4">
                            <div class="pt-0 pb-0 card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">
                                        <div class="widget-content p-0">
                                            <div class="widget-content-outer">
                                                <div class="widget-content-wrapper">
                                                    <div class="widget-content-left">
                                                        <div class="widget-heading">Total Transactions</div>
                                                        <div class="widget-subheading">Total cash value </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="widget-content p-0">
                                            <div class="widget-content-outer">
                                                <div class="widget-content-wrapper">
                                                    <div class="widget-content-left">
                                                        <div class="widget-heading">Total Transactions</div>
                                                        <div class="widget-subheading">Total Count of succcessful
                                                            transactions</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="pt-0 pb-0 card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">
                                        <div class="widget-content p-0">
                                            <div class="widget-content-outer">
                                                <div class="widget-content-wrapper">
                                                    <div class="widget-content-left">
                                                        <div class="widget-heading">Buy</div>
                                                        <div class="widget-subheading">Transactions</div>
                                                    </div>
                                                    <div class="widget-content-right">
                                                        <div class="widget-numbers text-info">
                                                            N{{number_format($pBuyCash)}}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="widget-content p-0">
                                            <div class="widget-content-outer">
                                                <div class="widget-content-wrapper">
                                                    <div class="widget-content-left">
                                                        <div class="widget-heading">Buy</div>
                                                        <div class="widget-subheading">Transactions</div>
                                                    </div>
                                                    <div class="widget-content-right">
                                                        <div class="widget-numbers text-info">{{$pBuyCount}}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="pt-0 pb-0 card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">
                                        <div class="widget-content p-0">
                                            <div class="widget-content-outer">
                                                <div class="widget-content-wrapper">
                                                    <div class="widget-content-left">
                                                        <div class="widget-heading">Sell</div>
                                                        <div class="widget-subheading">Transactions</div>
                                                    </div>
                                                    <div class="widget-content-right">
                                                        <div class="widget-numbers text-success">
                                                            N{{number_format($pSellCash)}}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="widget-content p-0">
                                            <div class="widget-content-outer">
                                                <div class="widget-content-wrapper">
                                                    <div class="widget-content-left">
                                                        <div class="widget-heading">Sell</div>
                                                        <div class="widget-subheading">Transactions</div>
                                                    </div>
                                                    <div class="widget-content-right">
                                                        <div class="widget-numbers text-success">{{$pSellCount}}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

@foreach ($waiting_transactions_chinese as $cwt)
<div class="modal fade  item-badge-rightm" id="edit-transac{{$cwt->id}}" role="dialog">
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
                        <input type="hidden" readonly name="id" value="{{$cwt->id}}">
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="">Card</label>
                                <select name="card_id" class="form-control">
                                    <option value="{{$cwt->card_id}}" id="e_card">{{$cwt->asset->name}}</option>
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
                                    <option value="{{$cwt->country}}">{{$cwt->country}}</option>
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
                                <input type="text" placeholder="Value" value="{{$cwt->amount}}" class="form-control" name="amount">
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="form-group">
                                <label for="">Cash Value</label>
                                <input type="text" placeholder="Amount paid" value="{{$cwt->amount_paid}}" class="form-control"
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
                                    <option value="{{$cwt->status}}">{{$cwt->status}}</option>
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
                                    <option value="{{$cwt->type}}">{{$cwt->type}}</option>
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
@endforeach

@foreach ($in_progress_transactions_chinese as $cwt)
<div class="modal fade  item-badge-rightm" id="edit-transac{{$cwt->id}}" role="dialog">
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
                        <input type="hidden" readonly name="id" value="{{$cwt->id}}">
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="">Card</label>
                                <select name="card_id" class="form-control">
                                    <option value="{{$cwt->card_id}}" id="e_card">{{$cwt->asset->name}}</option>
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
                                    <option value="{{$cwt->country}}">{{$cwt->country}}</option>
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
                                <input type="text" placeholder="Value" value="{{$cwt->amount}}" class="form-control" name="amount">
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="form-group">
                                <label for="">Cash Value</label>
                                <input type="text" placeholder="Amount paid" value="{{$cwt->amount_paid}}" class="form-control"
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
                                    <option value="{{$cwt->status}}">{{$cwt->status}}</option>
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
                                    <option value="{{$cwt->type}}">{{$cwt->type}}</option>
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
@endforeach

@if($usersChart)
{!! $usersChart->script() !!}
@endif
@endsection
