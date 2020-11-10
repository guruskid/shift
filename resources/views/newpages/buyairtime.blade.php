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
                                        <span class="h3 giftcard-text">Recharge</span>
                                    </div>
                                    <div class="widget-n text-center" style="justify-content: center;">
                                        <span class="d-block" style="h6 walletbalance-text">Wallet Balance</span>
                                        <span class="d-block price">₦56,758</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="widget widget-chart-one">
                                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-center pb-2"
                                    style="">
                                    <div class="list-cards-title primary-color" style="line-height: 40px;">
                                        <span class="ml-1" style="color: rgba(0, 0, 112, 0.75);">Recharge and pay your utility bills</span>
                                    </div>
                                </div>
                                <div class="d-flex flex-column flex-lg-row flex-wrap justify-content-start py-4 px-2">
                                    <div class="airtimepage_card d-flex flex-column justify-content-center align-items-center">
                                        <div>
                                            <svg width="100" height="100" viewBox="0 0 140 140" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M93.333 23.333H46.6663C43.4447 23.333 40.833 25.9447 40.833 29.1663V110.833C40.833 114.055 43.4447 116.666 46.6663 116.666H93.333C96.5547 116.666 99.1663 114.055 99.1663 110.833V29.1663C99.1663 25.9447 96.5547 23.333 93.333 23.333Z" stroke="#000070" stroke-width="2.75" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M64.167 29.167H75.8337" stroke="#000070" stroke-width="2.75" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M70 104.167V104.225" stroke="#000070" stroke-width="2.75" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M72.54 73.3727C74.5077 75.3406 73.0948 78.7055 70.331 78.7055C67.5689 78.7055 66.1525 75.3422 68.1221 73.3727C69.3427 72.1516 71.319 72.1514 72.54 73.3727ZM62.673 67.9238C62.0224 68.5744 62.0224 69.6293 62.673 70.2799C63.3239 70.9307 64.3785 70.9307 65.0294 70.2799C67.9592 67.3498 72.7023 67.3496 75.6324 70.2799C75.9579 70.6054 76.384 70.7679 76.8105 70.7679C78.2815 70.7679 79.0415 68.9764 77.9887 67.9236C73.7567 63.6914 66.9062 63.6908 62.673 67.9238ZM84.1742 61.7385C76.5229 54.0875 64.1401 54.0868 56.488 61.7385C55.8373 62.3891 55.8373 63.444 56.488 64.0946C57.1388 64.7453 58.1935 64.7453 58.8443 64.0946C65.1781 57.7607 75.4839 57.7611 81.8176 64.0946C82.4687 64.7455 83.5231 64.7453 84.174 64.0946C84.8248 63.444 84.8248 62.3891 84.1742 61.7385Z" fill="#000070"/>
                                                </svg>                                                
                                        </div>
                                        <span class="d-block bills_type_text">Airtime & Data Subscription</span>
                                    </div>
                                    <div class="airtimepage_card d-flex flex-column justify-content-center align-items-center">
                                        <div>
                                            <svg width="100" height="100" viewBox="0 0 140 140" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M110.833 52.5H52.4997C46.0564 52.5 40.833 57.7233 40.833 64.1667V99.1667C40.833 105.61 46.0564 110.833 52.4997 110.833H110.833C117.276 110.833 122.5 105.61 122.5 99.1667V64.1667C122.5 57.7233 117.276 52.5 110.833 52.5Z" stroke="#000070" stroke-width="2.75" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M81.6667 93.3333C88.11 93.3333 93.3333 88.11 93.3333 81.6667C93.3333 75.2233 88.11 70 81.6667 70C75.2233 70 70 75.2233 70 81.6667C70 88.11 75.2233 93.3333 81.6667 93.3333Z" stroke="#000070" stroke-width="2.75" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M99.1667 52.5003V40.8337C99.1667 37.7395 97.9375 34.772 95.7496 32.5841C93.5617 30.3962 90.5942 29.167 87.5 29.167H29.1667C26.0725 29.167 23.105 30.3962 20.9171 32.5841C18.7292 34.772 17.5 37.7395 17.5 40.8337V75.8337C17.5 78.9279 18.7292 81.8953 20.9171 84.0832C23.105 86.2712 26.0725 87.5003 29.1667 87.5003H40.8333" stroke="#000070" stroke-width="2.75" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>                                                                                           
                                        </div>
                                        <span class="d-block bills_type_text">Airtime to cash</span>
                                    </div>
                                    <div class="airtimepage_card d-flex flex-column justify-content-center align-items-center">
                                        <div>
                                            <svg width="100" height="100" viewBox="0 0 140 140" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M52.5 87.5L87.5 52.5" stroke="#000070" stroke-width="2.75" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M55.4167 58.3333C57.0275 58.3333 58.3333 57.0275 58.3333 55.4167C58.3333 53.8058 57.0275 52.5 55.4167 52.5C53.8058 52.5 52.5 53.8058 52.5 55.4167C52.5 57.0275 53.8058 58.3333 55.4167 58.3333Z" fill="black" stroke="#000070" stroke-width="2.75" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M84.5837 87.5003C86.1945 87.5003 87.5003 86.1945 87.5003 84.5837C87.5003 82.9728 86.1945 81.667 84.5837 81.667C82.9728 81.667 81.667 82.9728 81.667 84.5837C81.667 86.1945 82.9728 87.5003 84.5837 87.5003Z" fill="black" stroke="#000070" stroke-width="2.75" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M29.1664 41.9998C29.1664 38.5961 30.5185 35.3319 32.9252 32.9252C35.3319 30.5185 38.5961 29.1664 41.9998 29.1664H47.8331C51.2217 29.1645 54.472 27.8224 56.8748 25.4331L60.9581 21.3498C62.1507 20.1504 63.5686 19.1987 65.1303 18.5492C66.692 17.8998 68.3667 17.5654 70.0581 17.5654C71.7495 17.5654 73.4241 17.8998 74.9858 18.5492C76.5476 19.1987 77.9655 20.1504 79.1581 21.3498L83.2414 25.4331C85.6442 27.8224 88.8945 29.1645 92.2831 29.1664H98.1164C101.52 29.1664 104.784 30.5185 107.191 32.9252C109.598 35.3319 110.95 38.5961 110.95 41.9998V47.8331C110.952 51.2217 112.294 54.472 114.683 56.8748L118.766 60.9581C119.966 62.1507 120.918 63.5686 121.567 65.1303C122.216 66.692 122.551 68.3667 122.551 70.0581C122.551 71.7495 122.216 73.4241 121.567 74.9858C120.918 76.5476 119.966 77.9655 118.766 79.1581L114.683 83.2414C112.294 85.6442 110.952 88.8945 110.95 92.2831V98.1164C110.95 101.52 109.598 104.784 107.191 107.191C104.784 109.598 101.52 110.95 98.1164 110.95H92.2831C88.8945 110.952 85.6442 112.294 83.2414 114.683L79.1581 118.766C77.9655 119.966 76.5476 120.918 74.9858 121.567C73.4241 122.216 71.7495 122.551 70.0581 122.551C68.3667 122.551 66.692 122.216 65.1303 121.567C63.5686 120.918 62.1507 119.966 60.9581 118.766L56.8748 114.683C54.472 112.294 51.2217 110.952 47.8331 110.95H41.9998C38.5961 110.95 35.3319 109.598 32.9252 107.191C30.5185 104.784 29.1664 101.52 29.1664 98.1164V92.2831C29.1645 88.8945 27.8224 85.6442 25.4331 83.2414L21.3498 79.1581C20.1504 77.9655 19.1987 76.5476 18.5492 74.9858C17.8998 73.4241 17.5654 71.7495 17.5654 70.0581C17.5654 68.3667 17.8998 66.692 18.5492 65.1303C19.1987 63.5686 20.1504 62.1507 21.3498 60.9581L25.4331 56.8748C27.8224 54.472 29.1645 51.2217 29.1664 47.8331V41.9998" stroke="#000070" stroke-width="2.75" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>                                                                                                                                       
                                        </div>
                                        <span class="d-block bills_type_text">Buy Discounted Airtime</span>
                                    </div>
                                    <div class="airtimepage_card d-flex flex-column justify-content-center align-items-center">
                                        <div>
                                            <svg width="100" height="100" viewBox="0 0 140 140" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M110.833 40.833H29.1667C22.7233 40.833 17.5 46.0564 17.5 52.4997V105C17.5 111.443 22.7233 116.666 29.1667 116.666H110.833C117.277 116.666 122.5 111.443 122.5 105V52.4997C122.5 46.0564 117.277 40.833 110.833 40.833Z" stroke="#000070" stroke-width="2.75" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M93.3337 17.5L70.0003 40.8333L46.667 17.5" stroke="#000070" stroke-width="2.75" stroke-linecap="round" stroke-linejoin="round"/>
                                                <line x1="31.5" y1="71.5" x2="68.5" y2="71.5" stroke="#000070" stroke-width="3" stroke-linecap="round"/>
                                                <line x1="31.5" y1="87.5" x2="82.5" y2="87.5" stroke="#000070" stroke-width="3" stroke-linecap="round"/>
                                                </svg>                                                                                                                                                                                       
                                        </div>
                                        <span class="d-block bills_type_text">Cable Subscription and TV</span>
                                    </div>
                                    <div class="airtimepage_card d-flex flex-column justify-content-center align-items-center">
                                        <div>
                                            <svg width="100" height="100" viewBox="0 0 140 140" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M75.8337 17.5V58.3333H110.834L64.167 122.5V81.6667H29.167L75.8337 17.5Z" stroke="#000070" stroke-width="2.75" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>                                                                                                                                                                                                                                 
                                        </div>
                                        <span class="d-block bills_type_text">Electricity Bills</span>
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