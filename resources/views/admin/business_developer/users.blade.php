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
                        <div>{{ strtoupper($segment) }}</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 pb-3 card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="">
                                {{ $segment }} @if (isset($count))
                                    [{{ number_format($count) }}]
                                @endif 
                            </div>
                            <form class="form-inline p-2"
                                method="GET">
                                {{-- @csrf --}}
                                <div class="form-group mr-2">
                                    <label for="">Start date </label>
                                    <input type="date" required name="start" value="{{app('request')->input('start')}}" class="ml-2 form-control">
                                </div>
                                <div class="form-group mr-2">
                                    <label for="">End date </label>
                                    <input type="date" required name="end" value="{{app('request')->input('end')}}" class="ml-2 form-control">
                                </div>
                                @if($segment == "Call Log")
                                <div class="form-group mr-2">
                                    <select name="status" class="form-control" required>
                                        <option value="">Select Category</option>
                                        @foreach ($call_categories as $cc)
                                            <option value="{{ $cc->id }}">{{ $cc->category }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                                <button class="btn btn-outline-primary"><i class="fa fa-filter"></i></button>
                            </form>
                    </div>
                    
                    <div class="table-responsive p-3">
                        <table
                            class="align-middle mb-0 table table-borderless table-striped table-hover text-center 
                            {{-- transactions-table --}}
                            ">
                            <thead>
                                <tr>
                                    <th><div class="">Name</div></th>
                                    <th><div class="">Username</div></th>
                                    <th><div class="">Last Transaction Date</div></th>
                                    @if ($type =="callLog")
                                    <th><div class="">Category</div></th>
                                    @endif
                                    @if ($type == "Called_Users")
                                        <th><div class="">Called Date</div></th>
                                    @endif
                                    @if ($type == "Quarterly_Inactive")
                                        <th><div class="">Responded Cycle</div></th>
                                        <th><div class="">Recalcitrant Cycle</div></th>
                                    @endif
                                    @if($type == "Recalcitrant_Users")
                                        <th><div class="">Recalcitrant Date</div></th>
                                    @endif
                                    @if ($type == "Quarterly_Inactive")
                                        <th><div class="">Previous Cycle</div></th>
                                    @endif
                                    @if ($type == "Quarterly_Inactive" OR $type =="Responded_Users" OR $type == "Called_Users" OR $type =="callLog")
                                        <th><div class="">Action</div></th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data_table as $u)
                                <tr>
                                    <td><div class="td-content customer-name">{{$u->user->first_name}}</div></td>
                                    <td>{{ $u->user->username }}</td>
                                    <td>{{ $u->last_transaction_date }}</td>
                                    @if ($type =="callLog")
                                    <td>{{ ($u->call_category) ? $u->call_category->category : 'none' }}</td>
                                    @endif
                                    @if ($type == "Called_Users")
                                        <td>{{ $u->call_log->created_at->format('d M y, h:ia') }}</td>
                                    @endif
                                    @if ($type == "Quarterly_Inactive")
                                    <td>{{ ($u->Responded_Cycle == null) ? 0 : $u->Responded_Cycle }}</td>
                                    <td>{{ ($u->Recalcitrant_Cycle == null) ? 0 : $u->Recalcitrant_Cycle  }}</td>
                                    @endif
                                    @if($type == "Recalcitrant_Users")
                                        <td>{{ $u->updated_at->format('d M y, h:ia') }}</td>
                                    @endif
                                    @if ($type == "Quarterly_Inactive")
                                    <td>{{ ($u->Previous_Cycle == null) ? 'none' : $u->Previous_Cycle }}
                                        @if ($u->Previous_Cycle == "Responded")
                                            @if ($u->Responded_streak != null)
                                                ({{ $u->Responded_streak }})
                                            @endif
                                        @else
                                            @if ($u->Recalcitrant_streak != null)
                                                 ({{ $u->Recalcitrant_streak }})
                                             @endif
                                        @endif
                                        </td>
                                    @endif
                                    @if ($type == "Quarterly_Inactive" OR $type =="Responded_Users" OR $type == "Called_Users" OR $type =="callLog")
                                    <td>
                                        @if ($type == "Quarterly_Inactive")
                                        <a href="#" class="my-2 mr-2" data-toggle="modal" data-target="#view-phone-number" onclick="showPhoneNumber({{$u->user}})">
                                            <span class="btn btn btn-info">View Phone Number</span>
                                        </a>
                                        <a href="#" class="my-2" data-toggle="modal" data-target="#add-call-log" onclick="AddResponse({{$u->user}})">
                                            <span class="btn btn btn-info">Response</span>
                                        </a>
                                        
                                        @endif
                                        @if ($type == "Called_Users")
                                            @if ($type =="Responded_Users" OR $type == "Called_Users")
                                            <a href="#" class="my-2" data-toggle="modal" data-target="#view-call-log" onclick="ViewResponse({{$u->call_log}},{{ $u->user }},{{ $u->call_log->call_category }})">
                                                <span class="btn btn btn-info">View</span>
                                            </a>
                                            @endif 
                                        @else
                                            @if ($type =="Responded_Users" OR $type == "Called_Users" OR $type =="callLog")
                                            <a href="#" class="my-2" data-toggle="modal" data-target="#view-call-log" onclick="ViewResponse({{$u}},{{ $u->user }},{{ $u->call_category }})">
                                                <span class="btn btn btn-info">View</span>
                                            </a>
                                            @endif

                                        @endif
                                        
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $data_table->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

