@extends('layouts.admin')
@section('content')
<div id="content" class="main-content">
    <div class="layout-px-spacing">

        <div class="dashboard-title d-flex">
            <ion-icon name="home-outline"></ion-icon>
            <div class="description">
                <h5>Dashboard Home</h5>
                <P>Hi {{ Auth::user()->first_name ?? Auth::user()->username }}, good to see you again</P>
            </div>
        </div>
            <div class="row layout-top-spacing">
                
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing" 
                onclick="window.location = '{{ route('admin.sales.type',['type'=>'All_Users_App']) }}'">
                    <div class="widget widget-chart-one @if(isset($type) && $type =='All_Users_App') bg-primary @endif">
                        <div class="widget-heading">
                            <div>
                                <h5 class="@if(isset($type) && $type =='All_Users_App') text-white @endif">Total Signed Up Users<br>App </h5>
                            </div>
                            <div class="widget-n">
                                <h5>{{ $total_app_signed_up }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing"
                onclick="window.location = '{{ route('admin.sales.type',['type'=>'All_Users_Web']) }}'">
                    <div class="widget widget-chart-one @if(isset($type) && $type =='All_Users_Web') bg-primary @endif">
                        <div class="widget-heading">
                            <div>
                                <h5 class="@if(isset($type) && $type =='All_Users_Web') text-white @endif">Total Signed Up Users<br>Web </h5>
                            </div>
                            <div class="widget-n">
                                <h5>{{ $total_web_signed_up }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing"
                onclick="window.location = '{{ route('admin.sales.type',['type'=>'All_Transactions_App']) }}'">
                    <div class="widget widget-chart-one @if(isset($type) && $type =='All_Transactions_App') bg-primary @endif">
                        <div class="widget-heading">
                            <div>
                                <h5 class="@if(isset($type) && $type =='All_Transactions_App') text-white @endif">Total Transactions<br>App </h5>
                            </div>
                            <div class="widget-n">
                                <h5>{{ $total_app_transactions }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing"
                onclick="window.location = '{{ route('admin.sales.type',['type'=>'All_Transactions_Web']) }}'">
                    <div class="widget widget-chart-one @if(isset($type) && $type =='All_Transactions_Web') bg-primary @endif">
                        <div class="widget-heading">
                            <div>
                                <h5 class="@if(isset($type) && $type =='All_Transactions_Web') text-white @endif">Total Transactions<br>Web </h5>
                            </div>
                            <div class="widget-n">
                                <h5>{{ $total_web_transactions }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing"
                onclick="window.location = '{{ route('admin.sales.type',['type'=>'Daily_Users_App']) }}'">
                    <div class="widget widget-chart-one @if(isset($type) && $type =='Daily_Users_App') bg-primary @endif">
                        <div class="widget-heading">
                            <div>
                                <h5 class="@if(isset($type) && $type =='Daily_Users_App') text-white @endif">Daily No. of Signup Users<br>App </h5>
                            </div>
                            <div class="widget-n">
                                <h5>{{ $daily_app_signed_up }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing"
                onclick="window.location = '{{ route('admin.sales.type',['type'=>'Daily_Users_Web']) }}'">
                    <div class="widget widget-chart-one @if(isset($type) && $type =='Daily_Users_Web') bg-primary @endif">
                        <div class="widget-heading">
                            <div>
                                <h5 class="@if(isset($type) && $type =='Daily_Users_Web') text-white @endif">Daily No. of Signup Users<br>Web </h5>
                            </div>
                            <div class="widget-n">
                                <h5>{{ $daily_web_signed_up }}</h5>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing"
                onclick="window.location = '{{ route('admin.sales.type',['type'=>'Daily_Transactions_App']) }}'">
                    <div class="widget widget-chart-one @if(isset($type) && $type =='Daily_Transactions_App') bg-primary @endif">
                        <div class="widget-heading">
                            <div>
                                <h5 class="@if(isset($type) && $type =='Daily_Transactions_App') text-white @endif">Daily No. of Transactions<br>App </h5>
                            </div>
                            <div class="widget-n">
                                <h5>{{ $daily_app_transactions }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing"
                onclick="window.location = '{{ route('admin.sales.type',['type'=>'Daily_Transactions_Web']) }}'">
                    <div class="widget widget-chart-one @if(isset($type) && $type =='Daily_Transactions_Web') bg-primary @endif">
                        <div class="widget-heading">
                            <div>
                                <h5 class="@if(isset($type) && $type =='Daily_Transactions_Web') text-white @endif">Daily No. of Transactions<br>Web </h5>
                            </div>
                            <div class="widget-n">
                                <h5>{{ $daily_web_transactions }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing"
                onclick="window.location = '{{ route('admin.sales.type',['type'=>'Monthly_Users_App']) }}'">
                    <div class="widget widget-chart-one @if(isset($type) && $type =='Monthly_Users_App') bg-primary @endif">
                        <div class="widget-heading">
                            <div>
                                <h5 class="@if(isset($type) && $type =='Monthly_Users_App') text-white @endif">Monthly No. of Signup Users<br>App </h5>
                            </div>
                            <div class="widget-n">
                                <h5>{{ $monthly_app_signed_up }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing"
                onclick="window.location = '{{ route('admin.sales.type',['type'=>'Monthly_Users_Web']) }}'">
                    <div class="widget widget-chart-one @if(isset($type) && $type =='Monthly_Users_Web') bg-primary @endif">
                        <div class="widget-heading">
                            <div>
                                <h5 class="@if(isset($type) && $type =='Monthly_Users_Web') text-white @endif">Monthly No. of Signup Users<br>Web </h5>
                            </div>
                            <div class="widget-n">
                                <h5>{{ $monthly_web_signed_up }}</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing"
                onclick="window.location = '{{ route('admin.sales.type',['type'=>'Monthly_Transactions_App']) }}'">
                    <div class="widget widget-chart-one @if(isset($type) && $type =='Monthly_Transactions_App') bg-primary @endif">
                        <div class="widget-heading">
                            <div>
                                <h5 class="@if(isset($type) && $type =='Monthly_Transactions_App') text-white @endif">Monthly No. of Transactions<br>App </h5>
                            </div>
                            <div class="widget-n">
                                <h5>{{ $monthly_app_transactions }}</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing"
                onclick="window.location = '{{ route('admin.sales.type',['type'=>'Monthly_Transactions_Web']) }}'">
                    <div class="widget widget-chart-one @if(isset($type) && $type =='Monthly_Transactions_Web') bg-primary @endif">
                        <div class="widget-heading">
                            <div>
                                <h5 class="@if(isset($type) && $type =='Monthly_Transactions_Web') text-white @endif">Monthly No. of Transactions<br>Web </h5>
                            </div>
                            <div class="widget-n">
                                <h5>{{ $monthly_web_transactions }}</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing"
                onclick="window.location = '{{ route('admin.sales.type',['type'=>'Daily_Utility_Transactions']) }}'">
                    <div class="widget widget-chart-one @if(isset($type) && $type =='Daily_Utility_Transactions') bg-primary @endif">
                        <div class="widget-heading">
                            <div>
                                <h5 class="@if(isset($type) && $type =='Daily_Utility_Transactions') text-white @endif">Utility Transactions(Daily)</h5>
                            </div>
                            <div class="widget-n">
                                <h5>{{ $utility_daily }}</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing"
                onclick="window.location = '{{ route('admin.sales.type',['type'=>'Monthly_Utility_Transactions']) }}'">
                    <div class="widget widget-chart-one @if(isset($type) && $type =='Monthly_Utility_Transactions') bg-primary @endif">
                        <div class="widget-heading">
                            <div>
                                <h5 class="@if(isset($type) && $type =='Monthly_Utility_Transactions') text-white @endif">Utility Transactions(Monthly)</h5>
                            </div>
                            <div class="widget-n">
                                <h5>{{ $utility_monthly }}</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing"
                onclick="window.location = '{{ route('admin.sales.type',['type'=>'Monthly_New_Tranding_Users']) }}'">
                    <div class="widget widget-chart-one @if(isset($type) && $type =='Monthly_New_Tranding_Users') bg-primary @endif">
                        <div class="widget-heading">
                            <div>
                                <h5 class="@if(isset($type) && $type =='Monthly_New_Tranding_Users') text-white @endif">Monthly New Trading <br>Users</h5>
                            </div>
                            <div class="widget-n">
                                <h5>{{ $new_trading_users }}</h5>
                                {{-- <h5>{{ $new_trading_users }}%</h5> --}}
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing">
                    {{-- bg-primary text-white --}}
                        <div class="card mb-1 widget-content ">
                            <div class="widget-content-wrapper">
                                <div class="widget-heading">
                                    <h6 class="text-center" id='revenue_growth_summary_name'>% Revenue Growth(Monthly)</h6>
                                    <div class="widget-n" style="justify-content: center; text-align: center;">
                                        <div id='revenue_growth_summary_a' class="d-block">
                                            @if($revenueGrowth->revenueGrowth <= 0)
                                            <h5 class="text-danger">{{ $revenueGrowth->revenueGrowth }} %</h5>
                                            @else
                                            <h5 class="text-success" >{{ $revenueGrowth->revenueGrowth }} %</h5>
                                            @endif
                                        </div>
        
                                        <div id='revenue_growth_summary_b' class="d-none">
                                            <h5 class="" id='revenue_growth_summary' >.......</h5>
                                        </div>
                                    </div>
                                    <div class="form-group mr-2">
                                        <select name="sortingType" id='revenue_growth_summary_sort' onchange="revenueGrowthSort()" class="form-control">
                                            <option value="noData">SortingType</option>
                                            <option value="weekly">Weeekly</option>
                                            <option value="monthly">Monthly</option>
                                            <option value="quarterly">Quaterly</option>
                                            <option value="yearly">Yearly</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

            @if(isset($type) AND strpos($type,'Users') !== false)
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                    <div class="widget widget-table-three">

                        <div class="widget-content">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Full Name</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Signup Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($table_data as $t)
                                        <tr>
                                            <td>{{$t->first_name.' '.$t->last_name}}</td>
                                            <td>{{ $t->username }}</td>
                                            <td>{{ $t->email }}</td>
                                            <td>{{ $t->created_at->format('d M Y h:ia')}}</td>

                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                {{-- {{ $table_data->links() }} --}}
                                <div class="View-all">
                                    <a href="{{ route('admin.users.view.type',['type'=>$type]) }}">View all </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif(isset($type) AND strpos($type,'Transactions') !== false)
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                    <div class="widget widget-table-three">

                        <div class="widget-content">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Full Name</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Transaction</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($table_data as $t)
                                        <tr>
                                            <td>{{$t->user->first_name." ".$t->user->last_name}}</td>
                                            <td>{{ $t->user->username }}</td>
                                            <td>{{ $t->user->email }}</td>
                                            <td>{{ucwords($t->tradename)}}</td>
                                            <td>{{ $t->created_at->format('d M Y h:ia')}}</td>

                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                {{-- {{ $table_data->links() }} --}}
                                <div class="View-all">
                                    <a href="{{ route('admin.transactions.view.type',['type'=>$type]) }}">View all </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                    <div class="widget widget-table-three">

                        <div class="widget-content">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Full Name</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Signup Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($table_data as $t)
                                        <tr>
                                            <td>{{$t->first_name.' '.$t->last_name}}</td>
                                            <td>{{ $t->username }}</td>
                                            <td>{{ $t->email }}</td>
                                            <td>{{ $t->created_at->format('d M Y h:ia')}}</td>

                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                {{-- {{ $table_data->links() }} --}}
                                <div class="View-all">
                                    <a href="{{ route('admin.users.view.type',['type'=>'all_users']) }}">View all </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            @endif


        </div>

    </div>

</div>
@endsection
