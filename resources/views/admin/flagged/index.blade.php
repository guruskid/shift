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
                            <i class="pe-7s-graph1 icon-gradient bg-warm-flame">
                            </i>
                        </div>
                        <div> 
                            Flagged Transactions
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-3">
                    {{-- bg-primary text-white --}}
                    <a href="{{ route('admin.flagged.home', ['clearWithdrawal']) }}">
                        <div class="card mb-1 widget-content @if (isset($type) AND $type == "clearWithdrawal")
                        bg-primary
                         @endif">
                            <div class="widget-content-wrapper">
                                <div class="widget-heading">
                                    <h6 class="text-center @if (isset($type) AND $type == "clearWithdrawal")
                                    text-white
                                     @endif">All Cleared Withdrawal</h6>
                                </div>
                                
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    {{-- bg-primary text-white --}}
                    <a href="{{ route('admin.flagged.home', ['bulkCredit']) }}">
                        <div class="card mb-1 widget-content @if (isset($type) AND $type == "bulkCredit")
                        bg-primary
                         @endif">
                            <div class="widget-content-wrapper">
                                <div class="widget-heading">
                                    <h6 class="text-center @if (isset($type) AND $type == "bulkCredit")
                                    text-white
                                     @endif">Bulk Credit</h5>
                                </div>
                                
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    {{-- bg-primary text-white --}}
                    <a href="{{ route('admin.flagged.home', ['withdrawal']) }}">
                        <div class="card mb-1 widget-content @if (isset($type) AND $type == "withdrawal")
                        bg-primary
                         @endif">
                            <div class="widget-content-wrapper">
                                <div class="widget-heading">
                                    <h6 class="text-center @if (isset($type) AND $type == "withdrawal")
                                    text-white
                                     @endif">Withdrawals</h6>
                                </div>
                                
                            </div>
                        </div>
                    </a>
                </div>
                
            </div>
            <div class="row">
                <div class="col-md-12">
                </div>


                @if ($show_data != false)
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                            @if ($type == "bulkCredit") 
                            <div class="table-responsive">
                                <table class="mb-2 table table-bordered transactions-table">
                                    <thead>
                                        <tr>
                                            <th class="text-center">S/N</th>
                                            <th class="text-center">Name</th>
                                            <th class="text-center">Previous Balance</th>
                                            <th class="text-center">Current Balance</th>
                                            <th class="text-center">Transaction</th>
                                            <th class="text-center">Amount Credited</th>
                                            <th class="text-center">Last Amount Credited</th>
                                            <th class="text-center">SignedUp Date</th>
                                            <th class="text-center">Date</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                            @php
                                                $key = 0;
                                            @endphp
                                            @foreach ($transactions as $t)
                                            <tr>
                                                <td class="text-center text-muted">{{ ++ $key }}</td>
                                                <td class="text-center text-muted">{{ $t->user->first_name." ".$t->user->last_name}}</td>
                                                <td class="text-center text-muted">₦{{ number_format($t->naira_transaction->previous_balance) }}</td>
                                                <td class="text-center text-muted">₦{{ number_format($t->naira_transaction->current_balance) }}</td>
                                                <td class="text-center text-muted">{{ $t->transaction->card }}</td>
                                                <td class="text-center text-muted">₦{{ number_format($t->transaction->amount_paid)}}</td>
                                                <td class="text-center text-muted">₦{{ number_format($t->previousTransactionAmount)}}</td>
                                                <td class="text-center text-muted">{{ $t->user->created_at->format('d M y, H:ia')}}</td>
                                                <td class="text-center text-muted">{{ $t->created_at->format('d M y, h:ia')}}</td>
                                                <td class="text-center text-muted">
                                                    @if($t->transaction->is_flagged == 1)
                                                        <a href="{{route('admin.flagged.clear', [$t->id] )}}">
                                                            <span class="btn btn-sm btn-primary">Clear</span>
                                                        </a>

                                                        <a href="{{route('admin.user', [$t->user->id, $t->user->email] )}}">
                                                            <span class="btn btn-sm btn-warning">View History</span>
                                                        </a>

                                                        
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                            
                                        
                                    </tbody>
                                </table>
                            </div>
                            @endif
                            
                            @if ($type == "withdrawal")
                            <div class="table-responsive">
                                <table class="mb-2 table table-bordered transactions-table">
                                    <thead>
                                        <tr>
                                        <th class="text-center">S/N</th>
                                        <th class="text-center">Name</th>
                                        <th class="text-center">Previous Balance</th>
                                        <th class="text-center">Current Balance</th>
                                        <th class="text-center">Verfication Level</th>
                                        <th class="text-center">Withdrawal Amount</th>
                                        <th class="text-center">Previous Withdrawal Amount</th>
                                        <th class="text-center">Daily Limit</th>
                                        <th class="text-center">Monthly Limit</th>
                                        <th class="text-center">SignedUp Date</th>
                                        <th class="text-center">Date</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        
                                            @php
                                                $key = 0;
                                            @endphp
                                            @foreach ($transactions as $t)
                                            <tr>
                                                <td class="text-center text-muted">{{ ++ $key }}</td>
                                                <td class="text-center text-muted">{{ $t->user->first_name." ".$t->user->last_name}}</td>
                                                <td class="text-center text-muted">₦{{ number_format($t->naira_transaction->previous_balance) }}</td>
                                                <td class="text-center text-muted">₦{{ number_format($t->naira_transaction->current_balance) }}</td>
                                                <td class="text-center text-muted">
                                                @if($t->user->phone_verified_at != null AND $t->user->address_verified_at == null AND $t->user->idcard_verified_at == null)
                                                     {{'Level 1'}}
                                                @endif
                                                @if($t->user->phone_verified_at != null AND $t->user->address_verified_at != null AND $t->user->idcard_verified_at == null)
                                                    {{ 'Level 2' }}
                                                @endif
                                                @if($t->user->phone_verified_at != null AND $t->user->address_verified_at != null AND $t->user->idcard_verified_at != null)
                                                    {{ 'Level 3' }}
                                                @endif
                                                </td>
                                                <td class="text-center text-muted">₦{{ number_format($t->naira_transaction->amount)}}</td>
                                                <td class="text-center text-muted">₦{{ number_format($t->previousTransactionAmount)}}</td>
                                                <td class="text-center text-muted">₦{{ number_format($t->user->daily_max)}}</td>
                                                <td class="text-center text-muted">₦{{ number_format($t->user->monthly_max)}}</td>
                                                <td class="text-center text-muted">{{ $t->user->created_at->format('d M y, H:ia')}}</td>
                                                <td class="text-center text-muted">{{ $t->created_at->format('d M y, h:ia')}}</td>
                                                <td class="text-center text-muted">
                                                    @if($t->nairaTrade->is_flagged == 1)
                                                        <a href="{{route('admin.flagged.clear', [$t] )}}">
                                                        <button type="button" class="btn btn-sm btn-primary">Clear</button>
                                                        </a>

                                                        <a href="{{route('admin.user', [$t->user->id, $t->user->email] )}}">
                                                        <button type="button" class="btn btn-sm btn-warning">View History</button>
                                                        </a>

                                                        
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        
                                    </tbody>


                                </table>
                            </div>
                            @endif

                            @if ($type == "clearWithdrawal")
                            <div class="table-responsive">
                                <table class="mb-2 table table-bordered transactions-table">
                                    <thead>
                                        <tr>
                                        <th class="text-center">S/N</th>
                                        <th class="text-center">Name</th>
                                        <th class="text-center">Previous Balance</th>
                                        <th class="text-center">Current Balance</th>
                                        <th class="text-center">Transaction</th>
                                        <th class="text-center">Amount</th>
                                        <th class="text-center">Last Amount </th>
                                        <th class="text-center">Verfication Level</th>
                                        <th class="text-center">Daily Limit</th>
                                        <th class="text-center">Monthly Limit</th>
                                        <th class="text-center">SignedUp Date</th>
                                        <th class="text-center">Date</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        
                                            @php
                                                $key = 0;
                                            @endphp
                                            @foreach ($transactions as $t)
                                            <tr>
                                                <td class="text-center text-muted">{{ ++ $key }}</td>
                                                <td class="text-center text-muted">{{ $t->user->first_name." ".$t->user->last_name}}</td>
                                                <td class="text-center text-muted">₦{{ number_format($t->naira_transaction->previous_balance) }}</td>
                                                <td class="text-center text-muted">₦{{ number_format($t->naira_transaction->current_balance) }}</td>
                                                <td class="text-center text-muted">
                                                    @if ($t->transaction)
                                                    {{ $t->transaction->card }}
                                                    @else
                                                    PayBridge {{ $t->nairaTrade->type }}
                                                    @endif
                                                </td>
                                                <td class="text-center text-muted">₦{{ number_format($t->naira_transaction->amount)}}</td>
                                                <td class="text-center text-muted">₦{{ number_format($t->previousTransactionAmount)}}</td>
                                                <td class="text-center text-muted">
                                                    @if($t->user->phone_verified_at != null AND $t->user->address_verified_at == null AND $t->user->idcard_verified_at == null)
                                                         {{'Level 1'}}
                                                    @endif
                                                    @if($t->user->phone_verified_at != null AND $t->user->address_verified_at != null AND $t->user->idcard_verified_at == null)
                                                        {{ 'Level 2' }}
                                                    @endif
                                                    @if($t->user->phone_verified_at != null AND $t->user->address_verified_at != null AND $t->user->idcard_verified_at != null)
                                                        {{ 'Level 3' }}
                                                    @endif
                                                    </td>

                                                <td class="text-center text-muted">₦{{ number_format($t->user->daily_max)}}</td>
                                                <td class="text-center text-muted">₦{{ number_format($t->user->monthly_max)}}</td>
                                                <td class="text-center text-muted">{{ $t->user->created_at->format('d M y, h:ia')}}</td>
                                                <td class="text-center text-muted">{{ $t->created_at->format('d M y, h:ia')}}</td>
                                            </tr>
                                            @endforeach
                                        
                                    </tbody>


                                </table>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

               
            </div>
        </div>
    </div>
</div>
@endsection
