@php
    $banks = App\Bank::all();
    $countries = App\Country::orderBy('phonecode', 'asc')->get();
@endphp

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
                <div class="container card card-body profile_card_content d-none d-lg-block">
                    <div class="shadow_bg"></div>

                    <div class="d-flex flex-column flex-md-row profile_root_container">
                        <div class="profile_image_root_container pb-5">
                            <div class="profile_image_container">
                                <img src="/storage/avatar/{{ Auth::user()->dp }}" class="img-fluid profile_image" />
                                <div class="camera_button" data-target="#upload-dp-modal" data-toggle="modal">
                                    <svg width="20" height="20" viewBox="0 0 40 37" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M5.21739 5.21739V0H8.69565V5.21739H13.913V8.69565H8.69565V13.913H5.21739V8.69565H0V5.21739H5.21739ZM10.4348 15.6522V10.4348H15.6522V5.21739H27.8261L31.0087 8.69565H36.5217C38.4348 8.69565 40 10.2609 40 12.1739V33.0435C40 34.9565 38.4348 36.5217 36.5217 36.5217H8.69565C6.78261 36.5217 5.21739 34.9565 5.21739 33.0435V15.6522H10.4348ZM22.6087 31.3043C27.4087 31.3043 31.3043 27.4087 31.3043 22.6087C31.3043 17.8087 27.4087 13.913 22.6087 13.913C17.8087 13.913 13.913 17.8087 13.913 22.6087C13.913 27.4087 17.8087 31.3043 22.6087 31.3043ZM17.0435 22.6087C17.0435 25.687 19.5304 28.1739 22.6087 28.1739C25.687 28.1739 28.1739 25.687 28.1739 22.6087C28.1739 19.5304 25.687 17.0435 22.6087 17.0435C19.5304 17.0435 17.0435 19.5304 17.0435 22.6087Z"
                                            fill="white" />
                                    </svg>
                                </div>
                            </div>
                            <div class="d-flex flex-column justify-content-center align-items-center mb-4"
                                style="z-index: 2;position:relative;top:-110px;">
                                <span class="d-block text-upper realtime-wallet-balance"
                                    style="font-size: 26px;font-weight:normal;color:#000070;"></span>
                                <span class="d-block"
                                    style="font-size: 18px;letter-spacing: 0.01em;color: #676B87;text-transform:uppercase;">wallet
                                    balance</span>
                            </div>
                            <div class="d-flex flex-column align-items-center wallet_balance_section py-3 pb-4">
                                <div class="d-flex flex-column">
                                    <div class="my-2">
                                        <span class="d-block text-center labelText">Name</span>
                                        <div class="d-flex justify-content-center align-items-center details">
                                            {{ Auth::user()->first_name }} </div>
                                    </div>
                                    <div class="my-2">
                                        <span class="d-block text-center labelText">Email</span>
                                        <div style="font-size: 14px"
                                            class="d-flex justify-content-center text-lowercase  align-items-center details">
                                            {{ Auth::user()->email }} </div>
                                    </div>
                                    <div class="my-2">
                                        <span class="d-block text-center labelText">Phone</span>
                                        <div class="d-flex justify-content-center align-items-center details">
                                            {{ Auth::user()->phone }} </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="profile_details ml-lg-4 mt-3">
                            <ul class="nav nav-tabs mb-0 d-flex flex-column flex-lg-row" id="myTab" role="tablist"
                                style="border-radius: 10px 10px 0px 0px;background: linear-gradient(180deg, #EFF4F7 -92.86%, #FBFBFF 100%);">
                                <li class="nav-item profile_tab_title" role="presentation">
                                    <a class="nav-link active d-flex justify-content-center" id="profile-tab"
                                        style="height: 52px;padding:0;margin:0 !important;" data-toggle="tab"
                                        href="#profile" role="tab" aria-controls="home" aria-selected="true">
                                        PROFILE</a>
                                </li>
                                <li class="nav-item profile_tab_title" role="presentation"
                                    style="border-left: 0.3px solid #969CBA;">
                                    <a class="nav-link d-flex justify-content-center" id="security-tab"
                                        data-toggle="tab" style="height: 52px;" href="#security" role="tab"
                                        aria-controls="security" aria-selected="false">SECURITY</a>
                                </li>
                                <li class="nav-item profile_tab_title" role="presentation"
                                    style="border-left: 0.3px solid #969CBA;">
                                    <a class="nav-link d-flex justify-content-center" id="notification-tab"
                                        data-toggle="tab" style="height: 52px;" href="#notification" role="tab"
                                        aria-controls="contact" aria-selected="false">NOTIFICATIONS</a>
                                </li>
                                <li class="nav-item profile_tab_title" role="presentation"
                                    style="border-left: 0.3px solid #969CBA;">
                                    <a class="nav-link d-flex justify-content-center" id="limits-tab" data-toggle="tab"
                                        style="height: 52px;" href="#limits" role="tab" aria-controls="limits"
                                        aria-selected="false">VERIFICATIONS</a>
                                </li>
                            </ul>
                            <div class="tab-content" id="myTabContent">

                                {{-- Profile Tab Content --}}
                                <div class="tab-pane fade show active" id="profile" role="tabpanel"
                                    aria-labelledby="profile-tab">
                                    <div class="d-flex flex-column">
                                        <div class="d-flex align-items-center mt-0 profile_details_col">
                                            <div class="user_profile_text text-center" style="width: 14%;">Name</div>
                                            <div class="user_profile_text ml-5" style="font-size: 18px;width: 56%;">
                                                {{ Auth::user()->first_name }}</div>
                                            <div class="user_profile_text text-center ml-5" style="width: 30%;">
                                                <div class="profile_verification_status_text">
                                                    {{-- Pending verification --}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mt-0 profile_details_col"
                                            style="background: #F7F7F7;">
                                            <div class="user_profile_text text-center" style="width: 14%;">Email</div>
                                            <div class="user_profile_text ml-5" style="font-size: 18px;width: 56%;">
                                                {{ Auth::user()->email }}</div>
                                            <div class="user_profile_text text-center ml-5" style="width: 30%;">
                                                <div class="profile_verification_status_text text-accent">
                                                    Verified
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mt-0 profile_details_col">
                                            <div class="user_profile_text"
                                                style="position:relative;left:1.7em;width: 14%;">Bank</div>
                                            <div class="" style="width:56%;">
                                                <div class="d-flex" style="position: relative;left:35px;">
                                                    @if (Auth::user()->accounts->count() > 0)
                                                    {{ Auth::user()->accounts->first()->bank_name }}
                                                    <div class="user_profile_text ml-4" style="font-size: 18px;">
                                                        <div style="font-size:16px;">Acc. No.
                                                            {{ Auth::user()->accounts->first()->account_number }}
                                                        </div>
                                                    </div>
                                                    @else
                                                    <button id="add_bank" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#add-bank-modal">Add Bank</button>
                                                    {{-- <button class="btn btn-sm btn-primary">Add Bank1</button> --}}
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="user_profile_text text-center ml-5"
                                                style="width: 30%;position:relative;left:12px;">
                                                <div class="profile_verification_status_text" style="color: #00B9CD;">
                                                    {{ Auth::user()->accounts->count() > 0 ? 'verified' : 'Not verified' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mt-0 profile_details_col"
                                            style="background:#F7F7F7;">
                                            <div class="user_profile_text"
                                                style="position:relative;left:1.7em;width: 14%;">Mobile No.</div>
                                            <div class="" style="width:56%;">
                                                <div style="position: relative;left:35px;">
                                                    @if (!Auth::user()->phone)
                                                        {{-- <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#add-bank-modal">Add Phone</button>
                                                    @else --}}
                                                        {{ Auth::user()->phone }}
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="user_profile_text text-center"
                                                style="width: 30%;position:relative;left:25px;">
                                                <div class="profile_verification_status_text" style="color: #00B9CD;">
                                                    {{ Auth::user()->phone_verified_at == null ? 'Not verified' : 'Verified' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mt-0 profile_details_col">
                                            <div class="user_profile_text"
                                                style="position:relative;left:1.7em;width: 14%;">Status</div>
                                            <div class="" style="width:56%;">
                                                <div class="d-flex" style="position: relative;left:35px;">
                                                    <div class="user_profile_text text-capitalize"
                                                        style="font-size: 18px;">
                                                        <span>{{ Auth::user()->status }}</span>
                                                    </div>
                                                    {{-- <div class="user_profile_text ml-4" style="font-size: 18px;">
                                                        <div style="font-size:16px;">Acc. No. XXXXXXXXXX</div>
                                                    </div> --}}
                                                </div>
                                            </div>
                                            <div class="user_profile_text text-center" style="width: 30%;">
                                                <div class="profile_verification_status_text" style="color: #00B9CD;">
                                                    {{-- verified --}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="upload_id_column d-flex align-items-center px-4 mt-4 mb-5">
                                            <div class="text-center upload_id_text py-1 mr-2" style="font-size:14px;">
                                                Verification Progress</div>
                                            <div style="width: 100%;">
                                                <div style="position: relative;left:88%;font-size:14px;">
                                                    @if (Auth::user()->v_progress == 100)
                                                    Verified
                                                    <span><svg width="18" height="18" viewBox="0 0 27 27" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <circle cx="13.5" cy="13.5" r="13.5" fill="white" />
                                                            <path
                                                                d="M11.4999 16.4993L7.99992 12.9993L6.83325 14.166L11.4999 18.8327L21.4999 8.83268L20.3333 7.66602L11.4999 16.4993Z"
                                                                fill="black" fill-opacity="0.87" />
                                                        </svg>
                                                    </span>
                                                    @endif
                                                </div>
                                                <div class="progress" style="height: 8px;">
                                                    <div class="progress-bar" role="progressbar"
                                                        style="border-radius: 50px;background:#fff;width: {{ Auth::user()->v_progress }}%">
                                                    </div>
                                                </div>
                                                <div style="position: relative;left:82%;font-size:14px;">
                                                    ..{{ Auth::user()->v_progress }}% complete
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- SECURITY TAB CONTENT --}}
                                <div class="tab-pane fade" id="security" role="tabpanel" aria-labelledby="security-tab">
                                    <div class="container px-4">
                                        <div class="d-flex changepassword_text mt-4 pb-1">
                                            Change Account Password</div>
                                        <div style="border: 1px solid #CBCBCB;width:100%;"></div>
                                        <div class="my-4 mb-4">
                                            <form method="POST" action="{{ route('user.change_password') }}"> @csrf
                                                <div class="row">
                                                    <div class="col-12 col-md-6 my-2 my-lg-0">
                                                        <label for="oldpassword" class="changePasswordLabelText">Old
                                                            Password</label>
                                                        <input type="password" name="old_password"
                                                            style="border: 1.3px solid #D7D7D7;" class="form-control"
                                                            id="oldpassword" />
                                                    </div>
                                                    <div class="col-12 col-md-6 my-2 my-lg-0">
                                                        <label for="oldpassword" class="changePasswordLabelText">New
                                                            Password</label>
                                                        <input type="password" name="new_password"
                                                            class="form-control" />
                                                    </div>
                                                    <div class="col-12 my-2 my-lg-0">
                                                        <label for="oldpassword" class="changePasswordLabelText">Confirm
                                                            Password</label>
                                                        <input type="password" name="new_password_confirmation"
                                                            class="form-control" />
                                                    </div>
                                                    <div class="col-md-3 mt-3"><button
                                                            class="btn btn-primary">Update</button></div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="d-flex changepassword_text mt-4 pb-1">
                                            Change Naira Wallet Pin</div>
                                        <div style="border: 1px solid #CBCBCB;width:100%;"></div>
                                        <div class="my-4 mb-4">
                                            <form method="POST" action="{{ route('user.update-naira-password') }}">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-12 col-md-6 my-2 my-lg-0">
                                                        <label for="oldpassword" class="changePasswordLabelText">Account
                                                            Password</label>
                                                        <input type="password" name="old_password"
                                                            style="border: 1.3px solid #D7D7D7;" class="form-control"
                                                            id="oldpassword" />
                                                    </div>
                                                    <div class="col-12 col-md-6 my-2 my-lg-0">
                                                        <label for="oldpassword" class="changePasswordLabelText">New
                                                            Wallet Pin</label>
                                                        <input type="password" name="new_password" placeholder="- - - -"
                                                            minlength="4" maxlength="4" class="form-control" />
                                                    </div>
                                                    <div class="col-12 my-2 my-lg-0">
                                                        <label for="oldpassword" class="changePasswordLabelText">Confirm
                                                            Wallet Pin</label>
                                                        <input type="password" placeholder="- - - -" minlength="4"
                                                            maxlength="4" name="new_password_confirmation"
                                                            class="form-control" />
                                                    </div>
                                                    <div class="col-md-3 mt-3"><button
                                                            class="btn btn-primary">Update</button></div>
                                                </div>
                                            </form>
                                        </div>

                                    </div>
                                </div>

                                {{-- NOTIFICATIONS --}}
                                <div class="tab-pane fade" id="notification" role="tabpanel"
                                    aria-labelledby="notification-tab">
                                    <div class="container">
                                        <div
                                            class="mx-auto d-flex flex-column justify-content-center align-items-center notifications-container py-5">
                                            <div class="d-flex flex-row justify-content-center align-items-center my-3"
                                                style="position: relative;left:40px;">
                                                <div class="transaction-sms-notification">
                                                    Wallet transaction SMS notification</div>
                                                <div>
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input" id="w-s"
                                                            {{Auth::user()->notificationSetting->wallet_sms ? 'checked' : '' }}
                                                            onclick="notSw('w-s')">
                                                        <label class="custom-control-label" for="w-s"></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex flex-row justify-content-start align-items-center my-3"
                                                style="position: relative;left:40px;">
                                                <div class="transaction-email-alert">
                                                    Wallet transaction email alert</div>
                                                <div style="position: relative;left:23px;">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input" id="w-e"
                                                            {{Auth::user()->notificationSetting->wallet_email ? 'checked' : '' }}
                                                            onclick="notSw('w-e')">
                                                        <label class="custom-control-label" for="w-e"></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex flex-row justify-content-center align-items-center my-3"
                                                style="position: relative;left:40px;">
                                                <div class="transaction-sms-notification2">
                                                    Trade transaction SMS notification</div>
                                                <div style="position: relative;left:3px;">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input" id="t-s"
                                                            {{Auth::user()->notificationSetting->trade_sms ? 'checked' : '' }}
                                                            onclick="notSw('t-s')">
                                                        <label class="custom-control-label" for="t-s"></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex flex-row justify-content-center align-items-center my-3"
                                                style="position: relative;left:40px;">
                                                <div class="transaction-email-alert2">
                                                    Trade transaction email alert</div>
                                                <div style="position: relative;left:25px;">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input" id="t-e"
                                                            {{Auth::user()->notificationSetting->trade_email ? 'checked' : '' }}
                                                            onclick="notSw('t-e')">
                                                        <label class="custom-control-label" for="t-e"></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- LIMITS --}}
                                <div class="tab-pane fade" id="limits" role="tabpanel" aria-labelledby="v-tab">
                                    <div class="d-flex mt-4 accordion_full_container">
                                        <div
                                            class="mt-4 pt-2 d-flex flex-column justify-content-start align-items-start align-items-lg-center">
                                            <div class="d-flex flex-column align-items-center ml-4 ml-lg-0">
                                                <span class="my-1" style="color: #000070;font-size:15px;">Maximum
                                                    monthly limit:
                                                    <span
                                                        style="font-weight: bold;">₦{{ number_format(Auth::user()->monthly_max) }}</span></span>
                                                <span class="my-1" style="color: #000070;font-size:15px;">Maximum daily
                                                    limit:
                                                    <span
                                                        style="font-weight: bold;">₦{{ number_format(Auth::user()->monthly_max) }}</span></span>
                                                {{-- <span class="my-1" style="color: #000070;font-size:14px;">Remaining
                                                    daily limit:
                                                    <span>N300,000</span></span> --}}
                                            </div>

                                            <div class="container-fluid mt-3">
                                                <div class="row px-lg-3">
                                                    <!-- <div class="col-lg-2"></div> -->

                                                    <div class="col-12 col-lg-10">

                                                        {{-- Phone verification card --}}
                                                        @if (Auth::user()->phone_verified_at == null)
                                                        <a href="{{ (Auth::user()->first_name == ' ' || strlen(Auth::user()->first_name) < 2) ? '#' : route('user.verify-phone') }}" onclick="{{(strlen(Auth::user()->first_name) < 2) ? 'show_bank()' : ''}}">
                                                            <div class="d-flex flex-row justify-content-center align-items-center accordion_cards phoneVerificationCard">
                                                                <span class="d-block">Level 1  - Phone number verification</span>
                                                                <span class="d-block ml-lg-5 mr-3 mr-lg-0 accordion_arrow" style="position: relative;left: 10px;">
                                                                    <svg width="20" height="20" viewBox="0 0 20 20"
                                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path
                                                                            d="M15.001 8.3332L13.826 7.1582L10.001 10.9749L6.17598 7.1582L5.00098 8.3332L10.001 13.3332L15.001 8.3332Z"
                                                                            fill="#000070" />
                                                                    </svg>
                                                                </span>
                                                            </div>
                                                        </a>
                                                        @endif
                                                        @if (Auth::user()->address_verified_at == null && Auth::user()->phone_verified_at != null)
                                                        {{-- Address verification tab --}}
                                                        <div
                                                            class="d-flex flex-row justify-content-center align-items-center accordion_cards addressVerificationCard mt-4">
                                                            <span class="d-block">Level 2 - Address verification</span>
                                                            <span class="d-block ml-5 accordion_arrow"
                                                                style="position: relative;left: 22px;">
                                                                <svg width="20" height="20" viewBox="0 0 20 20"
                                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path
                                                                        d="M15.001 8.3332L13.826 7.1582L10.001 10.9749L6.17598 7.1582L5.00098 8.3332L10.001 13.3332L15.001 8.3332Z"
                                                                        fill="#000070" />
                                                                </svg>
                                                            </span>
                                                        </div>
                                                        {{-- Address verification content --}}
                                                        <div class="accordion_content mt-3 pb-3"
                                                            id="AddressVerification" style="display: none;">
                                                            <div class="d-flex justify-content-center">
                                                                <button class="btn btn-primary mb-2 ml-2"
                                                                    data-target="#upload-address-modal"
                                                                    data-toggle="modal"
                                                                    style="height:35px;width:78px;position: relative;top:8px;">Verify</button>
                                                            </div>
                                                        </div>
                                                        @endif
                                                    </div>

                                                    <div class="col-12 col-lg-12 mt-3">

                                                        @if (Auth::user()->idcard_verified_at == null && Auth::user()->address_verified_at != null)
                                                        {{-- ID verification card --}}
                                                        <div
                                                            class="d-flex flex-row justify-content-center align-items-center accordion_cards idVerificationCard mt-2">
                                                            <span class="d-block">Level 3 - ID verification</span>
                                                            <span
                                                                class="d-block ml-5 accordion_arrow id_verification_arrow">
                                                                <svg width="20" height="20" viewBox="0 0 20 20"
                                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path
                                                                        d="M15.001 8.3332L13.826 7.1582L10.001 10.9749L6.17598 7.1582L5.00098 8.3332L10.001 13.3332L15.001 8.3332Z"
                                                                        fill="#000070" />
                                                                </svg>
                                                            </span>
                                                        </div>
                                                        {{-- Id verification content --}}
                                                        <div class="accordion_content" id="idVerification"
                                                            style="display: none;">
                                                            <div class="mt-2">
                                                                <form> @csrf
                                                                    <div class="d-flex justify-content-start">
                                                                        <div class="d-flex justify-content-center mx-auto">
                                                                            <button type="button" data-toggle="modal"
                                                                                data-target="#upload-id-modal"
                                                                                class="btn btn-primary verifyIdSubmitBtn">Verify</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                        @endif
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

                {{-- Mobile --}}

                <div class="container-fluid d-lg-none mb-4">
                    <div class="d-flex flex-column align-items-center">
                        <div class="profilepicture_mobile">
                            <img class="img-fluid rounded-circle" style="height: 200px"
                                src="/storage/avatar/{{ Auth::user()->dp }}" alt="">
                            <div class="camera_button" data-toggle="modal" data-target="#upload-dp-modal">
                                <svg width="20" height="20" viewBox="0 0 40 37" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M5.21739 5.21739V0H8.69565V5.21739H13.913V8.69565H8.69565V13.913H5.21739V8.69565H0V5.21739H5.21739ZM10.4348 15.6522V10.4348H15.6522V5.21739H27.8261L31.0087 8.69565H36.5217C38.4348 8.69565 40 10.2609 40 12.1739V33.0435C40 34.9565 38.4348 36.5217 36.5217 36.5217H8.69565C6.78261 36.5217 5.21739 34.9565 5.21739 33.0435V15.6522H10.4348ZM22.6087 31.3043C27.4087 31.3043 31.3043 27.4087 31.3043 22.6087C31.3043 17.8087 27.4087 13.913 22.6087 13.913C17.8087 13.913 13.913 17.8087 13.913 22.6087C13.913 27.4087 17.8087 31.3043 22.6087 31.3043ZM17.0435 22.6087C17.0435 25.687 19.5304 28.1739 22.6087 28.1739C25.687 28.1739 28.1739 25.687 28.1739 22.6087C28.1739 19.5304 25.687 17.0435 22.6087 17.0435C19.5304 17.0435 17.0435 19.5304 17.0435 22.6087Z"
                                        fill="white" />
                                </svg>
                            </div>
                            <span class="d-block text-center my-3"
                                style="color: #000070;font-size:16px;">{{ Auth::user()->first_name }}</span>
                        </div>
                        <div class="mb-4">
                            <span class="d-block text-center" style="color: #676B87;">Wallet Balance</span>
                            <span class="d-block text-center realtime-wallet-balance"
                                style="color: #000070;font-size: 16px;font-weight: 600;"></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center" style="width:92vw;">
                            <div onclick="switchTab('mobile_profile_tab')" id="mobile_profile_tab"
                                class="d-flex justify-content-center align-items-center profile_tab_title_mobile text-center tab_active_mobile"
                                style="border-left: 0px;">
                                Profile</div>
                            <div onclick="switchTab('mobile_security_tab')" id="mobile_security_tab"
                                class="d-flex justify-content-center align-items-center profile_tab_title_mobile text-center">
                                Security</div>
                            <div onclick="switchTab('mobile_notifications_tab')" id="mobile_notifications_tab"
                                class="d-flex justify-content-center align-items-center profile_tab_title_mobile text-center">
                                Notification</div>
                            <div onclick="switchTab('mobile_limits_tab')" id="mobile_limits_tab"
                                class="d-flex justify-content-center align-items-center profile_tab_title_mobile text-center"
                                style="border-right: 0px;">
                                Limits</div>
                        </div>


                        <!-- Profile Tab -->
                        <div id="mobile_profile_contents" class="container p-0 mobile_tab_contents"
                            style="margin-top: 0px;">
                            <div class="row">
                                <div class="col-12 card mx-0 p-0 p-2" style="border-radius: 5px;">
                                    <div class="row py-1 my-1">
                                        <div style="font-size: 14px;" class="col-3 col_name">Name</div>
                                        <div class="col-9">{{ Auth::user()->first_name }}</div>
                                    </div>
                                    <div class="row py-1 my-1">
                                        <div style="font-size: 14px;" class="col-3 col_name">Email</div>
                                        <div class="col-9">{{ Auth::user()->email }}</div>
                                    </div>
                                    <div class="row py-1 my-1">
                                        <div style="font-size: 14px;" class="col-3 col_name">Bank</div>
                                        <div class="col-9">

                                            @if (Auth::user()->accounts->count() == 0)
                                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#add-bank-modal">Add Bank</button>
                                            @else
                                                {{ Auth::user()->accounts->first()->account_number  }}
                                                {{ Auth::user()->accounts->first()->bank_name }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row py-1 my-1">
                                        <div style="font-size: 14px;" class="col-3 col_name">Mobile No</div>
                                        <div class="col-9"> {{ Auth::user()->phone }} </div>
                                    </div>
                                    <div class="row py-1 my-1">
                                        <div style="font-size: 14px;" class="col-3 col_name">Status</div>
                                        <div class="col-9 text-capitalize">{{ Auth::user()->status }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Security Tab -->
                        <div id="mobile_security_contents" class="container p-0 mobile_tab_contents"
                            style="margin-top: 0px;display:none;">
                            <div class="row">
                                <div class="col-12 card p-0 px-2" style="border-radius:5px;">
                                    <span class="d-block h6 mt-3" style="color: rgba(0, 0, 112, 0.75);">Change Account
                                        password</span>
                                    <div style="height: 0;width:100%;border: 1px solid rgba(0, 0, 112, 0.75);"></div>
                                    <div class="row my-3">
                                        <div class="col-12">
                                            <form action="{{ route('user.change_password') }}" method="post"> @csrf
                                                <div class="form-group  mx-auto">
                                                    <label for="password">Old password</label>
                                                    <input type="password" id="password" class="form-control  mx-auto"
                                                        name="old_password" />
                                                </div>
                                                <div class="form-group  mx-auto">
                                                    <label for="new_password">New password</label>
                                                    <input type="password" id="new_password"
                                                        class="form-control  mx-auto" name="new_password" />
                                                </div>
                                                <div class="form-group  mx-auto">
                                                    <label for="cnew_password">New password</label>
                                                    <input type="password" id="cnew_password"
                                                        class="form-control  mx-auto"
                                                        name="new_password_confirmation" />
                                                </div>
                                                <button type="submit" class="btn text-white"
                                                    style="background-color: #000070;">Update password</button>
                                            </form>
                                        </div>
                                    </div>

                                    <span class="d-block h6 mt-3" style="color: rgba(0, 0, 112, 0.75);">Change Naira
                                        Wallet
                                        Pin</span>
                                    <div style="height: 0;width:100%;border: 1px solid rgba(0, 0, 112, 0.75);"></div>
                                    <div class="row my-3">
                                        <div class="col-12">
                                            <form action="{{ route('user.update-naira-password') }}" method="post">
                                                @csrf
                                                <div class="form-group  mx-auto">
                                                    <label for="password">Account password</label>
                                                    <input type="password" id="password" class="form-control  mx-auto"
                                                        name="old_password" />
                                                </div>
                                                <div class="form-group  mx-auto">
                                                    <label for="new_password">New password</label>
                                                    <input type="password" placeholder="- - - -" id="new_password"
                                                        class="form-control  mx-auto" name="new_password" />
                                                </div>
                                                <div class="form-group  mx-auto">
                                                    <label for="cnew_password">New password</label>
                                                    <input type="password" placeholder="- - - -" id="cnew_password"
                                                        class="form-control  mx-auto"
                                                        name="new_password_confirmation" />
                                                </div>
                                                <button type="submit" class="btn text-white"
                                                    style="background-color: #000070;">Update pin</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notifications tab -->
                        <div id="mobile_notification_contents" class="container p-0 mobile_tab_contents"
                            style="margin-top: 0px;display:none;">
                            <div class="row">
                                <div class="col-12 card p-0 px-2 py-3">
                                    <div class="row px-2 my-2">
                                        <div class="col-9">
                                            <span style="font-size: 12px;color: #000070;">Wallet transaction SMS
                                                notification</span>
                                        </div>
                                        <div class="col-2">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="w-s2"
                                                    {{Auth::user()->notificationSetting->wallet_sms ? 'checked' : '' }}
                                                    onclick="notSw('w-s2')">
                                                <label class="custom-control-label" for="w-s2"></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row px-2 my-2">
                                        <div class="col-9">
                                            <span style="font-size: 12px;color: #000070;">Wallet transaction email
                                                alert</span>
                                        </div>
                                        <div class="col-2">
                                            <div class="custom-control custom-switch">
                                                <input
                                                    {{Auth::user()->notificationSetting->wallet_email ? 'checked' : '' }}
                                                    onclick="notSw('w-e2')" type="checkbox" class="custom-control-input"
                                                    id="w-e2">
                                                <label class="custom-control-label" for="w-e2"></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row px-2 my-2">
                                        <div class="col-9">
                                            <span style="font-size: 12px;color: #000070;">Trade transaction SMS
                                                notification</span>
                                        </div>
                                        <div class="col-2">
                                            <div class="custom-control custom-switch">
                                                <input
                                                    {{Auth::user()->notificationSetting->trade_sms ? 'checked' : '' }}
                                                    onclick="notSw('t-s2')" type="checkbox" class="custom-control-input"
                                                    id="t-s2">
                                                <label class="custom-control-label" for="t-s2"></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row px-2 my-2">
                                        <div class="col-9">
                                            <span style="font-size: 12px;color: #000070;">Trade transaction email
                                                alert</span>
                                        </div>
                                        <div class="col-2">
                                            <div class="custom-control custom-switch">
                                                <input
                                                    {{Auth::user()->notificationSetting->trade_email ? 'checked' : '' }}
                                                    onclick="notSw('t-e2')" type="checkbox" class="custom-control-input"
                                                    id="t-e2">
                                                <label class="custom-control-label" for="t-e2"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Limits tab -->
                        <div id="mobile_limits_contents" class="container p-0 mobile_tab_contents"
                            style="margin-top: 0px;display:none;">
                            <div class="row">
                                <div class="col-12 card p-0 px-2 py-3">
                                    <div class="d-flex mt-4 accordion_full_container">
                                        <div
                                            class="mt-4 pt-2 d-flex flex-column justify-content-start align-items-start align-items-center">
                                            <div class="d-flex flex-column align-items-center ml-4 ml-lg-0">
                                                <span class="my-1" style="color: #000070;font-size:15px;">Maximum
                                                    monthly limit:
                                                    <span
                                                        style="font-weight: bold;">₦{{ number_format(Auth::user()->monthly_max) }}</span></span>
                                                <span class="my-1" style="color: #000070;font-size:15px;">Maximum daily
                                                    limit:
                                                    <span
                                                        style="font-weight: bold;">₦{{ number_format(Auth::user()->daily_max) }}</span></span>
                                                {{-- <span class="my-1" style="color: #000070;font-size:14px;">Remaining
                                                    daily limit:
                                                    <span>N300,000</span></span> --}}
                                            </div>

                                            <div class="container-fluid mt-3">
                                                <div class="row px-lg-3">


                                                    <!-- Phone verification card mobile -->
                                                    @if (Auth::user()->phone_verified_at == null)
                                                    <a href="{{ route('user.verify-phone') }}">
                                                        <div class="col-8 col-md-8 mx-auto my-2">
                                                            <div id="mobile_phone_verification_card"
                                                                class="d-flex flex-row justify-content-center align-items-center accordion_cards phoneVerificationCard">
                                                                <span class="d-block">Phone number verification</span>
                                                                <span class="d-block ml-lg-5 mr-3 mr-lg-0 accordion_arrow"
                                                                    style="position: relative;left: 21px;">
                                                                    <img src="/svg/accordion_arrow.svg" class="img-fluid" />
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </a>
                                                    @endif


                                                    <!-- Address verification card mobile -->
                                                    @if (Auth::user()->address_verified_at == null)
                                                    <div class="col-8 col-md-8 mx-auto my-2">
                                                        <div id="mobile_address_verification_card"
                                                            class="d-flex flex-row justify-content-center align-items-center accordion_cards phoneVerificationCard">
                                                            <span class="d-block">Address verification</span>
                                                            <span class="d-block ml-lg-5 mr-3 mr-lg-0 accordion_arrow"
                                                                style="position: relative;left: 41px;">
                                                                <img src="/svg/accordion_arrow.svg" class="img-fluid" />
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <!-- Address verification content -->
                                                    <div id="mobile_address_verification_card_content"
                                                        class="d-none flex-column justify-content-center mx-auto mt-3">
                                                        <div class="col-12">
                                                            <div class="d-flex justify-content-start align-items-end">
                                                                <button type="button" data-toggle="modal"
                                                                    data-target="#upload-address-modal"
                                                                    class="btn btn-primary mb-2 ml-2"
                                                                    style="height:35px;width:78px;position: relative;top:8px;">Verify</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif

                                                    <!-- ID verification card mobile -->
                                                    @if (Auth::user()->idcard_verified_at == null)
                                                    <div class="col-8 col-md-8 mx-auto my-2">
                                                        <div id="mobile_id_verification_card"
                                                            class="d-flex flex-row justify-content-center align-items-center accordion_cards phoneVerificationCard">
                                                            <span class="d-block">ID verification</span>
                                                            <span class="d-block ml-lg-5 mr-3 mr-lg-0 accordion_arrow"
                                                                style="position: relative;left: 59px;">
                                                                <img src="/svg/accordion_arrow.svg" class="img-fluid" />
                                                            </span>
                                                        </div>
                                                    </div>


                                                    <!-- ID verification content -->
                                                    <div id="id_verification_card_content"
                                                        class="d-none flex-column justify-content-center mx-auto mt-3">
                                                        <form action="" method="post">
                                                            @csrf
                                                            <div class="container">
                                                                <div class="row">
                                                                    <div class="col-6">

                                                                    </div>
                                                                    <div class="col-6">
                                                                        <div id="mobile_back_photo_click"
                                                                            class="d-flex flex-column align-items-center px-2 py-0"
                                                                            style="border: 0px dashed;">
                                                                            <input type="file" name="mobile_backOfCard"
                                                                                id="uploadBackPhotoInputMobile"
                                                                                style="display: none;" />
                                                                            <span class="d-block text-center text-white">Back</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <button type="button" class="btn text-white mt-3"
                                                                data-toggle="modal" data-target="#upload-id-modal"
                                                                style="background: #000070;margin-left:24%;">Upload</button>
                                                        </form>
                                                    </div>
                                                    @endif


                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Upload ID card -->
                        <div class="card card-body mt-3 m-0 p-0"
                            style="border-radius: 5px;width:90vw;overflow-x:hidden;">
                            <div class="d-flex justify-content-between">
                                <div class="ml-1">
                                    <span>
                                        <svg width="17" height="17" viewBox="0 0 17 17" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M3.29199 12.0815V13.4149C3.29199 13.7685 3.43247 14.1076 3.68252 14.3577C3.93256 14.6077 4.2717 14.7482 4.62533 14.7482H12.6253C12.9789 14.7482 13.3181 14.6077 13.5681 14.3577C13.8182 14.1076 13.9587 13.7685 13.9587 13.4149V12.0815"
                                                stroke="#2C3E50" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M5.29199 6.74788L8.62533 3.41455L11.9587 6.74788" stroke="#2C3E50"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M8.625 3.41455V11.4145" stroke="#2C3E50" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                    <span style="font-size: 12px;">Verification Progress</span>
                                </div>
                                <div class="mr-2">
                                    @if (Auth::user()->v_progress == 100)
                                    <span>verified</span>
                                    <span>
                                        <svg width="8" height="8" viewBox="0 0 8 8" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M3.625 0.498047C1.693 0.498047 0.125 2.06605 0.125 3.99805C0.125 5.93005 1.693 7.49805 3.625 7.49805C5.557 7.49805 7.125 5.93005 7.125 3.99805C7.125 2.06605 5.557 0.498047 3.625 0.498047ZM2.925 5.74805L1.175 3.99805L1.6685 3.50455L2.925 4.75755L5.5815 2.10105L6.075 2.59805L2.925 5.74805Z"
                                                fill="#219653" />
                                        </svg>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="progress mx-2 my-2" style="height: 8px;">
                                <div class="progress-bar" role="progressbar"
                                    style="border-radius: 50px;background:#000070;width: {{ Auth::user()->v_progress }}%">
                                </div>
                            </div>
                            <div class="mb-2" style="position: relative;left:60%;font-size:14px;">
                                ..{{ Auth::user()->v_progress }}% complete</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



{{-- Add image --}}
<div class="modal fade  item-badge-rightm" id="upload-dp-modal" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <form method="POST" action="{{route('user.dp')}}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <input type="file" name="dp" class="form-control" accept="images/*">
                    </div>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Id upload modal --}}
<div class="modal fade  item-badge-rightm" id="upload-id-modal" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <form method="POST" action="{{route('user.upload-id')}}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="">select ID card image</label>
                        <input type="file" name="id_card" class="form-control" accept="images/*">
                    </div>
                    <button type="submit" class="btn btn-primary">Upload Id Card</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Address upload modal --}}
<div class="modal fade  item-badge-rightm" id="upload-address-modal" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <form method="POST" action="{{route('user.upload-address')}}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="form-group ">
                        <label for="" class="address_verification_labelText">
                            Enter your address as shown in your document
                        </label>
                        <textarea placeholder="Your address" class="form-control" style="resize: none;" name="location"
                            rows="2"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="">Bank Statement</label>
                        <input type="file" name="address" class="form-control" >
                    </div>
                    <button type="submit" class="btn btn-primary">Upload Address Image</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Add bank account --}}
<div class="modal fade  item-badge-rightm" id="add-bank-modal" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <form id="user-bank-details" class="mb-4">
                    {{ csrf_field() }}
                    <div class="form-row ">
                        <div class="col-md-12">
                            <div class="position-relative form-group">
                                <label>Bank Name</label>
                                <select name="bank_code" class="form-control">
                                    @foreach ($banks as $b)
                                    <option value="{{$b->code}}">{{$b->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="position-relative form-group">
                                <label>Account Number</label>
                                <input type="text" required class="form-control" name="account_number">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="position-relative form-group">
                                <label>Account Name</label>
                                @if (Auth::user()->accounts->count() == 0)
                                <input type="text" required class="form-control " name="account_name">
                                @else
                                <input type="text" required class="form-control" readonly value="{{ Auth::user()->first_name }}" name="account_name">
                                @endif
                            </div>
                        </div>

                        {{-- @if (Auth::user()->phone_verified_at == null)
                        <div class="col-md-12">
                            <label for="">Phone Number</label>
                            <div class="position-relative input-group mb-0 mx-auto mx-md-0" style="">
                                <div class="input-group-prepend"
                                    style="border: 1px solid rgba(0, 0, 112, 0.25);border-right:0px;border-top-left-radius:5px;border-bottom-left-radius:5px;">
                                    <select name="country_id" id="country-id" class="form-control">
                                        <option value="156">+234</option>
                                        @foreach ($countries as $country)
                                        <option value="{{ $country->id }}">+ {{ $country->phonecode }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <input type="tel" id="signup_phonenumber" min="1" maxlength="10" name="phone" value="{{ Auth::user()->phone ?? '' }}"
                                    placeholder="8141894420" class="form-control col-12" style="border-left: 0px;"
                                    pattern="[1-9]\d*" title="Number not starting with 0">
                                <div class="input-group-prepend">
                                    <button id="otp-text" type="button" onclick="sendOtp()" class="btn btn-outline-primary btn-block">Send OTP</button>
                                </div>
                            </div>
                            <small>Number must not start with '0'.</small>
                        </div>

                        <div class="col-md-12">
                            <div class="position-relative form-group">
                                <label>OTP Code</label>
                                <input type="nnumber" required class="form-control " name="otp">
                            </div>
                        </div>

                        @endif --}}



                    </div>
                    <button type="submit" id="sign-up-btn" class="mt-2 btn btn-outline-primary">
                        <i class="spinner-border spinner-border-sm" id="s-b" style="display: none;"></i>
                        Save</button>
                </form>
            </div>
        </div>
    </div>
</div>
@if (strlen(Auth::user()->first_name) < 2)
    <script>
        function show_bank() {
            swal({
                title: "Failed!",
                text: "Please add your bank details to continue your level 1 verification",
                icon: "error",
                button: 'Add bank',
            }).then(function() {
                document.getElementById('add_bank').click();
            });
        }
    </script>
@endif
@endsection
