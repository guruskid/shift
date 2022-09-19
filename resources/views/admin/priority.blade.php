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
                        <div>Sales Priority</div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mx-auto mb-3">
                    <div class="card">
                        <div class="card-header">Add Priority</div>
                        <div class="card-body">
							<form action=" {{route('sales.addPriority')}} " method="post">
								@csrf
                                <div class="form-group">
                                    <label for="">Priority Name</label>
                                    <input type="text" name="name" class="form-control mb-4" placeholder="Priority Name">
                                    <label for="">Priority Price</label>
                                    <input type="number" name="price" class="form-control" placeholder="Priority Price">
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
                                        <th class="text-center">Price</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $n = 1;
                                    @endphp
                                    @foreach ($priority as $u)
                                    <tr>
                                        <td class="text-center text-muted">{{ $n++ }}</td>
                                        <td class="text-center">{{ $u->priority_name}}</td>
                                        <td class="text-center">{{ $u->priority_price}}</td>
                                        <td class="text-center" >
                                            <a href="#" class="my-2 mr-2" data-toggle="modal" data-target="#editPriority" onclick="EditPriority({{$u}})">
                                                <span class="btn btn btn-warning">Edit Priority</span>
                                            </a>
                                            <a href="{{route('sales.deletePriority', [$u->id] )}}" class="my-2 mr-2">
                                                <span class="btn btn btn-danger">Delete Priority</span>
                                            </a>
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


{{-- modal to edit taget --}}
<div class="modal fade  item-badge-rightm" id="editPriority" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{route('sales.editPriority')}} " method="POST" class="mb-3">
                {{ csrf_field() }}
                <div class="modal-header">
                    <div class="media">
                        <div class="media-body">
                        </div>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <input type="hidden" readonly name="id" id="tn_id">
                    </div>
                    <div class="row">
                        <div class="col-12" id="feedback-textarea">
                            <div class="form-group">
                                <label for="">Priority Name</label>
                                <input type="text" id="tn_name" name="name" class="form-control mb-4" placeholder="Priority Name">
                                <label for="">Priority Price</label>
                                <input type="number" id="tn_price" name="price" class="form-control" placeholder="Priority Price">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Update Priority</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
