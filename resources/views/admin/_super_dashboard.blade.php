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

                <div class="col-md-3 col-xl-3">
                    <a href="{{route('admin.wallet-transactions')}} " class="text-white" style="text-decoration: none">
                        <div class="card mb-3 widget-content bg-alternate">
                            <div class="widget-content-wrapper py-5 text-white">
                                <div class="widget-content- mx-auto">
                                    <div class="widget-heading text-center">
                                        <h5>Users Balance</h5>
                                        <h5>₦{{number_format($users_wallet_balance)}}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 col-xl-3">
                    <a class="text-white" style="text-decoration: none">
                        <div class="card mb-3 widget-content bg-amy-crisp">
                            <div class="widget-content-wrapper py-5 text-white">
                                <div class="widget-content- mx-auto">
                                    <div class="widget-heading text-center">
                                        <h5>Rubies Balance</h5>
                                        <h5>₦{{number_format($rubies_balance)}}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 col-xl-3">
                    <a href="{{route('admin.admin-wallet')}} " class="text-white" style="text-decoration: none">
                        <div class="card mb-3 widget-content bg-custom-gradient">
                            <div class="widget-content-wrapper py-5 text-white">
                                <div class="widget-content- mx-auto">
                                    <div class="widget-heading text-center">
                                        <h5>Company's Balance</h5>
                                        <h5>₦{{number_format($company_balance)}}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 col-xl-3">
                    <a class="text-white" style="text-decoration: none">
                        <div class="card mb-3 widget-content bg-royal">
                            <div class="widget-content-wrapper py-5 text-white">
                                <div class="widget-content- mx-auto">
                                    <div class="widget-heading text-center">
                                        <h5>Transfer Charges</h5>
                                        <h5>₦{{number_format($charges)}}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="row mb-3">

                <div class="col-md-8">
                    <div class="card card-body shadow p-5" style="height: 400px;">
                        <h3>Card and Crypto Transactions Chart</h3>
                    </div>
                </div>
                {{-- Recent Transactions --}}
                <div class="col-md-4">
                    <a href="{{route('admin.wallet-transactions', 5)}}" class="text-white" style="text-decoration: none">
                        <div class="card mb-3 widget-content bg-warm-flame">
                            <div class="widget-content-wrapper py-2 text-white">
                                <div class="widget-content- mx-auto">
                                    <div class="widget-heading text-center">
                                        <h5>Buy Transactions via wallet</h5>
                                        <h5>₦{{number_format($buy_txns_wallet)}}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                    <a href="{{route('admin.wallet-transactions', 3)}}" class="text-white" style="text-decoration: none">
                        <div class="card mb-3 widget-content bg-sunny-morning">
                            <div class="widget-content-wrapper py-2 text-white">
                                <div class="widget-content- mx-auto">
                                    <div class="widget-heading text-center">
                                        <h5>Withdraw Transactions</h5>
                                        <h5>₦{{number_format($withdraw_txns)}}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                    <a href="{{route('admin.wallet-transactions', 9)}}" class="text-white" style="text-decoration: none">
                        <div class="card mb-3 widget-content bg-tempting-azure">
                            <div class="widget-content-wrapper py-2 text-white">
                                <div class="widget-content- mx-auto">
                                    <div class="widget-heading text-center">
                                        <h5>Airtime Transactions</h5>
                                        <h5>₦{{number_format($airtime_txns)}}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>



                </div>
            </div>

            <div class="row">
                <div class="col-md-6 col-lg-6">
                    <div class="main-card mb-3 card">
                        <div class="card-header">Gift Card Transactions </div>
                        <div class="card-body px-1">
                            <div class="table-responsive">
                                <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Asset</th>
                                            <th class="text-center">Type</th>
                                            <th class="text-center">Value</th>
                                            <th class="text-center">Cash</th>
                                            <th class="text-center">User</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($g_txns as $t)
                                        <tr>
                                            <td class="text-center">{{ucwords($t->card)}}</td>
                                            <td class="text-center">{{$t->type}}</td>
                                            <td class="text-center">{{$t->amount}}</td>
                                            <td class="text-center">{{number_format($t->amount_paid)}}</td>
                                            <td class="text-center"> {{$t->user->first_name}} </td>
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
                                    <a class="m-3" href=" {{route('admin.transactions')}} ">View all</button></a>
                                </tfoot>
                            </div>
                        </div>
                    </div>

                    <div class="main-card mb-3 card">
                        <div class="card-header">Crypto Transactions </div>
                        <div class="card-body px-1">
                            <div class="table-responsive">
                                <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Asset</th>
                                            <th class="text-center">Type</th>
                                            <th class="text-center">Value</th>
                                            <th class="text-center">Cash</th>
                                            <th class="text-center">User</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($c_txns as $t)
                                        <tr>
                                            <td class="text-center">{{ucwords($t->card)}}</td>
                                            <td class="text-center">{{$t->type}}</td>
                                            <td class="text-center">{{$t->amount}}</td>
                                            <td class="text-center">{{number_format($t->amount_paid)}}</td>
                                            <td class="text-center"> {{$t->user->first_name}} </td>
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
                                    <a class="m-3" href=" {{route('admin.transactions')}} ">View all</button></a>
                                </tfoot>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="main-card mb-3 card">
                        <div class="card-header">Wallet Transactions </div>
                        <div class="card-body px-1">
                            <div class="table-responsive">
                                <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Type</th>
                                            <th class="text-center">Amount</th>
                                            <th class="text-center">User</th>
                                            <th class="text-center">Prev. Bal</th>
                                            <th class="text-center">Cur. Bal</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($n_txns as $t)
                                        <tr>
                                            <td class="text-center">{{ucwords($t->transactionType->name)}}</td>
                                            <td class="text-center">₦{{number_format($t->amount) }}</td>
                                            <td class="text-center"> {{$t->user->first_name}} </td>
                                            <td class="text-center">₦{{number_format($t->previous_balance) }}</td>
                                            <td class="text-center">{{number_format($t->current_balance)}}</td>
                                            <td class="text-center">
                                                @switch($t->status)
                                                @case('success')
                                                <div class="badge badge-success">{{$t->status}}</div>
                                                @break
                                                @case("failed")
                                                <div class="badge badge-danger">{{$t->status}}</div>
                                                @break
                                                @case('refunded')
                                                <div class="badge badge-warning">{{$t->status}}</div>
                                                @break
                                                @case('pending')
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
                                    <a href="{{route('admin.wallet-transactions')}}" class="m-3">View all</button></a>
                                </tfoot>
                            </div>
                        </div>
                    </div>

                    <div class="main-card mb-3 pb-3 card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="">
                                Recent users
                            </div>
                        </div>
                        <div class="table-responsive p-3">
                            <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th> Name</th>
                                        {{-- <th >Last name</th> --}}
                                        <th >Email</th>
                                        <th >Phone</th>
                                        <th >Wallet balance</th>
                                        <th >Date added</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $u)
                                    <tr>
                                        <td ><a href=" {{route('admin.user', [$u->id, $u->email ] )}} "> {{ucwords(Str::limit($u->first_name, 10, '..') )}}</a> </td>
                                        {{-- <td >{{$u->last_name}}</td> --}}
                                        <td >{{Str::limit($u->email, 12, '...') }}</td>
                                        <td >{{$u->phone}}</td>
                                        <td >
                                            {{$u->nairaWallet ? number_format($u->nairaWallet->amount) : 0 }}
                                        </td>
                                        <td >{{$u->created_at->format('d M y')}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <tfoot>
                                <a class="m-3" href=" {{route('admin.users')}} ">View all</button></a>
                            </tfoot>
                        </div>
                    </div>
                    {{-- <div class="main-card mb-3 card">
                        <div class="card-header">
                            Transactions overview
                        </div>
                        <div class="card-body">
                            {!! $usersChart->container() !!}
                        </div>
                    </div> --}}
                </div>

            </div>

            {{-- Transactions and Users Overview --}}
            @if (Auth::user()->role == 999)
            <div class="row">
                <div class="main-card mb-3 card col-md-10">
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
                                                            ₦{{number_format($buyCash)}}</div>
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
                                                        <div class="widget-numbers text-info">{{$buyCount}}</div>
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
                                                            N{{number_format($sellCash)}}</div>
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
                                                        <div class="widget-numbers text-success">{{$sellCount}}</div>
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

                <div class="main-card mb-3 card col-md-2 ">
                    <div class="card-header">Total Users</div>
                    <div class="card-body">
                        <h4 class="text-center"><strong>{{$users_count}}</strong></h4>
                    </div>

                </div>
            </div>
            @endif

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

@if($usersChart)
{!! $usersChart->script() !!}
@endif
@endsection
