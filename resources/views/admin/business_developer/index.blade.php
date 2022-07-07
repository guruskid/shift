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
                onclick="window.location = '{{ route('business-developer.user-category',['type'=>'Quarterly_Inactive']) }}'">
                    <div class="widget widget-chart-one @if(isset($type) AND $type =='Quarterly_Inactive') bg-primary @endif">
                        <div class="widget-heading">
                            <div>
                                <h5 class="@if(isset($type) AND $type =='Quarterly_Inactive') text-white @endif">Quaterly Inactive Users </h5>
                            </div>
                            <div class="widget-n">
                                <h5>{{ $QuarterlyInactiveUsers }}</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing"
                onclick="window.location = '{{ route('business-developer.user-category',['type'=>'NoResponse']) }}'">
                    <div class="widget widget-chart-one @if(isset($type) AND $type =='NoResponse') bg-primary @endif">
                        <div class="widget-heading">
                            <div>
                                <h5 class="@if(isset($type) AND $type =='NoResponse') text-white @endif">No Response </h5>
                            </div>
                            <div class="widget-n">
                                <h5>{{ $NoResponse }}</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-2 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing"
                onclick="window.location = '{{ route('business-developer.user-category',['type'=>'Called_Users']) }}'">
                    <div class="widget widget-chart-one @if(isset($type) AND $type =='Called_Users') bg-primary @endif">
                        <div class="widget-heading">
                            <div>
                                <h5 class="@if(isset($type) AND $type =='Called_Users') text-white @endif">Called Users</h5>
                            </div>
                            <div class="widget-n">
                                <h5>{{ $CalledUsers }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing"
                onclick="window.location = '{{ route('business-developer.user-category',['type'=>'Responded_Users']) }}'">
                    <div class="widget widget-chart-one @if(isset($type) AND $type =='Responded_Users') bg-primary @endif">
                        <div class="widget-heading">
                            <div>
                                <h5 class="@if(isset($type) AND $type =='Responded_Users') text-white @endif">Responded Users</h5>
                            </div>
                            <div class="widget-n">
                                <h5>{{ $RespondedUsers }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing"
                onclick="window.location = '{{ route('business-developer.user-category',['type'=>'Recalcitrant_Users']) }}'">
                    <div class="widget widget-chart-one @if(isset($type) AND $type =='Recalcitrant_Users') bg-primary @endif">
                        <div class="widget-heading">
                            <div>
                                <h5 class="@if(isset($type) AND $type =='Recalcitrant_Users') text-white @endif">Recalcitrant Users</h5>
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
                                                    @if ($type !="NoResponse")
                                                    <th><div class="">Last Transaction Date</div></th>
                                                    @endif
                                                    @if ($type == "NoResponse")
                                                    <th><div class="">No Response Cycle</div></th>
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
                                                    @if ($type == "Quarterly_Inactive" OR $type =="Responded_Users" OR $type == "Called_Users" OR $type =="NoResponse")
                                                        <th><div class="">Action</div></th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($data_table as $u)
                                                <tr>
                                                    <td><div class="td-content customer-name">{{$u->user->first_name." ".$u->user->last_name}}</div></td>
                                                    <td>{{ $u->user->username }}</td>
                                                    @if ($type !="NoResponse")
                                                    <td>{{ $u->last_transaction_date }}</td>
                                                    @endif
                                                    @if ($type == "NoResponse")
                                                    <td>{{ ($u->noResponse_streak == null) ? 0 : $u->noResponse_streak }}</td>
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
                                                    @if ($type == "Quarterly_Inactive" OR $type =="Responded_Users" OR $type == "Called_Users" OR $type =="NoResponse")
                                                    <td class="text-center">
                                                        @if ($type == "Quarterly_Inactive" OR $type =="NoResponse")
                                                        
                                                        <div class="btn-group">
                                                            <a href="#" class="my-2 mr-2" data-toggle="modal" data-target="#view-phone-number" onclick="showPhoneNumber({{$u->user}})">
                                                                <span class="btn btn btn-info">View Phone Number</span>
                                                            </a>
    
                                                            <a href="#" class="my-2" data-toggle="modal" data-target="#add-call-log" onclick="AddResponse({{$u->user}})">
                                                                <span class="btn btn btn-info">Response</span>
                                                            </a>
                                                        </div>
                                                        
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
    <div class="modal-dialog modal-lg" role="document">
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
                                        <option value="NoResponse">No Response</option>
                                </select>
                            </div>
                        </div>
                        <div class="d-none col-12" id="feedback-textarea">
                            <div class="form-group">
                            <label for="feedback">Feedback</label>
                            <textarea class="form-control" name="feedback" rows="5"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Query</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade  item-badge-rightm" id="view-phone-number" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
                <div class="modal-header">
                    <div class="media">
                        <div class="media-body">
                            <h1 class="media-heading " id="ph_email">User Email</h1>
                            <h1 class="media-body" id="ph_phoneNumber">User PhoneNumber</h1>
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
    <div class="modal-dialog modal-lg" role="document">
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
                    <button type="submit" class="btn btn-primary">Update Query</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
