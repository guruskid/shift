@php
$all_users= App\User::orderBy('email', 'asc' )->get();
@endphp
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
                        <div>Chinesse Admin</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mx-auto mb-3">
                    <div class="card">
                        <div class="card-header">Add Admin</div>
                        <div class="card-body">
							<form action=" {{route('admin.chinese_add_admin')}} " method="post">
								@csrf
                                <div class="form-group">
                                    <label for="">User Email</label>
                                    <select name="id" class="form-control">
                                        <option value=""></option>
                                        @foreach ($all_users as $u)
                                        <option value="{{$u->id}}"> {{ ucfirst($u->email) }} </option>
                                        @endforeach
                                    </select>
								</div>
								<button class="btn btn-success">Add</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 pb-3 card">
                        <div class="table-responsive">
                            <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Name</th>
                                        <th class="text-center">Email</th>
                                        <th class="text-center">Phone</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $u)
                                    @if ($u->id != Auth::user()->id)
                                    <tr>
                                        <td class="text-center text-muted">{{$u->id}}</td>
                                        <td class="text-center">{{ucwords($u->first_name)}}</td>
                                        <td class="text-center">{{$u->email}}</td>
                                        <td class="text-center">{{$u->phone}}</td>
                                        <td class="text-center">
                                            @switch($u->status)
                                            @case('verified')
                                            <div class="badge badge-success">{{$u->status}}</div>
                                            @break
                                            @case("declined")
                                            <div class="badge badge-danger">{{$u->status}}</div>
                                            @break
                                            @case('not verified')
                                            <div class="badge badge-warning">{{$u->status}}</div>
                                            @break
                                            @case('waiting')
                                            <div class="badge badge-info">{{$u->status}}</div>
                                            @break
                                            @default
                                            <div class="badge badge-primary">{{$u->status}}</div>
                                            @endswitch
                                            @if ($u->role == 889)
                                            Senior Accountant
                                            @endif
                                        </td>
                                        <td class="text-center" >
                                            @if ($u->status == 'waiting')
                                            <a href="{{route('admin.chinese_add_admin.action', [$u->id, 'active'] )}}" class="btn btn-sm btn-info">Activate</a>
                                            @else
                                            <a href="{{route('admin.chinese_add_admin.action', [$u->id, 'waiting'] )}}" class="btn btn-sm btn-warning">Deactivate</a>
											@endif
                                            <a href="{{route('admin.chinese_add_admin.action', [$u->id, 'remove'] )}} "  class="btn btn-sm btn-danger">Remove</a>
                                            @if (Auth::user()->role == 449 AND $u->role != 449)
                                            <a href="{{route('admin.chinese_add_admin.action', [$u->id, 'upgrade-to-senior'] )}} "  class="btn btn-sm btn-success">Upgrade</a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
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
@endsection
