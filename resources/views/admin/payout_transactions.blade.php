@php
$cards = App\Card::orderBy('name', 'asc')->get(['name', 'id']);
@endphp
@extends('layouts.app')
@section('content')
<div class="app-main">
    <div class="app-sidebar sidebar-shadow">
        <div class="app-header__logo">
            <div class="logo-src"></div>
            <div class="header__pane ml-auto">
                <div>
                    <button type="button" class="hamburger close-sidebar-btn hamburger--elastic" data-class="closed-sidebar">
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
                        <div>Payout  Transactions
                            <div class="page-title-subheading">Hi {{Auth::user()->first_name}}, good to see you again
                                Boss.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">

                <div class="col-md-6 col-xl-3">
                    <div class="card mb-3 widget-content bg-ripe-malin">
                        <div class="widget-content-wrapper py-2 text-white">
                            <div class="widget-content-actions mx-auto ">
                                <div class="widget-heading text-center">
                                    <h5>Number of GiftCard <br>Transactions</h5>
                                    <h6>{{ number_format($giftcard_tranx_count)}}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card mb-3 widget-content bg-ripe-malin">
                        <div class="widget-content-wrapper py-2 text-white">
                            <div class="widget-content-actions mx-auto ">
                                <div class="widget-heading text-center">
                                    <h5>Worth of all <br>Traded Assets</h5>
                                    <h6>{{number_format($total_traded_asset)}}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-2">
                    <div class="card mb-3 widget-content bg-ripe-malin">
                        <div class="widget-content-wrapper py-2 text-white">
                            <div class="widget-content-actions mx-auto ">
                                <div class="widget-heading text-center">
                                    <h5>Total Sum of Chinese Amount</h5>
                                    <h6>{{number_format($total_chinese_amount)}}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-2">
                    <div class="card mb-3 widget-content bg-ripe-malin">
                        <div class="widget-content-wrapper py-2 text-white">
                            <div class="widget-content-actions mx-auto ">
                                <div class="widget-heading text-center">
                                    <h5>Gift cards Asset volume</h5>
                                    <h6>{{number_format($payoutVolume)}}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- payoutVolume
                assetsInNaira --}}
                <div class="col-md-6 col-xl-2">
                    <div class="card mb-3 widget-content bg-ripe-malin">
                        <div class="widget-content-wrapper py-2 text-white">
                            <div class="widget-content-actions mx-auto ">
                                <div class="widget-heading text-center">
                                    <h5>Total card volume in Naira </h5>
                                    <h6>N{{ number_format($assetsInNaira) }}</h6>
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
                        <div class="card-header">Payout Transactions </div>

                        <div class="col-md-12">
                            <div class="col-md-12">
                                <div class="main-card mb-3 card">
                                    <div class="card-header d-flex justify-content-between">
                                        <span>Succcessful Transactions</span>
                                        <div class="page-title-subheading d-flex">
                                            <a href="{{route('admin.payout_history')}}" class="btn btn-primary mr-2"> History </a>
                                            @if(Auth::user()->role == 999)
                                            <form action="{{route('admin.payout')}}" method="POST">@csrf
                                                <button class="btn btn-primary">WIPE TRANSACTIONS</button>
                                            </form>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">ID</th>
                                                    <th class="text-center">Asset type</th>
                                                    <th class="text-center">Tran. type</th>
                                                    <th class="text-center">Asset value</th>
                                                    <th class="text-center">Chinese amount</th>
                                                    <th class="text-center">User</th>
                                                    <th class="text-center">Agent</th>
                                                    <th class="text-center">Date</th>
                                                    <th class="text-center">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($success_transactions as $t)
                                                <tr>

                                                    <td class="text-center text-muted">
                                                        <a href="{{route('admin.view-transaction', [$t->id, $t->uid] )}} ">
                                                                <span>{{$t->uid}}</span>
                                                        </a>
                                                    </td>
                                                    <td class="text-center">{{ucwords($t->card)}}</td>
                                                    <td class="text-center">{{$t->type}}</td>
                                                    <td class="text-center">{{$t->amount}}</td>
                                                    <td class="text-center">{{number_format($t->amount_paid + $t->commission)}}
                                                    </td>
                                                    <td class="text-center"> {{$t->user->first_name .' '. $t->user->last_name}} </td>
                                                    <td class="text-center"> {{$t->agent->first_name}} </td>
                                                    <td class="text-center">{{ $t->created_at->format('d M y, h:ia') }}</td>
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
                                            <a href=" {{route('admin.payout_transactions', 'all')}} "><button class="m-3 btn btn-outline-info">View all</button></a>
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
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
                                {{-- @if(in_array(Auth::user()->role,[444]))
                                    <input type="text" placeholder="Amount paid" id="e_amount_paid" value="{{$cwt->amount_paid + $cwt->commission}}" class="form-control" name="amount_paid">
                                @else
                                    <input type="text" placeholder="Amount paid" id="e_amount_paid" value="{{$cwt->amount_paid}}" class="form-control" name="amount_paid">
                                @endif --}}
                                <input type="text" placeholder="Amount paid" id="e_amount_paid" class="form-control" name="amount_paid">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <!-- ///////////// WORK IN PROGRESS ////////////// -->
                            <div class="form-group">
                                <label for="">Status</label>
                                <select onchange="feedback_status()" id="f_status" name="status" class="form-control">
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
                                    <option value="The card/code was not clear"> The card/code was not clear </option>
                                    <option value="Your card/code needed more info"> Your card/code needed more info
                                    </option>
                                    <option value="Multiple transaction was opened"> Multiple transaction was opened
                                    </option>
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

@if($usersChart)
{!! $usersChart->script() !!}
@endif
@endsection
