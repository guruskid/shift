@extends('layouts.admin')
@section('content')
<div id="content" class="main-content">
    <div class="layout-px-spacing">

        <div class="dashboard-title d-flex">
            <ion-icon name="home-outline"></ion-icon>
            <div class="description">
                <h5>Dashboard Home</h5>
                <P>Hi DANTOWN ADMIN, good to see you again Boss.</P>
            </div>
        </div>
            <div class="row layout-top-spacing">
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing">
                    <div class="widget widget-chart-one">
                        <div class="widget-heading">
                            <div>
                                <h5 class="">Users Balnce</h5>
                                <a href="{{route('admin.wallet-transactions')}} ">View all</a>
                            </div>
                            <div class="widget-n">
                                <h5>₦{{number_format($users_wallet_balance)}}</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing">
                    <div class="widget widget-chart-one">
                        <div class="widget-heading">
                            <div>
                                <h5 class="">Rubies Balance </h5>
                                <p>From Rubies API</p>
                            </div>
                            <div class="widget-n">
                                <h5>₦{{number_format($rubies_balance)}}</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing">
                    <div class="widget widget-chart-one">
                        <div class="widget-heading">
                            <div>
                                <h5 class="">Comp Bal </h5>
                                <a href="{{route('admin.admin-wallet')}} ">View all</a>
                            </div>
                            <div class="widget-n">
                                <h5>₦{{number_format($company_balance)}}</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing">
                    <div class="widget widget-chart-one">
                        <div class="widget-headin">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div>
                                        <a href="{{route('admin.wallet-charges')}}" ><h6 title="click to view" class="mb-0">Charges</h6> </a>
                                    </div>
                                    <div class="widget-n">
                                        <h6 class="mb-0">₦{{$charges}}</h6>
                                    </div>
                                </div>
                                <div>
                                    <div>
                                        <a href="{{route('admin.old-wallet-charges')}} "><h6 title="click to view" class="mb-0">Old Charges</h6> </a>
                                    </div>
                                    <div class="widget-n">
                                        <h6 class="mb-0">₦{{$old_charges}}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <div class="col-xl-8 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                <div class="widget widget-chart-one">
                    <div class="widget-heading">
                        <h5 class="">Revenue</h5>
                        <ul class="tabs tab-pills">
                            <li><a href="javascript:void(0);" id="tb_1" class="tabmenu">Monthly</a></li>
                        </ul>
                    </div>

                    <div class="widget-content">
                        <div class="tabs tab-content">
                            <div id="content_1" class="tabcontent">
                                <div id="revenueMonthly"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                <div class="row">
                    <div class="col-12">
                        <div class="widget widget-chart-two  mb-4">
                            <div class="widget-heading">
                                <div>
                                    <h5 class="">Verified Users </h5>
                                    <a href="{{route('admin.verified-users')}} ">View all</a>
                                </div>
                                <div class="widget-n">
                                    <h5>{{number_format($verified_users)}}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="w-100"></div>
                        <div class="widget widget-chart-two mb-4">
                            <div class="widget-heading">
                                <div>
                                    <h5 class="">Buy transactions via Wallet</h5>
                                    <a href="{{route('admin.wallet-transactions', 5)}} ">View all</a>
                                </div>
                                <div class="widget-n">
                                    <h5>₦{{number_format($buy_txns_wallet)}}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="w-100"></div>
                        <div class="widget widget-chart-two">
                            <div class="widget-heading">
                                <div>
                                    <h5 class="">Withdraw Transaction </h5>
                                    <a href="{{route('admin.wallet-transactions', 3)}} ">View all</a>
                                </div>
                                <div class="widget-n">
                                    <h5>₦{{number_format($withdraw_txns)}}</h5>
                                </div>
                            </div>
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
                                                <td><div class="td-content pricing"><span class="">{{$t->user->first_name}}</span></div></td>
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
                                    <div class="View-all">
                                        <a href="{{route('admin.asset-transactions', 0)}}">View all </a>
                                    </div>
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
                                                <td><div class="td-content pricing"><span class="">{{$t->user->first_name}}</span></div></td>
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
                                    <div class="View-all">
                                        <a href="{{route('admin.asset-transactions', 1)}}">View all </a>
                                    </div>
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
                                        <td><div class="td-content">{{$t->user->first_name}}</div></td>
                                        <td><div class="td-content pricing"><span class="">₦{{number_format($t->previous_balance) }}</span></div></td>
                                        <td><div class="td-content pricing"><span class="">₦{{number_format($t->current_balance)}}</span></div></td>
                                        <td><div class="td-content"><span class="badge badge-success">{{$t->status}} </span></div></td>
                                    </tr>
                                    @endforeach

                                </tbody>
                            </table>
                            <div class="View-all">
                                <a href="{{route('admin.wallet-transactions')}}">View all </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="widget widget-table-two mb-4">
                    <div class="widget-heading">
                        <h5 class="">Recent Users</h5>
                    </div>

                    <div class="widget-content">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th><div class="th-content">Name</div></th>
                                        <th><div class="th-content">Email</div></th>
                                        <th><div class="th-content">Phone</div></th>
                                        <th><div class="th-content th-heading">Wallet Balance</div></th>
                                        <th><div class="th-content">Date</div></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $u)
                                    <tr>
                                        <td><div class="td-content customer-name"><a href=" {{route('admin.user', [$u->id, $u->email ] )}} "> {{ucwords(Str::limit($u->first_name, 10, '..') )}}</a></div></td>
                                        <td><div class="td-content product-brand">{{Str::limit($u->email, 12, '...') }}</div></td>
                                        <td><div class="td-content">{{$u->phone}}</div></td>
                                        <td><div class="td-content pricing"><span class="">₦{{$u->nairaWallet ? number_format($u->nairaWallet->amount) : 0 }}</span></div></td>
                                        <td><div class="td-content pricing"><span class="">{{$u->created_at->format('d m')}}</span></div></td>
                                    </tr>
                                    @endforeach

                                </tbody>
                            </table>
                            <div class="View-all">
                                <a href="{{route('admin.users')}}">View all </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                <div class="widget widget-table-three">

                    <div class="widget-content">
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr class="border">
                                        <td><div class="td-content total"><h5>Total Transactions</h5> <p>Total cash value</p></div></td>
                                        <td><div class="td-content total"><h5>Buy</h5> <p>Transaction</p></div></td>
                                        <td><div class="td-content total"><h5 class="buy-n">₦{{number_format($buyCash)}}</h5></div></td>
                                        <td><div class="td-content total"><h5>Sell</h5> <p>Transaction</p></div></td>
                                        <td><div class="td-content total"><h5 class="sell-n">₦{{number_format($sellCash)}}</h5></div></td>
                                        <td><div class="td-content total"><h5 class="user">TOTAL USERS</h5></div></td>
                                    </tr>
                                    <tr>
                                        <td><div class="td-content total"><h5>Total Transactions</h5> <p>Total cash value</p></div></td>
                                        <td><div class="td-content total"><h5>Buy</h5> <p>Transaction</p></div></td>
                                        <td><div class="td-content total"><h5 class="buy-n">{{$buyCount}}</h5></div></td>
                                        <td><div class="td-content total"><h5>Sell</h5> <p>Transaction</p></div></td>
                                        <td><div class="td-content total"><h5 class="sell-n">{{$sellCount}}</h5></div></td>
                                        <td><div class="td-content total"><h5 class="user-n">{{$users_count}}</h5></div></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>


        </div>

    </div>

</div>
@endsection
