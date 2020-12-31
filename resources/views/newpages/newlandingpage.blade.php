@extends('layouts.landing')
@section('content')

@include('layouts.partials.landingpage_nav')
<!--Main Content-->


<div class="d-none d-lg-flex" style="position: absolute; margin-top: -190px; z-index: -1; ">
    <img src="landingpage_assets/img/Vector.svg">
</div>

<div class="d-none d-lg-flex" style="position:; margin-top: 0px; z-index: -1;">
    <img src="landingpage_assets/img/Vectorsmall.svg">
</div>

<div class="d-none d-lg-block" style="position: absolute;top:12%;right:40%;">
    <span>
        <svg width="10" height="102" viewBox="0 0 10 102" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g opacity="0.75">
                <path opacity="0.75"
                    d="M4.73001 101.779C3.47726 101.779 2.27567 101.312 1.38891 100.48C0.502155 99.648 0.0026485 98.5194 0 97.3418V4.51835C0 3.93441 0.122341 3.3562 0.360046 2.81672C0.59775 2.27724 0.946154 1.78705 1.38537 1.37415C1.82459 0.961245 2.34605 0.633711 2.91992 0.410249C3.49379 0.186788 4.10885 0.0717773 4.73001 0.0717773C5.35069 0.071776 5.96526 0.186852 6.53857 0.410429C7.11188 0.634005 7.63264 0.961688 8.07107 1.37471C8.50949 1.78774 8.85697 2.278 9.09359 2.81743C9.3302 3.35686 9.45132 3.93486 9.45 4.51835V97.3418C9.45 98.5186 8.9527 99.6472 8.06753 100.479C7.18236 101.311 5.98183 101.779 4.73001 101.779Z"
                    fill="url(#paint0_linear)" />
                <path opacity="0.75"
                    d="M4.72989 99.7953C4.38893 99.7966 4.05105 99.7345 3.73566 99.6127C3.42028 99.4909 3.13357 99.3117 2.89201 99.0855C2.65045 98.8593 2.45877 98.5905 2.32799 98.2945C2.19721 97.9984 2.1299 97.6811 2.1299 97.3605V6.27628C2.12858 5.95496 2.19493 5.63656 2.32512 5.33945C2.45531 5.04235 2.64678 4.77241 2.88847 4.54519C3.13017 4.31798 3.4173 4.13799 3.73334 4.0156C4.04939 3.8932 4.38808 3.83084 4.72989 3.83208C5.07085 3.83208 5.40847 3.89536 5.72335 4.01831C6.03824 4.14125 6.32417 4.32145 6.56481 4.54854C6.80544 4.77562 6.99602 5.04514 7.12559 5.34163C7.25516 5.63812 7.32118 5.95575 7.31987 6.27628V97.3605C7.31987 98.0063 7.04698 98.6256 6.56127 99.0822C6.07555 99.5388 5.41679 99.7953 4.72989 99.7953Z"
                    fill="url(#paint1_linear)" />
            </g>
            <defs>
                <linearGradient id="paint0_linear" x1="4.73" y1="100.839" x2="4.73" y2="33.7078"
                    gradientUnits="userSpaceOnUse">
                    <stop stop-color="#79D3FE" />
                    <stop offset="1" stop-color="#79D3FE" stop-opacity="0" />
                </linearGradient>
                <linearGradient id="paint1_linear" x1="2380.34" y1="19953.6" x2="2380.34" y2="11906.2"
                    gradientUnits="userSpaceOnUse">
                    <stop stop-color="#79D3FE" />
                    <stop offset="1" stop-color="#79D3FE" stop-opacity="0" />
                </linearGradient>
            </defs>
        </svg>
    </span>
</div>


<!-- First landing image container -->
<div class="container mt-4 mt-lg-0">
    <div class="d-flex align-items-center">
        <div class="row">
            <div class="col-10 mx-auto col-md-4">
                <span class="h3 h1-lg">Experience total Freedom with <br> <span
                        style="color: #000070;font-weight:700;">Bitcoins</span></span>
                <div id="first_icon">
                    <img src="/icon2.png" />
                </div>
            </div>
            <div class="col-12 mt-5 mt-md-0 col-md-8">
                <div class="ml-lg-5">
                    <div class="d-none d-md-block">
                        <img src="landingpage_assets/img/Group 21.png">
                    </div>
                    <div class="mobile_app_icon">
                        <img src="landingpage_assets/img/Group 22.png">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="second_icon" style="position: relative;left:25%;top:70px;">
        <img src="/icon3.png" />
    </div>

    <div class="d-none d-lg-block" style="position: absolute;right:6%;top:91%;">
        <img src="/icon4.png" />
    </div>
</div>




