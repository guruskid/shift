@extends('layouts.landing')
@section('content')

<!-- navbar -->
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

<div class="container">
    <div class="row">
        <div class="col-11 col-md-4 mt-5 mt-md-0 text-center mb-3 mb-lg-4 mx-auto">
            <span class="ask_assistance">Ask for assistance</span>
        </div>
        <div class="col-12 col-lg-9 mb-3 mx-auto pt-3">
            <form action="" method="post">
                @csrf
                <div class="input-group mb-3 col-12 col-lg-9 mx-auto">
                    <span class="faq_search_icon">
                        <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M12.5 21.25C17.3325 21.25 21.25 17.3325 21.25 12.5C21.25 7.66751 17.3325 3.75 12.5 3.75C7.66751 3.75 3.75 7.66751 3.75 12.5C3.75 17.3325 7.66751 21.25 12.5 21.25Z"
                                stroke="#CCCCCC" stroke-width="0.916667" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path d="M24.0341 24.0341L18.75 18.75" stroke="#CCCCCC" stroke-width="0.916667"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                    <input type="text" class="form-control pl-lg-5 faq_search_box"
                        placeholder="Enter your question" aria-label="Recipient's username"
                        aria-describedby="basic-addon2">
                    <div class="input-group-append col-12 col-md-auto">
                        <button class="input-group-text btn text-white border_sm mx-auto mt-3 mt-md-0" id="basic-addon2">Search</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <div class="d-flex justify-content-center align-items-center my-5">
        <div id="finance" class="faq_topic active_faq d-flex flex-column align-items-center justify-content-center mr-1 mx-lg-3">
            <div>
                <img class="img-fluid" src="{{asset('svg/finance_logo.svg')}}" />
            </div>
            <span class="d-block">Finance</span>
        </div>
        <div id="tech" class="faq_topic d-flex flex-column align-items-center justify-content-center mx-1 mx-lg-3">
            <div>
                <img class="img-fluid" src="{{asset('svg/tech_logo.svg')}}" />
            </div>
            <span class="d-block">Tech</span>
        </div>
        <div id="transaction" class="faq_topic d-flex flex-column align-items-center justify-content-center mx-1 mx-lg-3">
            <div>
                <img class="img-fluid" src="{{asset('svg/transaction_logo.svg')}}" />
            </div>
            <span class="d-block">Transaction</span>
        </div>
    </div>


    <div class="row mt-5 faq_tab_contents" id="finance_content">
        <div class="col-10 col-md-7 mx-auto my-2">
            <div onclick="accordion('content-1')" class="d-flex justify-content-between align-items-center px-2 px-lg-3 py-2 py-md-3" style="border: 1px solid #EBEAEA;cursor: pointer;">
                <div>Lorem ipsum dolor sit amet, consectetur adipiscing elit. title 1</div>
                
                <span id="plus-one" style="cursor: pointer;">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 4.16669V15.8334" stroke="#2C3E50" stroke-width="0.916667" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M4.16699 10H15.8337" stroke="#2C3E50" stroke-width="0.916667" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </span>

                <span id="minus-one" style="display: none;">
                    <svg width="14" height="2" viewBox="0 0 14 2" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1.16699 1H12.8337" stroke="#2C3E50" stroke-width="0.916667" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>                        
                </span>
            </div>

            <!-- Faq content -->
            <div class="p-2 my-2" id="content-1" style="display: none;">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit.Lorem ipsum dolor sit amet, consectetur adipiscing elit.Lorem ipsum do
                lor sit amet, consectetur adipiscing elit.Lorem ipsum dolor sit amet, consectetur adipiscing elit.Lorem ipsum dolor sit amet, 
                consectetur adipiscing elit.Lorem ipsum dolor sit amet, consectetur adipiscing elit.Lorem ipsum dolor sit amet, consectetur adipiscing elit
                .Lorem ipsum dolor sit amet, consectetur adipiscing elit.Lorem ipsum dolor sit amet, consectetur adipiscing elit.Lorem ipsu
                m dolor sit amet, consectetur adipiscing elit.Lorem ipsum dolor sit amet, consectetur adipiscing elit.
            </div>
        </div>

        <div class="col-10 col-md-7 mx-auto my-2">
            <div onclick="accordion('content-2')" class="d-flex justify-content-between align-items-center px-2 px-lg-3 py-2 py-md-3" style="border: 1px solid #EBEAEA;cursor: pointer;">
                <div>Lorem ipsum dolor sit amet, consectetur adipiscing elit.2</div>
                
                <span id="plus-2" style="cursor: pointer;">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 4.16669V15.8334" stroke="#2C3E50" stroke-width="0.916667" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M4.16699 10H15.8337" stroke="#2C3E50" stroke-width="0.916667" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </span>

                <span id="minus-2" style="display: none;">
                    <svg width="14" height="2" viewBox="0 0 14 2" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1.16699 1H12.8337" stroke="#2C3E50" stroke-width="0.916667" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>                        
                </span>

            </div>
            <!-- Faq content -->
            <div class="p-2 my-2" id="content-2" style="display: none;">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit.Lorem ipsum dolor sit amet, consectetur adipiscing elit.Lorem ipsum do
                lor sit amet, consectetur adipiscing elit.Lorem ipsum dolor sit amet, consectetur adipiscing elit.Lorem ipsum dolor sit amet, 
                consectetur adipiscing elit.Lorem ipsum dolor sit amet, consectetur adipiscing elit.Lorem ipsum dolor sit amet, consectetur adipiscing elit
                .Lorem ipsum dolor sit amet, consectetur adipiscing elit.Lorem ipsum dolor sit amet, consectetur adipiscing elit.Lorem ipsu
                m dolor sit amet, consectetur adipiscing elit.Lorem ipsum dolor sit amet, consectetur adipiscing elit.2
            </div>
        </div>
    </div>


    <div class="row mt-5 faq_tab_contents" id="tech_content" style="display: none">
        <div class="col-10 col-md-7 mx-auto my-2">
            <div onclick="accordion('content-1')" class="d-flex justify-content-between align-items-center px-2 px-lg-3 py-2 py-md-3" style="border: 1px solid #EBEAEA;cursor: pointer;">
                <div>Tech Lorem ipsum dolor sit amet, consectetur adipiscing elit.</div>
                
                <span id="plus-one" style="cursor: pointer;">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 4.16669V15.8334" stroke="#2C3E50" stroke-width="0.916667" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M4.16699 10H15.8337" stroke="#2C3E50" stroke-width="0.916667" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </span>

                <span id="minus-one" style="display: none;">
                    <svg width="14" height="2" viewBox="0 0 14 2" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1.16699 1H12.8337" stroke="#2C3E50" stroke-width="0.916667" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>                        
                </span>
            </div>
            <!-- Faq content -->
            <div class="p-2 my-2" id="content-1" style="display: none;">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit.Lorem ipsum dolor sit amet, consectetur adipiscing elit.Lorem ipsum do
                lor sit amet, consectetur adipiscing elit.Lorem ipsum dolor sit amet, consectetur adipiscing elit.Lorem ipsum dolor sit amet, 
                consectetur adipiscing elit.Lorem ipsum dolor sit amet, consectetur adipiscing elit.Lorem ipsum dolor sit amet, consectetur adipiscing elit
                .Lorem ipsum dolor sit amet, consectetur adipiscing elit.Lorem ipsum dolor sit amet, consectetur adipiscing elit.Lorem ipsu
                m dolor sit amet, consectetur adipiscing elit.Lorem ipsum dolor sit amet, consectetur adipiscing elit.
            </div>
        </div>
        <div class="col-10 col-md-7 mx-auto my-2">
            <div onclick="accordion('content-2')" class="d-flex justify-content-between align-items-center px-2 px-lg-3 py-2 py-md-3" style="border: 1px solid #EBEAEA;cursor: pointer;">
                <div>Lorem ipsum dolor sit amet, consectetur adipiscing elit.2</div>
                
                <span id="plus-2" style="cursor: pointer;">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 4.16669V15.8334" stroke="#2C3E50" stroke-width="0.916667" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M4.16699 10H15.8337" stroke="#2C3E50" stroke-width="0.916667" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </span>

                <span id="minus-2" style="display: none;">
                    <svg width="14" height="2" viewBox="0 0 14 2" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1.16699 1H12.8337" stroke="#2C3E50" stroke-width="0.916667" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>                        
                </span>

            </div>
            <!-- Faq content -->
            <div class="p-2 my-2" id="content-2" style="display: none;">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit.Lorem ipsum dolor sit amet, consectetur adipiscing elit.Lorem ipsum do
                lor sit amet, consectetur adipiscing elit.Lorem ipsum dolor sit amet, consectetur adipiscing elit.Lorem ipsum dolor sit amet, 
                consectetur adipiscing elit.Lorem ipsum dolor sit amet, consectetur adipiscing elit.Lorem ipsum dolor sit amet, consectetur adipiscing elit
                .Lorem ipsum dolor sit amet, consectetur adipiscing elit.Lorem ipsum dolor sit amet, consectetur adipiscing elit.Lorem ipsu
                m dolor sit amet, consectetur adipiscing elit.Lorem ipsum dolor sit amet, consectetur adipiscing elit.2
            </div>
        </div>
    </div>

    <div class="row mt-5 faq_tab_contents" id="transaction_content" style="display: none">
        <div class="col-10 col-md-7 mx-auto my-2">
            <div onclick="accordion('content-1')" class="d-flex justify-content-between align-items-center px-2 px-lg-3 py-2 py-md-3" style="border: 1px solid #EBEAEA;cursor: pointer;">
                <div>Transaction Lorem ipsum dolor sit amet, consectetur adipiscing elit.</div>
                
                <span id="plus-one" style="cursor: pointer;">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 4.16669V15.8334" stroke="#2C3E50" stroke-width="0.916667" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M4.16699 10H15.8337" stroke="#2C3E50" stroke-width="0.916667" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </span>

                <span id="minus-one" style="display: none;">
                    <svg width="14" height="2" viewBox="0 0 14 2" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1.16699 1H12.8337" stroke="#2C3E50" stroke-width="0.916667" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>                        
                </span>
            </div>
            <!-- Faq content -->
            <div class="p-2 my-2" id="content-1" style="display: none;">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit.Lorem ipsum dolor sit amet, consectetur adipiscing elit.Lorem ipsum do
                lor sit amet, consectetur adipiscing elit.Lorem ipsum dolor sit amet, consectetur adipiscing elit.Lorem ipsum dolor sit amet, 
                consectetur adipiscing elit.Lorem ipsum dolor sit amet, consectetur adipiscing elit.Lorem ipsum dolor sit amet, consectetur adipiscing elit
                .Lorem ipsum dolor sit amet, consectetur adipiscing elit.Lorem ipsum dolor sit amet, consectetur adipiscing elit.Lorem ipsu
                m dolor sit amet, consectetur adipiscing elit.Lorem ipsum dolor sit amet, consectetur adipiscing elit.
            </div>
        </div>
        <div class="col-10 col-md-7 mx-auto my-2">
            <div onclick="accordion('content-2')" class="d-flex justify-content-between align-items-center px-2 px-lg-3 py-2 py-md-3" style="border: 1px solid #EBEAEA;cursor: pointer;">
                <div>Lorem ipsum dolor sit amet, consectetur adipiscing elit.2</div>
                
                <span id="plus-2" style="cursor: pointer;">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 4.16669V15.8334" stroke="#2C3E50" stroke-width="0.916667" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M4.16699 10H15.8337" stroke="#2C3E50" stroke-width="0.916667" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </span>

                <span id="minus-2" style="display: none;">
                    <svg width="14" height="2" viewBox="0 0 14 2" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1.16699 1H12.8337" stroke="#2C3E50" stroke-width="0.916667" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>                        
                </span>

            </div>
            <!-- Faq content -->
            <div class="p-2 my-2" id="content-2" style="display: none;">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit.Lorem ipsum dolor sit amet, consectetur adipiscing elit.Lorem ipsum do
                lor sit amet, consectetur adipiscing elit.Lorem ipsum dolor sit amet, consectetur adipiscing elit.Lorem ipsum dolor sit amet, 
                consectetur adipiscing elit.Lorem ipsum dolor sit amet, consectetur adipiscing elit.Lorem ipsum dolor sit amet, consectetur adipiscing elit
                .Lorem ipsum dolor sit amet, consectetur adipiscing elit.Lorem ipsum dolor sit amet, consectetur adipiscing elit.Lorem ipsu
                m dolor sit amet, consectetur adipiscing elit.Lorem ipsum dolor sit amet, consectetur adipiscing elit.2
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-11 col-md-6 text-center mb-3 mb-lg-4 mx-auto">
            <span class="ask_question d-block mb-2">You still have a question?</span>
            <p style="">If you cannot find an answer to your question on our FAQ you can always contact us. We’ll answer to you shortly! </p>
        </div>
        <div class="col-12 mt-5">
            <div class="d-flex flex-column flex-md-row justify-content-center align-items-center">
                <div class="col-10 col-md-5 my-3 my-md-0">
                    <div class="contact_us_info p-2 d-flex flex-column align-items-center justify-content-md-between justify-content-lg-center">
                        <span class="d-block mb-3">
                            <svg width="44" height="44" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.16667 7.33331H16.5L20.1667 16.5L15.5833 19.25C17.5468 23.2311 20.7689 26.4532 24.75 28.4166L27.5 23.8333L36.6667 27.5V34.8333C36.6667 35.8058 36.2804 36.7384 35.5927 37.426C34.9051 38.1137 33.9725 38.5 33 38.5C25.8487 38.0654 19.1036 35.0286 14.0375 29.9625C8.97142 24.8964 5.93459 18.1513 5.5 11C5.5 10.0275 5.88631 9.09489 6.57394 8.40725C7.26158 7.71962 8.19421 7.33331 9.16667 7.33331" stroke="#000070" stroke-width="1.83333" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>                        
                        </span>
                        <p class="d-block">+234 098 098 098 00</p>
                        <p class="d-block">Get response in No-time</p>
                    </div>
                </div>
                <div class="col-10 col-md-5 my-3 my-md-0">
                    <div class="contact_us_info p-2 d-flex flex-column align-items-center justify-content-center">
                        <svg width="44" height="44" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M34.8333 9.16669H9.16667C7.14162 9.16669 5.5 10.8083 5.5 12.8334V31.1667C5.5 33.1917 7.14162 34.8334 9.16667 34.8334H34.8333C36.8584 34.8334 38.5 33.1917 38.5 31.1667V12.8334C38.5 10.8083 36.8584 9.16669 34.8333 9.16669Z" stroke="#000070" stroke-width="1.83333" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M5.5 12.8333L22 23.8333L38.5 12.8333" stroke="#000070" stroke-width="1.83333" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>                            
                        <p class="d-block">dantown@dantownms.com</p>
                        <p class="d-block">Get response in No-time</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
