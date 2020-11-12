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
                                        <span class="h3 giftcard-text" style="color: #000070;">Bitcoin Wallet</span>
                                    </div>
                                    <div class="widget-n" style="justify-content: center; text-align: center;">
                                        {{-- <span class="d-block" style="h6 walletbalance-text">Naira Wallet Balance</span>
                                        <span class="d-block price">₦{{ number_format(Auth::user()->nairaWallet->amount) }}</span> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="card card-body">
                                <div class="container px-4 d-flex justify-content-between align-items-center">
                                    <a href="{{ route('user.portfolio') }}">
                                        <div class="d-flex align-items-center">
                                            <div
                                                style="background: rgba(0, 0, 112, 0.25);width:24px;height:24px;border-radius:12px;">
                                                <span style="position: relative;left:33%;top:0;">
                                                    <svg width="8" height="12" viewBox="0 0 8 12" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M7.41 1.41L6 0L0 6L6 12L7.41 10.59L2.83 6L7.41 1.41Z"
                                                            fill="#000070" />
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="ml-2" style="color: #000070;font-size: 20px;">Back</div>
                                        </div>
                                    </a>
                                    <div class="d-flex">
                                        <div class="mr-3 mr-lg-4" style="color: #0D1F3C;font-size: 30px;">$ 8,452.98
                                        </div>
                                    </div>
                                </div>
                                {{-- border line --}}
                                <div class="mt-4" style="width: 100%;border: 1px solid #C9CED6;"></div>

                                {{-- Bitcoin  menu  --}}
                                <div class="walletpage__menu-container mx-auto mt-4">
                                    <div class="walletpage_menu d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="d-block" style="color: #565656;font-size: 16px;">Bitcoin wallet
                                                Balance</span>
                                            <span class="d-block">
                                                <span style="color: #000070;font-size: 30px;">{{ Auth::user()->bitcoinWallet->balance }}</span>
                                                <span style="color: #000070;font-size: 30px;">BTC</span>
                                            </span>
                                            <span class="d-block"
                                                style="color: #565656;font-size: 16px;opacity: 0.5;">₦20,000</span>
                                        </div>
                                        <div class="d-flex">
                                            <a href="#" class="btn walletpage_menu-active">
                                                <span class="d-block">
                                                    <svg width="44" height="44" viewBox="0 0 44 44" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M18.333 25.6667L38.4997 5.5" stroke="#2C3E50"
                                                            stroke-width="1.83333" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path
                                                            d="M38.5004 5.5L26.5837 38.5C26.5033 38.6755 26.3741 38.8243 26.2116 38.9285C26.0491 39.0328 25.8601 39.0883 25.667 39.0883C25.474 39.0883 25.2849 39.0328 25.1224 38.9285C24.96 38.8243 24.8308 38.6755 24.7504 38.5L18.3337 25.6667L5.50037 19.25C5.32485 19.1696 5.1761 19.0404 5.07182 18.8779C4.96754 18.7154 4.91211 18.5264 4.91211 18.3333C4.91211 18.1403 4.96754 17.9512 5.07182 17.7887C5.1761 17.6262 5.32485 17.4971 5.50037 17.4167L38.5004 5.5Z"
                                                            stroke="#2C3E50" stroke-width="1.83333"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                                <span class="d-block"
                                                    style="color: #000000;font-size: 14px;">Send</span>
                                            </a>
                                            <a href="#" class="btn">
                                                <span class="d-block">
                                                    <svg width="44" height="44" viewBox="0 0 44 44" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M7.33301 12.833V10.9997C7.33301 10.0272 7.71932 9.09458 8.40695 8.40695C9.09458 7.71932 10.0272 7.33301 10.9997 7.33301H14.6663"
                                                            stroke="#2C3E50" stroke-width="1.83333"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path
                                                            d="M7.33301 31.167V33.0003C7.33301 33.9728 7.71932 34.9054 8.40695 35.593C9.09458 36.2807 10.0272 36.667 10.9997 36.667H14.6663"
                                                            stroke="#2C3E50" stroke-width="1.83333"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path
                                                            d="M29.333 7.33301H32.9997C33.9721 7.33301 34.9048 7.71932 35.5924 8.40695C36.28 9.09458 36.6663 10.0272 36.6663 10.9997V12.833"
                                                            stroke="#2C3E50" stroke-width="1.83333"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path
                                                            d="M29.333 36.667H32.9997C33.9721 36.667 34.9048 36.2807 35.5924 35.593C36.28 34.9054 36.6663 33.9728 36.6663 33.0003V31.167"
                                                            stroke="#2C3E50" stroke-width="1.83333"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M9.16699 22H34.8337" stroke="#2C3E50"
                                                            stroke-width="1.83333" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                                <span class="d-block"
                                                    style="color: #000000;font-size: 14px;">Receive</span>
                                            </a>
                                            <a href="#" class="btn">
                                                <span class="d-block">
                                                    <svg width="40" height="40" viewBox="0 0 40 40" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M25.7041 4.94272C25.3021 2.12506 22.8495 0 19.9995 0C18.1666 0 16.4259 0.884466 15.3436 2.36679C15.1225 2.66909 15.1887 3.09315 15.491 3.31422C15.7923 3.53449 16.2173 3.46914 16.438 3.16644C17.2664 2.0329 18.5976 1.356 19.9993 1.356C22.179 1.356 24.0542 2.98033 24.3616 5.134C24.4101 5.47225 24.7003 5.71636 25.0322 5.71636C25.0638 5.71636 25.0963 5.71397 25.1287 5.7096C25.4993 5.65657 25.757 5.31315 25.7041 4.94272Z"
                                                            fill="#2C3E50" />
                                                        <path
                                                            d="M35.496 12.5325L33.4622 9.14259C33.3398 8.93821 33.1191 8.81348 32.8808 8.81348H30.9317C30.5575 8.81348 30.2538 9.11717 30.2538 9.49138C30.2538 9.86558 30.5575 10.1693 30.9317 10.1693H32.497L33.7174 12.2032H6.28145L7.50178 10.1693H8.2198C8.59401 10.1693 8.8977 9.86558 8.8977 9.49138C8.8977 9.11717 8.59401 8.81348 8.2198 8.81348H7.11805C6.8797 8.81348 6.65903 8.93821 6.53668 9.14259L4.50278 12.5325C4.37705 12.7422 4.37407 13.003 4.49424 13.2156C4.61441 13.4281 4.83965 13.5596 5.08415 13.5596H34.9147C35.1592 13.5596 35.3842 13.4281 35.5046 13.2156C35.625 13.003 35.6218 12.7418 35.496 12.5325Z"
                                                            fill="#2C3E50" />
                                                        <path
                                                            d="M32.1171 6.28101C32.0222 6.12787 31.8706 6.01922 31.6955 5.97831L22.4534 3.8167C22.2784 3.77558 22.0941 3.80617 21.9411 3.90071C21.7886 3.99566 21.6797 4.1472 21.6388 4.32239L20.9418 7.30173C20.8564 7.6662 21.0829 8.03087 21.4475 8.11588C21.812 8.20129 22.1771 7.97486 22.2621 7.61019L22.8047 5.29127L30.7264 7.14422L29.4816 12.4663C29.3962 12.8308 29.6226 13.1955 29.9873 13.2805C30.0395 13.2926 30.0914 13.2983 30.1426 13.2983C30.4505 13.2983 30.7292 13.0874 30.8017 12.775L32.2009 6.79286C32.2423 6.61787 32.2121 6.43355 32.1171 6.28101Z"
                                                            fill="#2C3E50" />
                                                        <path
                                                            d="M20.3581 7.88385L19.7404 2.65909C19.7193 2.48053 19.6281 2.31746 19.4867 2.20623C19.3453 2.095 19.1667 2.04415 18.9874 2.06521L7.64803 3.40551C7.27641 3.44961 7.01065 3.78647 7.05455 4.15849L8.0844 12.8727C8.12532 13.2177 8.41789 13.4713 8.75694 13.4713C8.78336 13.4713 8.81057 13.4695 8.83758 13.4665C9.20901 13.4224 9.47476 13.0856 9.43107 12.7136L8.48066 4.67213L18.4736 3.49112L19.0114 8.04255C19.0555 8.41437 19.3908 8.67854 19.7644 8.63643C20.136 8.59274 20.4018 8.25568 20.3581 7.88385Z"
                                                            fill="#2C3E50" />
                                                        <path
                                                            d="M25.7199 12.6237L24.3742 7.0277C24.3321 6.85311 24.2223 6.70196 24.0692 6.60801C23.916 6.51486 23.7323 6.48566 23.5565 6.52737L12.6349 9.15396C12.2708 9.24135 12.0467 9.60761 12.1341 9.97169L12.8067 12.7687C12.8941 13.1332 13.2603 13.3574 13.6242 13.2694C13.9883 13.182 14.2123 12.8158 14.1249 12.4517L13.6107 10.3137L23.2141 8.00453L24.4013 12.9411C24.4757 13.2519 24.7538 13.4605 25.0599 13.4605C25.1125 13.4605 25.1658 13.4543 25.2192 13.4414C25.5833 13.3538 25.8073 12.9878 25.7199 12.6237Z"
                                                            fill="#2C3E50" />
                                                        <path
                                                            d="M35.5928 12.8463C35.5741 12.486 35.2766 12.2031 34.9155 12.2031H5.08497C4.72388 12.2031 4.42634 12.4858 4.40767 12.8463L3.05167 39.2869C3.04214 39.4724 3.10928 39.6539 3.23719 39.7886C3.3653 39.9231 3.54287 39.9995 3.72878 39.9995H36.2711C36.4568 39.9995 36.6346 39.9233 36.7633 39.7886C36.891 39.6539 36.9581 39.4726 36.9488 39.2869L35.5928 12.8463ZM4.44223 38.6435L5.72871 13.5589H34.271L35.5575 38.6435H4.44223Z"
                                                            fill="#2C3E50" />
                                                        <path
                                                            d="M25.0847 14.915C23.9631 14.915 23.0508 15.8273 23.0508 16.9489C23.0508 18.0705 23.9631 18.9828 25.0847 18.9828C26.2063 18.9828 27.1186 18.0705 27.1186 16.9489C27.1186 15.8273 26.2063 14.915 25.0847 14.915ZM25.0847 17.627C24.7107 17.627 24.4068 17.3229 24.4068 16.9491C24.4068 16.5753 24.7109 16.2712 25.0847 16.2712C25.4585 16.2712 25.7626 16.5753 25.7626 16.9491C25.7626 17.3229 25.4585 17.627 25.0847 17.627Z"
                                                            fill="#2C3E50" />
                                                        <path
                                                            d="M14.9148 14.915C13.7931 14.915 12.8809 15.8273 12.8809 16.9489C12.8809 18.0705 13.7931 18.9828 14.9148 18.9828C16.0364 18.9828 16.9486 18.0705 16.9486 16.9489C16.9486 15.8273 16.0364 14.915 14.9148 14.915ZM14.9148 17.627C14.5409 17.627 14.2369 17.3229 14.2369 16.9491C14.2369 16.5753 14.5409 16.2712 14.9148 16.2712C15.2886 16.2712 15.5927 16.5753 15.5927 16.9491C15.5927 17.3229 15.2886 17.627 14.9148 17.627Z"
                                                            fill="#2C3E50" />
                                                        <path
                                                            d="M25.0847 16.9492H25.0324C24.6582 16.9492 24.3545 17.2529 24.3545 17.6271C24.3545 17.7197 24.3728 17.8081 24.4068 17.8885V21.3559C24.4068 23.7856 22.4299 25.7627 19.9999 25.7627C17.57 25.7627 15.5931 23.7858 15.5931 21.3559V17.6273C15.5931 17.2531 15.2894 16.9494 14.9152 16.9494C14.541 16.9494 14.2373 17.2531 14.2373 17.6273V21.3561C14.2373 24.5336 16.8224 27.1187 19.9999 27.1187C23.1775 27.1187 25.7626 24.5336 25.7626 21.3561V17.6273C25.7626 17.2531 25.4589 16.9492 25.0847 16.9492Z"
                                                            fill="#2C3E50" />
                                                    </svg>
                                                </span>
                                                <span class="d-block" style="color: #000000;font-size: 14px;">Buy</span>
                                            </a>
                                            <a href="#" class="btn">
                                                <span class="d-block">
                                                    <svg width="44" height="44" viewBox="0 0 44 44" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M5.5 38.5H38.5" stroke="#2C3E50" stroke-width="1.83333"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path
                                                            d="M27.5 14.6667C27.5 16.1254 28.0795 17.5243 29.1109 18.5558C30.1424 19.5872 31.5413 20.1667 33 20.1667C34.4587 20.1667 35.8576 19.5872 36.8891 18.5558C37.9205 17.5243 38.5 16.1254 38.5 14.6667V12.8333H5.5L9.16667 5.5H34.8333L38.5 12.8333M5.5 12.8333V14.6667C5.5 16.1254 6.07946 17.5243 7.11091 18.5558C8.14236 19.5872 9.54131 20.1667 11 20.1667C12.4587 20.1667 13.8576 19.5872 14.8891 18.5558C15.9205 17.5243 16.5 16.1254 16.5 14.6667V12.8333H5.5ZM16.5 14.6667C16.5 16.1254 17.0795 17.5243 18.1109 18.5558C19.1424 19.5872 20.5413 20.1667 22 20.1667C23.4587 20.1667 24.8576 19.5872 25.8891 18.5558C26.9205 17.5243 27.5 16.1254 27.5 14.6667V12.8333L16.5 14.6667Z"
                                                            stroke="#2C3E50" stroke-width="1.83333"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M9.16699 38.4999V19.8916" stroke="#2C3E50"
                                                            stroke-width="1.83333" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path d="M34.833 38.4999V19.8916" stroke="#2C3E50"
                                                            stroke-width="1.83333" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path
                                                            d="M16.5 38.5V31.1667C16.5 30.1942 16.8863 29.2616 17.5739 28.5739C18.2616 27.8863 19.1942 27.5 20.1667 27.5H23.8333C24.8058 27.5 25.7384 27.8863 26.4261 28.5739C27.1137 29.2616 27.5 30.1942 27.5 31.1667V38.5"
                                                            stroke="#2C3E50" stroke-width="1.83333"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                                <span class="d-block"
                                                    style="color: #000000;font-size: 14px;">Sell</span>
                                            </a>
                                        </div>
                                    </div>
                                    <div>
                                        <div style="color: #000070;font-size: 18px;position: relative;top:45.8px;left:15.5%;">Amount</div>
                                        <form action="">
                                            <div class="d-flex mt-3 mt-lg-5 mx-auto" style="border: 1px solid rgba(0, 0, 112, 0.12);height:42px;width:520px;">
                                                <div class="input-group mb-3" style="border:0px;">
                                                    <input type="text" class="form-control" aria-label="Recipient's username"
                                                        aria-describedby="basic-addon2" value="0" style="border:0px;">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text" style="border-radius:0px;background:white;border:0px;color: #000070;font-weight: 500;" id="basic-addon2">USD</span>
                                                    </div>
                                                </div>
                                                <div style="height: 40px;width:50px;">
                                                    <span style="position:relative;top:5px;">
                                                        <svg width="31" height="31" viewBox="0 0 31 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M20.667 3.875L25.8337 9.04167L20.667 14.2083" stroke="#000070" stroke-width="0.916667" stroke-linecap="round" stroke-linejoin="round"/>
                                                            <path d="M12.917 9.04199H25.8337" stroke="#000070" stroke-width="0.916667" stroke-linecap="round" stroke-linejoin="round"/>
                                                            <path d="M10.3337 16.792L5.16699 21.9587L10.3337 27.1253" stroke="#000070" stroke-width="0.916667" stroke-linecap="round" stroke-linejoin="round"/>
                                                            <path d="M5.16699 21.958H16.792" stroke="#000070" stroke-width="0.916667" stroke-linecap="round" stroke-linejoin="round"/>
                                                        </svg>    
                                                    </span>                                               
                                                </div>
                                                <div class="input-group mb-3" style="border:0px;">
                                                    <input type="text" class="form-control" value="0" aria-label="Recipient's username"
                                                        aria-describedby="basic-addon2" style="border:0px;">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text" style="background:white;border:0px;color: #000070;font-weight: 500;" id="basic-addon2">BTC</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mx-auto mt-3" style="width:520px;">
                                                <div class="form-group col-6" style="position: relative;left:-15px;">
                                                    <label for="" style="color: #000070;font-size: 18px;">Network fee</label>
                                                    <select class="custom-select" style="height: 42px;border-radius:0px;">
                                                        <option selected>Network fee</option>
                                                        <option value="1">Regular</option>
                                                    </select>
                                                </div>
                                                <div class="d-flex flex-column justify-content-between">
                                                    <span class="d-block align-self-start" style="color: #696969;font-size: 20px;letter-spacing: 0.01em;">0 BTC ($0.00)</span>
                                                    <span class="d-block align-self-end" style="color: #000070;font-size: 16px;font-weight: 600;letter-spacing: 0.01em;">Customize Fee</span>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn text-white mt-5 walletpage_btn">Continue</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mt-3">
                            <div class="card card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span style="color: #000070;font-size: 24px;font-weight: 500;">Recent Transactions</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <span class="mr-1" style="color: #000070;font-size: 14px;">Start Date</span>
                                            <input type="text" class="col-7 form-control" name="" id="" value="14-05-2020">
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span class="mr-1" style="color: #000070;font-size: 14px;">End Date</span>
                                            <input type="text" class="col-7 form-control" name="" id="" value="14-05-2020">
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive mt-3">
                                    <table class="table table-striped">
                                        <thead>
                                          <tr style="background-color: rgba(0, 0, 112, 0.05) !important;color:#000070;font-size:16px;">
                                            <th scope="col">ID</th>
                                            <th scope="col">TRANSACTION TYPE</th>
                                            <th scope="col">AMOUNT</th>
                                            <th scope="col">DATE</th>
                                            <th scope="col">TIME</th>
                                            <th scope="col">STATUS</th>
                                            <th scope="col"></th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                          <tr>
                                            <th scope="row">1</th>
                                            <td style="color: #FF001F;font-weight:600;">Sell</td>
                                            <td>
                                                <span class="d-block" style="font-size: 14px;color: #000000;font-weight: 500;">$200</span>
                                                <span class="d-block" style="font-size: 12px;color: #676B87;">N70,000</span>
                                            </td>
                                            <td style="color: #000000;font-size: 14px;">Aug. 10, 2019</td>
                                            <td style="font-weight: 500;">2 mins ago</td>
                                            <td>
                                                <span class="status_inprogress">in progress</span>
                                            </td>
                                            <td>
                                                <a href="#" style="color: #000070;font-size: 15px;font-weight: 600;">view</a>
                                            </td>
                                          </tr>
                                          <tr>
                                            <th scope="row">1</th>
                                            <td style="color: #023501;font-weight:600;">Sell</td>
                                            <td>
                                                <span class="d-block" style="font-size: 14px;color: #000000;font-weight: 500;">$200</span>
                                                <span class="d-block" style="font-size: 12px;color: #676B87;">N70,000</span>
                                            </td>
                                            <td style="color: #000000;font-size: 14px;">Aug. 10, 2019</td>
                                            <td style="font-weight: 500;">2 mins ago</td>
                                            <td>
                                                <span class="status_success">Successful</span>
                                            </td>
                                            <td>
                                                <a href="#" style="color: #000070;font-size: 15px;font-weight: 600;">view</a>
                                            </td>
                                          </tr>
                                          <tr>
                                            <th scope="row">1</th>
                                            <td style="color: #FF001F;font-weight:600;">Sell</td>
                                            <td>
                                                <span class="d-block" style="font-size: 14px;color: #000000;font-weight: 500;">$200</span>
                                                <span class="d-block" style="font-size: 12px;color: #676B87;">N70,000</span>
                                            </td>
                                            <td style="color: #000000;font-size: 14px;">Aug. 10, 2019</td>
                                            <td style="font-weight: 500;">2 mins ago</td>
                                            <td>
                                                <span class="status_declined">Declined</span>
                                            </td>
                                            <td>
                                                <a href="#" style="color: #000070;font-size: 15px;font-weight: 600;">view</a>
                                            </td>
                                          </tr>
                                          <tr>
                                            <th scope="row">1</th>
                                            <td style="color: #FF001F;font-weight:600;">Sell</td>
                                            <td>
                                                <span class="d-block" style="font-size: 14px;color: #000000;font-weight: 500;">$200</span>
                                                <span class="d-block" style="font-size: 12px;color: #676B87;">N70,000</span>
                                            </td>
                                            <td style="color: #000000;font-size: 14px;">Aug. 10, 2019</td>
                                            <td style="font-weight: 500;">2 mins ago</td>
                                            <td>
                                                <span class="status_waiting">Waiting</span>
                                            </td>
                                            <td>
                                                <a href="#" style="color: #000070;font-size: 15px;font-weight: 600;">view</a>
                                            </td>
                                          </tr>
                                        </tbody>
                                      </table>
                                </div>
                                <a href="#" class="btn mx-auto text-white mt-3" style="background: #000070;border-radius: 25px;width: 140px;" class="btn">View more <span><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10 17.5C14.1421 17.5 17.5 14.1421 17.5 10C17.5 5.85786 14.1421 2.5 10 2.5C5.85786 2.5 2.5 5.85786 2.5 10C2.5 14.1421 5.85786 17.5 10 17.5Z" stroke="white" stroke-width="0.916667" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M6.66699 10L10.0003 13.3333" stroke="white" stroke-width="0.916667" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M10 6.66699V13.3337" stroke="white" stroke-width="0.916667" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M13.3333 10L10 13.3333" stroke="white" stroke-width="0.916667" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    </span></a>
                            </div>
                        </div>
                    </div>



                </div>
            </div>

        </div>
    </div>
</div>

@endsection