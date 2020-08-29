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
                        <div>Admin Wallet Transctions
                            <div class="page-title-subheading">{{Auth::user()->nairaWallet->account_number }} -- {{Auth::user()->nairaWallet->account_name }} </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 col-xl-4">
                    <div class="card mb-3 widget-content bg-midnight-bloom">
                        <div class="widget-content-wrapper text-white">
                            <div class="widget-content-left">
                                <div class="widget-heading">Total Credits</div>
                            </div>
                            <div class="widget-content-right">
                                <div class="widget-numbers text-white">
                                    <span>₦{{number_format($credit_txns->sum('amount')) }}</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-4">
                    <div class="card mb-3 widget-content bg-amy-crisp">
                        <div class="widget-content-wrapper text-white">
                            <div class="widget-content-left">
                                <div class="widget-heading">Total Debits</div>
                            </div>
                            <div class="widget-content-right">
                                <div class="widget-numbers text-white">
                                    <span>₦{{number_format($debit_txns->sum('amount')) }}</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-4">
                    <div class="card mb-3 widget-content bg-custom-accent">
                        <div class="widget-content-wrapper text-white">
                            <div class="widget-content-left">
                                <div class="widget-heading">Balance</div>
                            </div>
                            <div class="widget-content-right">
                                <div class="widget-numbers text-white">
                                    <span>₦{{number_format($credit_txns->sum('amount') - $debit_txns->sum('amount') ) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                            <ul class="nav nav-tabs nav-justified">
                                <li class="nav-item"><a data-toggle="tab" href="#tab-eg11-0"
                                        class="active nav-link">Credit Transactions</a></li>
                                <li class="nav-item"><a data-toggle="tab" href="#tab-eg11-1" class="nav-link">Debit
                                        Transactions</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                {{-- Credit --}}
                                <div class="tab-pane active" id="tab-eg11-0" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="mb-2 transactions-table table">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Id</th>
                                                    <th class="text-center">Reference id</th>
                                                    <th class="text-center">Cr. Account</th>
                                                    <th class="text-center">Dr. Account</th>
                                                    <th class="text-center">Amount</th>
                                                    <th class="text-center">Narration</th>
                                                    <th class="text-center">Trans. Type</th>
                                                    <th class="text-center">Status</th>
                                                    <th class="text-center">Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($credit_txns as $t)
                                                <tr>
                                                    <td class="text-center">{{$t->id}}</td>
                                                    <td class="text-center">{{$t->reference}}</td>
                                                    <td class="text-center">{{$t->cr_acct_name}}</td>
                                                    <td class="text-center">{{$t->dr_acct_name}}</td>
                                                    <td class="text-center">₦{{number_format($t->amount)}}</td>
                                                    <td class="text-center">{{$t->narration}}</td>
                                                    <td class="text-center">{{ucwords($t->transactionType->name)}}</td>
                                                    <td class="text-center">{{ucwords($t->status)}} </td>
                                                    <td class="text-center">{{$t->created_at->format('d M y ')}}</td>
                                                </tr>
                                                @endforeach

                                            </tbody>
                                            {{$credit_txns->links()}}
                                        </table>
                                    </div>
                                </div>
                                {{-- Debit --}}
                                <div class="tab-pane" id="tab-eg11-1" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="mb-2 transactions-table table">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Id</th>
                                                    <th class="text-center">Reference id</th>
                                                    <th class="text-center">Cr. Account</th>
                                                    <th class="text-center">Dr. Account</th>
                                                    <th class="text-center">Amount</th>
                                                    <th class="text-center">Narration</th>
                                                    <th class="text-center">Trans. Type</th>
                                                    <th class="text-center">Status</th>
                                                    <th class="text-center">Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($debit_txns as $t)
                                                <tr>
                                                    <td class="text-center">{{$t->id}}</td>
                                                    <td class="text-center">{{$t->reference}}</td>
                                                    <td class="text-center">{{$t->cr_acct_name}}</td>
                                                    <td class="text-center">{{$t->dr_acct_name}}</td>
                                                    <td class="text-center">₦{{number_format($t->amount)}}</td>
                                                    <td class="text-center">{{$t->narration}}</td>
                                                    <td class="text-center">{{ucwords($t->transactionType->name)}}</td>
                                                    <td class="text-center">{{ucwords($t->status)}} </td>
                                                    <td class="text-center">{{$t->created_at->format('d M y ')}}</td>
                                                </tr>
                                                @endforeach

                                            </tbody>
                                            {{$debit_txns->links()}}
                                        </table>
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
@endsection
