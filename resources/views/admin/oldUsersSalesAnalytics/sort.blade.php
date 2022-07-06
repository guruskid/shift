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
                        <div>{{ $segment }}</div>
                    </div>
                </div>
            </div>
            <div class="card-header justify-content-between">{{ $segment }}
                <form action="{{route('sales.oldUsers.sort.salesAnalytics')}}" class="form-inline p-2" method="POST">
                    @csrf
                    <div class="form-group mr-2">
                        <select name="sortingType" id='e_sort' onchange="sortingchange()" class="form-control" required>
                            <option value="noData">SortingType</option>
                            <option value="period">Period</option>
                            <option value="days">days</option>
                            <option value="monthly">Monthly</option>
                            <option value="quarterly">Quaterly</option>
                            <option value="yearly">Yearly</option>
                        </select>
                    </div>
    
                    <div class="form-group mr-2">
                        <select name="sales" class="form-control" >
                            <option value="">Select Sales</option>
                            @foreach ($salesOldUsers as $sou)
                                <option value="{{ $sou->id }}">{{ $sou->first_name.' '.$sou->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" name="type" value="{{ $type }}">
                    <div id="period_start" class="form-group mr-2  d-none">
                        <label for="">Start date </label>
                        <input type="date" name="start" value="{{app('request')->input('start')}}" class="ml-2 form-control">
                    </div>
                    <div id="period_end" class="form-group mr-2  d-none">
                        <label for="">End date </label>
                        <input type="date" name="end" value="{{app('request')->input('end')}}" class="ml-2 form-control">
                    </div>
                    <div id="days" class="d-none">
                        <div class="form-group mr-2">
                            <input type="numbers" class="form-control" name="days" placeholder="Enter days">
                        </div>
                    </div>
                    <div id="months" class="d-none">
                        <div class="form-group mr-2">
                            <select name="month" class="form-control">
                                <option value="">Month</option>
                                @foreach($month as $m)
                                <option value="{{ $m['number'] }}">{{ ucwords($m['month']) }}</option>
                                @endforeach
                            </select>
    
                            
                        </div>
                    </div>
                    <div id="yearly" class="d-none">
                        <div class="form-group mr-2">
                            <select name="Year"class="form-control">
                                <option value="">Year</option>
                                @foreach ($years as $y)
                                <option value="{{ $y }}">{{ $y }}</option> 
                                @endforeach
                            </select>
                        </div>
                    </div>  
                    <div id="ac1" class="custom-control custom-switch mr-2 d-none">
                        <input type="checkbox" name="unique" id="send-btc" onclick="" class="custom-control-input toggle-settings" {{($unique== 1) ? 'checked' : ''}} data-name="SEND_BTC">
                        <label for="send-btc" class="custom-control-label">Unique</label>
                    </div>
                    <div id="ac2" class="custom-control custom-switch mr-2 d-none">
                        <input type="checkbox" name="total" id="receive-btc" onclick="" class="custom-control-input toggle-settings" {{($total== 1) ? 'checked' : ''}}  data-name="RECEIVE_BTC">
                        <label for="receive-btc" class="custom-control-label">Total</label>
                    </div>
                    <div id="ac3" class="d-none">
                    <button class="btn btn-outline-primary"><i class="fa fa-filter"></i></button>
                    </div>
                </form> 
            </div>
            @include('admin.oldUsersSalesAnalytics.includes.sortCards')
            @if($show_data == true)
            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 pb-3 card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="">
                                {{ $segment }}
                            </div>
                            {{--  <div class="">
                                <form action="{{route('admin.search')}}" method="post" class="form-inline" >
                            @csrf
                            <div class="form-group">
                                <input type="text" type="email" class="form-control" name="q"
                                    placeholder="Enter user name or email">
                            </div>
                            <button class="ml-3 btn btn-outline-secondary"> <i class="fa fa-search"></i></button>
                            </form>
                        </div> --}}
                    </div>
                    
                    <div class="table-responsive p-3">
                        <table
                            class="align-middle mb-0 table table-borderless table-striped table-hover">
                            <thead>
                                <tr>

                                    <th>Name</th>
                                    <th>Username</th>
                                    @if($type == 'calledUsers')
                                    <th>Called Date</th>
                                    <th>Called Time</th>
                                    <th>Call Duration</th>
                                    <th>Remark</th>
                                    @endif
                                    @if ($type == "respondedUsers")
                                    <th>SignupDate</th>
                                    <th>Responded Cycle</th>
                                    <th>Recalcitrant Cycle</th>
                                    <th>Last tran Date</th>
                                    <th>Vol of last tran</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($table_data as $t)
                                <tr>
                                    <td>{{($t->user) ? $t->user->first_name." ".$t->user->last_name :'' }}</td>
                                    <td>{{($t->user) ?  $t->user->username: '' }}</td>
                                   
                                    @if($type == 'calledUsers')
                                        <td>{{ $t->called_date->format('d M y') }}</td>
                                        <td>{{ $t->called_date->format('h:ia') }}</td>
                                        <td>{{ $t->callDuration }}</td>
                                        <td>{{ ($t->call_log) ? $t->call_log->call_response :''}}</td>
                                    @endif
                                    @if($type == 'respondedUsers')
                                        <td>{{ ($t->user) ? $t->user->created_at->format('d M y, h:ia'):'' }}</td>
                                        <td>{{ ($t->Responded_Cycle == null) ? 0 : $t->Responded_Cycle}}</td>
                                        <td>{{ ($t->Recalcitrant_Cycle == null) ? 0 : $t->Recalcitrant_Cycle }}</td>
                                        <td>{{ $t->lastTranxDate->format('d M y h:ia') }}</td>
                                        <td>${{ number_format($t->lastTranxVolume) }}</td>

                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="View-all">
                            <a href="{{ route('sales.oldUsers.show.salesAnalytics',['type'=>$type]) }}">View all </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
</div>


@endsection
