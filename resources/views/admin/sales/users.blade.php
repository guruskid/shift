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
                                {{ $segment }}[{{ number_format($count) }}]
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
                                <button class="btn btn-outline-primary"><i class="fa fa-filter"></i></button>
                            </form>
                    </div>
                    
                    <div class="table-responsive p-3">
                        <table
                            class="align-middle mb-0 table table-borderless table-striped table-hover text-center 
                            transactions-table
                            ">
                            <thead>
                                <tr>
                                    <th><div class="">Name</div></th>
                                    <th><div class="">Username</div></th>
                                    <th><div class="">Date</div></th>
                                    @if ($type != "traded_user")
                                        <th><div class="">Action</div></th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data_table as $u)
                                    <tr>
                                        <td><div class="td-content customer-name">{{$u->user->first_name}}</div></td>
                                        <td>{{ $u->user->username }}</td>
                                        <td>{{ $u->updated_at->format('d M y, h:ia') }}</td>
                                        @if ($type != "traded_user")
                                        <td>
                                            @if (in_array($type, ["pending_user","all_user"] ))
                                            <a href="#" class="my-2" data-toggle="modal" data-target="#view-phone-number" onclick="showPhoneNumber({{$u->user}})">
                                               <span class="btn btn btn-info">View Phone Number</span>
                                           </a>
                                           <a href="#" class="my-2" data-toggle="modal" data-target="#add-call-log" onclick="AddResponse({{$u->user}})">
                                               <span class="btn btn btn-info">Response</span>
                                           </a>
                                           
                                           @endif
                                           @if (!in_array($type, ["pending_user","all_user"] ))
                                           <a href="#" class="my-2" data-toggle="modal" data-target="#view-call-log" onclick="ViewNewUserData({{ $u->user }},{{ $u }})">
                                               <span class="btn btn btn-info">View</span>
                                           </a>
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
            <form action="{{route('sales.update.status')}} " method="POST" class="mb-3">
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
                        <input type="hidden" readonly name="type" value="{{ $type }}">
                    </div>
                    <div class="row">

                        <div class="col">
                            <div class="form-group">
                                <label for="">Category</label>
                                <select onchange="category_status()" id="category" name="status" class="form-control" required>
                                    <option value="" id="e_status">Select Category</option>
                                    <option value="pending">pending</option>
                                    <option value="goodlead">GoodLead</option>
                                    <option value="badlead">BadLead</option>
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
                    <button type="submit" class="btn btn-primary">Update Status</button>
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
            <form action="{{route('sales.update.status')}} " method="POST" class="mb-3">
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
                        <div class="col">
                            <div class="form-group">
                                <label for="">Category</label>
                                <select onchange="category_status()" id="category" name="status" class="form-control" 
                                @if (in_array($type, ["good_lead","bad_lead","called_user"] ))
                                 disabled
                                @endif
                                >
                                    <option value="" id="v_status">Select Category</option>
                                    <option value="pending">Pending</option>
                                    <option value="goodlead">Good Lead</option>
                                    <option value="badlead">Bad Lead</option>
                                </select>
                            </div>
                        </div>
                    
                        <div class="col-12">
                            <div class="form-group">
                            <label for="feedback">Feedback</label>
                            <textarea class="form-control" id="v_feedback" name="feedback" rows="5" required 
                            @if (in_array($type, ["good_lead","bad_lead","called_user"] ))
                            readonly
                           @endif
                           ></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
