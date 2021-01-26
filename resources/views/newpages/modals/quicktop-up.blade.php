<div class="modal" id="quickTopUpModal" tabindex="-1" style="background-color: #a9a9a994;display:none;">
    <div class="modal-dialog">
        <div class="modal-content modal-content-custom quickrecharge_modal" style="margin-top:50px;">

            <div id="modal_container_content" class="container py-4">
                <div class="d-flex justify-content-between mb-4">
                    <span class="d-block" style="color: #000070;font-size: 24px;font-weight: 500;">Quick top up</span>
                    <span class="d-block" id="closeQuickTopUp" data-dismiss="modal" style="cursor: pointer;">
                        <svg width="18" height="18" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g opacity="0.4">
                                <path
                                    d="M34 5.63477L28.3653 0L17 11.3652L5.63477 0L0 5.63477L11.3652 17L0 28.3652L5.63477 34L17 22.6348L28.3653 34L34 28.3652L22.6348 17L34 5.63477ZM31.1827 28.3652L28.3653 31.1826L17 19.8174L5.63477 31.1826L2.81742 28.3653L14.1826 17L2.81742 5.63477L5.63477 2.81742L17 14.1826L28.3653 2.81742L31.1827 5.63477L19.8174 17L31.1827 28.3652Z"
                                    fill="#000070" fill-opacity="0.75" />
                            </g>
                        </svg>
                    </span>
                </div>
                <div class="p-2 mx-auto d-flex justify-content-center">

                    <div id="airtel" class="quickcard_networks d-flex justify-content-center align-items-center">
                        <img class="img-fluid airtimelogo" src="/isp/airtel.png" />
                    </div>
                    <div id="mtn" class="quickcard_networks d-flex justify-content-center align-items-center">
                        <img class="img-fluid airtimelogo" src="/isp/mtn.png" />
                    </div>
                    <div id="9mobile" class="quickcard_networks d-flex justify-content-center align-items-center">
                        <img class="img-fluid airtimelogo" src="/isp/9mobile.png" />
                    </div>
                    <div id="glo" class="quickcard_networks d-flex justify-content-center align-items-center">
                        <img class="img-fluid airtimelogo" src="/isp/glo.png" />
                    </div>
                </div>


                <div class="container mt-4">
                    <form action="{{ route('user.airtime') }}" class="disable-form" method="post">@csrf
                        <div class="row">
                            <div class="col-md-12 d-flex justify-content-between">
                                <div class="form-check my-1">
                                    <input class="form-check-input" type="radio" name="rechargetype" id="rechargeme"
                                        value="self" checked />
                                    <label class="form-check-label" style="color: #000070;font-size: 16px;"
                                        for="rechargeme">
                                        Recharging for myself
                                    </label>
                                </div>
                                <div class="form-check my-1">
                                    <input class="form-check-input" type="radio" name="rechargetype" id="rechargeother"
                                        value="other" />
                                    <label class="form-check-label" style="color: #000070;font-size: 16px;"
                                        for="rechargeother">
                                        Other
                                    </label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group mt-3">
                                    <input type="hidden" name="network" id="airtime_network" />
                                    <input type="hidden" name="reference" value="{{ \Str::random(2) . time() }}">
                                    <label for="pin" style="color: #000070;font-size: 16px;">Pin</label>
                                    <span style="position: relative;top:37px;left:79%;">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M9.9987 5.83333C12.2987 5.83333 14.1654 7.7 14.1654 10C14.1654 10.5417 14.057 11.05 13.8654 11.525L16.2987 13.9583C17.557 12.9083 18.5487 11.55 19.157 10C17.7154 6.34167 14.157 3.75 9.99036 3.75C8.8237 3.75 7.70703 3.95833 6.6737 4.33333L8.4737 6.13333C8.9487 5.94167 9.45703 5.83333 9.9987 5.83333ZM1.66536 3.55833L3.56536 5.45833L3.9487 5.84167C2.56536 6.91667 1.48203 8.35 0.832031 10C2.2737 13.6583 5.83203 16.25 9.9987 16.25C11.2904 16.25 12.5237 16 13.6487 15.55L13.9987 15.9L16.4404 18.3333L17.4987 17.275L2.7237 2.5L1.66536 3.55833ZM6.2737 8.16667L7.56536 9.45833C7.5237 9.63333 7.4987 9.81667 7.4987 10C7.4987 11.3833 8.61536 12.5 9.9987 12.5C10.182 12.5 10.3654 12.475 10.5404 12.4333L11.832 13.725C11.2737 14 10.657 14.1667 9.9987 14.1667C7.6987 14.1667 5.83203 12.3 5.83203 10C5.83203 9.34167 5.9987 8.725 6.2737 8.16667ZM9.86536 7.51667L12.4904 10.1417L12.507 10.0083C12.507 8.625 11.3904 7.50833 10.007 7.50833L9.86536 7.51667Z"
                                                fill="#000070" />
                                        </svg>
                                    </span>
                                    <input type="password" id="pin" class="form-control" style="padding-right:29px;"
                                        name="password" />
                                </div>
                                <div class="form-group mt-3" id="quickRechargeOtherPhone" style="display: none;">
                                    <label for="phone" style="color: #000070;font-size: 16px;">Phone number</label>
                                    <input type="number" placeholder="" name="phone" id="phone" class="form-control" />
                                </div>
                            </div>
                            <div class="col-6 amount_to_recharge">
                                <div class="form-group mt-3">
                                    <label for="amount" style="color: #000070;font-size: 16px;">Amount</label>
                                    <input type="number" name="amount" placeholder="" class="form-control" />
                                </div>

                            </div>
                            <button id="upload_card_btn" type="submit" class="btn text-white mt-4 mt-lg-5 mx-auto"
                                style="width: 150px;height:50px;background: #000070;border-radius: 5px;position:relative;left:16px;">Recharge</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
