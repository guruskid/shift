@php
$banks = App\Bank::all();
@endphp

@extends('layouts.user')
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
        @include('layouts.partials.user')
    </div>

    {{-- Content Starts here --}}
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="app-page-title">
                <div class="page-title-wrapper">
                    <div class="page-title-heading">
                        <div class="page-title-icon" style=" height: 150px; width: 150px ">
                            <img src=" {{asset('storage/avatar/'.Auth::user()->dp)}} " height="120px" alt="">
                        </div>
                        <div>
                            <form method="POST" action="{{route('user.dp')}}" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <input type="file" name="dp" class="form-control" accept="images/*">
                                </div>
                                <button type="submit" class="btn btn-primary">Upload</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="m-0 p-4 c-rounded-top  bg-custom text-white">
                        </div>
                        <div class="bg-custom-gradient p-4 ">
                        </div>
                        <div class="card-body pt-0">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="user-balance-box">
                                        <div class="profile-img">
                                            <center>
                                                <img width="150" class="rounded-circle mb-2"
                                                    src="{{asset('storage/avatar/'.Auth::user()->dp)}} " alt="">
                                            </center>
                                            <center>
                                                <button class="btn">{{Auth::user()->first_name.' '.Auth::user()->last_name}}</button>
                                            </center>
                                        </div>
                                        <div class="box-1">
                                            <span>WALLET BALANCE</span>
                                            <h4>NGN 300,000</h4>
                                        </div>
                                        <div class="box-2">
                                           <div class="balances">
                                            <button class="btn btn-success" style="margin-bottom: 30px">
                                                <div class="media align-items-center ">
                                                    <img src="{{asset('svg/bitcoin.svg')}}">
                                                    <div class="media-body ml-3" >
                                                        BTC
                                                        <span class="d-block">$50</span>
                                                    </div>
                                                </div>
                                            </button>

                                            <button class="btn btn-success ">
                                                <div class="media align-items-center ">
                                                    <img src="{{asset('svg/ethereum.svg')}}">
                                                    <div class="media-body ml-3" >
                                                        ETH
                                                        <span class="d-block">$50</span>
                                                    </div>
                                                </div>
                                            </button>
                                           </div>
                                        </div>
                                        <div class="box-3">

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8" style="margin-top: 5rem !important;" >
                                    <div class="card c-rounded profile-details">
                                        <div class="card-header bg-custom-accent c-rounded-top">User Details</div>
                                        <form class="" id="user-profile-form">
                                            {{ csrf_field() }}
                                            <div class="card-body p-0">
                                                <table class="table table-responsive-md table-striped">
                                                    <tbody>
                                                        <tr>
                                                            <td>First Name</td>
                                                            <td class="ml-5"><input type="text" name="first_name"
                                                                    value="{{Auth::user()->first_name}}">
                                                                <span class="fa fa-pen"></span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Last Name</td>
                                                            <td class="ml-5"><input type="text" name="last_name"
                                                                    value="{{Auth::user()->last_name}}">
                                                                <span class="fa fa-pen"></span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Email</td>
                                                            <td class="ml-5">{{Auth::user()->email}}</td>
                                                        </tr>
                                                        @foreach (Auth::user()->accounts as $a)
                                                        <tr>
                                                            <td>{{$a->bank_name}}</td>
                                                            <td class="ml-5">{{$a->account_name}} ->
                                                                {{$a->account_number}}
                                                                <a href="#" data-toggle="modal" data-target="#edit-bank"
                                                                    onclick="editBank({{$a->id}})">
                                                                    <span class="fa fa-pen"></span>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Phone</td>
                                                            <td class="ml-5"><input type="text" name="first_name"
                                                                    value="{{Auth::user()->phone}}">
                                                                <span class="fa fa-pen"></span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Status</td>
                                                            <td class="ml-5"><input type="text" name="first_name"
                                                                    value="{{ucwords(Auth::user()->status)}}"></td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="card-footer bg-custom-accent c-rounded-bottom">
                                                <button class="btn bg-white c-rounded">
                                                    <i class="spinner-border spinner-border-sm" id="s-p"
                                                        style="display: none;"></i>Save</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                            <ul class="nav nav-tabs nav-justified">
                                <li class="nav-item"><a data-toggle="tab" href="#tab-eg11-0"
                                        class="active nav-link">Profile</a></li>
                                <li class="nav-item"><a data-toggle="tab" href="#tab-eg11-1" class="nav-link">Bank <div
                                            class="d-none d-md-block ml-1"> details</div> </a></li>
                                <li class="nav-item"><a data-toggle="tab" href="#tab-eg11-2"
                                        class="nav-link">Password</a></li>
                                <li class="nav-item"><a data-toggle="tab" href="#tab-eg11-3"
                                        class="nav-link">Verification <span class="badge badge-info">
                                            {{Auth::user()->status}} </span> </a></li>
                            </ul>
                            <div class="tab-content">
                                {{-- Profile --}}
                                <div class="tab-pane active" id="tab-eg11-0" role="tabpanel">
                                    <div class="tab-pane active" id="tab-eg115-0" role="tabpanel">
                                        <div class="row">
                                            <div class="col-md-8 mx-auto">
                                                <strong>Please note that your pofile name should match your Bank account
                                                    details to fast track payments.</strong>
                                            </div>
                                        </div>

                                        <form class="" id="user-profile-form">
                                            {{ csrf_field() }}
                                            <div class="form-row">
                                                <div class="col-md-6">
                                                    <div class="position-relative form-group">
                                                        <label>First Name</label>
                                                        <input type="text" class="form-control " name="first_name"
                                                            value="{{Auth::user()->first_name}}">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="position-relative form-group">
                                                        <label>Last Name</label>
                                                        <input type="text" class="form-control " name="last_name"
                                                            value="{{Auth::user()->last_name}}">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="col-md-6">
                                                    <div class="position-relative form-group">
                                                        <label>Your Email</label>
                                                        <input type="text" readonly class="form-control"
                                                            value="{{Auth::user()->email}}">
                                                    </div>


                                                </div>
                                                <div class="col-md-6">
                                                    <div class="position-relative form-group">
                                                        <label>Phone</label>
                                                        <input type="text" class="form-control" name="phone"
                                                            value="{{Auth::user()->phone}}">
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="submit" class="mt-2 btn btn-outline-primary">
                                                <i class="spinner-border spinner-border-sm" id="s-p"
                                                    style="display: none;"></i>
                                                Save</button>
                                        </form>
                                    </div>
                                </div>

                                {{-- Bank details --}}
                                <div class="tab-pane" id="tab-eg11-1" role="tabpanel">
                                    <form id="user-bank-details" class="mb-4">
                                        {{ csrf_field() }}
                                        <div class="form-row ">
                                            <div class="col-md-4">
                                                <div class="position-relative form-group">
                                                    <label>Bank Name</label>
                                                    <select name="bank_name" id="" class="form-control">
                                                        @foreach ($banks as $b)
                                                        <option value="{{$b->name}}">{{$b->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="position-relative form-group">
                                                    <label>Account Name</label>
                                                    <input type="text" class="form-control" name="account_name">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="position-relative form-group">
                                                    <label>Account Number</label>
                                                    <input type="text" class="form-control" name="account_number">
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit" class="mt-2 btn btn-outline-primary">
                                            <i class="spinner-border spinner-border-sm" id="s-b"
                                                style="display: none;"></i>
                                            Save</button>
                                    </form>

                                    <div class="row">
                                        @foreach (Auth::user()->accounts as $a)
                                        <div class="col-md-4">
                                            <div class="card card-body">
                                                <i class="ml-auto">{{$a->bank_name}}</i>
                                                <h5 class="text-center">{{$a->account_name}}</h5>
                                                <h5 class="text-center">{{$a->account_number}}</h5>
                                            </div>
                                            <div class="card-footer justify-content-around ">
                                                <a href="#" data-toggle="modal" data-target="#edit-bank"
                                                    onclick="editBank({{$a->id}})"><span
                                                        class="badge badge-sm badge-info">Edit</span></a>

                                                <a href="#" onclick="deleteBank({{$a->id}})">
                                                    <span class="badge badge-sm badge-danger">Delete</span>
                                                </a>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Password --}}
                                <div class="tab-pane" id="tab-eg11-2" role="tabpanel">
                                    <form action="{{route('user.change_password')}}" class="ml-3" method="POST">
                                        {{ csrf_field() }}
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label>Old Password</label>
                                                    <input type="password" class="form-control" name="old_password"
                                                        required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>New Password</label>
                                                    <input type="password" class="form-control form-control"
                                                        name="new_password" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Confirm Password</label>
                                                    <input name="new_password_confirmation" type="password"
                                                        class="form-control" required>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit" class="mt-2 btn btn-outline-primary">Save</button>
                                    </form>
                                </div>

                                {{-- Verification --}}
                                <div class="tab-pane" id="tab-eg11-3" role="tabpanel">
                                    <form method="POST" action=" {{route('user.idcard')}}"
                                        enctype="multipart/form-data">
                                        {{ csrf_field() }}
                                        <div class="row ">
                                            <div class="col-md-6">
                                                <img src=" {{asset('storage/idcards/'.Auth::user()->id_card)}} " alt=""
                                                    class="img-fluid">
                                            </div>
                                            <div class="col-md-6 ">
                                                <strong>Account Status:
                                                </strong><span>{{Auth::user()->status}}</span><br>
                                                <label>Upload valid ID card</label>
                                                <input type="file" name="id_card" required class="form-control"
                                                    accept="images/*">

                                                <button type="submit" class="mt-2 btn btn-outline-primary">Save</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade  item-badge-rightm" id="edit-bank" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{route('user.update_bank_details')}} " method="POST" class="mb-3">
                {{ csrf_field() }}
                <div class="modal-body">
                    <div class="form-group">
                        <input type="hidden" readonly name="id" id="e-account-id">
                    </div>
                    <div class="form-group">
                        <label for="">Bank Name</label>
                        <select name="bank_name" class="form-control">
                            <option value="" id="e-bank-name"></option>
                            @foreach ($banks as $b)
                            <option value="{{$b->name}}">{{$b->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Account Name</label>
                        <input type="text" class="form-control" name="account_name" id="e-account-name">
                    </div>
                    <div class="form-group">
                        <label>Account Number</label>
                        <input type="number" class="form-control" name="account_number" id="e-account-number">
                    </div>

                    <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
