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
            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="m-0 p-3 c-rounded-top  bg-custom text-white">
                            <strong>Notifications</strong>
                        </div>
                        <div class="card-body">

                            @foreach (Auth::user()->notifications as $n)
                            <div class="row">
                                <div class="col-10">
                                    <div class="media align-items-start ">
                                    <i  class="fa fa-2x mr-3 {{$n->is_seen ? 'fa-envelope-open  text-custom' : 'fa-envelope  text-warning' }} " id="envelope-{{$n->id}}" ></i>
                                        <div class="media-body">
                                            <h6 class="text-custom" >{{ucwords($n->title)}}</h6>
                                            <p class="text-muted" >{{$n->body}}</p>
                                        </div>
                                    </div>
                                    @if (!$n->is_seen)
                                    <p class="text-right mb-0" id="action-{{$n->id}}" ><a href="#" class="mr-3" onclick="readNot({{$n->id}})" > <i class="fa fa-check"></i> Mark as read</a>  </p>
                                    @endif
                                </div>
                                <div class="col-2">
                                    <p class="text-custom">{{$n->created_at->format('d M Y h:i a')}}</p>
                                </div>
                            </div>
                            <hr>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            @include('layouts.partials.live-feeds')
        </div>
    </div>
</div>


{{-- Naira wallet password --}}
<div class="modal fade " id="new-naira-wallet">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content  c-rounded">

            <!-- Modal Header -->
            <div class="modal-header bg-custom-gradient c-rounded-top p-4 ">
                <h4 class="modal-title">New Naira Wallet </h4>
                <button type="button" class="close bg-light rounded-circle " data-dismiss="modal">&times;</button>
            </div>

            <form action="{{route('user.create-naira')}}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="">Wallet Password (4 digits)</label>
                                <input type="password" class="form-control" required name="password" minlength="4"
                                    maxlength="4">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="">Confirm password</label>
                                <input type="password" class="form-control" required name="password_confirmation">
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-block c-rounded bg-custom-gradient">
                        Create wallet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
