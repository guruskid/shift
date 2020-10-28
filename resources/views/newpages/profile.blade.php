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
                <div class="container card card-body profile_card_content">
                    <div class="shadow_bg"></div>

                    <div class="d-flex">
                        <div class="profile_image_root_container pb-5">
                            <div class="profile_image_container">
                                <img src="data:image/jpeg;base64,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"
                                    class="img-fluid profile_image" />
                                <div class="camera_button">
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
                                <span class="d-block text-upper"
                                    style="font-size: 26px;font-weight:normal;color:#000070;">NGN 800,300.00</span>
                                <span class="d-block"
                                    style="font-size: 18px;letter-spacing: 0.01em;color: #676B87;text-transform:uppercase;">wallet
                                    balance</span>
                            </div>
                            <div class="d-flex flex-column align-items-center wallet_balance_section py-3 pb-4">
                                <div class="d-flex flex-column">
                                    <div class="my-2">
                                        <span class="d-block text-center labelText">Last Name</span>
                                        <div class="d-flex justify-content-center align-items-center details">Andrea
                                        </div>
                                    </div>
                                    <div class="my-2">
                                        <span class="d-block text-center labelText">First Name</span>
                                        <div class="d-flex justify-content-center align-items-center details">Jolly
                                        </div>
                                    </div>
                                    <div class="my-2">
                                        <span class="d-block text-center labelText">Username</span>
                                        <div class="d-flex justify-content-center align-items-center details">Jolly1029
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div style="position:relative;top:-65px;">
                                <button class="btn btn-outline-danger"
                                    style="font-size:16px;width: 190px;height:45px;">DELETE ACCOUNT</button>
                            </div>
                        </div>
                        <div class="profile_details ml-4 mt-3" style="width: 70%;">
                            <ul class="nav nav-tabs mb-0" id="myTab" role="tablist"
                                style="border-radius: 10px 10px 0px 0px;background: linear-gradient(180deg, #EFF4F7 -92.86%, #FBFBFF 100%);">
                                <li class="nav-item" role="presentation" style="width: 25%;">
                                    <a class="nav-link active d-flex justify-content-center" id="profile-tab"
                                        style="height: 52px;padding:0;margin:0 !important;" data-toggle="tab"
                                        href="#profile" role="tab" aria-controls="home" aria-selected="true">USER
                                        PROFILE</a>
                                </li>
                                <li class="nav-item" role="presentation"
                                    style="width: 25%;border-left: 0.3px solid #969CBA;">
                                    <a class="nav-link d-flex justify-content-center" id="security-tab"
                                        data-toggle="tab" style="height: 52px;" href="#security" role="tab"
                                        aria-controls="profile" aria-selected="false">SECURITY</a>
                                </li>
                                <li class="nav-item" role="presentation"
                                    style="width: 25%;border-left: 0.3px solid #969CBA;">
                                    <a class="nav-link d-flex justify-content-center" id="notification-tab"
                                        data-toggle="tab" style="height: 52px;" href="#notification" role="tab"
                                        aria-controls="contact" aria-selected="false">NOTIFICATIONS</a>
                                </li>
                                <li class="nav-item" role="presentation"
                                    style="width: 25%;border-left: 0.3px solid #969CBA;">
                                    <a class="nav-link d-flex justify-content-center" id="limits-tab" data-toggle="tab"
                                        style="height: 52px;" href="#limits" role="tab" aria-controls="limits"
                                        aria-selected="false">LIMITS</a>
                                </li>
                            </ul>
                            <div class="tab-content" id="myTabContent">

                                {{-- Profile Tab Content --}}
                                <div class="tab-pane fade shodw actdive" id="profile" role="tabpanel"
                                    aria-labelledby="profile-tab">
                                    <div class="d-flex flex-column">
                                        <div class="d-flex align-items-center mt-0 profile_details_col">
                                            <div class="user_profile_text text-center" style="width: 14%;">Name</div>
                                            <div class="user_profile_text ml-5" style="font-size: 18px;width: 56%;">
                                                Andrea Jolly</div>
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
                                                andrea_jolly@domain.com</div>
                                            <div class="user_profile_text text-center ml-5" style="width: 30%;">
                                                <div class="profile_verification_status_text">
                                                    Pending verification
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mt-0 profile_details_col">
                                            <div class="user_profile_text"
                                                style="position:relative;left:1.7em;width: 14%;">Bank Name</div>
                                            <div class="" style="width:56%;">
                                                <div class="d-flex" style="position: relative;left:35px;">
                                                    <div class="user_profile_text" style="font-size: 18px;">
                                                        <select class="custom-select profile_select_bank m-0 p-0"
                                                            id="inputGroupSelect01" style="width: 180px;">
                                                            <option selected>Choose...</option>
                                                        </select>
                                                    </div>
                                                    <div class="user_profile_text ml-4" style="font-size: 18px;">
                                                        <div style="font-size:16px;">Acc. No. XXXXXXXXXX</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="user_profile_text text-center ml-5"
                                                style="width: 30%;position:relative;left:12px;">
                                                <div class="profile_verification_status_text" style="color: #00B9CD;">
                                                    verified
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mt-0 profile_details_col"
                                            style="background:#F7F7F7;">
                                            <div class="user_profile_text"
                                                style="position:relative;left:1.7em;width: 14%;">Mobile No.</div>
                                            <div class="" style="width:56%;">
                                                <div style="position: relative;left:35px;">
                                                    XXX-XXX-XXX-XXXX
                                                </div>
                                            </div>
                                            <div class="user_profile_text text-center"
                                                style="width: 30%;position:relative;left:25px;">
                                                <div class="profile_verification_status_text" style="color: #00B9CD;">
                                                    verified
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mt-0 profile_details_col">
                                            <div class="user_profile_text"
                                                style="position:relative;left:1.7em;width: 14%;">Status</div>
                                            <div class="" style="width:56%;">
                                                <div class="d-flex" style="position: relative;left:35px;">
                                                    <div class="user_profile_text" style="font-size: 18px;">
                                                        <span>Active</span>
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
                                                UPLOAD I.D</div>
                                            <div style="width: 100%;">
                                                <div style="position: relative;left:88%;font-size:14px;">Verified
                                                    <span><svg width="18" height="18" viewBox="0 0 27 27" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <circle cx="13.5" cy="13.5" r="13.5" fill="white" />
                                                            <path
                                                                d="M11.4999 16.4993L7.99992 12.9993L6.83325 14.166L11.4999 18.8327L21.4999 8.83268L20.3333 7.66602L11.4999 16.4993Z"
                                                                fill="black" fill-opacity="0.87" />
                                                        </svg>
                                                    </span></div>
                                                <div class="progress" style="height: 8px;">
                                                    <div class="progress-bar" role="progressbar"
                                                        style="border-radius: 50px;background:#fff;width: 100%"
                                                        aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <div style="position: relative;left:82%;font-size:14px;">..100% complete
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- SECURITY TAB CONTENT --}}
                                <div class="tab-pane fade" id="security" role="tabpanel" aria-labelledby="security-tab">
                                    <div class="container px-4">
                                        <div class="d-flex changepassword_text mt-4 pb-1">
                                            Change Password</div>
                                        <div style="border: 1px solid #CBCBCB;width:100%;"></div>
                                        <div class="my-4 mb-4">
                                            <form>
                                                @csrf
                                                <div class="row">
                                                    <div class="col">
                                                        <label for="oldpassword" class="changePasswordLabelText">Old
                                                            Password</label>
                                                        <input type="text" name="oldpassword"
                                                            style="border: 1.3px solid #D7D7D7;" class="form-control"
                                                            id="oldpassword" />
                                                    </div>
                                                    <div class="col">
                                                        <label for="oldpassword" class="changePasswordLabelText">New
                                                            Password</label>
                                                        <input type="text" name="newpassword" class="form-control" />
                                                    </div>
                                                    <div class="col">
                                                        <label for="oldpassword" class="changePasswordLabelText">Confirm
                                                            Password</label>
                                                        <input type="text" name="confirm_newpassword"
                                                            class="form-control" />
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div style="border: 1px solid #CBCBCB;width:100%;"></div>
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
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="customSwitch1" checked>
                                                        <label class="custom-control-label" for="customSwitch1"></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex flex-row justify-content-start align-items-center my-3"
                                                style="position: relative;left:40px;">
                                                <div class="transaction-email-alert">
                                                    Wallet transaction email alert</div>
                                                <div style="position: relative;left:23px;">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="email-alert1" checked>
                                                        <label class="custom-control-label" for="email-alert1"></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex flex-row justify-content-center align-items-center my-3"
                                                style="position: relative;left:40px;">
                                                <div class="transaction-sms-notification2">
                                                    Trade transaction SMS notification</div>
                                                <div style="position: relative;left:3px;">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="sms-note1" checked>
                                                        <label class="custom-control-label" for="sms-note1"></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex flex-row justify-content-center align-items-center my-3"
                                                style="position: relative;left:40px;">
                                                <div class="transaction-email-alert2">
                                                    Trade transaction email alert</div>
                                                <div style="position: relative;left:25px;">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="email-alert2" checked>
                                                        <label class="custom-control-label" for="email-alert2"></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- LIMITS --}}
                                <div class="tab-pane active show fade" id="limits" role="tabpanel"
                                    aria-labelledby="v-tab">
                                    <div class="mt-4 pt-2 d-flex flex-column justify-content-center align-items-center"
                                        style="border: 0.5px solid #B1B1B1;border-radius: 7px;">
                                        <span class="my-1" style="color: #000070;font-size:15px;">Maximum monthly limit:
                                            <span style="font-weight: bold;">N300,000</span></span>
                                        <span class="my-1" style="color: #000070;font-size:15px;">Maximum daily limit:
                                            <span style="font-weight: bold;">N100,000</span></span>
                                        <span class="my-1" style="color: #000070;font-size:14px;">Remaining daily limit:
                                            <span>N300,000</span></span>
                                    </div>

                                    <div class="container-fluid mt-3">
                                        <div class="row">
                                            <div class="col-12 col-lg-6">
                                                {{-- Phone verification card --}}
                                                <div
                                                    class="d-flex flex-row justify-content-center align-items-center accordion_cards phoneVerificationCard">
                                                    <span class="d-block">Phone number verification</span>
                                                    <span class="d-block ml-5 accordion_arrow"
                                                        style="position: relative;left: 22px;">
                                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M15.001 8.3332L13.826 7.1582L10.001 10.9749L6.17598 7.1582L5.00098 8.3332L10.001 13.3332L15.001 8.3332Z"
                                                                fill="#000070" />
                                                        </svg>
                                                    </span>
                                                </div>
                                                {{-- Phone number verification content --}}
                                                <div class="accordion_content" id="phoneVerification"
                                                    style="display: none;">
                                                    <div class="mt-3">
                                                        <form>
                                                            @csrf
                                                            <div
                                                                class="form-row align-items-center d-flex justify-content-start align-items-center">
                                                                <div class="col-auto">
                                                                    <label for="inlineFormInput"
                                                                        style="color: #000070;">Phone
                                                                        number</label>
                                                                    <div class="input-group mb-3">
                                                                        <div class="input-group-prepend">
                                                                            <select name="" id=""
                                                                                class="custom-select select_dial_code">
                                                                                <option value="">+234</option>
                                                                                <option value="">+234</option>
                                                                            </select>
                                                                        </div>
                                                                        <input type="text" style="width: 185px;"
                                                                            class="form-control dial_code_input"
                                                                            aria-label="Text input with dropdown button" />
                                                                    </div>
                                                                </div>
                                                                <div class="col-auto"
                                                                    style="position: relative;top:11px;">
                                                                    <button type="submit"
                                                                        class="btn btn-primary mb-2 px-3"
                                                                        style="height:40px;width:80px;">Verify</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <div class="mt-2" id="phoneVerification">
                                                        <form>
                                                            @csrf
                                                            <div
                                                                class="form-row align-items-center d-flex justify-content-start align-items-center">
                                                                <div class="col-auto">
                                                                    <label for="inlineFormInput"
                                                                        style="color: #000070;font-size:13px;width:260px;">Enter
                                                                        the OTP sent to the Phone number your
                                                                        entered</label>
                                                                    <div class="input-group mb-3" style="width: 256px;">
                                                                        <input type="text"
                                                                            class="form-control otp_code_input"
                                                                            aria-label="Text input with dropdown button"
                                                                            placeholder="000000" />
                                                                    </div>
                                                                </div>
                                                                <div class="col-auto"
                                                                    style="position: relative;top:19px;">
                                                                    <button type="submit" class="btn btn-primary mb-2"
                                                                        style="height:40px;width:78px;">Confirm</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                                {{-- Address verification tab --}}
                                                <div
                                                    class="d-flex flex-row justify-content-center align-items-center accordion_cards addressVerificationCard mt-4">
                                                    <span class="d-block">Address verification</span>
                                                    <span class="d-block ml-5 accordion_arrow"
                                                        style="position: relative;left: 22px;">
                                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M15.001 8.3332L13.826 7.1582L10.001 10.9749L6.17598 7.1582L5.00098 8.3332L10.001 13.3332L15.001 8.3332Z"
                                                                fill="#000070" />
                                                        </svg>
                                                    </span>
                                                </div>
                                                {{-- Address verification content --}}
                                                <div class="accordion_content mt-3" id="AddressVerification"
                                                    style="display: none;">
                                                    <form action="" method="post">
                                                        @csrf
                                                        <div class="form-group" style="width: 350px;">
                                                            <label for="youraddress"
                                                                class="address_verification_labelText">Enter your
                                                                address as shown in your document</label>
                                                            <textarea placeholder="Your address" id="youraddress"
                                                                class="form-control" style="resize: none;" name=""
                                                                rows="2"></textarea>
                                                        </div>
                                                        <div class="d-flex justify-content-start align-items-end">
                                                            <div class="d-flex justify-content-center align-items-center px-2 upload_address_photo"
                                                                id="uploadAddressVerification">
                                                                <input type="file" id="uploadPhotoInput"
                                                                    style="display: none;" />
                                                                <span>
                                                                    <svg width="28" height="26" viewBox="0 0 20 20"
                                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path
                                                                            d="M17.5947 4.58215H14.7032V4.28509C14.7032 2.95796 13.6261 1.88086 12.299 1.88086H7.70049C6.37282 1.88086 5.29626 2.95796 5.29626 4.28509V4.58215H2.40423C1.07656 4.58215 0 5.65871 0 6.98638V15.7132C0 17.0403 1.07656 18.1174 2.40423 18.1174H17.5958C18.9234 18.1174 20 17.0403 20 15.7132V6.98638C19.9989 5.65817 18.9224 4.58215 17.5947 4.58215ZM9.99893 15.6234C7.49426 15.6234 5.45761 13.5868 5.45761 11.0821C5.45761 8.57798 7.49426 6.54079 9.99893 6.54079C12.5036 6.54079 14.5403 8.57745 14.5403 11.0821C14.5403 13.5868 12.5031 15.6234 9.99893 15.6234ZM12.4032 11.0821C12.4032 12.4066 11.3239 13.4864 9.99893 13.4864C8.67393 13.4864 7.5947 12.4066 7.5947 11.0821C7.5947 9.75712 8.67393 8.67789 9.99893 8.67789C11.3239 8.67789 12.4032 9.75712 12.4032 11.0821Z"
                                                                            fill="#A6ACBE" />
                                                                    </svg>
                                                                </span>
                                                                <span class="ml-3"
                                                                    style="font-size: 10px;color: #000070;letter-spacing: 0.01em;line-height: 10px;">Upload
                                                                    your Bank <br> Statement of Account</span>
                                                            </div>
                                                            <button type="submit" class="btn btn-primary mb-2 ml-2"
                                                                style="height:35px;width:78px;position: relative;top:8px;">Verify</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-6">
                                                <div
                                                    class="d-flex flex-row justify-content-center align-items-center accordion_cards bvnVerificationCard">
                                                    <span class="d-block">BVN verification</span>
                                                    <span class="d-block ml-5 accordion_arrow"
                                                        style="position: relative;left: 22px;">
                                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M15.001 8.3332L13.826 7.1582L10.001 10.9749L6.17598 7.1582L5.00098 8.3332L10.001 13.3332L15.001 8.3332Z"
                                                                fill="#000070" />
                                                        </svg>
                                                    </span>
                                                </div>

                                                {{-- BVN verification content --}}
                                                <div class="accordion_content" id="bvnVerification" style="display: none;">
                                                    <div class="mt-2">
                                                        <form>
                                                            @csrf
                                                            <div class="form-row">
                                                                <div class="col-12 mt-1">
                                                                    <label for="inlineFormInput"
                                                                        style="color: #000070;font-size:13px;width:93%;">You
                                                                        BVN cannot be used to carry out any other
                                                                        transaction from your Bank acount.</label>
                                                                    <label for="inlineFormInput"
                                                                        style="color: #000070;font-size:13px;width:75%;margin-top:5px;">Dantown
                                                                        need your BVN to carry out naira transactions as
                                                                        required by the CBN</label>
                                                                </div>
                                                                <div class="col">
                                                                    <input type="text"
                                                                        class="form-control otp_code_input"
                                                                        placeholder="000000" style="width: 256px;">
                                                                </div>
                                                                <div class="col">
                                                                    <button type="submit" class="btn btn-primary mb-2"
                                                                        style="height:40px;width:78px;">Verify</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                                <div
                                                    class="d-flex flex-row justify-content-center align-items-center accordion_cards mt-4">
                                                    <span class="d-block">ID verification</span>
                                                    <span class="d-block ml-5 accordion_arrow"
                                                        style="position: relative;left: 22px;">
                                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M15.001 8.3332L13.826 7.1582L10.001 10.9749L6.17598 7.1582L5.00098 8.3332L10.001 13.3332L15.001 8.3332Z"
                                                                fill="#000070" />
                                                        </svg>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    {{-- <div class="d-flex flex-row justify-content-center align-items-center">
                                        <div class="d-flex flex-column mt-5">
                                            <div class="d-flex flex-row justify-content-center align-items-center">
                                                
                                                <div class="mr-3" id="phone-verification-container">
                                                    <div class="d-flex flex-row justify-content-center align-items-center accordion_cards phoneVerificationCard">
                                                        <span class="d-block">Phone number verification</span>
                                                        <span class="d-block ml-5 accordion_arrow">
                                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M15.001 8.3332L13.826 7.1582L10.001 10.9749L6.17598 7.1582L5.00098 8.3332L10.001 13.3332L15.001 8.3332Z"
                                                                    fill="#000070" />
                                                            </svg>
                                                        </span>
                                                    </div>
                                                    <div id="phoneVerification" style="display: none;">
                                                        <div class="mt-3">
                                                            <form>
                                                                @csrf
                                                                <div
                                                                    class="form-row align-items-center d-flex justify-content-center align-items-center">
                                                                    <div class="col-auto">
                                                                        <label for="inlineFormInput" style="color: #000070;">Phone
                                                                            number</label>
                                                                        <div class="input-group mb-3">
                                                                            <div class="input-group-prepend">
                                                                                <select name="" id=""
                                                                                    class="custom-select select_dial_code">
                                                                                    <option value="">+234</option>
                                                                                    <option value="">+234</option>
                                                                                </select>
                                                                            </div>
                                                                            <input type="text" class="form-control dial_code_input"
                                                                                aria-label="Text input with dropdown button" />
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-auto" style="position: relative;top:11px;">
                                                                        <button type="submit" class="btn btn-primary mb-2 px-3"
                                                                            style="height:40px;width:80px;">Verify</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                        <div class="mt-2" id="phoneVerification">
                                                            <form>
                                                                @csrf
                                                                <div
                                                                    class="form-row align-items-center d-flex justify-content-center align-items-center">
                                                                    <div class="col-auto">
                                                                        <label for="inlineFormInput" style="color: #000070;font-size:13px;width:280px;">Enter the OTP sent to the Phone number your entered</label>
                                                                        <div class="input-group mb-3">
                                                                            <input type="text" class="form-control otp_code_input"
                                                                                aria-label="Text input with dropdown button" placeholder="000000" />
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-auto" style="position: relative;top:18px;">
                                                                        <button type="submit" class="btn btn-primary mb-2"
                                                                            style="height:40px;width:80px;">Confirm</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="d-flex flex-row justify-content-center align-items-center accordion_cards ml-3">
                                                    <span class="d-block">BVN verification</span>
                                                    <span class="d-block ml-5 accordion_arrow" style="position: relative;left: 34px;">
                                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M15.001 8.3332L13.826 7.1582L10.001 10.9749L6.17598 7.1582L5.00098 8.3332L10.001 13.3332L15.001 8.3332Z"
                                                                fill="#000070" />
                                                        </svg>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="d-flex flex-row justify-content-around align-items-center mt-lg-5">
                                                <div class="mr-3">
                                                    <div class="d-flex flex-row justify-content-center align-items-center accordion_cards">
                                                        <span class="d-block">Address verification</span>
                                                        <span class="d-block ml-5 accordion_arrow" style="position: relative;left: 22px;">
                                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M15.001 8.3332L13.826 7.1582L10.001 10.9749L6.17598 7.1582L5.00098 8.3332L10.001 13.3332L15.001 8.3332Z"
                                                                    fill="#000070" />
                                                            </svg>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-3">
                                                    <div class="d-flex flex-row justify-content-center align-items-center accordion_cards">
                                                        <span class="d-block">ID verification</span>
                                                        <span class="d-block ml-5 accordion_arrow" style="position: relative;left: 40px;">
                                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M15.001 8.3332L13.826 7.1582L10.001 10.9749L6.17598 7.1582L5.00098 8.3332L10.001 13.3332L15.001 8.3332Z"
                                                                    fill="#000070" />
                                                            </svg>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div> --}}


                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="card card-body d-flex flex-row justify-content-between mt-5 px-5"
                    style="border-radius: 10px;">
                    <div class="d-flex flex-column justify-content-center align-items-start">
                        <div style="line-height: 32px;">LTC/NGN</div>
                        <div style="color: #000070;">15,688.91NGN</div>
                    </div>
                    <div class="d-flex flex-column justify-content-center align-items-start">
                        <div style="line-height: 32px;">ETH/NGN</div>
                        <div style="color: #000070;">15,688.91NGN</div>
                    </div>
                    <div class="d-flex flex-column justify-content-center align-items-start">
                        <div style="line-height: 32px;">XRP/NGN</div>
                        <div style="color: #000070;">15,688.91NGN</div>
                    </div>
                    <div class="d-flex flex-column justify-content-center align-items-start">
                        <div style="line-height: 32px;">USDT/NGN</div>
                        <div style="color: #000070;">15,688.91NGN</div>
                    </div>
                    <div class="d-flex flex-column justify-content-center align-items-start">
                        <div style="line-height: 32px;">BTC/NGN</div>
                        <div style="color: #000070;">150,688,903.91NGN</div>
                    </div>
                </div>
            </div>

            {{-- @include('layouts.partials.live-feeds') --}}
        </div>
    </div>
</div>

@endsection