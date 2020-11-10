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
                                        <span class="h3 giftcard-text" style="color: #000070;">Naira Wallet (₦) </span>
                                    </div>
                                    <div class="widget-n" style="justify-content: center; text-align: center;">
                                        <span class="d-block" style="h6 walletbalance-text">Wallet Balance</span>
                                        <span class="d-block price">₦20,000</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="card card-body mb-4">
                                <div class="container px-4 d-flex justify-content-between align-items-center">
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
                                </div>
                                {{-- border line --}}
                                <div class="mt-4" style="width: 100%;border: 1px solid #C9CED6;"></div>

                                {{-- Bitcoin  menu  --}}
                                <div class="row">
                                    <div class="col-12 col-lg-6">
                                        <div class="mx-auto mt-4">
                                            <div class="walletpage_menu d-flex justify-content-between align-items-center">
                                                <div>
                                                    <span class="d-block" style="color: #565656;font-size: 16px;">Bitcoin wallet
                                                        Balance</span>
                                                    <span class="d-block">
                                                        <span style="color: #000070;font-size: 30px;">0.8934</span>
                                                        <span style="color: #000070;font-size: 30px;">BTC</span>
                                                    </span>
                                                    <span class="d-block"
                                                        style="color: #565656;font-size: 16px;opacity: 0.5;">₦20,000</span>
                                                </div>
        
                                                <div class="d-flex">
                                                    <a href="#" class="btn walletpage_menu-active">
                                                        <span class="d-block">
                                                            <svg width="40" height="40" viewBox="0 0 44 44" fill="none"
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
                                                    <a href="#" class="btn ">
                                                        <span class="d-block">
                                                            <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M28.0144 28.6527C27.7539 28.3922 27.3316 28.3922 27.0711 28.6527L20.6671 35.0573V16.6667C20.6671 16.2985 20.3686 16 20.0004 16C19.6322 16 19.3337 16.2985 19.3337 16.6667V35.0573L12.9297 28.6527C12.6692 28.3922 12.2469 28.3922 11.9864 28.6527C11.7259 28.9132 11.7259 29.3355 11.9864 29.596L19.5284 37.1373C19.7884 37.398 20.2106 37.3986 20.4712 37.1385C20.4716 37.1381 20.4721 37.1377 20.4724 37.1373L28.0144 29.596C28.2749 29.3355 28.2749 28.9132 28.0144 28.6527Z" fill="#2C3E50"/>
                                                                <path d="M38 2.66699H2C0.895417 2.66699 0 3.56241 0 4.66699V28.667C0 29.7716 0.895417 30.667 2 30.667H8.66667C9.03483 30.667 9.33333 30.3685 9.33333 30.0003C9.33333 29.6322 9.03483 29.3337 8.66667 29.3337H2C1.63183 29.3337 1.33333 29.0352 1.33333 28.667V10.667H38.6667V28.667C38.6667 29.0352 38.3682 29.3337 38 29.3337H31.3333C30.9652 29.3337 30.6667 29.6322 30.6667 30.0003C30.6667 30.3685 30.9652 30.667 31.3333 30.667H38C39.1046 30.667 40 29.7716 40 28.667V4.66699C40 3.56241 39.1046 2.66699 38 2.66699ZM38.6667 8.00033H1.33333V4.66699C1.33333 4.29883 1.63183 4.00033 2 4.00033H38C38.3682 4.00033 38.6667 4.29883 38.6667 4.66699V8.00033Z" fill="#2C3E50"/>
                                                                <path d="M19.3333 12H4.66667C4.2985 12 4 12.2985 4 12.6667C4 13.0348 4.2985 13.3333 4.66667 13.3333H19.3333C19.7015 13.3333 20 13.0348 20 12.6667C20 12.2985 19.7015 12 19.3333 12Z" fill="#2C3E50"/>
                                                                </svg>                                                        
                                                        </span>
                                                        <span class="d-block"
                                                            style="color: #000000;font-size: 14px;">Withdraw</span>
                                                    </a>
                                                    <a href="#" class="btn">
                                                        <span class="d-block">
                                                            <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M11.9876 24.6803C12.2481 24.9408 12.6704 24.9408 12.9309 24.6803L19.3349 18.2757V36.6663C19.3349 37.0345 19.6334 37.333 20.0016 37.333C20.3697 37.333 20.6682 37.0345 20.6682 36.6663L20.6682 18.2757L27.0722 24.6803C27.3327 24.9408 27.7551 24.9408 28.0156 24.6803C28.2761 24.4198 28.2761 23.9975 28.0156 23.737L20.4736 16.1957C20.2136 15.935 19.7914 15.9344 19.5307 16.1945C19.5303 16.1949 19.5299 16.1953 19.5296 16.1957L11.9876 23.737C11.7271 23.9975 11.7271 24.4198 11.9876 24.6803Z" fill="#2C3E50"/>
                                                                <path d="M38 2.66699H2C0.895417 2.66699 0 3.56241 0 4.66699L0 28.667C0 29.7716 0.895417 30.667 2 30.667H8.66667C9.03483 30.667 9.33333 30.3685 9.33333 30.0003C9.33333 29.6322 9.03483 29.3337 8.66667 29.3337H2C1.63183 29.3337 1.33333 29.0352 1.33333 28.667V10.667H38.6667V28.667C38.6667 29.0352 38.3682 29.3337 38 29.3337H31.3333C30.9652 29.3337 30.6667 29.6322 30.6667 30.0003C30.6667 30.3685 30.9652 30.667 31.3333 30.667H38C39.1046 30.667 40 29.7716 40 28.667V4.66699C40 3.56241 39.1046 2.66699 38 2.66699ZM38.6667 8.00033H1.33333V4.66699C1.33333 4.29883 1.63183 4.00033 2 4.00033H38C38.3682 4.00033 38.6667 4.29883 38.6667 4.66699V8.00033Z" fill="#2C3E50"/>
                                                                <path d="M19.3333 12H4.66667C4.2985 12 4 12.2985 4 12.6667C4 13.0348 4.2985 13.3333 4.66667 13.3333H19.3333C19.7015 13.3333 20 13.0348 20 12.6667C20 12.2985 19.7015 12 19.3333 12Z" fill="#2C3E50"/>
                                                                </svg>                                                        
                                                        </span>
                                                        <span class="d-block" style="color: #000000;font-size: 14px;">Deposit</span>
                                                    </a>
                                                </div>
                                                
        
                                            </div>
                                            
                                            <div class="d-flex flex-column justify-content-center align-items-center mt-4 mb-2" style="max-width: 700px;">
                                                <span class="d-block text-center primary_text_color mb-4" style="font-size: 20px;">Dantown to other Banks</span>
                                                <form action="" method="post">
                                                    @csrf
                                                    <div class="form-row mb-3">
                                                        <div class="col">
                                                            <label for="bankname" style="color: #000070;font-size: 16px;">Bank name</label>
                                                          <input type="text" class="form-control" id="bankname">
                                                        </div>
                                                        <div class="col">
                                                            <label for="account_number" style="color: #000070;font-size: 16px;">Account number</label>
                                                        <input type="text" class="form-control" name="account_number" id="account_number" />
                                                        </div>
                                                      </div>
                                                    <div class="form-group">
                                                        <label for="account_number" style="color: #000070;font-size: 16px;">Account name</label>
                                                        <input type="text" class="form-control" readonly name="account_number" id="account_number" />
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="account_number" style="color: #000070;font-size: 16px;">Amount</label>
                                                        <input type="text" class="form-control" name="account_number" placeholder="0000000000" id="account_number" />
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="narration" style="color: #000070;font-size: 16px;">Narration</label>
                                                        <textarea name="description" class="form-control" id="narration" cols="10" rows="3" style=resize:none;></textarea>
                                                    </div>
                                                    <button type="submit" class="btn text-white w-100 py-2 mt-2" style="background-color: #000070">Confirm</button>
                                                </form>
                                            </div>
        
                                        </div>
                                    </div>
                                    <div class="col-12 col-lg-6 mt-4" style="border: 1px solid #EFF4F6;border-radius:10px;">
                                        <span class="d-block txn_history_title mt-3">Transaction history</span>
                                        <div class="mt-3" style="width: 100%;border: 1px solid #EFEFF8;"></div>
                                        <div class="table-responsive">
                                            <table class="table table-borderless">
                                                {{-- <thead>
                                                  <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">First</th>
                                                    <th scope="col">Last</th>
                                                    <th scope="col">Handle</th>
                                                  </tr>
                                                </thead> --}}
                                                <tbody class="text-center wallet_trxs">
                                                  <tr class="my-2">
                                                    <th scope="row">
                                                        <span class="d-block" style="font-size: 16px;">01, Aug 2020</span>
                                                        <span class="d-block" style="font-size: 14px;font-weight: normal;">09:00AM</span>
                                                    </th>
                                                    <td>-N20,000</td>
                                                    <td>
                                                        <span style="color: #87676F;font-size: 16px;">Withdrawal</span>
                                                    </td>
                                                    <td>
                                                        <span class="px-3 py-2" style="font-size: 12px;color: #219653;background: rgba(115, 219, 158, 0.3);border-radius: 15px;">Completed</span>
                                                    </td>
                                                  </tr>
                                                  <tr class="my-2">
                                                    <th scope="row">
                                                        <span class="d-block" style="font-size: 16px;">01, Aug 2020</span>
                                                        <span class="d-block" style="font-size: 14px;font-weight: normal;">09:00AM</span>
                                                    </th>
                                                    <td>-N20,000</td>
                                                    <td>
                                                        <span style="color: #87676F;font-size: 16px;">Withdrawal</span>
                                                    </td>
                                                    <td>
                                                        <span class="px-3 py-2" style="font-size: 12px;color: #219653;background: rgba(115, 219, 158, 0.3);border-radius: 15px;">Completed</span>
                                                    </td>
                                                  </tr>
                                                </tbody>
                                              </table>
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