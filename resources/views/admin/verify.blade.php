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
                            <i class="pe-7s-user icon-gradient bg-night-sky">
                            </i>
                        </div>
                        <div>Verify Users</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-header">Waiting Users </div>
                        <div class="table-responsive">
                            <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-center">Name</th>
                                        <th class="text-center">Email</th>
                                        <th class="text-center">Phone</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Id card</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($users as $u)
                                    <tr>
                                        <td class="text-center">{{ ucfirst($u->first_name." ".$u->last_name) }}</td>
                                        <td class="text-center">{{$u->email}} </td>
                                        <td class="text-center">{{$u->phone}} </td>
                                        <td class="text-center">{{$u->status}} </td>
                                        <td class="text-center"> <img src="{{asset('storage/idcards/'.$u->id_card)}}"
                                                alt="id_card" height="100px">
                                        </td>
                                        <td class="text-center">
                                            <a href="{{asset('storage/idcards/'.$u->id_card)}}"><span
                                                    class="btn btn-info">View</span></a>
                                            <a href="#" onclick="update('{{$u->email}}')" data-toggle="modal"
                                                data-target="#update"><span class="btn  btn-primary">Update</span></a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>

                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

{{--Update Status --}}
<div class="modal fade  item-badge-rightm" id="update" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action=" {{route('admin.verify_user')}}" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="modal-header">
                    <div class="media">
                        <div class="media-body">
                            <h4 class="media-heading" id="user_name">Username</h4>
                        </div>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label for="lead_message">Status</label>
                        <select name="status" id="" class="form-control">
                            <option value="" id="user_status"></option>
                            <option value="verified">Verified</option>
                            <option value="not verified">Not verified</option>
                            <option value="waiting">Waiting</option>
                            <option value="declined">Declined</option>
                        </select>
                        <input type="hidden" name="id" id="user_id">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

