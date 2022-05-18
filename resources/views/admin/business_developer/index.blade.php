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
                
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing">
                    <div class="widget widget-chart-one @if(isset($type) AND $type =='all_Users') bg-primary @endif">
                        <div class="widget-heading">
                            <div>
                                <h5 class="@if(isset($type) AND $type =='all_Users') text-white @endif">Total Number <br>Of Users </h5>
                                <p><a class="@if(isset($type) AND $type =='all_Users') text-white @endif"
                                     href="{{ route('business-developer.user-category',['type'=>'all_Users']) }}">View</a></p>
                            </div>
                            <div class="widget-n">
                                <h5>{{ $total_users }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing">
                    <div class="widget widget-chart-one @if(isset($type) AND $type =='Quarterly_Inactive') bg-primary @endif">
                        <div class="widget-heading">
                            <div>
                                <h5 class="@if(isset($type) AND $type =='Quarterly_Inactive') text-white @endif">Quaterly Inactive<br> Users </h5>
                                <p><a class="@if(isset($type) AND $type =='Quarterly_Inactive') text-white @endif" 
                                    href="{{ route('business-developer.user-category',['type'=>'Quarterly_Inactive']) }}">View</a></p>
                            </div>
                            <div class="widget-n">
                                <h5>{{ $QuarterlyInactiveUsers }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing">
                    <div class="widget widget-chart-one @if(isset($type) AND $type =='Called_Users') bg-primary @endif">
                        <div class="widget-heading">
                            <div>
                                <h5 class="@if(isset($type) AND $type =='Called_Users') text-white @endif">Called<br>Users</h5>
                                <p><a class="@if(isset($type) AND $type =='Called_Users') text-white @endif"
                                    href="{{ route('business-developer.user-category',['type'=>'Called_Users']) }}">View</a></p>
                            </div>
                            <div class="widget-n">
                                <h5>{{ $CalledUsers }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing">
                    <div class="widget widget-chart-one @if(isset($type) AND $type =='Responded_Users') bg-primary @endif">
                        <div class="widget-heading">
                            <div>
                                <h5 class="@if(isset($type) AND $type =='Responded_Users') text-white @endif">Responded<br>Users</h5>
                                <p><a class="@if(isset($type) AND $type =='Responded_Users') text-white @endif"
                                    href="{{ route('business-developer.user-category',['type'=>'Responded_Users']) }}">View</a></p>
                            </div>
                            <div class="widget-n">
                                <h5>{{ $RespondedUsers }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing">
                    <div class="widget widget-chart-one @if(isset($type) AND $type =='Recalcitrant_Users') bg-primary @endif">
                        <div class="widget-heading">
                            <div>
                                <h5 class="@if(isset($type) AND $type =='Recalcitrant_Users') text-white @endif">Recalcitrant<br>Users</h5>
                                <p><a class="@if(isset($type) AND $type =='Recalcitrant_Users') text-white @endif"
                                     href="{{ route('business-developer.user-category',['type'=>'Recalcitrant_Users']) }}">View</a></p>
                            </div>
                            <div class="widget-n">
                                <h5>{{ $RecalcitrantUsers }}</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                            <div class="widget widget-table-two">
                                <div class="widget-heading">
                                    <h5 class=""> </h5>
                                </div>
    
                                <div class="widget-content">
                                    <div class="table-responsive">
                                        <table class="table text-center">
                                            <thead>
                                                <tr>
                                                    <th><div class="">Name</div></th>
                                                    <th><div class="">Username</div></th>
                                                    <th><div class="">Signup Date</div></th>
                                                    <th><div class="">Phone Number</div></th>
                                                    <th><div class="">Last Transaction Date</div></th>
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
                                                    @if ($type == "Quarterly_Inactive" OR $type =="Responded_Users" OR $type == "Called_Users")
                                                        <th><div class="">Action</div></th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($data_table as $u)
                                                <tr>
                                                    <td><div class="td-content customer-name">{{$u->user->first_name}}</div></td>
                                                    <td>{{ $u->user->username }}</td>
                                                    <td>{{ $u->user->created_at->format('d M y, h:ia') }}</td>
                                                    <td>{{ $u->user->phone }}</td>
                                                    <td>{{ $u->last_transaction_date }}</td>
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
                                                    @if ($type == "Quarterly_Inactive" OR $type =="Responded_Users" OR $type == "Called_Users")
                                                    <td>
                                                        @if ($type == "Quarterly_Inactive")
                                                        <a href="#" class="my-2" data-toggle="modal" data-target="#add-call-log" onclick="AddResponse({{$u->user}})">
                                                            <span class="btn btn btn-info">Response</span>
                                                        </a>
                                                        @endif
                                                        @if ($type =="Responded_Users" OR $type == "Called_Users")
                                                        <a href="#" class="my-2" data-toggle="modal" data-target="#view-call-log" onclick="ViewResponse({{$u->call_log}},{{ $u->user }},{{ $u->call_log->call_category }})">
                                                            <span class="btn btn btn-info">View</span>
                                                        </a>
                                                        @endif
                                                    </td>
                                                    @endif
                                                </tr>
                                                    
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <div class="View More">
                                            <a href="{{ route('business-developer.view-type',['type'=>$type]) }}">View all </a>
                                        </div>
                                    </div>
                                </div>
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
                        @if ($type =="Responded_Users" OR $type == "Recalcitrant_Users")
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
                            @if ($type =="Responded_Users" OR $type == "Recalcitrant_Users")
                                disabled
                            @endif></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
                    @if (!($type =="Responded_Users" OR $type == "Recalcitrant_Users"))
                    <button type="submit" class="btn btn-primary">Update Call Log</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endsection