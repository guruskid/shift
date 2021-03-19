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
                        <div>Trade Naira</div>
                    </div>
                </div>
            </div>

            @if (in_array(Auth::user()->role, [999]))
            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 pb-3 card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="">
                                Recent users
                                @foreach ($errors->all() as $err)
                                <p class="text-danger">{{ $err }}</p>
                                @endforeach
                            </div>
                        </div>
                        <div class="table-responsive p-3">
                            <table
                                class="align-middle mb-0 table table-borderless table-striped table-hover transactions-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Wallet balance</th>
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
                                        <td>{{$u->email}}</td>
                                        <td>{{$u->phone}}</td>
                                        <td>
                                            {{$u->nairaWallet ? number_format($u->nairaWallet->amount) : 0 }}
                                        </td>
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
                                                <a href="#" onclick="setUser('{{ $u->id }}')" data-target="#topup-modal" data-toggle="modal" class="btn btn-primary">Topup</a>
                                                <a href="#" onclick="setUser('{{ $u->id }}')" data-target="#deduct-modal" data-toggle="modal" class="btn btn-danger">Deduct</a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

{{-- Confirm topup account --}}
<div class="modal fade " id="topup-modal">
    <div class="modal-dialog modal-dialog-centered ">
        <form action="{{route('admin.trade-naira.topup')}}" id="freeze-form" method="post">
            @csrf
            <div class="modal-content  c-rounded">
                <!-- Modal Header -->
                <div class="modal-header bg-custom-gradient c-rounded-top p-4 ">
                    <h4 class="modal-title">Topup <i class="fa fa-paper-plane"></i></h4>
                    <button type="button" class="close bg-light rounded-circle " data-dismiss="modal">&times;</button>
                </div>
                <!-- Modal body -->

                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Amount</label>
                                <input type="number" name="amount" required class="form-control">
                                <input type="hidden" name="user_id" required class="form-control t-user-id">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Wallet pin </label>
                                <input type="password" name="pin" required class="form-control">
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

{{-- Confirm deduction account --}}
<div class="modal fade " id="deduct-modal">
    <div class="modal-dialog modal-dialog-centered ">
        <form action="{{route('admin.trade-naira.deduct')}}" id="freeze-form" method="post">
            @csrf
            <div class="modal-content  c-rounded">
                <!-- Modal Header -->
                <div class="modal-header bg-custom-gradient c-rounded-top p-4 ">
                    <h4 class="modal-title">Deduct <i class="fa fa-paper-plane"></i></h4>
                    <button type="button" class="close bg-light rounded-circle " data-dismiss="modal">&times;</button>
                </div>
                <!-- Modal body -->

                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Amount</label>
                                <input type="number" name="amount" required class="form-control">
                                <input type="hidden" name="user_id" required class="form-control t-user-id">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Wallet pin </label>
                                <input type="password" name="pin" required class="form-control">
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


@section('scripts')
    <script>
        function setUser(id) {
            $('.t-user-id').val(id)
        }
    </script>
@endsection
