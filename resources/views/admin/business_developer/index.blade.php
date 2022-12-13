@extends('layouts.admin')
@section('content')
<div id="content" class="main-content">
    <div class="layout-px-spacing">
        <div class="d-flex justify-content-between">
            <div></div>
        <div>
            <h6 >Responded Trade Count(Month) <span class="badge bg-warning text-white">{{number_format($analyticsCount)}}</span></h6>
            <h6 >Responded Trade Volume(Month) <span class="badge bg-warning text-white">{{number_format($analyticsVolume)}}</span></h6>
        </div>
        </div>

        <div class="dashboard-title d-flex">
            <ion-icon name="home-outline"></ion-icon>
            <div class="description">
                <h5>Dashboard Home</h5>
                <P>Hi {{ Auth::user()->first_name ?? Auth::user()->username }}, good to see you again</P>
            </div>
        </div>
        <div class="row layout-top-spacing">
            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-4 layout-spacing"
            onclick="window.location = '{{ route('business-developer.user-category',['type'=>'Quarterly_Inactive']) }}'">
                <div class="widget widget-chart-one @if(isset($salesCategory) AND $salesCategory =='old') bg-primary @endif">
                    <div class="widget-heading">
                        <div>
                            <b><h3 class="@if(isset($salesCategory) AND $salesCategory =='old') text-white @endif">Old Users</h3></b>
                            <h6 class="@if(isset($salesCategory) AND $salesCategory =='old') text-white @endif">Quarterly Inactive</h6>
                            <h6 class="@if(isset($salesCategory) AND $salesCategory =='old') text-white @endif">Called Users</h6>
                            <h6 class="@if(isset($salesCategory) AND $salesCategory =='old') text-white @endif">Responded Users</h6>
                        </div>
                        <div class="widget-n">
                            <h5>Value</h5>
                            <h5>{{ $QuarterlyInactiveUsersCount }}</h5>
                            <h5>{{ $calledUsersCount }}</h5>
                            <h5>{{ $RespondedUsersCount }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        
            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-4 layout-spacing"
            onclick="window.location = '{{ route('business-developer.new-users.index',['type'=>'newInactiveUser']) }}'">
                <div class="widget widget-chart-one @if(isset($salesCategory) AND $salesCategory =='new') bg-primary @endif">
                    <div class="widget-heading">
                        <div>
                            <b><h3 class="@if(isset($salesCategory) AND $salesCategory =='new') text-white @endif">New Users </h3></b>
                            <h6 class="@if(isset($salesCategory) AND $salesCategory =='new') text-white @endif">New Inactive Users</h6>
                            <h6 class="@if(isset($salesCategory) AND $salesCategory =='new') text-white @endif">Called New Users</h6>
                            <h6 class="@if(isset($salesCategory) AND $salesCategory =='new') text-white @endif">Unresponsive</h6>
                        </div>
                        <div class="widget-n">
                            <h5>Value</h5>
                            <h5>{{ $newInactiveUsersCount }}</h5>
                            <h5>{{ $newCalledUsersCount }}</h5>
                            <h5>{{ $newUnresponsiveUsersCount }}</h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-4 layout-spacing"
            onclick="window.location = '{{ route('business-developer.new-users.index',['type'=>'newInactiveUser']) }}'">
                <div class="widget widget-chart-one @if(isset($salesCategory) AND $salesCategory =='active') bg-primary @endif">
                    <div class="widget-heading">
                        <div>
                            <b><h3 class="@if(isset($salesCategory) AND $salesCategory =='active') text-white @endif">Active Users</h3></b>
                            <h6 class="@if(isset($salesCategory) AND $salesCategory =='active') text-white @endif">Not Available</h6>
                        </div>
                        <div class="widget-n">
                            <h5>{{ $activeUsersCount }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($salesCategory =='old')
        @include('admin.business_developer.include.oldUsersIndex')
        @elseif($salesCategory =='new')
        @include('admin.business_developer.include.newUsersIndex')
        @endif


    </div>

</div>


{{-- Add Called User Data Modal --}}
{{-- <div class="modal fade  item-badge-rightm" id="add-call-log" role="dialog">
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
</div> --}}

<div class="modal fade  item-badge-rightm" id="view-phone-number" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            @if ($salesCategory =='old')
            <form action="{{route('business-developer.create.call-log')}} " method="POST" class="mb-3">
            @elseif($salesCategory =='new')
            <form action="{{route('business-developer.new-users.create.call-log')}} " method="POST" class="mb-3">  
            @endif
                {{ csrf_field() }}
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
                <div class="modal-body">
                    <div class="d-block" id="ph_show_details_button">
                        <button onclick="open_call_log()" id="e_button_continue" type="button" class="float-right btn btn-primary">Continue</button>
                    </div>
                    <div class="d-none" id="ph_show_phone_details">
                        <div class="form-group">
                            <input type="hidden" readonly name="id" id="ph_id">
                            <input type="hidden" readonly name="start" id="ph_startTime">
                            <input type="hidden" readonly name="end" id="ph_endTime">
                        </div>

                        <div class="row">

                            <div class="col">
                                <div class="form-group">
                                    <label for="">Category</label>
                                    <select onchange="showFeedback()" id="ph_category" name="category_name" class="form-control" required>
                                        <option value="" id="e_status">Select Category</option>
                                        @foreach ($call_categories as $cc)
                                            <option value="{{ $cc->id }}">{{ $cc->category }}</option>
                                            @endforeach
                                            @if ($salesCategory =='old')
                                            <option value="NoResponse">No Response</option>
                                            @endif
                                    </select>
                                </div>
                            </div>
                            <div class="d-none col-12" id="ph_feedback">
                                <div class="form-group">
                                <label for="feedback">Feedback</label>
                                <textarea class="form-control" name="feedback" rows="5"></textarea>
                                </div>
                            </div>

                            <div class="d-none col-12" id="ph_proceed_button">
                                <button type="submit" class=" mt-2 btn btn-primary">Create Query</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
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
                            <h5 class="media-heading" id="v_phone"></h5>
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
                        @if (in_array($type,['newUnresponsiveUser','Responded_Users','Recalcitrant_Users']))
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
                            @if (in_array($type,['newUnresponsiveUser','Responded_Users','Recalcitrant_Users']))
                                disabled
                            @endif></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
                    @if (!in_array($type,['newUnresponsiveUser','Responded_Users','Recalcitrant_Users']))
                    <button type="submit" class="btn btn-primary">Update Query</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
