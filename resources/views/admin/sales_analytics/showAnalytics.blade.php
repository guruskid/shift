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
                        <div>
                            @php
                            switch ($type) {
                                case 'calledUsers':
                                    echo 'Called Users';
                                    break;
                                case 'GoodLead':
                                    echo 'Good Leads';
                                    break;
                                case 'BadLead':
                                    echo 'Bad Leads';
                                    break;
                                case 'GLConversion':
                                    echo 'Good Lead Conversion';
                                    break;
                                case 'BLConversion':
                                    echo 'Bad Lead Conversion';
                                    break;
                                default:
                                    echo 'Sales New Users Analytics';
                                    break;
                            }
                        @endphp
                        </div>
                    </div>
                </div>
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

                                <th>Name</th>
                                <th>Username</th>
                                @if($type != 'calledUsers')
                                <th>SignupDate</th>
                                @endif
                                @if($type == 'calledUsers' OR $type == 'GoodLead' OR $type == 'BadLead')
                                <th>Called Date</th>
                                <th>Called Time</th>
                                <th>Call Duration</th>
                                <th>Remark</th>
                                @endif
                                @if($type == 'GLConversion' OR $type == 'BLConversion')
                                <th>Trans Time</th>
                                <th>Trans Date</th>
                                <th>Volume of Trans</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($table_data as $t)
                            <tr>
                                <td>{{($t->user) ? $t->user->first_name." ".$t->user->last_name :'' }}</td>
                                <td>{{($t->user) ?  $t->user->username: '' }}</td>
                                @if($type != 'calledUsers')
                                    <td>{{ ($t->user) ? $t->user->created_at->format('d M y, h:ia'):'' }}</td>
                                @endif
                                @if($type == 'calledUsers' OR $type == 'GoodLead' OR $type == 'BadLead')
                                    <td>{{ $t->updated_at->format('d M y') }}</td>
                                    <td>{{ $t->updated_at->format('h:ia') }}</td>
                                    <td>{{ $t->callDuration }}</td>
                                    <td>{{ $t->comment }}</td>
                                @endif
                                @if($type == 'GLConversion' OR $type == 'BLConversion')
                                    <td>{{ $t->created_at->format('h:ia') }}</td>
                                    <td>{{ $t->created_at->format('d M y') }}</td>
                                    <td>${{ number_format($t->amount) }}</td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{$table_data->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
</div>


@endsection