<div class="icon1-bg"></div>


<footer>
    <div class="container" style="justify-content: center;margin-top:-100px;">
        <div class="footer_bg_img">
            <img src="/footer_bg.png" />
        </div>
        <div class="row" style="position: relative;left:7%;">
            <div class="col-5 col-md-3">
                <h5>Company</h5>
                <a href="#">About Us</a><br>
                <a href="#">Services</a><br>
                <a href="#">Our Company</a><br>
                <a href="#">Our Team</a><br>
            </div>
            <div class="col-5 col-md-3">
                <h5>Product</h5>
                <a href="#">Bitcoin</a><br>
                <a href="#">Gift Card</a><br>
                <a href="#">Digital Assests</a><br>
                <a href="#">Paybills</a><br>
                <a href="#">Airtime</a><br>
            </div>
            <div class="col-5 mt-4 mt-md-0 col-md-3">
                <h5>Legal</h5>
                <a href="#">Privacy Policy</a><br>
                <a href="#">Terms and Conditions</a><br>
            </div>
            <div class="col-5 mt-4 mt-md-0 col-md-3">
                <h5>Info</h5>
                <a href="#">Blog</a><br>
                <a href="#">User's Guide</a><br>
            </div>
        </div>
    </div>
    <div class="text-center mt-5">© 2020 Dantownms.com. All rights reserved</div>
</footer>



@endsection