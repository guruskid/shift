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
                        <div>Verfication Tracking</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <table class="table ">
                        <thead>
                            <tr>
                                <th class="text-center">Verfications Count</th>
                                <th class="text-center">Verfication Average Response Time</th>
                                <th class="text-center">Total Waiting Verfications</th>
                                <th class="text-center">Waiting Verifications Today</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center">{{ number_format($total) }}</td>
                                <td class="text-center">{{ $verification_average }}</td>
                                <td class="text-center">{{ number_format($waiting_verifications_count) }}</td>
                                <td class="text-center">{{ number_format($waiting_verifications_today_count) }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="main-card mb-3 pb-3 card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="">
                                Verfications
                            </div>
                            <form action="" class="form-inline p-2"
                                method="GET">
                                <div class="form-group mr-2">
                                    <label for="">Start date </label>
                                    <input type="date" name="start" class="ml-2 form-control" required>
                                </div>
                                <div class="form-group mr-2">
                                    <label for="">End date </label>
                                    <input type="date" name="end" class="ml-2 form-control" required>
                                </div>
                                <button class="btn btn-outline-primary"><i class="fa fa-filter"></i></button>
                            </form>
                    </div>
                    
                    <div class="table-responsive p-3">
                        <table
                            class="align-middle mb-0 table table-borderless table-striped table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Name</th>
                                    <th class="text-center">Verfication Type</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Verfied By</th>
                                    <th class="text-center">Verfied Time</th>
                                    <th class="text-center">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $n= 0;
                                @endphp
                                @foreach ($verifications as $v)
                                <tr>
                                    <td class="text-center">{{ ++$n }}</td>
                                    <td class="text-center">{{ $v->name }}</td>
                                    <td class="text-center">{{ $v->type }}</td>
                                    <td class="text-center">{{ $v->status }}</td>
                                    <td class="text-center">{{ $v->verifiedBy }}</td>
                                    <td class="text-center">{{ $v->verifyTime }}</td>
                                    <td class="text-center">{{ $v->created_at->format('d M Y h:ia') }}</td>
                                </tr>
                                @endforeach
                                
                            </tbody>
                        </table>
                        {{-- {{ $users->links() }} --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>


@endsection
