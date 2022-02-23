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
                        <div class="text-capitalize"> {{$segment}} Transactions
                            <div class="page-title-subheading">
                                <button class="btn btn-primary" onclick="location.reload()">Refresh Page</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if(isset($totalTransactions))
                @if (in_array(Auth::user()->role, [999] ) and isset($totalTransactions))
                    <div class="row">
                        <div class="col-md-3 col-xl-3">
                            <div class="card mb-3 widget-content bg-grow-early">
                                <div class="widget-content-wrapper py-2 text-white">
                                    <div class="widget-content-actions mx-auto ">
                                        <div class="widget-heading text-center">
                                            <h5>Total GC Transactions </h5>
                                            <h6>{{number_format($totalTransactions,2,".",",")}}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 col-xl-3">
                            <div class="card mb-3 widget-content bg-happy-fisher">
                                <div class="widget-content-wrapper py-2 text-white">
                                    <div class="widget-content- mx-auto">
                                        <div class="widget-heading text-center">
                                            <h5>Total GC volume</h5>
                                            <h6>{{number_format($totalVol,2,".",",")}}</h6>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-xl-3">
                            <div class="card mb-3 widget-content bg-sunny-morning">
                                <div class="widget-content-wrapper py-2 text-white">
                                    <div class="widget-content- mx-auto">
                                        <div class="widget-heading text-center">
                                            <h5>Total Commission</h5>
                                            <h6>{{number_format($totalComm,2,".",",")}}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 col-xl-3">
                            <div class="card mb-3 widget-content bg-ripe-malin">
                                <div class="widget-content-wrapper py-2 text-white">
                                    <div class="widget-content- mx-auto">
                                        <div class="widget-heading text-center">
                                            <h5>Total Chinese Amount</h5>
                                            <h6>{{number_format($totalChineseAmt,2,".",",")}}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 col-xl-3">
                            <div class="card mb-3 widget-content bg-ripe-malin">
                                <div class="widget-content-wrapper py-2 text-white">
                                    <div class="widget-content- mx-auto">
                                        <div class="widget-heading text-center">
                                            <h5>Avg. num of trades per day</h5>
                                            <h6>{{$totalAvgPerToday}}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">

                        <div class="card-header justify-content-between">{{$segment}} Transactions
                            {{-- Search for all users --}}
                            <form action="@if (in_array(Auth::user()->role, [555] ))
                                            {{route('customerHappiness.search-tnxs')}}
                                            @else
                                            {{route('admin.search-tnxs')}}
                                        @endif"
                            class="form-inline p-2"

                                method="POST">
                                @csrf
                                <div class="form-group mr-2">
                                    <label for=""> Search </label>
                                    <input type="text" required name="search" class="ml-2 form-control">
                                    <input type="hidden" name="segment" value="{{ $segment }}" class="ml-2 form-control">
                                </div>
                                <button class="btn btn-outline-primary"><i class="fa fa-search"></i></button>
                            </form>


                            {{-- <form action="@if (in_array(Auth::user()->role, [555] ))
                                            {{route('customerHappiness.transactions-by-date')}}
                                            @else
                                            {{route('admin.transactions-by-date')}}
                                        @endif"
                            class="form-inline p-2" method="POST">
                                @csrf
                                <div class="form-group mr-2">
                                    <label for="">Start date </label>
                                    <input type="date" required name="start" class="ml-2 form-control">
                                </div>

                                @endif


                                <div class="form-group mr-1">
                                    <label for="">Start</label>
                                    <input type="date" required name="start" class="ml-1 form-control">
                                </div>
                                <div class="form-group mr-1">
                                    <label for="">End</label>
                                    <input type="date" required name="end" class="ml-1 form-control">
                                </div>
                                @if (isset($status))

                                    <div class="form-group mr-1">
                                        <select name="status" class="ml-1 form-control">
                                            <option value="null">Status</option>
                                            @foreach ($status as $s)
                                                <option value="{{ $s->Status }}">{{ $s->Status }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                <button class="btn btn-outline-primary"><i class="fa fa-filter"></i></button>
                            </form> --}}

                            {{-- @if (!in_array(Auth::user()->role, [555] )) --}}
                            <form class="form-inline p-2"
                                method="GET">
                                {{-- @csrf --}}
                                <div class="form-group mr-2">
                                    <label for="">Start date </label>
                                    <input type="date" required name="start" value="{{app('request')->input('start')}}" class="ml-2 form-control">
                                </div>
                                <div class="form-group mr-2">
                                    <label for="">End date </label>
                                    <input type="date" required name="end" value="{{app('request')->input('end')}}" class="ml-2 form-control">
                                </div>
                                <button class="btn btn-outline-primary"><i class="fa fa-filter"></i></button>
                            </form>
                            {{-- @endif --}}
                        </div>
                        <div class="table-responsive p-3">
                            @foreach ($errors->all() as $err)
                            <span class="text-danger">{{ $err }}</span>
                            @endforeach
                            @if (in_array(Auth::user()->role, [999,899]))

                            <table class="align-middle mb-4 table table-bordered table-striped ">
                                <thead>
                                    <tr>
                                        <th class="text-center">Total Transaction Number</th>
                                        <th class="text-center">Total Asset Value</th>
                                        <th class="text-center">Total Card Price</th>
                                        <th class="text-center">Total Cash Value</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <tr>
                                        <td class="text-center">{{ number_format($total_transactions) }}</td>
                                        <td class="text-center">{{ number_format($asset_value_total) }}</td>
                                        <td class="text-center">₦{{ number_format($card_price_total,3) }}</td>
                                        <td class="text-center">₦{{ number_format($cash_value_total) }}</td>


                                    </tr>
                                </tbody>
                            </table>
                            @endif
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
                                        @if (in_array(Auth::user()->role, [444,449] ))
                                        <th class="text-center">Cash value</th>
                                        @endif
                                        @if (!in_array(Auth::user()->role, [449,444] ))
                                        <th class="text-center">User Amount</th>
                                        @endif
                                        @if (in_array(Auth::user()->role, [999] ))
                                            <th class="text-center">Commission</th>
                                            <th class="text-center">Chinese Amount</th>
                                        @endif
                                        @if (!in_array(Auth::user()->role, [449,444] ))
                                        <th class="text-center">Wallet ID</th>
                                        @endif
                                        <th class="text-center">User</th>
                                        <th class="text-center">Date</th>
                                        <th class="text-center">Status</th>
                                        @if (in_array(Auth::user()->role, [999, 889] ))
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

                                        @if ($t->asset->is_crypto)
                                        <td class="text-center">{{ sprintf('%.8f', floatval($t->quantity))}}</td>
                                        @else
                                        <td class="text-center">{{ $t->quantity}}</td>
                                        @endif
                                        <td class="text-center">{{$t->card_price}}</td>
                                        @if (in_array(Auth::user()->role, [444,449] ))
                                        <td class="text-center">N{{number_format($t->amount_paid)}}</td>
                                        @endif

                                        {{-- <td class="text-center">{{$t->wallet_id}}</td> --}}
                                        {{-- @if (isset($t->user))
                                        <td class="text-center">
                                            @if (in_array(Auth::user()->role, [555] ))
                                                <a
                                                href=" {{route('customerHappiness.user', [$t->user->id, $t->user->email] )}}">
                                                {{$t->user->first_name." ".$t->user->last_name}}</a>
                                              @else
                                                <a
                                                href=" {{route('admin.user', [$t->user->id, $t->user->email] )}}">
                                                {{$t->user->first_name." ".$t->user->last_name}}</a>
                                            @endif

                                        </td>
                                        @endif --}}

                                        @if (!in_array(Auth::user()->role, [449,444] ))
                                            <td class="text-center">N{{number_format($t->amount_paid - $t->commission)}}</td>
                                        @endif
                                        @if (in_array(Auth::user()->role, [999] ))
                                            <td class="text-center">{{$t->commission}}</td>
                                            <td class="text-center">N{{number_format($t->amount_paid)}}</td>
                                        @endif
                                        @if (!in_array(Auth::user()->role, [449,444] ))
                                        <td class="text-center">{{$t->wallet_id}}</td>
                                        @endif

                                        <td class="text-center">
                                            @if (isset($t->user))
                                                @if (in_array(Auth::user()->role, [555] ))
                                                    <a
                                                    href=" {{route('customerHappiness.user', [$t->user->id, $t->user->email] )}}">
                                                    {{$t->user->first_name." ".$t->user->last_name}}</a>
                                                  @else
                                                    @if (in_array(Auth::user()->role, [449,444] ))
                                                     {{$t->user->first_name." ".$t->user->last_name}}
                                                     @else
                                                     <a
                                                     href=" {{route('admin.user', [$t->user->id, $t->user->email] )}}">
                                                     {{$t->user->first_name." ".$t->user->last_name}}</a>
                                                    @endif


                                                @endif
                                            @endif
                                        </td>

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
                                        @if (in_array(Auth::user()->role, [999, 889] ))
                                        <td class="text-center"> {{$t->last_edited}} </td>
                                        <td class="text-center"> {{$t->agent->first_name}} </td>
                                        <td class="text-center"> {{$t->accountant->first_name ?? 'None' }} </td>
                                        @endif

                                        <td>
                                            @if (!in_array(Auth::user()->role, [555] ))
                                                <a href="{{route('admin.view-transaction', [$t->id, $t->uid] )}} ">
                                                    @if (Auth::user()->role != 888 )
                                                        <span class="btn btn-sm btn-success">View</span>
                                                    @endif
                                                </a>
                                            @endif

                                            @if (Auth::user()->role == 889 ) {{-- super accountant options --}}
                                                @if ($t->asset->is_crypto)
                                                <a href="#" data-toggle="modal" data-target="#edit-transac"
                                                    onclick="editTransac({{$t}})"><span
                                                        class="btn btn-sm btn-info">Edit</span></a>
                                                @endif
                                                @if ($t->status == 'approved')
                                                    @if (\Str::lower($t->card) == 'bitcoins')
                                                        <button data-toggle="modal" data-target="#confirm-btc-modal"
                                                        onclick="confirmBtcTransfer({{$t->id}}, {{$t->user}}, '{{number_format($t->amount_paid)}}' )"
                                                        class="btn btn-sm btn-outline-success">Pay BTC</button>
                                                    @else
                                                        <button data-toggle="modal" data-target="#confirm-modal"
                                                        onclick="confirmTransfer({{$t->id}}, {{$t->user}}, '{{number_format($t->amount_paid)}}' )"
                                                        class="btn btn-sm btn-outline-success">Pay</button>
                                                    @endif

                                                @elseif($t->status == 'success' || ($t->type == 'buy' && $t->status ==
                                                'declined' ) )
                                                <button data-toggle="modal" data-target="#refund-modal"
                                                    onclick="confirmRefund({{$t->id}}, {{$t->user}}, '{{number_format($t->amount_paid)}}' )"
                                                    class="btn btn-sm btn-outline-success">Refund</button>
                                                @endif

                                            @endif

                                            @if (Auth::user()->role == 999) {{-- Super Admin --}}
                                            <a href="#" data-toggle="modal" data-target="#edit-transac" onclick="editTransac({{$t}})">
                                                <span class="btn btn-sm btn-info">Edit</span>
                                            </a>

                                            @if ($t->status == 'approved')
                                                @if (\Str::lower($t->card) == 'bitcoins')
                                                        <button data-toggle="modal" data-target="#confirm-btc-modal"
                                                        onclick="confirmBtcTransfer({{$t->id}}, {{$t->user}}, '{{number_format($t->amount_paid)}}' )"
                                                        class="btn btn-sm btn-outline-success">Pay BTC</button>
                                                    @else
                                                        <button data-toggle="modal" data-target="#confirm-modal"
                                                        onclick="confirmTransfer({{$t->id}}, {{$t->user}}, '{{number_format($t->amount_paid)}}' )"
                                                        class="btn btn-sm btn-outline-success">Pay</button>
                                                @endif

                                            @elseif($t->status == 'success' || ($t->type == 'buy' && $t->status ==
                                            'declined' ))
                                            <button data-toggle="modal" data-target="#refund-modal"
                                                onclick="confirmRefund({{$t->id}}, {{$t->user}}, '{{number_format($t->amount_paid)}}' )"
                                                class="btn btn-sm btn-outline-success">Refund</button>
                                            @endif

                                            @endif

                                            @if (Auth::user()->role == 777) {{-- Junior Accountant --}}
                                                @if ($t->status != 'success' && $t->status != 'failed' && $t->status != 'declined')
                                                    <a href="#" data-toggle="modal" data-target="#edit-transac"
                                                        onclick="editTransac({{$t}})"><span
                                                            class="btn btn-sm btn-info">Edit</span></a>
                                                @endif

                                                @if ($t->status == 'approved')
                                                    @if (\Str::lower($t->card) == 'bitcoins')
                                                        <button data-toggle="modal" data-target="#confirm-btc-modal"
                                                        onclick="confirmBtcTransfer({{$t->id}}, {{$t->user}}, '{{number_format($t->amount_paid)}}' )"
                                                        class="btn btn-sm btn-outline-success">Pay BTC</button>
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

                                            @if (Auth::user()->role == 888 OR Auth::user()->role == 444 OR Auth::user()->role == 449 ) {{-- Sales rep --}}
                                                @if ($t->status != 'success' && $t->status != 'failed' && $t->status != 'declined')
                                                <a href="#" data-toggle="modal" data-target="#edit-transac"
                                                    onclick="editTransac({{$t}})"><span
                                                        class="btn btn-sm btn-info">Edit</span></a>
                                                @endif
                                            @endif



                                            @if($t->status == 'waiting' && (Auth::user()->role == 444 OR Auth::user()->role == 449))
                                                <form action="{{route('admin.transfer-chinese',$t->id)}} " method="post" class="admin-action">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{$t->id}}" required class="form-control">
                                                    <button class="btn btn-block c-rounded bg-custom-gradient admin-action">
                                                        Pay User
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{-- {{$transactions->links() ?? '' }} --}}
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
                        @if (Auth::user()->role != 888)

                        <div class="col">
                            <!-- ///////////// WORK IN PROGRESS ////////////// -->
                            <div class="form-group">
                                <label for="">Status</label>
                                <select onchange="feedback_status()" id="f_status" name="status" class="form-control"
                                @if (Auth::user()->role == 888)
                                    {{ "disabled" }}
                                @endif>
                                    <option value="" id="e_status"></option>
                                    @if (in_array(Auth::user()->role, [889, 777, 999, 444, 449]))
                                    <option value="success">Success</option>
                                    @endif
                                    <option value="waiting">Waiting</option>
                                    <option value="in progress">In progress</option>
                                    <option value="failed">Failed</option>
                                    <option value="declined">Declined</option>
                                </select>
                            </div>
                        </div>
                        @endif
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
                        <div class="col">
                            <div class="form-group">
                                <label for="">Quantity</label>
                                <input type="text" placeholder="Amount paid" id="e_quantity" class="form-control"
                                    name="quantity">
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
