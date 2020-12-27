<div class="container my-3 mt-lg-5 wallet_trx_tabs" id="bitcoin_wallet_send_tab">
    <form action="{{ route('user.send-bitcoin') }}" method="post"> @csrf
        <div class="row">
            <div class="col-12 col-md-10 col-lg-8 mx-auto" style="border: 1px solid rgba(0, 0, 112, 0.25);">
                <div class="input-group">
                    <input type="text" name="amount" required class="form-control" placeholder="0.00"
                        style="border: 0px;">
                   {{--  <div class="input-group-append">
                        <span class="input-group-text usd_bg_text pr-1">USD</span>
                        <span class="input-group-text usd_bg_text">
                            <img src="/svg/conversion-arrow.svg" alt="">
                        </span>
                    </div> --}}
                    {{-- <input type="text" aria-label="Last name" placeholder="0" class="form-control"
                        style="border: 0px;border-right:0px;"> --}}
                    <div class="input-group-prepend">
                        <span class="input-group-text usd_bg_text">BTC</span>
                    </div>
                </div>
            </div>
            <div class="container mt-3m mt-lg-5">
                <div class="row">
                    <div class="col-6 col-md-4 mx-0 p-0 ml-md-auto">
                        <div class="form-group">
                            <label for="" class="networkfee_text">Network fee</label>
                            <select class="custom-select" style="height: 42px;border-radius:0px;">
                                <option selected>Network fee</option>
                                {{-- <option value="1">Regular</option> --}}
                            </select>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 mr-md-auto">
                        <div class="d-flex flex-column mx-auto networkfee_container">
                            <span class="d-block align-self-end btctext">{{ number_format((float)$total_fees, 8) }} BTC</span>
                            <span class="d-block align-self-end customfee">Transaction Fee</span>
                        </div>
                    </div>
                    <div class="col-12 col-md-10 mx-auto">
                        <span class="address_input_label">Address</span>
                        <div class="input-group col-12 col-md-7 mx-auto mb-3 mt-4">
                            <input type="text" class="form-control" id="receipientAddress"
                                aria-label="Recipient's username" aria-describedby="basic-addon2">
                                <input type="hidden" name="fees" value="{{ $fees }}" >
                            <div class="input-group-append">
                                <span class="input-group-text" onclick="copywalletaddress('receipientAddress')"
                                    style="cursor:pointer;background: #000070;" id="basic-addon2"><svg width="17"
                                        height="19" viewBox="0 0 17 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M12.0909 0H1.72727C0.777273 0 0 0.777273 0 1.72727V13.8182H1.72727V1.72727H12.0909V0ZM14.6818 3.45455H5.18182C4.23182 3.45455 3.45455 4.23182 3.45455 5.18182V17.2727C3.45455 18.2227 4.23182 19 5.18182 19H14.6818C15.6318 19 16.4091 18.2227 16.4091 17.2727V5.18182C16.4091 4.23182 15.6318 3.45455 14.6818 3.45455ZM14.6818 17.2727H5.18182V5.18182H14.6818V17.2727Z"
                                            fill="white" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-10 mx-auto">
                        <span class="address_input_label">Pin</span>
                        <div class="input-group col-7 mx-auto mb-3 mt-4">
                            <input type="password" name="pin" required class="form-control" id="receipientAddress" >
                        </div>
                    </div>
                    <button type="submit" class="btn walletpage_btn text-white mt-3 mt-lg-5">Continue</button>
                </div>
            </div>
        </div>
    </form>
</div>