<div class="container h-100 currency_calculator_container">
    <div class="icon5_block">
        <img src="/icon5.png" />
    </div>
    <div class="card col-10 mx-auto pb-4" style="box-shadow: 0px 4px 10px rgba(191, 191, 191, 0.25);border-color:#fff;">
        <p class="my-4" style="text-align: center;">Check your naira equivalence in Bitcoin and Dollar</p>
        <div class="d-flex flex-column flex-lg-row justify-content-between pt-3"
            style="text-align: center; justify-content: center;border: 0.5px solid #E6E6E6;">
            <div class="form-group col-md-3">
                <input type="number" class="form-control form-control-style" id="ngn" placeholder="0.00">
                <label for="input">NGN</label>
            </div>
            <div>
                <svg width="31" height="31" viewBox="0 0 31 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M20.6667 3.875L25.8334 9.04167L20.6667 14.2083" stroke="#E5E5E5" stroke-width="0.916667"
                        stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M12.9167 9.04169H25.8334" stroke="#E5E5E5" stroke-width="0.916667" stroke-linecap="round"
                        stroke-linejoin="round" />
                    <path d="M10.3334 16.7917L5.16675 21.9584L10.3334 27.125" stroke="#E5E5E5" stroke-width="0.916667"
                        stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M5.16675 21.9583H16.7917" stroke="#E5E5E5" stroke-width="0.916667" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </div>
            <div class="form-group col-md-3">
                <input type="number" class="form-control form-control-style2" id="btc" placeholder="0.00">
                <label for="input">BTC</label>
            </div>
            <div>
                <svg width="31" height="31" viewBox="0 0 31 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M20.6667 3.875L25.8334 9.04167L20.6667 14.2083" stroke="#E5E5E5" stroke-width="0.916667"
                        stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M12.9167 9.04169H25.8334" stroke="#E5E5E5" stroke-width="0.916667" stroke-linecap="round"
                        stroke-linejoin="round" />
                    <path d="M10.3334 16.7917L5.16675 21.9584L10.3334 27.125" stroke="#E5E5E5" stroke-width="0.916667"
                        stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M5.16675 21.9583H16.7917" stroke="#E5E5E5" stroke-width="0.916667" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </div>
            <div class="form-group col-md-3">
                <input type="number" class="form-control form-control-style3" id="usd" placeholder="0.00">
                <label for="input">USD</label>
            </div>
        </div>
    </div>
</div>




<div class="container h-100">
    <div class="row">
        <div class="col-md-5 ml-md-auto col-sm-4 col-xl-5 col-lg-5 bank_on_terms" style="margin-top: auto; margin-bottom: auto;">
            <h3 class="bank_on_terms_text">
                You Got It!<br>
                Bank on your Terms.
            </h3>
            <div class="d-inline-flex" style="margin-top: 3%;">
                <img src="landingpage_assets/img/approval 1.svg" style="width: 39px; height: 39px;"><br>
                <p style="margin: auto; ">Receive, store, spend, and transfer with ease.</p>
            </div>
            <div class="d-inline-flex">
                <img src="landingpage_assets/img/approval 1.svg" style="width: 39px; height: 39px;"><br>
                <p style="margin: auto; ">Pay bills in seconds and cater for recurring bills</p>
            </div>
            <div class="d-inline-flex">
                <img src="landingpage_assets/img/approval 1.svg" style="width: 39px; height: 39px;"><br>
                <p style="margin: auto; ">Get airtime top instantly </p>
            </div>
        </div>
        <div class="col-md-4 mx-auto mobile_go_up">
            <div class="d-inline-flex">
                <img class="img-fluid mobile_1" src="/android.png">
                {{-- <img class="img-fluid mobile_2" src="landingpage_assets/img/Data 1 1.svg"> --}}
            </div>

        </div>
    </div>
    <div class="icon6_block d-none d-lg-block">
        <img src="/icon5.png" />
    </div>
</div>




<div class="container " style="text-align: center; padding: 10%; color: #000070; ">
    <div>
        <h3 class="mobile_text" style="color: #030118;">
            We offer you Ease and Convenience
        </h3>
        <div style="width: 90px;height: 0px;border: 5px solid #E7B548;position:relative;left:120px;"></div>
    </div>
</div>


<div class="container h-100">
    <div style="position: absolute;right:14%;">
        <img src="/icon3.png" />
    </div>
    <div class="row">
        <div class="col-md-5 ml-md-5 col-sm-4 move_col_down" style="margin-top: auto; margin-bottom: auto;">
            <h3 class="mobile_text2">
                Explore the World of Giftcard
            </h3>
            <div class="d-inline-flex" style="margin-top: 3%;">
                <img src="landingpage_assets/img/approval 1.svg" style="width: 39px; height: 39px;"><br>
                <p style="margin: auto; ">Exchange your gift cards to cash at the best rates</p>
            </div>
            <div class="d-inline-flex">
                <img src="landingpage_assets/img/approval 1.svg" style="width: 39px; height: 39px;"><br>
                <p style="margin: auto; ">Buy Gift Cards</p>
            </div><br>
            <div class="d-inline-flex">
                <img src="landingpage_assets/img/approval 1.svg" style="width: 39px; height: 39px;"><br>
                <p style="margin: auto; ">Sell Gift Cards </p>
            </div>
        </div>
        <div class="col-md-4 col-lg-5 move_col_up">
            <div class="d-inline-flex">
                <img class="img-fluid mobile_1" src="/android.png">
                {{-- <img class="img-fluid mobile_3" src="landingpage_assets/img/Data 1 1.svg" style="position: absolute;left: 70%; margin-top: 28%;"> --}}
            </div>

        </div>
    </div>
