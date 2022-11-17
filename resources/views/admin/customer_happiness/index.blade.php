@extends('layouts.customer_hapiness')

@section('content')
<div id="content" class="main-content">
    <div class="layout-px-spacing">

        <div class="dashboard-title d-flex">
            <ion-icon name="home-outline"></ion-icon>
            <div class="description">
                <h5>Dashboard Home</h5>
                <P>Hi good to see you again</P>
            </div>
        </div>
            <div class="row layout-top-spacing">
                <div class="col-xl-2.5 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing">
                    <div class="widget widget-chart-one">
                        <div class="widget-heading">
                            <div>
                                <h5 class="">Total Users</h5>
                                <a href="{{route('admin.wallet-transactions')}} ">View all</a>
                            </div>
                            <div class="widget-n">
                                <h5>{{ $total_user }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing">
                    <div class="widget widget-chart-one">
                        <div class="widget-heading">
                            <div>
                                <h5 class="">Called Users </h5>

                            </div>
                            <div class="widget-n">
                                <h5>500</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing">
                    <div class="widget widget-chart-one">
                        <div class="widget-heading">
                            <div>
                                <h5 class="">Responded Users </h5>
                            </div>
                            <div class="widget-n">
                                <h5>500</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2.5 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing">
                    <div class="widget widget-chart-one">
                        <div class="widget-heading">
                            <div>
                                <h5 class="">Quaterly Inactive User</h5>
                                <a href="{{route('admin.wallet-transactions')}} ">View all</a>
                            </div>
                            <div class="widget-n">
                                <h5>500</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2.5 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing">
                    <div class="widget widget-chart-one">
                        <div class="widget-heading">
                            <div>
                                <h5 class="">Recalcitrant Users</h5>
                                <a href="{{route('admin.wallet-transactions')}} ">View all</a>
                            </div>
                            <div class="widget-n">
                                <h5>500</h5>
                            </div>
                        </div>
                    </div>
                </div>






            <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <div class="widget widget-table-two mb-4">
                            <div class="widget-heading">
                                <h5 class="">GIFT CARD TRANSACTIONS</h5>
                            </div>

                            <div class="widget-content">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th><div class="th-content">Asset</div></th>
                                                <th><div class="th-content">Type</div></th>
                                                <th><div class="th-content">Value</div></th>
                                                <th><div class="th-content th-heading">Cash</div></th>
                                                <th><div class="th-content">User</div></th>
                                                <th><div class="th-content">Status</div></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($g_txns as $t)
                                            <tr>
                                                <td><div class="td-content customer-name">{{ucwords($t->card)}}</div></td>
                                                <td><div class="td-content product-brand">{{$t->type}}</div></td>
                                                <td><div class="td-content">{{$t->amount}}</div></td>
                                                <td><div class="td-content pricing"><span class="">₦{{number_format($t->amount_paid)}}</span></div></td>
                                                <td><div class="td-content pricing"><span class="">{{isset($t->user->first_name) ? $t->user->first_name :''}}</span></div></td>
                                                <td><div class="td-content">
                                                    @switch($t->status)
                                                    @case('success')
                                                    <span class="badge badge-success text-uppercase">{{$t->status}}</span>
                                                    @break
                                                    @case("failed")
                                                    <span class="badge badge-danger text-uppercase">{{$t->status}}</span>
                                                    @break
                                                    @case('declined')
                                                    <span class="badge badge-warning text-uppercase">{{$t->status}}</span>
                                                    @break
                                                    @case('waiting')
                                                    <span class="badge badge-info text-uppercase">{{$t->status}}</span>
                                                    @break
                                                    @default
                                                    <span class="badge badge-success text-uppercase">{{$t->status}}</span>
                                                    @endswitch
                                                </div></td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @if (auth()->user()->role ==555)
                                    <div class="View-all">
                                        <a href="{{route('admin.asset-transactions', 0)}}">View all </a>
                                    </div>
                                    @endif

                                </div>
                            </div>
                        </div>
                        <div class="w-100"></div>
                        <div class="widget widget-table-two">
                            <div class="widget-heading">
                                <h5 class="">CRYPTO TRANSACTIONS </h5>
                            </div>

                            <div class="widget-content">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th><div class="th-content">Asset</div></th>
                                                <th><div class="th-content">Type</div></th>
                                                <th><div class="th-content">Value</div></th>
                                                <th><div class="th-content th-heading">Cash</div></th>
                                                <th><div class="th-content">User</div></th>
                                                <th><div class="th-content">Status</div></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($c_txns as $t)
                                            <tr>
                                                <td><div class="td-content customer-name">{{ucwords($t->card)}}</div></td>
                                                <td><div class="td-content product-brand">{{$t->type}}</div></td>
                                                <td><div class="td-content">{{$t->amount}}</div></td>
                                                <td><div class="td-content pricing"><span class="">₦{{number_format($t->amount_paid)}}</span></div></td>
                                                <td><div class="td-content pricing"><span class="">{{isset($t->user->first_name)?$t->user->first_name:''}}</span></div></td>
                                                <td><div class="td-content">
                                                    @switch($t->status)
                                                    @case('success')
                                                    <span class="badge badge-success text-uppercase">{{$t->status}}</span>
                                                    @break
                                                    @case("failed")
                                                    <span class="badge badge-danger text-uppercase">{{$t->status}}</span>
                                                    @break
                                                    @case('declined')
                                                    <span class="badge badge-warning text-uppercase">{{$t->status}}</span>
                                                    @break
                                                    @case('waiting')
                                                    <span class="badge badge-info text-uppercase">{{$t->status}}</span>
                                                    @break
                                                    @default
                                                    <span class="badge badge-success text-uppercase">{{$t->status}}</span>
                                                    @endswitch
                                                </div></td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @if (auth()->user()->role ==555)
                                    <div class="View-all">
                                        <a href="{{route('admin.asset-transactions', 1)}}">View all </a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                <div class="widget widget-table-two mb-4">
                    <div class="widget-heading">
                        <h5 class="">Wallet Transactions</h5>
                    </div>

                    <div class="widget-content">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th><div class="th-content">Type</div></th>
                                        <th><div class="th-content">Amount</div></th>
                                        <th><div class="th-content">User</div></th>
                                        <th><div class="th-content th-heading">Prey.Bal</div></th>
                                        <th><div class="th-content">Cur.Bal</div></th>
                                        <th><div class="th-content">Status</div></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($n_txns as $t)
                                    <tr>
                                        <td><div class="td-content customer-name">{{ucwords($t->transactionType->name)}}</div></td>
                                        <td><div class="td-content product-brand">₦{{number_format($t->amount) }}</div></td>
                                        <td><div class="td-content">{{isset($t->user->first_name) ? $t->user->first_name : ''}}</div></td>
                                        <td><div class="td-content pricing"><span class="">₦{{number_format($t->previous_balance) }}</span></div></td>
                                        <td><div class="td-content pricing"><span class="">₦{{number_format($t->current_balance)}}</span></div></td>
                                        <td><div class="td-content"><span class="badge badge-success">{{$t->status}} </span></div></td>
                                    </tr>
                                    @endforeach

                                </tbody>
                            </table>
                            @if (auth()->user()->role ==555)
                            <div class="View-all">
                                <a href="{{route('admin.wallet-transactions')}}">View all </a>
                            </div>
                            @endif
                        </div>
                    </div>

                </div>
                <div class="w-100"></div>
                <div class="widget widget-table-two">
                    <div class="widget-heading">
                        <h5 class="">QUERY SUMMARY </h5>
                    </div>

                    <div class="widget-content">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th><div class="th-content">Name</div></th>
                                        <th><div class="th-content">Ticket Number</div></th>
                                        <th><div class="th-content">Time</div></th>
                                        <th><div class="th-content">Status</div></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tickets as $t)
                                    <tr>
                                        <th>
                                            <a style="color: black"
                                                href="#">{{ empty(trim($t->user->first_name)) ? $t->user->username : $t->user->first_name }}</a>
                                        </th>
                                        <td>
                                            {{ $t->ticketNo }}
                                        </td>
                                        <td>
                                            {{ $t->created_at->format('d M, H:ia') }}
                                        </td>
                                        <td><div class="td-content">
                                                    @switch($t->status)
                                                    @case('open')
                                                    <span class="badge badge-success text-uppercase">{{$t->status}}</span>
                                                    @break
                                                    @case("close")
                                                    <span class="badge badge-danger text-uppercase">{{$t->status}}</span>
                                                    @break
                                                    @default
                                                    <span class="badge badge-success text-uppercase">{{$t->status}}</span>
                                                    @endswitch
                                                </div></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if (auth()->user()->role ==555)
                            <div class="View-all">
                                <a href="{{route('customerHappiness.chatdetails',['status'=>'New'])}}">Load More Queries</a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>


            </div>





        </div>

    </div>

</div>
@endsection

