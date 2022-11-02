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
            <div class="row mt-2">
                <div class="col mw-100 mh-100">
                    {{-- bg-primary text-white --}}
                    <a href="{{ route('admin.naira-p2p.type',['type' => 'deposit']) }}">
                        <div class="card mb-1 widget-content p-4 @if ($type == 'deposit' AND $status == null)
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
                <div class="col mw-100 mh-100">
                    {{-- bg-primary text-white --}}
                    <a href="{{ route('admin.naira-p2p.type',['type' => 'withdrawal']) }}">
                        <div class="card mb-1 widget-content p-4 @if ($type == 'withdrawal' AND $status == null)
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
                <div class="col mw-100 mh-100">
                    {{-- bg-primary text-white --}}
                    <a href="{{ route('admin.naira-p2p.type',['type' => 'deposit','status'=>'unresolved']) }}">
                        <div class="card mb-1 widget-content p-4 @if ($type == 'deposit' AND $status == "unresolved")
                        bg-primary
                    @endif">
                            <div class="widget-content-wrapper">
                                <div class="widget-heading">
                                    <h5 class="text-center @if ($type == 'deposit' AND $status == "unresolved")
                                        text-white
                                    @endif">Unresolved Deposit [{{ $deposit_unresolved_tnx }}]</h5>
                                    <p class="text-center @if ($type == 'deposit' AND $status == "unresolved")
                                        text-white
                                    @endif">Open Transactions</p>
                                </div>

                            </div>
                        </div>
                    </a>
                </div>
                <div class="col mw-100 mh-100">
                    {{-- bg-primary text-white --}}
                    <a href="{{ route('admin.naira-p2p.type',['type' => 'withdrawal','status'=>'unresolved']) }}">
                        <div class="card mb-1 widget-content
                        @if ($type == 'withdrawal' AND $status == "unresolved")
                         bg-primary
                        @endif">
                            <div class="widget-content-wrapper">
                                <div class="widget-heading">
                                    <h5 class="text-center
                                    @if ($type == 'withdrawal' AND $status == "unresolved")
                                    text-white
                                    @endif">Unresolved Withdrawal <br>[{{ $withdrawal_unresolved_tnx }}]</h5>
                                    <p class="text-center
                                    @if ($type == 'withdrawal' AND $status == "unresolved")
                                    text-white
                                    @endif">Open Transactions</p>
                                </div>

                            </div>
                        </div>
                    </a>
                </div>
                <div class="col mw-100 mh-100">
                    {{-- bg-primary text-white --}}
                    <a href="{{ route('admin.naira-p2p.type',['type' => 'deposit','status'=>'success']) }}">
                        <div class="card mb-1 widget-content 
                        @if ($type == 'deposit' AND $status == "success")
                         bg-primary
                        @endif">
                            <div class="widget-content-wrapper">
                                <div class="widget-heading">
                                    <h5 class="text-center @if ($type == 'deposit' AND $status == "success")
                                        text-white
                                    @endif">Successfull/Paid Deposit [{{ $deposit_success_tnx }}]</h5>
                                    <p class="text-center
                                    @if ($type == 'deposit' AND $status == "success")
                                        text-white
                                    @endif">Open Transactions</p>
                                </div>

                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col mw-100 mh-100">
                    {{-- bg-primary text-white --}}
                    <a href="{{ route('admin.naira-p2p.type',['type' => 'deposit','status'=>'waiting']) }}">
                        <div class="card mb-1 widget-content p-4
                        @if ($type == 'deposit' AND $status == "waiting")
                         bg-primary
                        @endif">
                            <div class="widget-content-wrapper">
                                <div class="widget-heading">
                                    <h5 class="text-center @if ($type == 'deposit' AND $status == "waiting")
                                    text-white
                                     @endif">Waiting Deposit [{{ $deposit_waiting_tnx }}]</h5>
                                    <p class="text-center @if ($type == 'deposit' AND $status == "waiting")
                                    text-white
                                    @endif">₦ {{ number_format($deposit_waiting_amount) }}</p>
                                </div>

                            </div>
                        </div>
                    </a>
                </div>
                <div class="col mw-100 mh-100">
                    {{-- bg-primary text-white --}}
                    <a href="{{ route('admin.naira-p2p.type',['type' => 'withdrawal','status'=>'waiting']) }}">
                        <div class="card mb-1 widget-content @if ($type == 'withdrawal' AND $status == "waiting")
                        bg-primary
                       @endif">
                            <div class="widget-content-wrapper">
                                <div class="widget-heading">
                                    <h5 class="text-center @if ($type == 'withdrawal' AND $status == "waiting")
                                    text-white
                                   @endif">Waiting Withdrawal <br>[{{ $withdrawal_waiting_tnx }}]</h5>
                                    <p class="text-center @if ($type == 'withdrawal' AND $status == "waiting")
                                    text-white
                                   @endif">₦ {{ number_format($withdrawal_waiting_amount) }}</p>
                                </div>

                            </div>
                        </div>
                    </a>
                </div>
                <div class="col mw-100 mh-100">
                    {{-- bg-primary text-white --}}
                    <a href="{{ route('admin.naira-p2p.type',['type' => 'deposit','status'=>'cancelled']) }}">
                        <div class="card mb-1 widget-content p-4
                        @if ($type == 'deposit' AND $status == "cancelled")
                         bg-primary
                        @endif">
                            <div class="widget-content-wrapper">
                                <div class="widget-heading">
                                    <h5 class="text-center @if ($type == 'deposit' AND $status == "cancelled")
                                    text-white
                                     @endif">Declined Deposit [{{ $deposit_denied_tnx }}]</h5>
                                    <p class="text-center @if ($type == 'deposit' AND $status == "cancelled")
                                    text-white
                                    @endif">₦ {{ number_format($deposit_denied_amount) }}</p>
                                </div>

                            </div>
                        </div>
                    </a>
                </div>
                <div class="col mw-100 mh-100">
                    {{-- bg-primary text-white --}}
                    <a href="{{ route('admin.naira-p2p.type',['type' => 'withdrawal','status'=>'cancelled']) }}">
                        <div class="card mb-1 widget-content @if ($type == 'withdrawal' AND $status == "cancelled")
                        bg-primary
                       @endif">
                            <div class="widget-content-wrapper">
                                <div class="widget-heading">
                                    <h5 class="text-center @if ($type == 'withdrawal' AND $status == "cancelled")
                                    text-white
                                   @endif">Declined Withdrawal <br>[{{ $withdrawal_denied_tnx }}]</h5>
                                    <p class="text-center @if ($type == 'withdrawal' AND $status == "cancelled")
                                    text-white
                                   @endif">₦ {{ number_format($withdrawal_denied_amount) }}</p>
                                </div>

                            </div>
                        </div>
                    </a>
                </div>

                <div class="col mw-100 mh-100">
                    {{-- bg-primary text-white --}}
                    <a href="{{ route('admin.naira-p2p.type',['type' => 'withdrawal','status'=>'success']) }}">
                        <div class="card mb-1 widget-content @if ($type == 'withdrawal' AND $status == "success")
                        bg-primary
                       @endif">
                            <div class="widget-content-wrapper">
                                <div class="widget-heading">
                                    <h5 class="text-center @if ($type == 'withdrawal' AND $status == "success")
                                    text-white
                                   @endif">Successfull/Paid Withdrawal [{{ $withdrawal_success_tnx }}]</h5>
                                    <p class="text-center @if ($type == 'withdrawal' AND $status == "success")
                                    text-white
                                   @endif">Open Transactions</p>
                                </div>

                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="row mt-2">
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
                                        <th>Reference</th>
                                        @if ((!in_array($status,['waiting','unresolved'])) AND in_array($type,['withdrawal','deposit']))
                                        @if($type == 'withdrawal')
                                        <th>Account Number</th>
                                        <th>Bank Name</th>
                                        @endif
                                        <th>Amount</th>
                                        @endif

                                        <th>Prev Balance</th>
                                        <th>Current Balance</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        @if(in_array($status,['unresolved']))
                                        <th>Time Duration</th>
                                        @endif
                                        @if(!in_array($status,[null,'cancelled']))
                                        <th>Action</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transactions as $key => $t)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>
                                            @if ($t->user->total_trx > 10000000)
                                            {{-- @if ($t->user->total_trx > 10000) --}}
                                                <span><i class="fa fa-star" style="color: green;"></i></span> {{$t->user->total_trx}}
                                            @endif
                                            {{ $t->user->first_name .' '. $t->user->last_name }}</td>
                                        <td>{{ $t->reference }}</td>
                                        @if ((!in_array($status,['waiting','unresolved'])) AND in_array($type,['withdrawal','deposit']))
                                            @if($type == 'withdrawal')
                                            <td>{{ isset($t->account) ? $t->account->account_number : ''}}</td>
                                            <td>{{ isset($t->account) ? $t->account->bank_name : ''}}</td>
                                            @endif
                                            <td class="text-danger">{{ isset($t->amount) ? $t->amount : ''}}</td>
                                        @endif
                                        <td>₦{{ number_format($t->naira_transactions->previous_balance) }}</td>
                                        <td>₦{{ number_format($t->naira_transactions->current_balance) }}</td>
                                        <td>{{ $t->created_at->format('d M y, h:ia') }}</td>
                                        <td>
                                            @switch($t->status)
                                                @case('success')
                                                    <span class="text-success">{{ ucwords($t->status) }}</span>
                                                    @break

                                                @case('unresolved')
                                                    <span class="text-warning">{{ ucwords($t->status) }}</span>
                                                    @break

                                                @case('cancelled')
                                                <span class="text-danger">{{ 'Declined' }}</span>
                                                    @break
                                                
                                                @case('waiting')
                                                    <span class="text-primary">{{ ucwords($t->status) }}</span>
                                                    @break

                                                @default
                                                    <span>{{ ucwords($t->status) }}</span>
                                            @endswitch
                                        </td>
                                        @if(in_array($status,['unresolved']))
                                        <td>{{ now()->diffForHumans($t->updated_at) }}</td>
                                        @endif

                                        <td>
                                            <div class="btn-group">
                                                @if(!in_array($status,[null,'cancelled']))
                                                    @if (in_array($t->status,['waiting','unresolved']))
                                                        {{-- <button data-toggle="modal" data-target="#pay-view-{{ $t->id }}" class="btn btn-primary">Pay</button> --}}
                                                        <a class="text-white" href="{{ route('admin.naira-p2p.view', $t) }}"><button  class="btn btn-primary">Pay</button></a>
                                                        {{-- <button data-toggle="modal" data-target="#confirm-modal-{{ $t->id }}" class="btn btn-primary">Approve</button>
                                                        <button class="btn btn-danger" data-toggle="modal" data-target="#cancel-modal-{{ $t->id }}">Cancel</button> --}}
                                                    
                                                    @elseif($t->status == 'success')
                                                        @if(in_array(Auth::user()->role, [999, 889]))
                                                            <button class="btn btn-danger" data-toggle="modal" data-target="#refund-modal-{{ $t->id }}">Refund</button>
                                                        @endif
                                                        
                                                    @endif
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


