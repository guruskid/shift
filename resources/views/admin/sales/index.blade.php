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
                
                <div class="col-xl-2 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing">
                    <div class="widget widget-chart-one @if(isset($type) AND $type =='all_user') bg-primary @endif">
                        <div class="widget-heading">
                            <div>
                                <h5 class="@if(isset($type) AND $type =='all_user') text-white @endif">New Users </h5>
                                <p><a class="@if(isset($type) AND $type =='all_user') text-white @endif"
                                     href="{{ route('sales.user-category',['type'=>'all_user']) }}">View</a></p>
                            </div>
                            <div class="widget-n">
                                <h5>{{ $new_user }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing">
                    <div class="widget widget-chart-one @if(isset($type) AND $type =='called_user') bg-primary @endif">
                        <div class="widget-heading">
                            <div>
                                <h5 class="@if(isset($type) AND $type =='called_user') text-white @endif">Called Users </h5>
                                <p><a class="@if(isset($type) AND $type =='called_user') text-white @endif"
                                     href="{{ route('sales.user-category',['type'=>'called_user']) }}">View</a></p>
                            </div>
                            <div class="widget-n">
                                <h5>{{ $called_user }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing">
                    <div class="widget widget-chart-one @if(isset($type) AND $type =='pending_user') bg-primary @endif">
                        <div class="widget-heading">
                            <div>
                                <h5 class="@if(isset($type) AND $type =='pending_user') text-white @endif">Pending</h5>
                                <p><a class="@if(isset($type) AND $type =='pending_user') text-white @endif" 
                                    href="{{ route('sales.user-category',['type'=>'pending_user']) }}">View</a></p>
                            </div>
                            <div class="widget-n">
                                <h5>{{ $pending_user }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing">
                    <div class="widget widget-chart-one @if(isset($type) AND $type =='good_lead') bg-primary @endif">
                        <div class="widget-heading">
                            <div>
                                <h5 class="@if(isset($type) AND $type =='good_lead') text-white @endif">Good Leads</h5>
                                <p><a class="@if(isset($type) AND $type =='good_lead') text-white @endif"
                                    href="{{ route('sales.user-category',['type'=>'good_lead']) }}">View</a></p>
                            </div>
                            <div class="widget-n">
                                <h5>{{ $good_user }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing">
                    <div class="widget widget-chart-one @if(isset($type) AND $type =='bad_lead') bg-primary @endif">
                        <div class="widget-heading">
                            <div>
                                <h5 class="@if(isset($type) AND $type =='bad_lead') text-white @endif">Bad Leads</h5>
                                <p><a class="@if(isset($type) AND $type =='bad_lead') text-white @endif"
                                    href="{{ route('sales.user-category',['type'=>'bad_lead']) }}">View</a></p>
                            </div>
                            <div class="widget-n">
                                <h5>{{ $bad_user }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing">
                    <div class="widget widget-chart-one @if(isset($type) AND $type =='traded_user') bg-primary @endif">
                        <div class="widget-heading">
                            <div>
                                <h5 class="@if(isset($type) AND $type =='traded_user') text-white @endif">Traded Users</h5>
                                <p><a class="@if(isset($type) AND $type =='traded_user') text-white @endif"
                                     href="{{ route('sales.user-category',['type'=>'traded_user']) }}">View</a></p>
                            </div>
                            <div class="widget-n">
                                <h5>{{ $total }}</h5>
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
                                                    <th><div class="text-center">Name</div></th>
                                                    <th><div class="text-center">Username</div></th>
                                                    <th><div class="text-center">Signup Date</div></th>
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
                                                    <td>{{ $u->user->created_at->format('d M y, h:ia') }}</td>
                                                    @if ($type != "traded_user")
                                                    <td class="text-center">
                                                        @if ($type == "all_user")
                                                         <a href="#" class="my-2" data-toggle="modal" data-target="#view-phone-number" onclick="showPhoneNumber({{$u->user}})">
                                                            <span class="btn btn btn-info">View Phone Number</span>
                                                        </a>
                                                        <a href="#" class="my-2" data-toggle="modal" data-target="#add-call-log" onclick="AddResponse({{$u->user}})">
                                                            <span class="btn btn btn-info">Response</span>
                                                        </a>
                                                        
                                                        @endif
                                                        @if ($type !="all_user")
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
                                        <div class="View More">
                                            <a href="{{ route('sales.view-type',['type'=>$type]) }}">View all </a>
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
                                <select onchange="category_status()" id="category" name="status" class="form-control" required>
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
                            <textarea class="form-control" id="v_feedback" name="feedback" rows="5" required></textarea>
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
@endsection
