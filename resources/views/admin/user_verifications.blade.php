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
                                Verifications
                            </div>
                            <a href="{{ route('admin.verification-history') }}" class="btn btn-primary">Verification History</a>
                        </div>
                        <div class="table-responsive p-3">
                            <table
                                class="align-middle mb-0 table table-borderless table-striped table-hover transactions-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Wallet balance</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($verifications as $v)
                                    <tr>
                                        <td class="text-muted">{{$v->id}}</td>
                                        <td>{{ucwords($v->user->first_name .' '. $v->user->last_name)}}</td>
                                        <td>{{$v->user->phone}}</td>
                                        <td> {{$v->user->nairaWallet ? number_format($v->user->nairaWallet->amount) : 0 }}
                                        </td>
                                        <td>{{$v->type}}</td>
                                        <td>
                                            <div class="badge badge-warning">{{$v->status}}</div>
                                        </td>

                                        <td>
                                            <div class="btn-group">
                                                <button data-toggle="modal" data-target="#modal-{{ $v->id }}"
                                                    class="btn btn-primary">View</button>

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
        </div>
    </div>
</div>

{{-- Confirm freezze account --}}
@foreach ($verifications as $v)
<div class="modal fade " id="modal-{{ $v->id }}">
    <div class="modal-dialog modal-dialog-centered ">
        <div class="modal-content  c-rounded">
            <div class="modal-body p-4">
                <form action="{{route('admin.verify', $v)}}" id="freeze-form" method="post">@method('put') @csrf
                    @if($v->type == 'ID Card')
                    <img src="/storage/idcards/{{ $v->path }}" class="img-fluid">
                    {{-- <img src="{{ asset('storage/idcards/'.$v->path) }}" class="img-fluid"> --}}
                    <a href="/storage/idcards/{{ $v->path }}"><button type="button"
                            class="btn my-3 btn-outline-primary">View</button></a>
                    @else
                    <img src="/storage/address/{{ $v->path }}" class="img-fluid">
                    {{-- <img src="{{ asset('storage/idcards/'.$v->path) }}" class="img-fluid"> --}}
                    <a href="/storage/address/{{ $v->path }}"><button type="button"
                            class="btn my-3 btn-outline-primary">View</button></a>
                    @endif
                    @if ($v->type == 'Address')
                    <div class="form-group">
                        <label for="">Address</label>
                        <input type="text" class="form-control" value="{{ $v->user->address_img }}">
                    </div>
                    @endif

                    <button class="btn btn-block c-rounded bg-custom-gradient txn-btn">
                        Confirm
                    </button>
                    <span onclick="decline_reason('reason_for_decline_verification_div{{ $v->id }}')"
                        class="btn btn-warning rounded-pill btn-block m-1">Decline</span>
                </form>


                <form action="{{ route('admin.cancel-verification', $v) }}" method="post">@csrf @method('put')
                    {{-- <button class="btn btn-danger">Cancel verification</button> --}}

                    @if($v->type == 'ID Card')
                    <div class="col-12 d-none" id="reason_for_decline_verification_div{{ $v->id }}">
                        <div class="form-group mt-5">
                            <input type="hidden" name="user_id" value="{{$v->user_id}}">
                            <input type="hidden" name="type" value="{{$v->type}}">
                            <label for="reason" class="text-danger">Reason for card declination </label>
                            <select name="reason" id="" class="form-control">
                                <option value="Uploaded a wrong information">Uploaded a wrong information</option>
                                <option value="Unclear uploaded document">Unclear uploaded document</option>
                                <option value="Full image of the document was not uploaded">Full image of the document
                                    was not uploaded</option>
                                <option value="A mismatch of information">A mismatch of information</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-danger btn-block rounded-pill">Cancel verification</button>
                        </div>
                    </div>
                    @else
                    <div class="col-12 d-none" id="reason_for_decline_verification_div{{ $v->id }}">
                        <div class="form-group mt-5">
                            <input type="hidden" name="user_id" value="{{$v->user_id}}">
                            <input type="hidden" name="type" value="{{$v->type}}">
                            <label for="reason" class="text-danger">Reason for address declination</label>
                            <select name="reason" id="" class="form-control">
                                <option value="Uploaded a wrong information">Uploaded a wrong information</option>
                                <option value="Unclear uploaded document">Unclear uploaded document</option>
                                <option value="Full image of the document was not uploaded">Full image of the document
                                    was not uploaded</option>
                                <option value="A mismatch of information">A mismatch of information</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-danger btn-block rounded-pill">Cancel verification</button>
                        </div>
                    </div>
                    @endif
                </form>

            </div>
        </div>
    </div>
</div>
@endforeach
@endsection