@if (in_array($status,['waiting','unresolved']))
    @foreach ($transactions as $t)
    <div class="modal fade" id="pay-view-{{ $t->id }}">
        <div class="modal-dialog">
                <form action="{{route('admin.naira-p2p.update', $t)}}" id="freeze-form" method="post"> @method('put')
                @csrf
                <div class="modal-content c-rounded">
                    <div class="modal-header bg-light c-rounded-top p-4 ">
                        
                    </div>
                    <!-- Modal Header -->
                    <div class="modal-header bg-light c-rounded-top p-4 ">
                        <h4 class="modal-title">{{ ucwords($status)." ".ucwords($type) }}
                            @if($status == 'unresolved')
                            <p class="text-warning">{{ now()->diffForHumans($t->updated_at) }}</p>
                            @endif
                        </h4>
                        <button type="button" class="close bg-light" data-dismiss="modal">&times;</button>
                        
                    </div>
                    
                    <!-- Modal body -->

                    <div class="modal-body p-4">
                        <div class="row">
                            @if($t->is_flagged == 1)
                            <div class="col-md-12 mb-2">
                                <div class="bg-danger text-white text-center">This Transaction is flagged for Bulk Withdrawal<br>Contact the Junior Accountant to Confirm Action</div>
                            <div>
                            @endif
                            <div class="col-md-12 mt-2">
                                <label for="reason" >Name</label>
                                <input type="text" class="form-control mb-2" type="text" value="{{$t->user->first_name." ".$t->user->last_name}}" disabled>

                                @php
                                    $name = $t->user->first_name." ".$t->user->last_name;
                                @endphp

                                @if($t->type == 'withdrawal')
                                <label class="mt-2" for="reason" >Account Number</label>
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" type="text" id="accountNumbercopy" value="{{$t->account->account_number}}" readonly>
                                    @if($t->status != 'success')
                                    <div class="input-group-append">
                                        <button data-clipboard-target="#accountNumbercopy" class="input-group-text" id="accNoCopy" onclick="copyData('accountNumbercopy', '{{ $name }} Account Number is')">
                                        <img src="{{asset('svg/copy_btn.svg')}}" />
                                        </button>
                                    </div>
                                    @endif
                                </div>

                                <label class="mt-2" for="reason">Bank Name</label>
                                <input type="text" input class="form-control mb-2" type="text" id="bankName" value="{{$t->account->bank_name}}" disabled>
                                @endif

                                <label class="mt-2" for="reason">Amount</label>
                                <div class="input-group mb-2">
                                    <input type="text" input class="form-control" type="text" id="amountcopy" value="{{$t->amount}}" readonly>
                                    @if($t->status != 'success')
                                    <div class="input-group-append">
                                        <button data-clipboard-target="#amountcopy" class="input-group-text" id="amountNoCopy" onclick="copyData('amountcopy','{{ $name }} Amount is')">
                                        <img src="{{asset('svg/copy_btn.svg')}}" />
                                        </button>
                                    </div>
                                    @endif
                                </div>

                                @if($t->status == 'success')
                                <label for="reason" >Ref Num</label>
                                <input type="text" input class="form-control mb-2" type="text" value="{{$t->reference}}" disabled>

                                <label for="reason" >Status</label>
                                <input type="text" input class="form-control mb-2" type="text" value="{{$t->status}}" disabled>
                                @endif
                                @if($t->status != 'success')
                                <div class="btn-toolbar float-right" role="toolbar" id="p2p_buttongroup-{{ $t->id }}">
                                    <div class="btn-group mr-2 float-right" role="group" aria-label="First group">
                                        <button type="button" onclick="buttonSelect('statusInput-{{ $t->id }}','approve',{{ $t->id }})" class="mt-2 btn btn-outline-primary c-rounded txn-btn">Approve</button>
                                    </div>
                                    @if($t->status != 'unresolved')
                                    <div class="btn-group mr-2 float-right" role="group">
                                        <button type="button" onclick="buttonSelect('statusInput-{{ $t->id }}','decline',{{ $t->id }})" class="mt-2 btn btn-outline-primary c-rounded txn-btn">Decline</button>
                                    </div>
                                    @endif

                                    @if($t->type == 'withdrawal' AND $t->status != 'unresolved')
                                        <div class="btn-group mr-2 float-right" role="group">
                                        <button type="button"onclick="buttonSelect('statusInput-{{ $t->id }}','unresolved',{{ $t->id }})" class="mt-2 btn btn-outline-primary c-rounded txn-btn">unresolved</button>
                                        </div>
                                    @endif
                                    
                                    <div class="btn-group mr-2 float-right" role="group">
                                        <button type="button" data-dismiss="modal" class="mt-2 btn btn-outline-primary c-rounded txn-btn">Go Back</button>
                                    </div>
                                    </div>
                                </div>
                                @endif

                            <input type="hidden" name="status" id="statusInput-{{ $t->id }}">
                            <input type="hidden" name="id" value="{{ $t->id }}">
                            <div class="col-md-12 d-none" id="p2p_dropdown-{{ $t->id }}">
                                <div class="form-group mt-1">
                                    <label for="reason" class="text-danger">Reason for Declining </label>
                                    <select name="reason" id="" class="form-control">
                                        @if($t->type == 'withdrawal')
                                        <option value="Bank network issues">Bank network issues</option>
                                        <option value="Exceeded bank limit">Exceeded bank limit</option>
                                        <option value="Incorrect bank details">Incorrect bank details</option>
                                        <option value="A mismatch in name">A mismatch in name</option>
                                        @else
                                        <option value="payment not received">Payment Not Recieved</option>
                                        @endif;
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12 d-none" id="p2p_pin-{{ $t->id }}">
                                <div class="form-group">
                                    <label for="">Wallet pin </label>
                                    <input type="password" name="pin" required class="form-control">
                                </div>
                                <button class="btn btn-block c-rounded bg-custom-gradient txn-btn">
                                    Confirm
                                </button>
                            </div>


                        </div>
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

<script src="{{asset('assets/scripts/sweetalert.min.js')}} "></script>
<script>


    const __st_id = (activity) => document.getElementById(activity)

    const hideit = (ide) => {
        __st_id(ide).classList.remove("d-block")
        __st_id(ide).classList.add("d-none")
    }

    const showit = (ide) => {
        __st_id(ide).classList.remove("d-none")
        __st_id(ide).classList.add("d-block")
    }
    
    const buttonSelect = (id, status, uid) => {
        var approve = document.getElementById(id);
        approve.value = status;

        if(status == 'approve' || status == 'unresolved'){
            showit('p2p_pin-'+uid);
            hideit('p2p_buttongroup-'+uid);
        }

        if(status == 'decline') {
            showit('p2p_pin-'+uid);
            showit('p2p_dropdown-'+uid);
            hideit('p2p_buttongroup-'+uid);
        }
    }

    const copyData = (id, type) => {
        var copyText = document.getElementById(id);
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard
        .writeText(copyText.value)
        .then(() => {
            swal(type+" copied: " + copyText.value);
        })
        .catch(() => {
            swal("something went wrong");
        });
    }

</script>
@endsection


