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
                                <div class="form-group mr-2">
                                    <input class="form-control" name="search" type="text" placeholder="Search User">
                                </div>
                                <button class="btn btn-outline-primary"><i class="fa fa-filter"></i></button>
                            </form>

                            <form class="form-inline p-2"
                                method="GET">

                                @if($type != "Quarterly_Inactive")
                                    <div class="form-group mr-2">
                                        <label for="">Start date </label>
                                        <input type="date" required name="start" value="{{app('request')->input('start')}}" class="ml-2 form-control">
                                    </div>
                                    <div class="form-group mr-2">
                                        <label for="">End date </label>
                                        <input type="date" required name="end" value="{{app('request')->input('end')}}" class="ml-2 form-control">
                                    </div>
                                @else
                                    <div class="form-group mr-2">
                                        <input class="form-control" name="month" type="number" placeholder="Enter Month Range">
                                    </div>
                                @endif

                                @if($type == "callLog")
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
                    @if ($salesCategory =='old')
                    @include('admin.business_developer.include.oldUsersViewCategory')
                    @elseif($salesCategory =='new')
                    @include('admin.business_developer.include.newUsersViewCategory')
                    @endif
                    
                </div>
            </div>
        </div>
    </div>
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
            <form action="{{route('business-developer.create.call-log')}} " method="POST" class="mb-3">
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
                                            <option value="NoResponse">No Response</option>
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
                        @if (in_array($type,['newUnresponsiveUser','Responded_Users','Recalcitrant_Users','callLog']))
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
                            
                            @if (in_array($type,['newUnresponsiveUser','Responded_Users','Recalcitrant_Users','callLog']))
                                disabled
                            @endif></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
                    @if (!($type =="Responded_Users" OR $type == "Recalcitrant_Users" OR $type =="callLog"))
                    <button type="submit" class="btn btn-primary">Update Query</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>


@endsection
