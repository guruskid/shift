<div class="container px-5" id="cablesubscription_tab" style="display: none;">
    <div class="row">
        <div class="col-12 text-center my-3 my-lg-4" style="color: #000070;font-size: 18px;">Cable Subscription and TV</div>
        <div class="col-12 col-md-8 mx-md-auto px-0 mx-0 px-lg-0 ml-lg-0 mr-lg-3 col-lg-5 mt-3">
            <label for="phoneNumber" class="mb-0 pb-0" style="color: #000070;">Decoder number</label>
            <div class="form-group">
                <input type="text" class="form-control" placeholder="0000 0000 0000 0000" />
            </div>
        </div>
        <div class="col-12 col-md-8 mx-md-auto px-0 mx-0 px-lg-0 ml-lg-5 col-lg-5 mt-3">
            <label for="amount" class="mb-0 pb-0" style="color: #000070;">Package</label>
            <select class="custom-select" id="amount" name="amount">
                <option selected>10gb ( ₦3,880 ) - monthly</option>
                <option value="1">One</option>
            </select>
        </div>
        <div class="col-12 col-md-8 mx-md-auto px-0 mx-0 px-lg-0 mx-lg-0 col-lg-5 mt-3">
            <div class="form-group mb-0">
                <label for="password_field" class="mb-0 pb-0" style="color: #000070;">Pin</label>
                <span id="removeobscure_pwd2" class="removeobscure_pwd smartbudget">
                    <img id="toggleshowpassword2" src="{{asset('svg/obscure-password.svg')}}" /></span>
                <input type="password" name="password" id="password_field2" placeholder="Password"
                    class="form-control pr-4" value="" />
            </div>
        </div>
        <div class="col-12 d-flex flex-wrap flex-md-nowrap justify-content-center align-items-center mt-3 mt-md-5 mb-md-5">
            <button type="button" onclick="closeTab('cablesubscription_tab')" class="btn mb-3 mb-md-0 mr-md-2"
                style="border: 1px solid #000070;border-radius: 3px;width:128px;">
                <span>
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M10 17.5C14.1421 17.5 17.5 14.1421 17.5 10C17.5 5.85786 14.1421 2.5 10 2.5C5.85786 2.5 2.5 5.85786 2.5 10C2.5 14.1421 5.85786 17.5 10 17.5Z"
                            stroke="#000070" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M11.6673 8.33203L8.33398 11.6654M8.33398 8.33203L11.6673 11.6654L8.33398 8.33203Z"
                            stroke="#000070" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </span>
                <span style="color: #000070;font-size: 16px;position: relative;top:2px;">Close</span>
            </button>
            <button type="submit" class="btn text-white ml-md-2" style="background-color:#000070;">
                <span>
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M10 17.5C14.1421 17.5 17.5 14.1421 17.5 10C17.5 5.85786 14.1421 2.5 10 2.5C5.85786 2.5 2.5 5.85786 2.5 10C2.5 14.1421 5.85786 17.5 10 17.5Z"
                            stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M7.5 10H12.5" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M10 7.5V12.5" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </span>
                <span style="position: relative;top:2px;">Add Budget</span>
            </button>
        </div>
    </div>
</div>