</div>


<div class="container h-100" style="margin-top: 10%; margin-bottom: 10%;">
    <div class="row">
        <div class="col-12 col-sm-6">
            <img src="/virtual_cards.png" />
        </div>
        <div class="col-12 col-sm-6" style="margin-top: auto; margin-bottom: auto; text-align: right;">
            <div style="position: relative;right:2%;">
                <img src="/icon3.png" />
            </div>
            <h3>
                Endless Possibilities with our Debit cards
                <br>(Virtual/physical)
            </h3>
            <div class="mt-3" style="position: absolute;right:35%;">
                <img src="/icon2.png" />
            </div>
        </div>
    </div>



    <div class="container" style="margin-top: 140px;">
        <div class="text-center">
            <h3 style="color: #030118;">
                Featured on
            </h3>
            <div style="width: 70px;height: 0px;border: 5px solid #E7B548;position:relative;left:40%;"></div>
        </div>
        <div class="d-flex justify-content-center align-items-center mt-5">
            <div class="mx-3 mx-lg-5">
                <span>
                    <svg width="100" height="28" viewBox="0 0 100 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0)">
                            <path
                                d="M13.8328 0.5C11.5903 0.50086 9.38165 1.04052 7.39695 2.07253C5.41225 3.10453 3.711 4.59795 2.43968 6.42419C1.16837 8.25044 0.36508 10.3548 0.0989839 12.5561C-0.167112 14.7573 0.111958 16.9895 0.912166 19.0605C1.71237 21.1315 3.00974 22.9792 4.69256 24.4445C6.37538 25.9098 8.39323 26.9488 10.5724 27.472C12.7515 27.9953 15.0266 27.9871 17.2019 27.4482C19.3771 26.9092 21.3872 25.8557 23.0592 24.3784C21.4191 25.4664 19.4892 26.0487 17.514 26.0517C14.217 26.0517 11.2679 24.4348 9.28454 21.8838C7.75286 20.1026 6.76117 17.4643 6.69367 14.5026V13.8559C6.76117 10.8942 7.75286 8.25585 9.27934 6.46959C11.2679 3.92366 14.217 2.31192 17.514 2.31192C19.5389 2.31192 21.4393 2.91761 23.0592 3.98526C20.6189 1.82943 17.4102 0.515399 13.8899 0.5H13.838H13.8328Z"
                                fill="url(#paint0_linear)" />
                            <path
                                d="M9.27832 6.46969C10.5504 4.99141 12.1859 4.09828 13.9824 4.09828C18.0114 4.09828 21.2773 8.61525 21.2773 14.1793C21.2773 19.7486 18.0114 24.2604 13.9824 24.2604C12.1911 24.2604 10.5556 23.3673 9.28351 21.889C11.2669 24.4349 14.216 26.0518 17.513 26.0518C19.5379 26.0518 21.4382 25.441 23.0582 24.3785C24.5116 23.0964 25.6742 21.5248 26.4697 19.7671C27.2652 18.0093 27.6756 16.105 27.674 14.1793C27.674 10.1243 25.8931 6.48509 23.0634 3.98022C21.4235 2.89165 19.4935 2.30924 17.5182 2.30688C14.2212 2.30688 11.2721 3.92376 9.2887 6.47482"
                                fill="url(#paint1_linear)" />
                            <path
                                d="M42.3209 2.9895C37.2949 2.9895 33.2087 7.22417 33.2087 12.4341C33.2087 17.6389 37.2949 21.8787 42.3209 21.8787C47.3469 21.8787 51.433 17.6389 51.433 12.4341C51.433 7.22417 47.3469 2.9895 42.3209 2.9895ZM42.3209 4.40619C46.5577 4.40106 50 8.00437 50 12.4341C50 16.8587 46.5577 20.4569 42.3209 20.4569C38.0893 20.4569 34.6418 16.8587 34.6418 12.4341C34.6418 8.00437 38.0841 4.40619 42.3209 4.40619ZM59.0706 7.97357C57.1807 7.97357 55.5556 8.85644 54.4497 10.2475V8.28668H52.9439V25.3691H54.4497V19.2455C54.9903 19.9512 55.6888 20.5237 56.4904 20.9181C57.2921 21.3126 58.1752 21.5184 59.0706 21.5194C62.5753 21.5194 65.431 18.4807 65.431 14.7439C65.431 11.0123 62.5753 7.97357 59.0706 7.97357ZM93.863 7.97357C90.3583 7.97357 87.5026 11.0123 87.5026 14.749C87.5026 18.4807 90.3583 21.5245 93.863 21.5245C95.7529 21.5245 97.378 20.6365 98.4839 19.2455V21.1652H99.9897V8.28155H98.4839V10.2526C97.9436 9.5466 97.2452 8.97391 96.4434 8.5794C95.6417 8.18489 94.7585 7.97929 93.863 7.97871V7.97357ZM73.1464 7.99924C69.6314 7.99924 66.7809 11.0328 66.7809 14.7696C66.7809 18.5012 69.6366 21.5348 73.1413 21.5348C75.649 21.5348 77.8297 19.9846 78.863 17.7313L77.5649 16.9819C76.7965 18.7989 75.1039 20.0616 73.1464 20.0616C70.6023 20.0616 68.5099 17.9417 68.2918 15.2367H79.4912C79.5016 15.0827 79.5068 14.9287 79.5068 14.7644C79.5068 10.7762 76.89 7.98897 73.1464 7.98897L73.1361 7.99924H73.1464ZM86.4694 8.03004C85.9502 8.05057 85.4621 8.18402 85.0052 8.4304C84.0719 8.92238 83.3439 9.72389 82.9491 10.694C82.944 10.7197 82.9284 10.7454 82.918 10.7608H82.9024V8.36881H81.3967V21.1755H82.9024V21.0831V14.4616C82.8857 13.6173 83.0448 12.7787 83.3697 11.9978C83.642 11.3082 84.0762 10.6924 84.6366 10.2013C84.9515 9.91667 85.3281 9.70717 85.7376 9.58883C86.1471 9.47049 86.5786 9.44646 86.999 9.51858L87.4403 9.60071C87.5234 9.17468 87.7311 8.63572 87.8193 8.20969C87.404 8.09163 86.8536 8.0249 86.4642 8.0403L86.4694 8.03004ZM59.0654 9.44672C61.755 9.44672 63.9408 11.8284 63.9408 14.749C63.9408 17.6748 61.7498 20.0462 59.0603 20.0462C56.3707 20.0462 54.1901 17.6697 54.1901 14.749C54.1901 11.8233 56.3759 9.44672 59.0654 9.44672ZM93.863 9.44672C96.5525 9.44672 98.7383 11.8284 98.7383 14.749C98.7383 17.6748 96.5473 20.0462 93.8578 20.0462C91.1683 20.0462 88.9824 17.6697 88.9824 14.749C88.9824 11.8233 91.1735 9.44672 93.863 9.44672ZM73.1361 9.46725C75.7944 9.46725 77.6532 11.2381 77.9647 13.9483H68.323C68.6864 11.4178 70.7062 9.46725 73.1361 9.46725Z"
                                fill="black" />
                        </g>
                        <defs>
                            <linearGradient id="paint0_linear" x1="11.5275" y1="0.946565" x2="11.5275" y2="27.4581"
                                gradientUnits="userSpaceOnUse">
                                <stop stop-color="#D9A7AB" />
                                <stop offset="0.3" stop-color="#FF1B2D" />
                                <stop offset="0.6" stop-color="#FF1B2D" />
                                <stop offset="1" stop-color="#A70014" />
                            </linearGradient>
                            <linearGradient id="paint1_linear" x1="18.4787" y1="2.50707" x2="18.4787" y2="25.9594"
                                gradientUnits="userSpaceOnUse">
                                <stop stop-color="#9C0000" />
                                <stop offset="0.7" stop-color="#FF4B4B" />
                                <stop offset="1" stop-color="#FF4B4B" />
                            </linearGradient>
                            <clipPath id="clip0">
                                <rect width="100" height="27.3585" fill="white" transform="translate(0 0.5)" />
                            </clipPath>
                        </defs>
                    </svg>
                </span>
            </div>
            <div class="mx-3 mx-lg-5">
                <span>
                    <svg width="90" height="90" viewBox="0 0 90 90" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M11.7139 78.7498V11.3398H78.3749V64.1698L78.1079 64.4668C74.7615 67.8598 71.4062 71.2438 68.0509 74.6278C66.6892 76.0048 65.3275 77.3728 63.9569 78.7498H11.7139ZM55.5998 37.3138H58.0117C58.7059 37.3138 58.9017 37.5028 58.9106 38.2228C58.9106 39.4198 58.9017 40.6258 58.9017 41.8228C58.9017 42.5248 59.142 42.7948 59.8273 42.7948C62.3371 42.8038 64.838 42.8038 67.3478 42.7948C68.2111 42.7858 68.4069 42.5518 68.4069 41.6788C68.4069 37.8808 68.4158 34.0918 68.4158 30.2938C68.4158 29.2588 68.22 29.0428 67.2054 29.0428C62.9067 29.0338 58.5991 29.0428 54.3004 29.0338C53.7931 29.0338 53.3481 29.1598 52.9654 29.5378C51.0163 31.5088 49.0761 33.4978 47.0914 35.4238C46.326 36.1618 46.0679 37.0078 46.0679 38.0338C46.0768 45.8008 46.0768 53.5678 46.0768 61.3438C46.0768 61.5508 46.0857 61.7668 46.0946 61.9738C46.148 62.6488 46.148 62.6488 46.8333 62.6488C51.399 62.6488 55.9736 62.6398 60.5393 62.6578C61.1 62.6578 61.545 62.4868 61.9366 62.0908C63.8501 60.1648 65.7725 58.2478 67.686 56.3218C68.1221 55.8808 68.3535 55.3498 68.3535 54.7108C68.3624 51.7678 68.3802 48.8338 68.398 45.8908C68.398 45.1528 68.1577 44.9278 67.4279 44.9278C65.6479 44.9278 63.8679 44.9368 62.0879 44.9368C61.3136 44.9368 60.5482 44.9188 59.7739 44.9278C59.1687 44.9278 58.9462 45.1528 58.9017 45.7558C58.8928 45.9178 58.8928 46.0888 58.8928 46.2508C58.8928 49.0678 58.8928 51.8938 58.9017 54.7108V55.1968H55.5909C55.5998 49.2478 55.5998 43.3258 55.5998 37.3138ZM21.762 29.1868V29.6458C21.762 37.5028 21.7709 45.3598 21.7709 53.2168C21.7709 54.3688 22.1091 55.3768 22.8834 56.2138C24.6456 58.1128 26.4345 59.9938 28.2145 61.8838C28.7574 62.4598 29.3893 62.6938 30.1992 62.6938C34.578 62.6668 38.9479 62.6758 43.3267 62.6758C43.9853 62.6758 44.0031 62.6578 44.012 61.9648L44.1188 43.7038C44.1277 42.8038 43.9586 42.6418 43.0508 42.6418C40.6923 42.6328 38.3338 42.6328 35.9753 42.6148C34.7204 42.6058 34.5335 42.7858 34.5424 44.0548C34.5602 47.0428 34.5602 50.0218 34.5691 53.0098C34.5691 53.4508 34.5335 53.8918 34.5157 54.3238H31.3295V39.1588H31.8279C35.4324 39.1498 39.0369 39.1498 42.6414 39.1408C42.7749 39.1408 42.9084 39.1498 43.0419 39.1318C43.5403 39.0688 43.9764 38.7808 43.9764 38.4568C43.9942 36.7378 43.9853 35.0098 43.9853 33.2458C43.7895 33.2278 43.567 33.2008 43.3534 33.2008C40.0426 33.1828 36.7407 33.1738 33.4299 33.1468C33.0116 33.1468 32.6022 33.1288 32.1839 33.0748C31.6054 32.9938 31.3562 32.7688 31.3028 32.1838C31.2316 31.4998 31.2227 30.8068 31.2227 30.1228C31.2138 29.2768 31.0358 29.0698 30.1725 29.0698H22.7855C22.4473 29.0878 22.1269 29.1508 21.762 29.1868Z" fill="#F13204"/>
                        <path d="M63.9659 78.7501C65.3276 77.3731 66.6982 76.0051 68.0599 74.6281C71.4152 71.2441 74.7616 67.8511 78.1169 64.4671C78.2148 64.3681 78.2949 64.2691 78.3839 64.1701C78.4106 64.1701 78.4373 64.1701 78.4729 64.1611V78.8311H63.9659C63.957 78.8131 63.957 78.7861 63.9659 78.7501Z" fill="#FEFEFE"/>
                        <path d="M78.464 64.1701C78.4373 64.1701 78.4106 64.1791 78.375 64.1791V32.3011V11.3491C78.4017 11.3671 78.4551 11.3761 78.464 11.3941C78.4729 11.5111 78.464 11.6371 78.464 11.7541V64.1701Z" fill="#F45E3A"/>
                        <path d="M63.9658 78.75C63.9569 78.777 63.9569 78.804 63.9569 78.84H12.1144C11.9809 78.84 11.8474 78.786 11.7139 78.75H63.9658Z" fill="#F46543"/>
                        <path d="M55.5999 37.3141V55.1881H58.9107V54.7021C58.9107 51.8851 58.9107 49.0591 58.9018 46.2421C58.9018 46.0801 58.8929 45.9091 58.9107 45.7471C58.9552 45.1441 59.1777 44.9191 59.7829 44.9191C60.5572 44.9191 61.3226 44.9281 62.0969 44.9281C63.8769 44.9281 65.6569 44.9191 67.4369 44.9191C68.1667 44.9191 68.407 45.1441 68.407 45.8821C68.3981 48.8251 68.3714 51.7591 68.3625 54.7021C68.3625 55.3411 68.1311 55.8721 67.695 56.3131C65.7815 58.2391 63.8591 60.1561 61.9456 62.0821C61.554 62.4691 61.109 62.6491 60.5483 62.6491C55.9826 62.6401 51.408 62.6401 46.8423 62.6401C46.157 62.6401 46.157 62.6401 46.1036 61.9651C46.0858 61.7581 46.0858 61.5421 46.0858 61.3351C46.0858 53.5681 46.0947 45.8011 46.0769 38.0251C46.0769 36.9991 46.335 36.1531 47.1004 35.4151C49.0851 33.4801 51.0253 31.5001 52.9744 29.5291C53.3482 29.1511 53.8021 29.0251 54.3094 29.0251C58.6081 29.0251 62.9157 29.0251 67.2144 29.0341C68.229 29.0341 68.4248 29.2591 68.4248 30.2851C68.4248 34.0831 68.4248 37.8721 68.4159 41.6701C68.4159 42.5521 68.2112 42.7861 67.3568 42.7861C64.847 42.8041 62.3461 42.7951 59.8363 42.7861C59.151 42.7861 58.9107 42.5251 58.9107 41.8141C58.9107 40.6171 58.9196 39.4111 58.9196 38.2141C58.9196 37.4941 58.7238 37.3051 58.0207 37.3051C57.2108 37.3141 56.4098 37.3141 55.5999 37.3141ZM21.7621 29.1871C22.1359 29.1511 22.4563 29.0881 22.7767 29.0881C25.242 29.0791 27.6984 29.0791 30.1637 29.0881C31.027 29.0881 31.205 29.2861 31.2139 30.1411C31.2228 30.8251 31.2317 31.5181 31.294 32.2021C31.3563 32.7871 31.5966 33.0121 32.1751 33.0931C32.5845 33.1471 33.0028 33.1651 33.4211 33.1651L43.3446 33.2191C43.5671 33.2191 43.7807 33.2461 43.9765 33.2641C43.9765 35.0281 43.9854 36.7471 43.9676 38.4751C43.9676 38.8081 43.5315 39.0961 43.0331 39.1501C42.8996 39.1681 42.7661 39.1591 42.6326 39.1591C39.0281 39.1681 35.4236 39.1681 31.8191 39.1771H31.3207V54.3421H34.5069C34.5247 53.9101 34.5692 53.4691 34.5603 53.0281C34.5603 50.0401 34.5514 47.0611 34.5336 44.0731C34.5247 42.8041 34.7116 42.6241 35.9665 42.6331C38.325 42.6511 40.6835 42.6511 43.042 42.6601C43.9498 42.6601 44.1189 42.8221 44.11 43.7221L44.0032 61.9832C44.0032 62.6852 43.9765 62.6941 43.3179 62.6941C38.9391 62.6941 34.5692 62.6761 30.1904 62.7121C29.3894 62.7211 28.7486 62.4781 28.2057 61.9021C26.4257 60.0121 24.6368 58.1311 22.8746 56.2321C22.1003 55.3951 21.7621 54.3871 21.7621 53.2351C21.771 45.3781 21.7621 37.5211 21.7532 29.6641C21.7621 29.5201 21.7621 29.3851 21.7621 29.1871Z" fill="white"/>
                        </svg>                        
                </span>
            </div>
            <div class="mx-3 mx-lg-5">
                <span>
                    <svg width="100" height="44" viewBox="0 0 100 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21.506 42.0843L17.2217 35.8669V19.6452V3.42358H29.1929L31.2277 7.04093H21.506V9.64093H32.3581L34.732 13.7105H21.506V42.0843Z" fill="black"/>
                        <path d="M23.3667 42.7041V15.5737H40.3232L38.6275 19.6433H27.8884V21.9042H37.8362L36.3666 25.9737H27.8884V39.765L23.3667 42.7041Z" fill="black"/>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M0.271729 12.2142L11.463 1.13599L15.6456 3.39684V23.6315L11.463 20.5794V16.3968L5.69777 13.1185L3.32388 15.6055L0.271729 12.2142ZM11.3499 7.86203V12.3838L7.95863 10.6881L11.3499 7.86203Z" fill="black"/>
                        <path d="M55.2064 9.24805H53.6485L53.242 8.1545H50.4743L50.0678 9.24805H48.5098L50.9969 2.79322H52.7194L55.2064 9.24805ZM52.8646 6.94482L51.8582 4.16741L50.8517 6.94482H52.8646ZM57.1769 9.24805H55.8027V2.79322H60.3704V4.0029H57.1769V5.35773H60.3027V6.5674H57.1769V9.24805ZM66.5667 9.24805H64.9796L63.7118 6.9545H62.7054V9.24805H61.3312V2.79322H64.3505C65.015 2.79322 65.5441 2.98677 65.9376 3.37386C66.3312 3.76096 66.5279 4.26096 66.5279 4.87386C66.5279 5.4029 66.386 5.83193 66.1021 6.16095C65.8247 6.48998 65.4925 6.69966 65.1054 6.78998L66.5667 9.24805ZM64.157 5.74483C64.4344 5.74483 64.6634 5.66741 64.8441 5.51257C65.0247 5.35128 65.115 5.13515 65.115 4.86419C65.115 4.60612 65.0247 4.39967 64.8441 4.24483C64.6634 4.08354 64.4344 4.0029 64.157 4.0029H62.7054V5.74483H64.157ZM68.9236 9.24805H67.5494V2.79322H68.9236V9.24805ZM73.3408 9.36417C72.3538 9.36417 71.5312 9.0545 70.8731 8.43514C70.2215 7.80934 69.8957 7.00611 69.8957 6.02548C69.8957 5.03838 70.2215 4.23515 70.8731 3.6158C71.5312 2.99645 72.3538 2.68677 73.3408 2.68677C74.5473 2.68677 75.4441 3.21257 76.0312 4.26419L74.8505 4.84483C74.7086 4.57386 74.4989 4.35128 74.2215 4.17709C73.9505 3.99644 73.657 3.90612 73.3408 3.90612C72.7537 3.90612 72.2667 4.10612 71.8796 4.50612C71.4989 4.90612 71.3086 5.41257 71.3086 6.02548C71.3086 6.63837 71.4989 7.14482 71.8796 7.54482C72.2667 7.94482 72.7537 8.14482 73.3408 8.14482C73.657 8.14482 73.9505 8.05772 74.2215 7.88353C74.4989 7.70288 74.7086 7.47708 74.8505 7.20611L76.0312 7.77708C75.4312 8.83514 74.5344 9.36417 73.3408 9.36417ZM82.8021 9.24805H81.2441L80.8376 8.1545H78.0699L77.6634 9.24805H76.1054L78.5925 2.79322H80.315L82.8021 9.24805ZM80.4602 6.94482L79.4537 4.16741L78.4473 6.94482H80.4602Z" fill="black"/>
                        <path d="M50.5614 24.1189H49.1873V17.6641H53.755V18.8737H50.5614V20.2286H53.6873V21.4383H50.5614V24.1189ZM56.0898 24.1189H54.7156V17.6641H56.0898V24.1189ZM63.149 24.1189H61.8232L58.7457 19.9092V24.1189H57.3716V17.6641H58.7845L61.7748 21.7189V17.6641H63.149V24.1189ZM67.2957 24.1189H65.9119V18.8737H64.0248V17.6641H69.1732V18.8737H67.2957V24.1189ZM74.6221 24.1189H70.0543V17.6641H74.6221V18.8737H71.4285V20.2286H74.5544V21.4383H71.4285V22.9092H74.6221V24.1189ZM78.8318 24.235C77.8447 24.235 77.0221 23.9254 76.364 23.306C75.7124 22.6802 75.3866 21.877 75.3866 20.8963C75.3866 19.9092 75.7124 19.106 76.364 18.4866C77.0221 17.8673 77.8447 17.5576 78.8318 17.5576C80.0382 17.5576 80.935 18.0834 81.5221 19.135L80.3414 19.7157C80.1995 19.4447 79.9898 19.2221 79.7124 19.0479C79.4414 18.8673 79.1479 18.777 78.8318 18.777C78.2447 18.777 77.7576 18.977 77.3705 19.377C76.9898 19.777 76.7995 20.2834 76.7995 20.8963C76.7995 21.5092 76.9898 22.0157 77.3705 22.4157C77.7576 22.8157 78.2447 23.0157 78.8318 23.0157C79.1479 23.0157 79.4414 22.9286 79.7124 22.7544C79.9898 22.5737 80.1995 22.3479 80.3414 22.077L81.5221 22.6479C80.9221 23.706 80.0253 24.235 78.8318 24.235ZM88.1463 24.1189H86.7624V21.3996H83.714V24.1189H82.3398V17.6641H83.714V20.1996H86.7624V17.6641H88.1463V24.1189Z" fill="black"/>
                        <path d="M50.5614 38.9895H49.1873V32.5347H53.755V33.7444H50.5614V35.0992H53.6873V36.3089H50.5614V38.9895ZM57.7834 39.1056C56.8027 39.1056 55.9931 38.7927 55.3544 38.1669C54.7221 37.5411 54.406 36.7411 54.406 35.7669C54.406 34.7927 54.7221 33.9927 55.3544 33.3669C55.9931 32.7411 56.8027 32.4282 57.7834 32.4282C58.7576 32.4282 59.5608 32.7444 60.1931 33.3766C60.8318 34.0024 61.1511 34.7992 61.1511 35.7669C61.1511 36.7347 60.8318 37.5347 60.1931 38.1669C59.5608 38.7927 58.7576 39.1056 57.7834 39.1056ZM56.3608 37.2863C56.7221 37.6863 57.1963 37.8863 57.7834 37.8863C58.3705 37.8863 58.8415 37.6863 59.1963 37.2863C59.5576 36.8863 59.7382 36.3798 59.7382 35.7669C59.7382 35.154 59.5576 34.6476 59.1963 34.2476C58.8415 33.8476 58.3705 33.6476 57.7834 33.6476C57.1898 33.6476 56.7124 33.8476 56.3511 34.2476C55.9963 34.6476 55.8189 35.154 55.8189 35.7669C55.8189 36.3798 55.9995 36.8863 56.3608 37.2863ZM67.1963 38.3798C66.706 38.8637 65.9834 39.1056 65.0286 39.1056C64.0737 39.1056 63.3479 38.8637 62.8511 38.3798C62.3608 37.8895 62.1156 37.2314 62.1156 36.4056V32.5347H63.5092V36.3669C63.5092 36.8315 63.6415 37.2024 63.906 37.4798C64.1705 37.7508 64.5447 37.8863 65.0286 37.8863C65.5124 37.8863 65.8834 37.7508 66.1415 37.4798C66.406 37.2024 66.5382 36.8315 66.5382 36.3669V32.5347H67.9415V36.4056C67.9415 37.2314 67.6931 37.8895 67.1963 38.3798ZM75.0001 38.9895H73.6742L70.5968 34.7798V38.9895H69.2226V32.5347H70.6355L73.6259 36.5895V32.5347H75.0001V38.9895ZM78.8275 38.9895H76.2726V32.5347H78.8178C79.8307 32.5347 80.6533 32.8314 81.2855 33.425C81.9242 34.0185 82.2436 34.796 82.2436 35.7573C82.2436 36.7314 81.9275 37.5153 81.2952 38.1089C80.663 38.696 79.8404 38.9895 78.8275 38.9895ZM78.8178 37.7798C79.4372 37.7798 79.9275 37.5863 80.2888 37.1992C80.6565 36.8121 80.8404 36.3314 80.8404 35.7573C80.8404 35.1702 80.663 34.6895 80.3081 34.3153C79.9597 33.9347 79.4662 33.7444 78.8275 33.7444H77.6468V37.7798H78.8178ZM88.4544 38.9895H86.8673L85.5995 36.696H84.5931V38.9895H83.2189V32.5347H86.2382C86.9027 32.5347 87.4318 32.7282 87.8253 33.1153C88.2189 33.5024 88.4157 34.0024 88.4157 34.6153C88.4157 35.1444 88.2737 35.5734 87.9898 35.9024C87.7124 36.2314 87.3802 36.4411 86.9931 36.5315L88.4544 38.9895ZM86.0447 35.4863C86.3221 35.4863 86.5511 35.4089 86.7318 35.254C86.9124 35.0927 87.0027 34.8766 87.0027 34.6056C87.0027 34.3476 86.9124 34.1411 86.7318 33.9863C86.5511 33.825 86.3221 33.7444 86.0447 33.7444H84.5931V35.4863H86.0447ZM92.4415 38.9895H91.0673V36.3476L88.5898 32.5347H90.1576L91.7544 35.1185L93.3511 32.5347H94.9092L92.4415 36.3476V38.9895Z" fill="black"/>
                        </svg>                        
                </span>
            </div>
            <div class="mx-3 mx-lg-5">
                <span>
                    <svg width="100" height="100" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M34.8799 31.48H39.8799V68.52H34.8799V31.48ZM42.5599 31.48H47.5599V68.52H42.5599V31.48Z" fill="#893168"/>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M57.7629 31.4836H62.7536L55.2112 68.5032H50.2205L57.7629 31.4836Z" fill="#893168"/>
                        <path d="M60.38 68.52C61.7608 68.52 62.8801 67.4007 62.8801 66.02C62.8801 64.6393 61.7608 63.52 60.38 63.52C58.9992 63.52 57.8799 64.6393 57.8799 66.02C57.8799 67.4007 58.9992 68.52 60.38 68.52Z" fill="#893168"/>
                        </svg>                        
                </span>
            </div>
        </div>
    </div>


    <footer>
        <div class="container" style="justify-content: center;margin-top:-100px;">
            <div style="position: relative;top:350px;left:-100px;">
                <img src="/footer_bg.png" />
            </div>
            <div class="row" style="position: relative;left:7%;">
                <div class="col-md-3">
                    <h5>Company</h5>
                    <a href="#">About Us</a><br>
                    <a href="#">Services</a><br>
                    <a href="#">Our Company</a><br>
                    <a href="#">Our Team</a><br>
                </div>
                <div class="col-md-3">
                    <h5>Product</h5>
                    <a href="#">Bitcoin</a><br>
                    <a href="#">Gift Card</a><br>
                    <a href="#">Digital Assests</a><br>
                    <a href="#">Paybills</a><br>
                    <a href="#">Airtime</a><br>
                </div>
                <div class="col-md-3">
                    <h5>Legal</h5>
                    <a href="#">Privacy Policy</a><br>
                    <a href="#">Terms and Conditions</a><br>
                </div>
                <div class="col-md-3">
                    <h5>Info</h5>
                    <a href="#">Blog</a><br>
                    <a href="#">User's Guide</a><br>
                </div>
            </div>
        </div>
        <div class="text-center mt-5">© 2020 Dantownms.com. All rights reserved</div>
    </footer>



    @endsection