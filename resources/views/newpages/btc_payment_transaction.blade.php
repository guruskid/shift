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

                    <div class="row">
                        <div class="col-12">
                            <div class="widget widget-chart-one">
                                <div class="container">
                                    <div class="d-flex flex-column align-items-center justify-content-center mt-3">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="trxn_summary_text_header mb-3">Transaction Summary</div>
                                            </div>
                                            <div class="col-5 text-right py-1 my-1">
                                                <span style="">Status:</span>
                                            </div>
                                            <div class="col-7 py-1 my-1">
                                                <span class="trxn_status px-3 py-1">waiting</span>
                                            </div>
                                            <div class="col-5 text-right py-1 my-1">
                                                <span>Narration:</span>
                                            </div>
                                            <div class="col- py-1 my-1">
                                                <span style="font-size: 13px;color: #000070;">Lorem ipsum dolor sit
                                                    amet, consectetur adipiscing elit. <br> Nam lacus enim, fringilla
                                                    sit amet pharetra quis.</span>
                                            </div>

                                            <div class="my-4 mx-auto"
                                                style="height: 0px;border: 1px solid #EFEFF8;width:60vw;"></div>

                                            <div class="col-5 text-right my-1 py-1">
                                                <span style="">Transaction ID:</span>
                                            </div>
                                            <div class="col-7 my-1 py-1">
                                                <span class="py-1">0000000</span>
                                            </div>
                                            <div class="col-5 text-right my-1 py-1">
                                                <span style="">Asset</span>
                                            </div>
                                            <div class="col-7 my-1 py-1">
                                                <span class="py-1">Bitcoin</span>
                                            </div>
                                            <div class="col-5 text-right my-1 py-1">
                                                <span style="">Trade type:</span>
                                            </div>
                                            <div class="col-7 my-1 py-1">
                                                <span class="py-1">Sell</span>
                                            </div>
                                            <div class="col-5 text-right py-1 my-1">
                                                <span style="">Value:</span>
                                            </div>
                                            <div class="col-7 py-1 my-1">
                                                <span class="py-1">$200</span> <br>
                                                <span class="py-1">₦20,000</span>
                                            </div>
                                            <div class="col-5 text-right my-1 py-1">
                                                <span style="">Date:</span>
                                            </div>
                                            <div class="col-7 my-1 py-1">
                                                <span class="py-1">Dec, 20 2020</span>
                                            </div>
                                            <div class="col-5 text-right my-1 py-1">
                                                <span style="">Time:</span>
                                            </div>
                                            <div class="col-7 my-1 py-1">
                                                <span class="py-1">2 days ago</span>
                                            </div>
                                            <button type="button" class="btn text-white my-5 trxn_ok_btn">Ok</button>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>

        </div>
    </div>
</div>


@endsection