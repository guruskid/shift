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

            <div id="content" class="main-content">
                <div class="layout-px-spacing">
                    <div class="row layout-top-spacing"></div>
                    <div class="row layout-top-spacing">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                            <div class="widget widget-chart-one">
                                <div class="widget-heading">
                                    <div>
                                        <span class="h3 giftcard-text" style="color: #000070;">Notifications</span>
                                    </div>
                                    <div class="widget-n text-center" style="justify-content: center;">
                                        <span class="d-block" style="h6 walletbalance-text">Wallet Balance</span>
                                        <span class="d-block price realtime-wallet-balance"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="card card-body mb-4 card-body_buyairtime">
                                <div class="container px-4 d-flex flex-column flex-md-row justify-content-between align-items-center">
                                    <div class="d-flex align-items-center mb-3 mb-md-0">
                                        <div class="ml-2" style="color: #000070;font-size: 20px;">This Month</div>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center mb-2 mb-md-0">
                                        <span class="mr-1" style="color: #000070;font-size:13px;">Filter by month</span>
                                        <form id="filtermonthForm" method="GET" action="{{route('user.notifications')}}">
                                            @csrf
                                            <select name="month" id="filter_month" class="custom-select">
                                                <option value="01">January</option>
                                                <option value="02">February</option>
                                                <option value="03">March</option>
                                                <option value="04">April</option>
                                                <option value="05">May</option>
                                                <option value="06">June</option>
                                                <option value="07">July</option>
                                                <option value="08">August</option>
                                                <option value="09">September</option>
                                                <option value="10">October</option>
                                                <option value="11">November</option>
                                                <option value="12">December</option>
                                            </select>
                                        </form>
                                    </div>
                                </div>
                                {{-- border line --}}
                                <div class="mt-4 d-none d-lg-block" style="width: 100%;border: 1px solid #C9CED6;">
                                </div>

                                {{-- Bitcoin  menu  --}}
                                <div class="container">
                                    <div class="row">

                                        @foreach ($notifications as $notification)
                                        <div class="col-12 my-3 card-xs border-bottom">
                                            <div class="d-flex justify-content-between">
                                                <div class="d-flex">
                                                    <div class="mr-2">
                                                        <svg width="25" height="25" viewBox="0 0 25 25" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M22.026 8.15576C22.026 7.43576 21.6554 6.80576 21.0845 6.45576L12.02 1.15576L2.95543 6.45576C2.38452 6.80576 2.00391 7.43576 2.00391 8.15576V18.1558C2.00391 19.2558 2.90535 20.1558 4.00712 20.1558H20.0328C21.1346 20.1558 22.036 19.2558 22.036 18.1558L22.026 8.15576ZM12.02 13.1558L3.7467 7.99576L12.02 3.15576L20.2932 7.99576L12.02 13.1558Z"
                                                                fill="#000070" />
                                                        </svg>
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <span class="d-block font-weight-bold" style="color: #000070;">{{$notification->title}}</span>
                                                        <span class="d-block" style="color: #666666;font-size:13px;">{{$notification->body}}</span>
                                                    </div>
                                                </div>
                                                <div class="d-none d-md-flex flex-column align-items-end">
                                                    <span class="d-block">{{$notification->created_at}}</span>
                                                    <span class="d-block">
                                                        <svg width="26" height="25" viewBox="0 0 26 25" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M17.5797 8.74561L12.9823 13.3256L8.38492 8.74561L6.97266 10.1556L12.9823 16.1556L18.9919 10.1556L17.5797 8.74561Z"
                                                                fill="#000070" />
                                                        </svg>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                        <div>{{$notifications->links()}}</div>



                                        {{-- <div class="col-12 my-3 card-xs border-bottom">
                                            <div class="d-flex justify-content-between">
                                                <div class="d-flex">
                                                    <div class="mr-2">
                                                        <svg width="25" height="25" viewBox="0 0 25 25" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M22.026 8.15576C22.026 7.43576 21.6554 6.80576 21.0845 6.45576L12.02 1.15576L2.95543 6.45576C2.38452 6.80576 2.00391 7.43576 2.00391 8.15576V18.1558C2.00391 19.2558 2.90535 20.1558 4.00712 20.1558H20.0328C21.1346 20.1558 22.036 19.2558 22.036 18.1558L22.026 8.15576ZM12.02 13.1558L3.7467 7.99576L12.02 3.15576L20.2932 7.99576L12.02 13.1558Z"
                                                                fill="#000070" />
                                                        </svg>
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <span class="d-block font-weight-bold" style="color: #000070;">Successful Login From New IP</span>
                                                        <span class="d-block" style="color: #666666;font-size:13px;">Lorem ipsum dolor sit amet, consectetuer
                                                            adipiscing elit. Maecenas porttitor congue massa.</span>
                                                    </div>
                                                </div>
                                                <div class="d-none d-md-flex flex-column align-items-end">
                                                    <span class="d-block">2020-06-22 08:31:24</span>
                                                    <span class="d-block">
                                                        <svg width="26" height="25" viewBox="0 0 26 25" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M17.5797 8.74561L12.9823 13.3256L8.38492 8.74561L6.97266 10.1556L12.9823 16.1556L18.9919 10.1556L17.5797 8.74561Z"
                                                                fill="#000070" />
                                                        </svg>
                                                    </span>
                                                </div>
                                            </div>
                                        </div> --}}

                                        {{-- <div class="col-12 my-3 card-xs border-bottom">
                                            <div class="d-flex justify-content-between">
                                                <div class="d-flex">
                                                    <div class="mr-2">
                                                        <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M20 4.71143H4C2.9 4.71143 2.01 5.61143 2.01 6.71143L2 18.7114C2 19.8114 2.9 20.7114 4 20.7114H20C21.1 20.7114 22 19.8114 22 18.7114V6.71143C22 5.61143 21.1 4.71143 20 4.71143ZM20 8.71143L12 13.7114L4 8.71143V6.71143L12 11.7114L20 6.71143V8.71143Z" fill="#FFB800"/>
                                                            </svg>                                                            
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <span class="d-block font-weight-bold" style="color: #000070;">Successful Login From New IP</span>
                                                        <span class="d-block" style="color: #666666;font-size:13px;">Lorem ipsum dolor sit amet, consectetuer
                                                            adipiscing elit. Maecenas porttitor congue massa.</span>
                                                    </div>
                                                </div>
                                                <div class="d-none d-md-flex flex-column align-items-end">
                                                    <span class="d-block">2020-06-22 08:31:24</span>
                                                    <span class="d-block">
                                                        <svg width="26" height="25" viewBox="0 0 26 25" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M17.5797 8.74561L12.9823 13.3256L8.38492 8.74561L6.97266 10.1556L12.9823 16.1556L18.9919 10.1556L17.5797 8.74561Z"
                                                                fill="#000070" />
                                                        </svg>
                                                    </span>
                                                </div>
                                            </div>
                                        </div> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>

    {{-- @include('layouts.partials.live-feeds') --}}
</div>
</div>
</div>

@endsection