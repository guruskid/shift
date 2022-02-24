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
                            @if(isset($accountant_name))
                                {{ $accountant_name }}<br>
                            @endif 
                            @if(isset($segment))
                                {{ $segment }} Transactions 
                            @endif 
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-3">
                    {{-- bg-primary text-white --}}
                    <a href="{{ route('admin.junior-summary-details', [ $month , $day, 'all']) }}">
                        <div class="card mb-1 widget-content @if (isset($show_category) AND $show_category == "all")
                        bg-primary
                         @endif">
                            <div class="widget-content-wrapper">
                                <div class="widget-heading">
                                    <h6 class="text-center @if (isset($show_category) AND $show_category == "all")
                                    text-white
                                     @endif">Crypto and Giftcards</h5>
                                </div>
                                
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    {{-- bg-primary text-white --}}
                    <a href="{{ route('admin.junior-summary-details', [ $month , $day, 'utilities']) }}">
                        <div class="card mb-1 widget-content @if (isset($show_category) AND $show_category == "utilities")
                        bg-primary
                         @endif">
                            <div class="widget-content-wrapper">
                                <div class="widget-heading">
                                    <h6 class="text-center @if (isset($show_category) AND $show_category == "utilities")
                                    text-white
                                     @endif">Utilities</h6>
                                </div>
                                
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    {{-- bg-primary text-white --}}
                    <a href="{{ route('admin.junior-summary-details', [ $month , $day, 'paybridge']) }}">
                        <div class="card mb-1 widget-content @if ( isset($show_category) AND strpos($show_category,'paybridge') !== false)
                        bg-primary
                         @endif">
                            <div class="widget-content-wrapper">
                                <div class="widget-heading">
                                    <h6 class="text-center @if (isset($show_category) AND strpos($show_category,'paybridge') !== false)
                                    text-white
                                     @endif">Paybridge</h6>
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
                                
                            @if ($show_category == "all")
                           {{-- all tnx start --}}
                           <form action="{{ route('admin.junior-summary-sort-details') }}" method="POST">
                                @csrf
                                <div class="form-inline mb-3">
                                    <label class="mr-1">Start Date</label>
                                    <input type="datetime-local" name="startdate"  value="{{app('request')->input('startdate')}}"class="form-control mr-1" >

                                    <label class="mr-1">End Date</label>
                                    <input type="datetime-local" name="enddate" value="{{app('request')->input('enddate')}}" class="form-control mr-1" >

                                    <input type="hidden" name="day" value="{{ $day }}">
                                    <input type="hidden" name="month" value="{{ $month }}">
                                    <input type="hidden" name="category" value="{{ $show_category }}">
                                    {{-- @if (isset($accountant))
                                        @foreach ($accountant as $a)
                                            <input type="hidden" name="name" value="{{ $a->first_name }}" class="form-control mr-4">
                                        @endforeach
                                    @endif --}}

                                    @if (isset($accountant))
                                        <select name="Accountant" class="ml-1 form-control">
                                            <option value="null">Accountant</option>
                                            @foreach ($accountant as $a) 
                                                <option value="{{ $a->id }}">{{ $a->first_name ?:$a->email }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                    
                                    {{-- <input type="number" name="entries" class="form-control mr-1  ml-1" placeholder="Enteries"> --}}
                                    <button class="btn btn-primary ml-1"><i class="fa fa-search"></i></button>
                                </div>
                            </form>
                            <div class="table-responsive">
                                <div class="card card-body mb-3">
                                    @if($show_summary == true)
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Total Transactions</th>
                                                <th class="text-center">Total Crypto Transactions</th>
                                                <th class="text-center">Total Giftcard Transactions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                    <td class="text-center">{{ isset($all_tnx_count) ? number_format($all_tnx_count) : 0 }}</td>
                                                    
                                                    <td>
                                                        <table class="table">
                                                            <thead>
                                                                <th class="text-center">Buy</th>
                                                                <th class="text-center">Sell</th>
                                                            </thead>
                                                            <tbody>
                                                                <td class="text-center">{{ isset($crypto_totaltnx_buy) ? number_format($crypto_totaltnx_buy) : 0 }} 
                                                                    [{{sprintf('%.8f', floatval($bitcoin_total_tnx_buy))}} BTC]</td>
                                                                <td class="text-center">{{ isset($crypto_totaltnx_sell) ? number_format($crypto_totaltnx_sell) : 0 }} 
                                                                    [{{sprintf('%.8f', floatval($bitcoin_total_tnx_sell))  }} BTC]</td>
                                                            </tbody>
                                                            <tfoot>
                                                                <td>Total <h6 class="text-right">${{ $crypto_totaltnx_buy_amount }}</h6></td>
                                                                <td>Total <h6 class="text-right">${{ $crypto_totaltnx_sell_amount }}</h6></td>
                                                            </tfoot>
                                                        </table>
                                                    </td>
                                                    <td>
                                                        <table class="table">
                                                            <thead>
                                                                <th class="text-center">Buy</th>
                                                                <th class="text-center">Sell</th>
                                                            </thead>
                                                            <tbody>
                                                                <td class="text-center">{{ isset($giftcards_totaltnx_buy) ? number_format($giftcards_totaltnx_buy) : 0 }}</td>
                                                                <td class="text-center">{{ isset($giftcards_totaltnx_sell) ? number_format($giftcards_totaltnx_sell) : 0 }}</td>
                                                            </tbody>
                                                            <tfoot>
                                                                <td>Total <h6 class="text-right">${{ $giftcards_totaltnx_buy_amount }}</h6></td>
                                                                <td>Total <h6 class="text-right">${{ $giftcards_totaltnx_sell_amount }}</h6></td>
                                                            </tfoot>
                                                        </table>
                                                    </td>
                                                    {{-- <td class="text-center">{{ isset($giftcards_totaltnx) ? number_format($giftcards_totaltnx) : 0 }}</td> --}}
                                            </tr>
                                        </tbody>
                                    </table>
                                    @endif
                                </div>
                                <table class="mb-2 table table-bordered transactions-table">
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
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @if (isset($all_tnx))
                                        @foreach ($all_tnx as $t)
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

                                            
                                        </tr>
                                        
                                        @endforeach
                                    </tbody>
                                        
                                    {{-- {{ $all_tnx->links() }} --}}
                                    @endif


                                </table>
                            </div>
                            {{-- all tnx end --}}
                            @endif

                            @if ($show_category == "utilities")
                            {{-- util tnx start --}}
                            <form action="{{ route('admin.junior-summary-sort-details') }}" method="POST">
                                @csrf
                                <div class="form-inline mb-3">
                                    <label class="mr-1">Start Date</label>
                                    <input type="datetime-local" name="startdate" value="{{app('request')->input('startdate')}}" class="form-control mr-1" >

                                    <label class="mr-1">End Date</label>
                                    <input type="datetime-local" name="enddate" value="{{app('request')->input('enddate')}}" class="form-control mr-1" >
                                    <input type="hidden" name="day" value="{{ $day }}">
                                    <input type="hidden" name="month" value="{{ $month }}">
                                    <input type="hidden" name="category" value="{{ $show_category }}">
                                    {{-- @if (isset($accountant))
                                        @foreach ($accountant as $a)
                                            <input type="hidden" name="name" value="{{ $a->first_name }}" class="form-control mr-4">
                                        @endforeach
                                    @endif --}}

                                    @if (isset($accountant))
                                        <select name="Accountant" class="ml-1 form-control">
                                            <option value="null">Accountant</option>
                                            @foreach ($accountant as $a) 
                                                <option value="{{ $a->id }}">{{ $a->first_name ?:$a->email }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                    
                                    {{-- <input type="number" name="entries" class="form-control mr-1  ml-1" placeholder="Enteries"> --}}
                                    <button class="btn btn-primary ml-1"><i class="fa fa-search"></i></button>
                                </div>
                            </form>
                            <div class="table-responsive">
                                        
                                <div class="card card-body mb-3">
                                    @if($show_summary == true)
                                    <table class="table ">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Total Transactions</th>
                                                <th class="text-center">Total Amount</th>
                                                <th class="text-center">Convenience fee Total</th>
                                                <th class="text-center">Total Amount Paid</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                    <td class="text-center">{{ isset($util_total_tnx) ? number_format($util_total_tnx) : 0 }}</td>
                                                    <td class="text-center">₦ {{ isset($util_tnx_amount) ? number_format($util_tnx_amount) : 0 }}</td>
                                                    <td class="text-center">₦ {{ isset($util_tnx_fee) ? number_format($util_tnx_fee) : 0 }}</td>
                                                    <td class="text-center">₦ {{ isset($util_amount_paid) ? number_format($util_amount_paid) : 0 }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    @endif
                                </div>
                                <table class="mb-2 table table-bordered transactions-table">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Reference ID</th>
                                            <th class="text-center">Date</th>
                                            <th class="text-center">User</th>
                                            <th class="text-center">Amount</th>
                                            <th class="text-center">Convenience fee</th>
                                            <th class="text-center">Total</th>
                                            <th class="text-center">Type</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Extras</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (isset($util_tnx))
                                            @foreach ($util_tnx as $t)
                                            <tr>
                                                <td class="text-center text-muted">{{$t->reference_id}}</td>
                                                <td class="text-center text-muted">{{$t->created_at->format('d M, H:ia')}}</td>
                                                <td class="text-center">
                                                    <a
                                                        href="{{route('admin.user', [$t->user->id ?? ' ', $t->user->email ?? ' ' ] )}}">

                                                    @if(strlen($t->user->first_name) < 3 )
                                                        {{$t->user->email}}
                                                    @else
                                                        {{$t->user->first_name}}
                                                    @endif

                                                    </a>
                                                </td>
                                                {{-- <td class="text-center">{{$t->user->first_name}}</td> --}}

                                                <td class="text-center">{{$t->amount}}</td>
                                                <td class="text-center">{{$t->convenience_fee}}</td>
                                                <td class="text-center">{{$t->total}}</td>
                                                <td class="text-center">{{$t->type}}</td>
                                                <td class="text-center">
                                                    @switch($t->status)
                                                @case('success')
                                                <div class="text-success">{{$t->status}}</div>
                                                @break
                                                @case("failed")
                                                <div class="text-danger">{{$t->status}}</div>
                                                @break
                                                @default
                                                <div class="text-warning">{{$t->status}}</div>
                                                @endswitch
                                                </td>
                                                <td class="text-center" style="word-wrap: break-word;min-width: 160px;max-width: 160px;">
                                                    <pre>{{$t->extras}}</pre>
                                                </td>
                                            </tr>
                                            @endforeach
                                        
                                            {{-- {{ $util_tnx->links() }} --}}
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            {{-- util tnx end --}}
                            @endif

                            @if ($show_category == "paybridge" OR $show_category == "paybridgewithdrawal" OR 
                            $show_category == "paybridgeothers")
                            {{-- tabs --}}
                            <div class="row mb-2">
                                <div class="col-md-3">
                                    {{-- bg-primary text-white --}}
                                    <a href="{{ route('admin.junior-summary-details', [ $month , $day, 'paybridge']) }}">
                                        <div class="card mb-1 widget-content @if ($show_category == "paybridge")
                                        bg-primary
                                         @endif">
                                            <div class="widget-content-wrapper">
                                                <div class="widget-heading">
                                                    <h6 class="text-center @if ($show_category == "paybridge")
                                                    text-white
                                                     @endif">Deposit</h5>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    {{-- bg-primary text-white --}}
                                    <a href="{{ route('admin.junior-summary-details', [ $month , $day, 'paybridgewithdrawal']) }}">
                                        <div class="card mb-1 widget-content @if ($show_category == "paybridgewithdrawal")
                                        bg-primary
                                         @endif">
                                            <div class="widget-content-wrapper">
                                                <div class="widget-heading">
                                                    <h6 class="text-center @if ($show_category == "paybridgewithdrawal")
                                                    text-white
                                                     @endif">Withdrawal</h6>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    {{-- bg-primary text-white --}}
                                    <a href="{{ route('admin.junior-summary-details', [ $month , $day, 'paybridgeothers']) }}">
                                        <div class="card mb-1 widget-content @if ($show_category == "paybridgeothers")
                                        bg-primary
                                         @endif">
                                            <div class="widget-content-wrapper">
                                                <div class="widget-heading">
                                                    <h6 class="text-center @if ($show_category == "paybridgeothers")
                                                    text-white
                                                     @endif">Others</h6>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            {{-- tabs end --}}
                            {{-- paybridge tnx --}}
                            
                            <div class="table-responsive">
                                       @if ($show_category == "paybridge")
                                        <form action="{{ route('admin.junior-summary-sort-details') }}" method="POST">
                                            @csrf
                                            <div class="form-inline mb-3">
                                                <label class="mr-1">Start Date</label>
                                                <input type="datetime-local" name="startdate" value="{{app('request')->input('startdate')}}"  class="form-control mr-1" >
            
                                                <label class="mr-1">End Date</label>
                                                <input type="datetime-local" name="enddate" value="{{app('request')->input('enddate')}}" class="form-control mr-1" >
                                                <input type="hidden" name="day" value="{{ $day }}">
                                                <input type="hidden" name="month" value="{{ $month }}">
                                                <input type="hidden" name="category" value="{{ $show_category }}">
                                                {{-- @if (isset($accountant))
                                                    @foreach ($accountant as $a)
                                                        <input type="hidden" name="name" value="{{ $a->first_name }}" class="form-control mr-4">
                                                    @endforeach
                                                @endif --}}
            
                                                @if (isset($accountant))
                                                    <select name="Accountant" class="ml-1 form-control">
                                                        <option value="null">Accountant</option>
                                                        @foreach ($accountant as $a) 
                                                            <option value="{{ $a->id }}">{{ $a->first_name ?:$a->email }}</option>
                                                        @endforeach
                                                    </select>
                                                @endif
                                                
                                                {{-- <input type="number" name="entries" class="form-control mr-1  ml-1" placeholder="Enteries"> --}}
                                                <button class="btn btn-primary ml-1"><i class="fa fa-search"></i></button>
                                            </div>
                                        </form>
                                            <div class="card card-body mb-3">
                                                @if($show_summary == true)
                                                        <table class="table ">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-center">Total Deposit Transactions</th>
                                                                    <th class="text-center">Total Deposit Amount Paid</th>
                                                                    <th class="text-center">Total Deposit Charges</th>
                                                                    <th class="text-center">Total Deposit Amount</th>
                                                                    <th class="text-center">Pending Deposit Today</th>
                                                                    <th class="text-center">Pending Deposit Total</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                        <td class="text-center">{{ isset($nw_deposit_tnx_total) ? number_format($nw_deposit_tnx_total) : 0 }}</td>
                                                                        <td class="text-center">₦ {{ isset($nw_deposit_amount_paid) ? number_format($nw_deposit_amount_paid) : 0 }}</td>
                                                                        <td class="text-center">₦ {{ isset($nw_deposit_tnx_charges) ? number_format($nw_deposit_tnx_charges) : 0 }}</td>
                                                                        <td class="text-center">₦ {{ isset($nw_deposit_total_amount) ? number_format($nw_deposit_total_amount) :0 }}</td>
                                                                        <td class="text-center">[{{ isset($nw_deposit_pending_total) ? number_format($nw_deposit_pending_total) :0 }}] ₦ {{ isset($nw_deposit_pending_amount) ? number_format($nw_deposit_pending_amount) :0 }}</td>
                                                                        <td class="text-center">
                                                                            [{{ isset($deposit_total_pending) ? number_format($deposit_total_pending) :0 }}]
                                                                             ₦ {{ isset($deposit_total_pending_amount) ? number_format($deposit_total_pending_amount) :0 }}
                                                                        </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    @endif
                                            </div>  
                                            
                                            <table class="mb-2 table table-bordered transactions-table">
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
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if (isset($nw_deposit_tnx))
                                                        @foreach ($nw_deposit_tnx as $t)
                                                            <tr>
                                                                <td>{{$t->id}} </td>
                                                                <td>{{$t->reference}} </td>
                                                                <td>
                                                                    <a
                                                                        href="{{route('admin.user', [$t->user->id ?? ' ', $t->user->email ?? ' ' ] )}}">

                                                                    @if(strlen($t->user->first_name) < 3 )
                                                                        {{$t->user->email}}
                                                                    @else
                                                                        {{$t->user->first_name}}
                                                                    @endif

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
                                                                <td>
                                                                    @switch($t->status)
                                                                    @case('success')
                                                                    <div class="text-success">{{$t->status}}</div>
                                                                    @break
                                                                    @case("failed")
                                                                    <div class="text-danger">{{$t->status}}</div>
                                                                    @break
                                                                    @default
                                                                    <div class="text-warning">{{$t->status}}</div>
                                                                    @endswitch
                                                                </td>
                                                                <td>{{$t->extras}} </td>
                                                            </tr>
                                                        @endforeach
                                                        {{-- {{ $nw_deposit_tnx->links() }} --}}
                                                    @endif
                                                </tbody>
                                            </table>
                                            
                                       @endif
                                    
                                       @if ($show_category == "paybridgewithdrawal")
                                        <form action="{{ route('admin.junior-summary-sort-details') }}" method="POST">
                                                @csrf
                                                <div class="form-inline mb-3">
                                                    <label class="mr-1">Start Date</label>
                                                    <input type="datetime-local" name="startdate" value="{{app('request')->input('startdate')}}" class="form-control mr-1" >
                
                                                    <label class="mr-1">End Date</label>
                                                    <input type="datetime-local" name="enddate" value="{{app('request')->input('enddate')}}" class="form-control mr-1" >
                                                    <input type="hidden" name="day" value="{{ $day }}">
                                                    <input type="hidden" name="month" value="{{ $month }}">
                                                    <input type="hidden" name="category" value="{{ $show_category }}">
                                                    {{-- @if (isset($accountant))
                                                        @foreach ($accountant as $a)
                                                            <input type="hidden" name="name" value="{{ $a->first_name }}" class="form-control mr-4">
                                                        @endforeach
                                                    @endif --}}
                
                                                    @if (isset($accountant))
                                                        <select name="Accountant" class="ml-1 form-control">
                                                            <option value="null">Accountant</option>
                                                            @foreach ($accountant as $a) 
                                                                <option value="{{ $a->id }}">{{ $a->first_name ?:$a->email }}</option>
                                                            @endforeach
                                                        </select>
                                                    @endif
                                                    
                                                    {{-- <input type="number" name="entries" class="form-control mr-1  ml-1" placeholder="Enteries"> --}}
                                                    <button class="btn btn-primary ml-1"><i class="fa fa-search"></i></button>
                                                </div>
                                        </form>
                                        <div class="card card-body mb-3">
                                            @if($show_summary == true)
                                                    <table class="table ">
                                                        <thead>
                                                            <tr>
                                                                <th class="text-center">Total Withdrawal Transactions</th>
                                                                <th class="text-center">Total Withdrawal Amount Paid</th>
                                                                <th class="text-center">Total Withdrawal Charges</th>
                                                                <th class="text-center">Total Withdrawal Amount</th>
                                                                <th class="text-center">Pending Withdrawal Today</th>
                                                                <th class="text-center">Pending Withdrawal Total</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                    <td class="text-center">{{ isset($nw_withdrawal_tnx_total) ? number_format($nw_withdrawal_tnx_total) : 0 }}</td>
                                                                    <td class="text-center">₦ {{ isset($nw_withdrawal_amount_paid) ? number_format($nw_withdrawal_amount_paid) : 0 }}</td>
                                                                    <td class="text-center">₦ {{ isset($nw_withdrawal_tnx_charges) ? number_format($nw_withdrawal_tnx_charges) : 0 }}</td>
                                                                    <td class="text-center">₦ {{ isset($nw_withdrawal_total_amount) ? number_format($nw_withdrawal_total_amount) :0 }}</td>
                                                                    <td class="text-center">[{{ isset($nw_withdrawal_pending_total) ? number_format($nw_withdrawal_pending_total) :0 }}] ₦ {{ isset($nw_withdrawal_pending_amount) ? number_format($nw_withdrawal_pending_amount) :0 }}</td>
                                                                    <td class="text-center">
                                                                        [{{ isset($withdrawal_total_pending) ? number_format($withdrawal_total_pending) :0 }}]
                                                                         ₦ {{ isset($withdrawal_total_pending_amount) ? number_format($withdrawal_total_pending_amount) :0 }}
                                                                    </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                @endif
                                        </div>  
                                        
                                        <table class="mb-2 table table-bordered transactions-table">
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
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (isset($nw_withdrawal_tnx))
                                                    @foreach ($nw_withdrawal_tnx as $t)
                                                        <tr>
                                                            <td>{{$t->id}} </td>
                                                            <td>{{$t->reference}} </td>
                                                            <td>
                                                                <a
                                                                    href="{{route('admin.user', [$t->user->id ?? ' ', $t->user->email ?? ' ' ] )}}">

                                                                @if(strlen($t->user->first_name) < 3 )
                                                                    {{$t->user->email}}
                                                                @else
                                                                    {{$t->user->first_name}}
                                                                @endif

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
                                                            <td>@switch($t->status)
                                                                @case('success')
                                                                <div class="text-success">{{$t->status}}</div>
                                                                @break
                                                                @case("failed")
                                                                <div class="text-danger">{{$t->status}}</div>
                                                                @break
                                                                @default
                                                                <div class="text-warning">{{$t->status}}</div>
                                                                @endswitch </td>
                                                            <td>{{$t->extras}} </td>
                                                        </tr>
                                                    @endforeach
                                                    {{-- {{ $nw_withdrawal_tnx->links() }} --}}
                                                @endif
                                            </tbody>
                                        </table>
                                      @endif

                                      @if ($show_category == "paybridgeothers")
                                         <form action="{{ route('admin.junior-summary-sort-details') }}" method="POST">
                                            @csrf
                                            <div class="form-inline mb-3">
                                                <label class="mr-1">Start Date</label>
                                                <input type="datetime-local" name="startdate" value="{{app('request')->input('startdate')}}" class="form-control mr-1">
            
                                                <label class="mr-1">End Date</label>
                                                <input type="datetime-local" name="enddate" value="{{app('request')->input('enddate')}}"class="form-control mr-1">
                                                <input type="hidden" name="day" value="{{ $day }}">
                                                <input type="hidden" name="month" value="{{ $month }}">
                                                <input type="hidden" name="category" value="{{ $show_category }}">
                                                   
                                                {{-- @if (isset($accountant))
                                                    @foreach ($accountant as $a)
                                                        <input type="hidden" name="name" value="{{ $a->first_name }}" class="form-control mr-4">
                                                    @endforeach
                                                @endif --}}
            
                                                @if (isset($accountant))
                                                    <select name="Accountant" class="ml-1 form-control">
                                                        <option value="null">Accountant</option>
                                                        @foreach ($accountant as $a) 
                                                            <option value="{{ $a->id }}">{{ $a->first_name ?:$a->email }}</option>
                                                        @endforeach
                                                    </select>
                                                @endif
                                                
                                                {{-- <input type="number" name="entries" class="form-control mr-1  ml-1" placeholder="Enteries"> --}}
                                                <button class="btn btn-primary ml-1"><i class="fa fa-search"></i></button>
                                            </div>
                                        </form>
                                        <div class="card card-body mb-3">
                                            @if($show_summary == true)
                                                    <table class="table ">
                                                        <thead>
                                                            <tr>
                                                                <th class="text-center">Total Transactions</th>
                                                                <th class="text-center">Total Amount Paid</th>
                                                                <th class="text-center">Total Charges</th>
                                                                <th class="text-center">Total Amount</th>
                                                                <th class="text-center">Total Pending Today</th>
                                                                <th class="text-center">Total Pending Total</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                    <td class="text-center">{{ isset($nw_other_tnx_total) ? number_format($nw_other_tnx_total) : 0 }}</td>
                                                                    <td class="text-center">₦ {{ isset($nw_other_amount_paid) ? number_format($nw_other_amount_paid) : 0 }}</td>
                                                                    <td class="text-center">₦ {{ isset($nw_other_tnx_charges) ? number_format($nw_other_tnx_charges) : 0 }}</td>
                                                                    <td class="text-center">₦ {{ isset($nw_other_total_amount) ? number_format($nw_other_total_amount) :0 }}</td>
                                                                    <td class="text-center">[{{ isset($nw_other_pending_total) ? number_format($nw_other_pending_total) :0 }}] ₦ {{ isset($nw_other_pending_amount) ? number_format($nw_other_pending_amount) :0 }}</td>
                                                                    <td class="text-center">
                                                                        [{{ isset($other_total_pending) ? number_format($other_total_pending) :0 }}]
                                                                         ₦ {{ isset($other_total_pending_amount) ? number_format($other_total_pending_amount) :0 }}
                                                                        </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                @endif
                                         </div>  
                                        
                                        <table class="mb-2 table table-bordered transactions-table">
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
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (isset($nw_other_tnx))
                                                    @foreach ($nw_other_tnx as $t)
                                                        <tr>
                                                            <td>{{$t->id}} </td>
                                                            <td>{{$t->reference}} </td>
                                                            <td>
                                                                <a
                                                                    href="{{route('admin.user', [$t->user->id ?? ' ', $t->user->email ?? ' ' ] )}}">

                                                                @if(strlen($t->user->first_name) < 3 )
                                                                    {{$t->user->email}}
                                                                @else
                                                                    {{$t->user->first_name}}
                                                                @endif

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
                                                            <td>@switch($t->status)
                                                                @case('success')
                                                                <div class="text-success">{{$t->status}}</div>
                                                                @break
                                                                @case("failed")
                                                                <div class="text-danger">{{$t->status}}</div>
                                                                @break
                                                                @default
                                                                <div class="text-warning">{{$t->status}}</div>
                                                                @endswitch  </td>
                                                            <td>{{$t->extras}} </td>
                                                        </tr>
                                                    @endforeach
                                                    {{-- {{ $nw_other_tnx->links() }} --}}
                                                @endif
                                            </tbody>
                                        </table>
                                        @endif
                                  </div>
                            </div>

                            {{-- paybridge tnx end --}}
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
