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
                        <div>Pay Bridge Withdrawal Queue Range</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 pb-3 card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="">
                                Ranges
                            </div>
                            <div>
                                <button data-toggle="modal" data-target="#account-modal" class="btn btn-primary">Add range</button>
                            </div>
                        </div>
                        <div class="table-responsive p-3">
                            <table
                                class="align-middle mb-0 table table-borderless table-striped table-hover transactions-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Pending Requests</th>
                                        <th>Time to payment</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ranges as $key => $range)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>{{ $range->pending_requests }}</td>
                                        <td>{{ $range->pay_time }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <button data-toggle="modal" data-target="#edit-modal-{{ $range->id }}" class="btn btn-primary">Edit</button>
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

@foreach($ranges as $key => $range)
    {{-- Edit Modal --}}
    <div class="modal fade " id="edit-modal-{{ $range->id }}">
        <div class="modal-dialog modal-dialog-centered ">
            <div class="modal-content">
                <div class="modal-body">
                    <form action="{{ route('admin.naira-p2p.update-withdrawal-queue') }}" method="POST" class="mb-4">@csrf
                        <div class="form-row ">
                            <input type="hidden" value="{{$range->id}}" name="id">
                            <div class="col-md-12">
                                <div class="position-relative form-group">
                                    <label>Pending Requests</label>
                                    <input type="text" required class="form-control" value="{{$range->pending_requests}}" name="pending_requests">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="position-relative form-group">
                                    <label>Time to payment(in Minutes)</label>
                                    <input type="text" required class="form-control" value="{{$range->pay_time}}" name="pay_time">
                                </div>
                            </div>
                        </div>
                        <button type="submit" id="sign-up-btn" class="mt-2 btn btn-outline-primary">
                            <i class="spinner-border spinner-border-sm" id="s-b" style="display: none;"></i>
                            Update
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach


<div class="modal fade " id="account-modal">
    <div class="modal-dialog modal-dialog-centered ">
        <div class="modal-content">
            <div class="modal-body">
                <form action="{{ route('admin.naira-p2p.add-withdrawal-queue') }}" method="POST" class="mb-4">@csrf
                    <div class="form-row ">
                        <div class="col-md-12">
                            <div class="position-relative form-group">
                                <label>Pending Request</label>
                                <input type="text" required class="form-control" value="" name="pending_requests">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="position-relative form-group">
                                <label>Time to payment(in Minutes)</label>
                                <input type="text" required class="form-control" value="" name="pay_time">
                            </div>
                        </div>
                    </div>
                    <button type="submit" id="sign-up-btn" class="mt-2 btn btn-outline-primary">
                        <i class="spinner-border spinner-border-sm" id="s-b" style="display: none;"></i>
                        Save
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- @if(!empty($account))
    <div class="modal fade " id="account-modal">
        <div class="modal-dialog modal-dialog-centered ">
            <div class="modal-content">
                <div class="modal-body">
                    <form action="{{ route('agent.update-bank') }}" method="POST" class="mb-4">@csrf
                        <div class="form-row ">
                            <div class="col-md-12">
                                <input type="hidden" value="{{ $account->id }}" name="id">
                                <div class="position-relative form-group">
                                    <label>Bank Name</label>
                                    <select name="bank_id" class="form-control">
                                        <option value="{{ $account->bank_id }}">{{ $account->bank_name }}</option>
                                        @foreach ($banks as $b)
                                        <option value="{{$b->id}}">{{$b->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="position-relative form-group">
                                    <label>Account Number</label>
                                    <input type="text" required class="form-control" value="{{ $account->account_number }}" name="account_number">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="position-relative form-group">
                                    <label>Account Name</label>
                                    <input type="text" required class="form-control" value="{{ $account->account_name }}" name="account_name">
                                </div>
                            </div>
                        </div>
                        <button type="submit" id="sign-up-btn" class="mt-2 btn btn-outline-primary">
                            <i class="spinner-border spinner-border-sm" id="s-b" style="display: none;"></i>
                            Save
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif --}}
@endsection