{{-- Add Called User Data Modal --}}
<div class="modal fade  item-badge-rightm" id="add-call-log" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{route('business-developer.create.call-log')}} " method="POST" class="mb-3">
                {{ csrf_field() }}
                <div class="modal-header">
                    <div class="media">
                        <div class="media-body">
                            <h4 class="media-heading" id="e_email">User Email</h4>
                        </div>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <input type="hidden" readonly name="id" id="e_id">
                        <input type="hidden" readonly name="phoneNumber" id="e_phoneNumber">
                    </div>
                    <div class="row">

                        <div class="col">
                            <div class="form-group">
                                <label for="">Category</label>
                                <select onchange="category_status()" id="category" name="status" class="form-control" required>
                                    <option value="" id="e_status">Select Category</option>
                                    @foreach ($call_categories as $cc)
                                        <option value="{{ $cc->id }}">{{ $cc->category }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="d-none col-12" id="feedback-textarea">
                            <div class="form-group">
                            <label for="feedback">Feedback</label>
                            <textarea class="form-control" name="feedback" rows="5" required></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Call Log</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade  item-badge-rightm" id="view-phone-number" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
                <div class="modal-header">
                    <div class="media">
                        <div class="media-body">
                            <h4 class="media-heading " id="ph_email">User Email</h4>
                            <h6 class="media-heading" id="ph_phoneNumber">User PhoneNumber</h6>
                        </div>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
                </div>
        </div>
    </div>
</div>

<div class="modal fade  item-badge-rightm" id="view-call-log" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{route('business-developer.update.call-log')}} " method="POST" class="mb-3">
                {{ csrf_field() }}
                <div class="modal-header">
                    <div class="media">
                        <div class="media-body">
                            <h4 class="media-heading" id="v_email">User Email</h4>
                        </div>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <input type="hidden" readonly name="id" id="v_id">
                    </div>
                    <div class="row">
                        @if ($type =="Responded_Users" OR $type == "Recalcitrant_Users" OR $type =="callLog")
                            <div class="col">
                                <div class="form-group">
                                    <label for="">Category</label>
                                    <input type="text" class="form-control" id="v_status_input" disabled>
                                </div>
                            </div>
                        @else
                        <div class="col">
                            <div class="form-group">
                                <label for="">Category</label>
                                <select onchange="category_status()" id="category" name="status" class="form-control" required>
                                    <option value="" id="v_status">Select Category</option>
                                    @foreach ($call_categories as $cc)
                                        <option value="{{ $cc->id }}">{{ $cc->category }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif
                    
                        <div class="col-12">
                            <div class="form-group">
                            <label for="feedback">Feedback</label>
                            <textarea class="form-control" id="v_feedback" name="feedback" rows="5" required
                            @if ($type =="Responded_Users" OR $type == "Recalcitrant_Users" OR $type =="callLog")
                                disabled
                            @endif></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
                    @if (!($type =="Responded_Users" OR $type == "Recalcitrant_Users" OR $type =="callLog"))
                    <button type="submit" class="btn btn-primary">Update Call Log</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>


@endsection
