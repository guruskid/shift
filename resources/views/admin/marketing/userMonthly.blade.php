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
            <form class="form-inline p-2"
                                method="GET">
                                {{-- @csrf --}}
                            <div class="form-group mr-2">
                                <select name="status" class="form-control" required>
                                    <option value="null">Select Years</option>
                                    @foreach ($date_collection as $key => $value)
                                        <option value="{{ $key }}">{{ $key }}</option>
                                     @endforeach
                                </select>
                            </div>
                            <button class="btn btn-outline-primary"><i class="fa fa-filter"></i></button>
                             </form> 
            <div class="row">
                @foreach ($collection as $c)
                    <div class="col-md-3 col-xl-3 to_trans_page"
                    onclick="window.location = '{{route('admin.users.view.month',['month'=>$c['Month_number'],'type'=>$c['type']])}}'" >
                        <div class="card mb-3 widget-content  bg-ripe-malin">
                            <div class="widget-content-wrapper py-2 text-white">
                                <div class="widget-content-actions mx-auto ">
                                    <div class="widget-heading text-center">
                                        <h5>{{ $c['month_name'] }}</h5>
                                        <h6>{{ number_format($c['number_of_Transactions']) }}</h6>
                                        {{-- Month_number --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
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
                                    <th>Full Name</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Signup Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $t)
                                <tr>
                                    
                                    <td>{{$t->first_name.' '.$t->last_name}}</td>
                                    <td>{{ $t->username }}</td>
                                    <td>{{ $t->email }}</td>
                                    <td>{{ $t->created_at->format('d M Y h:ia')}}</td>
                                    
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $data->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
</div>


@endsection
