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
                                                <span style="">Hash code:</span>
                                            </div>
                                            <div class="col-7 my-1 py-1">
                                                <span class="py-1">0000000</span>
                                            </div>
                                            <div class="col-5 text-right my-1 py-1">
                                                <span style="">Hash code:</span>
                                            </div>
                                            <div class="col-7 my-1 py-1">
                                                <span class="py-1">0000000</span>
                                            </div>
                                            <div class="col-5 text-right my-1 py-1">
                                                <span style="">Amount of BTC</span>
                                            </div>
                                            <div class="col-7 my-1 py-1">
                                                <span class="py-1">0000000</span>
                                            </div>
                                            <div class="col-5 text-right my-1 py-1">
                                                <span style="">Value:</span>
                                            </div>
                                            <div class="col-7 my-1 py-1">
                                                <span class="py-1">0000000</span>
                                            </div>
                                            <div class="col-5 text-right py-1 my-1">
                                                <span style="">Fee:</span>
                                            </div>
                                            <div class="col-7 py-1 my-1">
                                                <span class="py-1">0000000</span>
                                            </div>
                                            <div class="col-5 text-right my-1 py-1">
                                                <span style="">Wallet ID(From):</span>
                                            </div>
                                            <div class="col-7 my-1 py-1">
                                                <span class="py-1">0000000</span>
                                            </div>
                                            <div class="col-5 text-right my-1 py-1">
                                                <span style="">Wallet ID(To):</span>
                                            </div>
                                            <div class="col-7 my-1 py-1">
                                                <span class="py-1">0000000</span>
                                            </div>
                                            <div class="col-5 text-right my-1 py-1">
                                                <span style="">Confirmation:</span>
                                            </div>
                                            <div class="col-7 my-1 py-1">
                                                <span class="py-1">0000000</span>
                                            </div>
                                            <button type="button" class="btn text-white my-5 trxn_explorer_btn">
                                                <span>
                                                    <svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M9.00098 16.4736C12.7289 16.4736 15.751 13.4516 15.751 9.72363C15.751 5.99571 12.7289 2.97363 9.00098 2.97363C5.27305 2.97363 2.25098 5.99571 2.25098 9.72363C2.25098 13.4516 5.27305 16.4736 9.00098 16.4736Z" stroke="#000070" stroke-width="0.916667" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M2.70117 7.47363H15.3012" stroke="#000070" stroke-width="0.916667" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M2.70117 11.9736H15.3012" stroke="#000070" stroke-width="0.916667" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M8.62573 2.97363C7.36223 4.99834 6.69238 7.33703 6.69238 9.72363C6.69238 12.1102 7.36223 14.4489 8.62573 16.4736" stroke="#000070" stroke-width="0.916667" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M9.37598 2.97363C10.6395 4.99834 11.3093 7.33703 11.3093 9.72363C11.3093 12.1102 10.6395 14.4489 9.37598 16.4736" stroke="#000070" stroke-width="0.916667" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                </span>
                                                <span style="color: #000070;">Visit Block explorer</span>    
                                            </button>
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