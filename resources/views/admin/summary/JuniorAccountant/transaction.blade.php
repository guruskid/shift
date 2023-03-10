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
                            <i class="pe-7s-graph1 icon-gradient bg-warm-flame">
                            </i>
                        </div>
                        <div> 
                            @if(isset($accountantName))
                                {{ $accountantName }}<br>
                            @endif 
                            @if(isset($segment))
                                {{ $segment }} Transactions 
                            @endif 
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-3">
                    {{-- bg-primary text-white --}}
                    <a href="{{ route('admin.junior-summary-details', [ $month , $day, 'all']) }}">
                        <div class="card mb-1 widget-content @if (isset($showCategory) AND $showCategory == "all")
                        bg-primary
                         @endif">
                            <div class="widget-content-wrapper">
                                <div class="widget-heading">
                                    <h6 class="text-center @if (isset($showCategory) AND $showCategory == "all")
                                    text-white
                                     @endif">Crypto and Giftcards</h5>
                                </div>
                                
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    {{-- bg-primary text-white --}}
                    <a href="{{ route('admin.junior-summary-details', [ $month , $day, 'utilities']) }}">
                        <div class="card mb-1 widget-content @if (isset($showCategory) AND $showCategory == "utilities")
                        bg-primary
                         @endif">
                            <div class="widget-content-wrapper">
                                <div class="widget-heading">
                                    <h6 class="text-center @if (isset($showCategory) AND $showCategory == "utilities")
                                    text-white
                                     @endif">Utilities</h6>
                                </div>
                                
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    {{-- bg-primary text-white --}}
                    <a href="{{ route('admin.junior-summary-details', [ $month , $day, 'paybridge']) }}">
                        <div class="card mb-1 widget-content @if ( isset($showCategory) AND strpos($showCategory,'paybridge') !== false)
                        bg-primary
                         @endif">
                            <div class="widget-content-wrapper">
                                <div class="widget-heading">
                                    <h6 class="text-center @if (isset($showCategory) AND strpos($showCategory,'paybridge') !== false)
                                    text-white
                                     @endif">Paybridge</h6>
                                </div>
                                
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                </div>


                @if ($showData != false)
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                            @if ($showCategory == "all")    
                                @include('admin.summary.JuniorAccountant.include.allTransactions')
                            @endif
                            
                            @if ($showCategory == "utilities")
                                @include('admin.summary.JuniorAccountant.include.utilities')
                            @endif

                            @if ($showCategory == "paybridge" OR $showCategory == "paybridgewithdrawal")
                                @include('admin.summary.JuniorAccountant.include.paybridge')
                            @endif
                        </div>
                    </div>
                </div>
                @endif

               
            </div>
        </div>
    </div>
</div>
@endsection
