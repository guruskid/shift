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
                            <i class="pe-7s-cash icon-gradient bg-vicious-stance">
                            </i>
                        </div>
                        <div>Cards / Cryptocurrencies</div>
                    </div>
                </div>
            </div>

            <h5>Add New Card /Crypto </h5>
            <div class="row">
                @if (count($errors) > 0)
                <div class="alert alert-danger alert-dismissible">
                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <form action=" {{route('admin.add_card')}} " method="POST" class="form-inline mx-3 mb-3">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <input type="text" name="name" placeholder="Card Name" class="form-control">
                    </div>
                    <div class="form-group">
                        <select name="is_crypto" class="form-control">
                            <option value="0">Is Crypto?</option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="text" placeholder="Wallet id" class="form-control" name="wallet_id">
                    </div>
                    <button class="btn-success btn mx-2">Add</button>
                </form>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-header">All Cards and Cryptos </div>
                        <div class="table-responsive">
                            <table class="align-middle mb-4 table table-borderless table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th>Asset Type</th>
                                        <th>Is Crypto</th>
                                        <th>Wallet id</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($cards as $c)
                                    <tr>
                                        <td class="text-center text-muted">{{$c->id}}</td>
                                        <td>{{ucwords($c->name)}}</td>
                                        <td>
                                            @if ($c->is_crypto == 1)
                                            Yes
                                            @else
                                            No
                                            @endif
                                        </td>
                                        <td>{{$c->wallet_id}}</td>
                                        <td>
                                            <a href="#" data-toggle="modal" data-target="#edit-card"
                                                onclick="editCard({{$c->id}})"><span
                                                    class="btn btn-info">Edit</span></a>
                                            <a href="#" onclick="deleteCard({{$c->id}})"><span
                                                    class="btn btn-danger">Delete</span></a>
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

<div class="modal fade  item-badge-rightm" id="edit-card" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{route('admin.edit_card')}} " method="POST" class="mb-3">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="card-title" id="e-card-name-2"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <input type="hidden" readonly name="id" id="e-card-id">
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="">Asset name</label>
                                <input type="text" placeholder="Name" id="e-card-name" class="form-control" name="name">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">Crypto</label>
                            <select name="is_crypto" class="form-control">
                                <option value="" id="e-card-crypto"></option>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="">Wallet id</label>
                                <input type="text" placeholder="Wallet id" id="e-card-wallet" class="form-control"
                                    name="wallet_id">
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-link" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
