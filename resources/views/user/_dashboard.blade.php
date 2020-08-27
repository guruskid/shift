@extends('layouts.user')
@section('content')
@php
    $not = $notifications->last();
@endphp
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
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="app-page-title">
                <div class="page-title-wrapper">
                    <div class="page-title-heading">
                        <div class="page-title-icon">
                            <i class="pe-7s-home icon-gradient bg-night-sky">
                            </i>
                        </div>
                        <div>Dashboard Home
                            <div class="page-title-subheading">Hi {{Auth::user()->first_name}}, welcome to Dantown Multi
                                services.
                            </div>
                        </div>
                    </div>
                </div>
                @if($notifications->count() != 0)
                <div class="page-title-wrapper mt-2 bg-white p-3">
                    <div class="page-title-heading">
                        <div class="page-title-icon">
                            <i class="pe-7s-bell icon-gradient bg-info">
                            </i>
                        </div>
                        <div class="text-black">{{$not->title}}
                            <div class="page-title-subheading">{{$not->body}}
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                {{-- @if ( Auth::user()->status == 'not verified' )
                <div class="page-title-wrapper mt-2 bg-strong-bliss p-3">
                    <div class="page-title-heading">
                        <div class="page-title-icon">
                            <i class="pe-7s-delete-user icon-gradient bg-info">
                            </i>
                        </div>
                        <div class="text-white">Verify Account
                            <div class="page-title-subheading">Hi {{Auth::user()->first_name}}, Please complete your
                profile by uploading a valid ID card and adding your bank details. Please note you must
                be verified before making any trade on this platform. Feel free to contact us at
                customercare@dantownms.com for any inquiries or assistance.
            </div>
            <a href="{{route('user.profile')}} ">
                <button type="button" class="btn-shadow btn btn-info">
                    <span class="btn-icon-wrapper pr-2 ">
                        <i class="fa fa-cogs fa-w-20"></i>
                    </span>
                    Verify Account
                </button>
            </a>
        </div>
    </div>
</div>
@endif --}}
</div>
<div class="row">
    <div class="col-md-6 col-xl-3">
        <a href=" {{route('user.calcCrypto')}} " class="text-white">
            <div class="card mb-3 widget-content bg-night-sky">
                <div class="widget-content-wrapper py-0 text-white">
                    <div class="widget-content- mx-auto">
                        <img src=" {{asset('svg/cryptos.png')}}" class="img-fluid" alt="">
                        <h5 class="text-center" >Trade Assets</h5>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-xl-3">
        <a href=" {{route('user.calcCard')}} " class="text-white">
            <div class="card mb-3 widget-content bg-night-sky">
                <div class="widget-content-wrapper py-0 text-white">
                    <div class="widget-content- mx-auto">
                            <img src=" {{asset('svg/cards.png')}}" class="img-fluid" alt="">
                            <h5 class="text-center">Trade Gift Cards</h5>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-xl-3">
        <a href="{{route('user.calculator')}}" class="text-white">
            <div class="card mb-3 widget-content bg-night-sky">
                <div class="widget-content-wrapper py-0">
                    <div class="widget-content- mx-auto ">
                        <div class="widget-heading">
                            <img src=" {{asset('svg/calculator.png')}}" class="img-fluid"  alt="">
                            <h5 class="text-center" >Rate Calculator</h5>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-xl-3">
        <a href="#" class="text-white">
            <div class="card mb-3 widget-content bg-night-sky">
                <div class="widget-content-wrapper py-0">
                    <div class="widget-content- mx-auto ">
                        <div class="widget-heading">
                            <img src=" {{asset('svg/gift.png')}} " class="img-fluid" alt="">
                            <h5 class="text-center" >Send Gift</h5>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-header">Transactions </div>
            <div class="table-responsive">
                <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">ID</th>
                            <th class="text-center">Asset type</th>
                            <th class="text-center">Tran. type</th>
                            <th class="text-center">Asset value</th>
                            <th class="text-center">Cash value</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $t)
                        <tr>
                            <td class="text-center text-muted">{{$t->uid}}</td>
                            <td class="text-center">{{ucwords($t->card)}}</td>
                            <td class="text-center">{{$t->type}}</td>
                            <td class="text-center">{{$t->amount}}</td>
                            <td class="text-center">{{number_format($t->amount_paid)}}</td>
                            <td class="text-center">
                                @switch($t->status)
                                @case('success')
                                <div class="badge badge-success">{{$t->status}}</div>
                                @break
                                @case("failed")
                                <div class="badge badge-danger">{{$t->status}}</div>
                                @break
                                @case('declined')
                                <div class="badge badge-warning">{{$t->status}}</div>
                                @break
                                @case('waiting')
                                <div class="badge badge-info">{{$t->status}}</div>
                                @break
                                @default
                                <div class="badge badge-success">{{$t->status}}</div>

                                @endswitch
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 col-lg-7">
        <div class="main-card mb-3 card">
            <div class="card-header">Notifications</div>
            <div class="scroll-area-lg">
                <div class="scrollbar-container p-3">
                    @foreach (Auth::user()->notifications as $n)
                    <div class="media p-2 mb-3 shadow">
                        <i class="fa fa-2x mr-2 fa-bell icon-gradient bg-sunny-morning "></i>
                        <div class="media-body ">
                            <h5>{{$n->title}}</h5>
                            <p>{{$n->body}}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12 col-lg-5">
        <div class="main-card mb-3 card">
            <div class="card-header">
                <h5 class="card-title">Transactions Summary</h5>
            </div>
            <div class="card-body">
                <div>{!! $usersChart->container() !!}</div>
                <div class="m-1">
                    <strong class="text-success">Successfull Transactions </strong><span
                        class="float-right badge badge-success">{{$s}}</span>
                </div>
                <div class="m-1">
                    <strong class="text-info">Waiting Transactions </strong><span
                        class="float-right badge badge-info">{{$w}}</span>
                </div>
                <div class="m-1">
                    <strong class="text-warning">Declined Transactions </strong><span
                        class="float-right badge badge-warning">{{$d}}</span>
                </div>
                <div class="m-1">
                    <strong class="text-danger">Failed Transactions </strong><span
                        class="float-right badge badge-danger">{{$f}}</span>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
</div>


@if($usersChart)
{!! $usersChart->script() !!}
@endif
@endsection
