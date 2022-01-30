
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
                        @if (isset($month_name))
                         <div>{{ $month_name }} Summary Page
                            @else
                         <div>Transaction Summary Page
                        @endif
                        </div>
                    </div>
                </div>
            </div>
            @if (isset($month))
                <div class="row mb-4">
                        @foreach ($month as $mon)
                        <div class="col-md-2">
                            <a href="{{ route('admin.junior-summary', [$mon['number'] ]) }}">
                                <div class="card mb-2 widget-content ">
                                    <div class="widget-content-wrapper">
                                        <div class="widget-heading">
                                            <h6>{{ ucwords($mon['month']) }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endforeach
                </div>
            @endif

            @if (isset($days))
                <div class="row mb-4">
                    @for ($i=1; $i<=$days; $i++)
                        <div class="col-md-2">
                            <a href="{{ route('admin.junior-summary', [ $month_num , $i]) }}">
                                <div class="card mb-2 widget-content ">
                                    <div class="widget-content-wrapper">
                                        <div class="widget-heading">
                                            <h6 class="text-center">{{ $i }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endfor
                </div>
            @endif
            
        </div>
    </div>
</div>
@endsection
