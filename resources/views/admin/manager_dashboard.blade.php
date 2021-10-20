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
        <div class="row">
            <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                <div class="widget widget-table-two mb-4">
                    <div class="widget-heading">
                        <h5 class="">GIFT CARD TRANSACTIONS</h5>
                    </div>

                    <div class="widget-content">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="th-content">Asset</div>
                                        </th>
                                        <th>
                                            <div class="th-content">Type</div>
                                        </th>
                                        <th>
                                            <div class="th-content">Value</div>
                                        </th>
                                        <th>
                                            <div class="th-content th-heading">Cash</div>
                                        </th>
                                        <th>
                                            <div class="th-content">User</div>
                                        </th>
                                        <th>
                                            <div class="th-content">Status</div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($g_txns as $t)
                                    <tr>
                                        <td>
                                            <div class="td-content customer-name">{{ucwords($t->card)}}</div>
                                        </td>
                                        <td>
                                            <div class="td-content product-brand">{{$t->type}}</div>
                                        </td>
                                        <td>
                                            <div class="td-content">{{$t->amount}}</div>
                                        </td>
                                        <td>
                                            <div class="td-content pricing"><span
                                                    class="">₦{{number_format($t->amount_paid)}}</span></div>
                                        </td>
                                        <td>
                                            <div class="td-content pricing"><span
                                                    class="">{{$t->user->first_name}}</span></div>
                                        </td>
                                        <td>
                                            <div class="td-content">
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
                                            </div>
                                        </td>
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
            </div>

            <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                <div class="widget widget-table-two">
                    <div class="widget-heading">
                        <h5 class="">CRYPTO TRANSACTIONS </h5>
                    </div>

                    <div class="widget-content">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="th-content">Asset</div>
                                        </th>
                                        <th>
                                            <div class="th-content">Type</div>
                                        </th>
                                        <th>
                                            <div class="th-content">Value</div>
                                        </th>
                                        <th>
                                            <div class="th-content th-heading">Cash</div>
                                        </th>
                                        <th>
                                            <div class="th-content">User</div>
                                        </th>
                                        <th>
                                            <div class="th-content">Status</div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($c_txns as $t)
                                    <tr>
                                        <td>
                                            <div class="td-content customer-name">{{ucwords($t->card)}}</div>
                                        </td>
                                        <td>
                                            <div class="td-content product-brand">{{$t->type}}</div>
                                        </td>
                                        <td>
                                            <div class="td-content">{{$t->amount}}</div>
                                        </td>
                                        <td>
                                            <div class="td-content pricing"><span
                                                    class="">₦{{number_format($t->amount_paid)}}</span></div>
                                        </td>
                                        <td>
                                            <div class="td-content pricing"><span
                                                    class="">{{$t->user->first_name}}</span></div>
                                        </td>
                                        <td>
                                            <div class="td-content">
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
                                            </div>
                                        </td>
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

</div>
@endsection
