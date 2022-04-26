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
                        <div>All Users</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 pb-3 card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="">
                                {{ $segment }}
                            </div>
                            @if ($segment == "Verification Level")
                            <form class="form-inline p-2"
                                method="GET">
                                {{-- @csrf --}}
                            <div class="form-group mr-2">
                                <select name="status" class="form-control" required>
                                    <option value="">Select Category</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Level 1">Level 1</option>
                                        <option value="Level 2">Level 2</option>
                                        <option value="Level 3">Level 3</option>
                                </select>
                            </div>
                            <button class="btn btn-outline-primary"><i class="fa fa-filter"></i></button>
                             </form> 
                        @endif
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
                                    <th>#</th>
                                    <th>Name</th>
                                    {{-- <th >Last name</th> --}}
                                    <th>Email</th>
                                    @if($segment == "Users Birthday")
                                    <th>Birthday</th>
                                    @else
                                    <th>Verification Level</th>
                                    @endif
                                    <th>Date Of SignUp</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $u)
                                <tr>
                                    <td class="text-muted">{{$u->id}}</td>
                                    <td>{{ucwords($u->first_name)}}</td>
                                    {{-- <td >{{$u->last_name}}</td> --}}
                                    <td>{{$u->email}}</td>
                                    @if($segment == "Users Birthday")
                                    <td>{{ $u->birthday}}</td>
                                    @else
                                    <td>
                                        @switch($u->verification_status)
                                        @case('Level 3')
                                        <div class="badge badge-success">{{$u->verification_status}}</div>
                                        @break
                                        @case("Pending")
                                        <div class="badge badge-danger">{{$u->verification_status}}</div>
                                        @break
                                        @case('Level 1')
                                        <div class="badge badge-warning">{{$u->verification_status}}</div>
                                        @break
                                        @case('Level 2')
                                        <div class="badge badge-info">{{$u->verification_status}}</div>
                                        @break
                                        @default
                                        <div class="badge badge-primary">{{$u->verification_status}}</div>

                                        @endswitch
                                    </td>
                                    <td>{{ $u->verification_status}}</td>
                                    @endif
                                    <td>{{$u->created_at->format('d M y')}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>


@endsection
