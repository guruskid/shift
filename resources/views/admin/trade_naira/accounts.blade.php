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
                        <div>Pay Bridge Accounts</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 pb-3 card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="">
                                Accounts
                            </div>
                            @if(Auth::user()->role == 889)
                            <div>
                                <button data-toggle="modal" data-target="#account-modal" class="btn btn-primary">Add account</button>
                            </div>
                            @endif
                        </div>
                        <div class="table-responsive p-3">
                            <table
                                class="align-middle mb-0 table table-borderless table-striped table-hover transactions-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Account Name</th>
                                        <th>Bank Name</th>
                                        <th>Account Number</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($accounts as $key => $account)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>{{ $account->account_name }}</td>
                                        <td>{{ $account->bank_name }}</td>
                                        <td>{{ $account->account_number }}</td>
                                        <td>{{ $account->account_type }}</td>
                                        <td>{{ $account->status }}</td>
                                        <td>
                                            <div class="btn-group">
                                                @if(in_array(Auth::user()->role,[889,999,777]))
                                                    <button data-toggle="modal" data-target="#edit-modal-{{ $account->id }}" class="btn btn-primary">Edit</button>
                                                @endif

                                                @if(in_array(Auth::user()->role,[889,999]))
                                                    <button data-toggle="modal" data-target="#delete-modal-{{ $account->id }}" class="btn btn-danger ml-2">Delete</button>
                                                @endif
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

@foreach($accounts as $key => $account)
    {{-- Edit Modal --}}
    <div class="modal fade " id="edit-modal-{{ $account->id }}">
        <div class="modal-dialog modal-dialog-centered ">
            <div class="modal-content">
                <div class="modal-body">
                    <form action="{{ route('agent.update-account') }}" method="POST" class="mb-4">@csrf
                        <div class="form-row ">
                            <input type="hidden" value="{{$account->id}}" name="id">
                            @if(in_array(Auth::user()->role,[889,999]))
                            <div class="col-md-12">
                                <div class="position-relative form-group">
                                    <label>Bank Name</label>
                                    <input type="text" required class="form-control" value="{{$account->bank_name}}" name="bank_name">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="position-relative form-group">
                                    <label>Account Number</label>
                                    <input type="text" required class="form-control" value="{{$account->account_number}}" name="account_number">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="position-relative form-group">
                                    <label>Account Name</label>
                                    <input type="text" required class="form-control" value="{{$account->account_name}}" name="account_name">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="position-relative form-group">
                                    <label>Account Type</label>
                                    <select name="account_type" class="form-control">
                                        <option value="{{$account->account_type}}">{{ucfirst($account->account_type)}}</option>
                                        <option value="deposit">Deposit</option>
                                        <option value="withdrawal">Withdrawal</option>
                                    </select>
                                </div>
                            </div>
                            @endif
                            <div class="col-md-12">
                                <div class="position-relative form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="{{$account->status}}">{{ucfirst($account->status)}}</option>
                                        <option value="active">Active</option>
                                        <option value="in-active">In-Active</option>
                                    </select>
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

@foreach($accounts as $key => $account)
    {{-- Edit Modal --}}
    <div class="modal fade " id="delete-modal-{{ $account->id }}">
        <div class="modal-dialog modal-dialog-centered ">
            <div class="modal-content">
                <div class="modal-body">
                    <form action="{{ route('agent.delete-paybridge-account') }}" method="POST" class="mb-4">@csrf
                        <div class="form-row ">
                            <input type="hidden" value="{{$account->id}}" name="id">
                            <div>
                                Are you sure?
                            </div>
                        </div>
                        <button type="submit" id="sign-up-btn" class="mt-2 btn btn-outline-primary">
                            Delete
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
                <form action="{{ route('agent.add-account') }}" method="POST" class="mb-4">@csrf
                    <div class="form-row ">
                        <div class="col-md-12">
                            <div class="position-relative form-group">
                                <label>Bank Name</label>
                                <input type="text" required class="form-control" value="" name="bank_name">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="position-relative form-group">
                                <label>Account Number</label>
                                <input type="text" required class="form-control" value="" name="account_number">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="position-relative form-group">
                                <label>Account Name</label>
                                <input type="text" required class="form-control" value="" name="account_name">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="position-relative form-group">
                                <label>Account Type</label>
                                <select name="account_type" class="form-control">
                                    <option value="deposit">Deposit</option>
                                    <option value="withdrawal">Withdrawal</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="position-relative form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="active">Active</option>
                                    <option value="in-active">In-Active</option>
                                </select>
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
