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
                                case 'respondedUsers':
                                    echo 'Responded Users';
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
                                    @if($type == 'TradesRespondedUsers')
                                    <th>Email Address</th>
                                    <th>Type of Asset</th>
                                    <th>Value of Asset</th>
                                    <th>Date</th>
                                    <th>Agent</th>
                                    @endif
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
                                   
                                    @if($type == 'TradesRespondedUsers')
                                    <td>{{($t->user) ?  $t->user->email: '' }}</td>
                                    <td>{{($t->tranxCard) ? $t->tranxCard : $t->card }}</td>
                                    <td>
                                        @if (in_array($t->card_id,$giftCardKeys))
                                            {{ $t->quantity * $t->amount }}
                                        @else
                                            {{ $t->amount }}
                                        @endif
                                    </td>
                                    <td>{{ $t->updated_at->format('d M y, h:ia') }}</td>
                                    <td>{{($t->agent) ? $t->agent->first_name." ".$t->agent->last_name : 'none'  }}</td>
                                    @endif

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
