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
                            <i class="pe-7s-volume icon-gradient bg-happy-itmeo">
                            </i>
                        </div>
                        <div>Notifications</div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-md-around ">
                <div class="col-md-4">
                    <div class="card shadow-lg ">
                        <div class="card-header">Add New Notifications</div>
                        <div class="card-body">
                            <form action=" {{route('admin.add_notification')}} " method="post">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label for="">Title</label>
                                    <input type="text" name="title" id="" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="">Body</label>
                                    <textarea name="body" class="form-control" rows="3"></textarea>
                                </div>
                                <button class="btn btn-outline-success">Send</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="main-card mb-3 card">
                        <div class="card-header">
                            Notifications
                        </div>
                        <div class="card-body">
                            @foreach ($notifications as $n)
                            <div class="media shadow  p-2 mb-3">
                                <i class="fa fa-2x mr-2 fa-clock icon-gradient bg-sunny-morning "></i>
                                <div class="media-body ">
                                    <h5 class="card-title">{{$n->title}}</h5>
                                    <p>{{$n->body}}</p>
                                    <i class="float-right"> {{$n->created_at}} </i>
                                    <a href="#" onclick="getNotification({{$n->id}})" data-toggle="modal"
                                        data-target="#edit-notification"><span class="badge badge-info">Edit</span></a>
                                    <a href="#" onclick="deleteNot({{$n->id}})"><span class="badge badge-danger">Delete</span></a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="card-footer">
                            {{$notifications->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade  item-badge-rightm" id="edit-notification" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <form action=" {{route('admin.edit_notification')}} " method="post">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="">Title</label>
                        <input type="hidden" readonly name="id" id="n-id">
                        <input type="text" name="title" id="n-title" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">Body</label>
                        <textarea name="body" id="n-body" class="form-control" rows="3"></textarea>
                    </div>
                    <button class="btn btn-outline-success">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
