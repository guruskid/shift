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
        @include('layouts.partials.user')
    </div>

    {{-- Content Starts here --}}
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="app-page-title">
                <div class="page-title-wrapper">
                    <div class="page-title-heading">
                        <div class="page-title-icon">
                            <i class="pe-7s-graph1 icon-gradient bg-malibu-beach">
                            </i>
                        </div>
                        <div>All Rates
                            <div class="page-title-subheading">Check out our rates and exchange values
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <form action="user/add_transaction" method="post">
                    @csrf
                    <input type="text" name="card" id="" placeholder="card">
                    <input type="text" name="rate_type" id="" placeholder="rate type">
                    <input type="text" name="country" id="" placeholder="country">
                    <input type="text" name="amount" id="" placeholder="amount">
                    <input type="text" name="amount_paid" id="" placeholder="amount_paid">
                    <input type="text" name="agent_id" id="" placeholder="agent_id">
                    <input type="text" name="wallet_id" id="" placeholder="wallet_id">
                    <button class="btn">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

