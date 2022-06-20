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
                                Recent users
                            </div>
                            <div class="text-right">
                                <form action="{{ route('admin.user-search') }}" class="form-inline" method="POST">
                                    @csrf
                                    <input type="text" class="form-control mr-2" name="search" placeholder="Search">
                                    <button class="btn btn-outline-primary"><i class="fa fa-filter"></i></button>
                                </form>
                            </div>
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
                                    <th>Phone</th>
                                    @if (!in_array(Auth::user()->role, [777,775] ))
                                    <th>Naira balance</th>
                                    @endif
                                    <th>Date added</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $u)
                                <tr>
                                    <td class="text-muted">{{$u->id}}</td>
                                    <td>{{ucwords($u->first_name)}}</td>
                                    {{-- <td >{{$u->last_name}}</td> --}}
                                    <td>{{$u->email}}</td>
                                    <td>{{$u->phone}}</td>
                                    @if (!in_array(Auth::user()->role, [777,775] ))
                                    <td>₦{{$u->nairaWallet ? number_format($u->nairaWallet->amount) : 0 }} </td>
                                    @endif
                                    <td>{{$u->created_at->format('d M y')}}</td>
                                    <td>
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
                                    </td>

                                    <td>
                                        <div class="btn-group">
                                            <a class="btn btn-alternate"
                                                href=" {{route('admin.user', [$u->id, $u->email ] )}} ">View</a>
                                            @if ($u->nairaWallet)
                                                @if ($u->nairaWallet->status == 'active')
                                                <a class="btn btn-outline-danger" onclick="freezeAccount({{ $u }}, '/admin/freeze-account')"
                                                data-toggle="modal" data-target="#freeze-modal" href="#">Freeze</a>
                                                @else
                                                <a class="btn btn-outline-success" onclick="freezeAccount({{ $u }}, '/admin/activate-account')"
                                                data-toggle="modal" data-target="#freeze-modal" href="#">Activate</a>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
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

{{-- Confirm freezze account --}}
<div class="modal fade " id="freeze-modal">
    <div class="modal-dialog modal-dialog-centered ">
        <form action="{{route('admin.freeze-account')}}" id="freeze-form" method="post" >
            @csrf
            <div class="modal-content  c-rounded">
                <!-- Modal Header -->
                <div class="modal-header bg-custom-gradient c-rounded-top p-4 ">
                    <h4 class="modal-title">Confirm Refund <i class="fa fa-paper-plane"></i></h4>
                    <button type="button" class="close bg-light rounded-circle " data-dismiss="modal">&times;</button>
                </div>
                <!-- Modal body -->

                <div class="modal-body p-4">
                    <p class="text-success">Enter your pin to confirm action</p>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Wallet pin </label>
                                <input type="password" name="pin" required class="form-control">
                                <input type="hidden" name="user_id" id="user-id" required class="form-control">
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-block c-rounded bg-custom-gradient txn-btn">
                        Confirm
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
