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
                            <i class="pe-7s-wallet icon-gradient bg-warm-flame">
                            </i>
                        </div>
                        <div>Tron Settings <br>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">

                <div class="col-md-3">
                    <div class="card mb-3 widget-content">
                        <a href="#">
                            <div class="widget-content-wrapper ">
                                <div class="widget-content-left">
                                    <div class="widget-heading">
                                        <form action="{{ route('admin.settings.update') }} " method="post">@csrf
                                            <h5>Sell Charges (%)</h5>
                                            <input type="hidden" name="name" value="tron_sell_charge">
                                            <input type="number" step="any" name="value"
                                                value="{{ AppSetting::get('tron_sell_charge') }}" class="form-control mb-2">
                                            <button class="btn btn-primary">Save</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card mb-3 widget-content">
                        <a href="#">
                            <div class="widget-content-wrapper ">
                                <div class="widget-content-left">
                                    <div class="widget-heading">
                                        <form action="{{ route('admin.settings.update') }} " method="post">@csrf
                                            <h5>Send Charges (TRON)</h5>
                                            <input type="hidden" name="name" value="tron_send_charge">
                                            <input type="number" step="any" name="value"
                                                value="{{ AppSetting::get('tron_send_charge') }}" class="form-control mb-2">
                                            <button class="btn btn-primary">Save</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card mb-3 widget-content">
                        <a href="#">
                            <div class="widget-content-wrapper ">
                                <div class="widget-content-left">
                                    <div class="widget-heading">
                                        <form action="{{ route('admin.settings.update') }} " method="post">@csrf
                                            <h5>Fees Percentage (%)</h5>
                                            <input type="hidden" name="name" value="trading_tron_per">
                                            <input type="number" step="any" name="value"
                                                value="{{ AppSetting::get('trading_tron_per') }}" class="form-control mb-2">
                                            <button class="btn btn-primary">Save</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
