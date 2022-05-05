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
                    <div class="card mb-3 widget-content bg-grow-early">
                        <div class="widget-content-wrapper py-2 text-white">
                            <div class="widget-content-actions mx-auto ">
                                <div class="widget-heading text-center">
                                    <h5>Successfull </h5>
                                    <h6>{{$a_s_c}}</h6>
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
                                    <h5>Approved</h5>
                                    <h6>{{$a_a_c}}</h6>
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
                                    <h5>In Progress </h5>
                                    <h6>{{$a_i_c}}</h6>
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
                                    <h5>Waiting</h5>
                                    <h6>{{$a_w_c}}</h6>
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
                        <div class="table-responsive">
                            <table class="align-middle mb-0 table table-borderless table-striped table-hover">
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
                                    @foreach ($transactions as $t)
                                    <tr>
                                        <td class="text-center text-muted">{{$t->uid}}</td>
                                        <td class="text-center">{{ucwords($t->card)}}</td>
                                        <td class="text-center">{{$t->type}}</td>
                                        <td class="text-center">{{$t->amount}}</td>
                                        <td class="text-center">{{number_format($t->amount_paid)}}</td>
                                        <td class="text-center"> {{$t->user->first_name .' '. $t->user->last_name}} </td>
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
                                <a href=" {{route('admin.transactions')}} "><button
                                        class="m-3 btn btn-outline-info">View all</button></a>
                            </tfoot>
                        </div>
                    </div>
                </div>

                {{-- In progress Transactions --}}
                <div class="col-md-6">
                    <div class="main-card mb-3 card">
                        <div class="card-header d-flex justify-content-between">
                            <span>In Progress Transactions</span>
                            <div class="page-title-subheading">
                                <button class="btn btn-primary" onclick="location.reload()"> Refresh Page</button>
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
                                        <th class="text-center">Cash value</th>
                                        <th class="text-center">User</th>
                                        <th class="text-center">Agent</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($in_progress_transactions as $t)
                                    <tr>
                                        <td class="text-center text-muted">{{$t->uid}}</td>
                                        <td class="text-center">{{ucwords($t->card)}}</td>
                                        <td class="text-center">{{$t->type}}</td>
                                        <td class="text-center">{{$t->amount}}</td>
                                        <td class="text-center">{{number_format($t->amount_paid)}}</td>
                                        <td class="text-center"> {{$t->user->first_name .' '. $t->user->last_name}} </td>
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
                                <a href=" {{route('admin.transactions-status', 'in progress')}} "><button
                                        class="m-3 btn btn-outline-info">View all</button></a>
                            </tfoot>
                        </div>
                    </div>
                </div>

                {{-- Waiting Transactions --}}
                <div class="col-md-6">
                    <div class="main-card mb-3 card">
                        <div class="card-header d-flex justify-content-between">
                            <span>Waiting Transactions</span>
                            <div class="page-title-subheading">
                                <button class="btn btn-primary" onclick="location.reload()"> Refresh Page</button>
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
                                        <th class="text-center">Cash value</th>
                                        <th class="text-center">User</th>
                                        <th class="text-center">Agent</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($waiting_transactions as $t)
                                    <tr>
                                        <td class="text-center text-muted">{{$t->uid}}</td>
                                        <td class="text-center">{{ucwords($t->card)}}</td>
                                        <td class="text-center">{{$t->type}}</td>
                                        <td class="text-center">{{$t->amount}}</td>
                                        <td class="text-center">{{number_format($t->amount_paid)}}</td>
                                        <td class="text-center"> {{$t->user->first_name .' '. $t->user->last_name}} </td>
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
                                <a href=" {{route('admin.transactions-status', 'waiting')}} "><button
                                    class="m-3 btn btn-outline-info">View all</button></a>
                            </tfoot>
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

@if($usersChart)
{!! $usersChart->script() !!}
@endif
@endsection
