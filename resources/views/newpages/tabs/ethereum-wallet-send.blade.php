<div class="container my-3 mt-lg-5 wallet_trx_tabs" id="bitcoin_wallet_send_tab">
    <form action="" method="post">
        @csrf
        <div class="row">
            <div class="col-12 col-md-10 col-lg-8 mx-auto" style="border: 1px solid rgba(0, 0, 112, 0.25);">
                <div class="input-group">
                    <input type="text" aria-label="usd" class="form-control" placeholder="$0.00" style="border: 0px;">
                    <div class="input-group-append">
                      <span class="input-group-text usd_bg_text pr-1">USD</span>
                      <span class="input-group-text usd_bg_text">
                          <img src="/svg/conversion-arrow.svg" alt="">
                      </span>
                    </div>
                    <input type="text" aria-label="eth" placeholder="0" class="form-control" style="border: 0px;border-right:0px;">
                    <div class="input-group-prepend">
                        <span class="input-group-text usd_bg_text">ETH</span>
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
                                <option value="1">Regular</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 mr-md-auto">
                        <div class="d-flex flex-column mx-auto networkfee_container">
                            <span class="d-block align-self-end btctext">0 ETH ($0.00)</span>
                            <span class="d-block align-self-end customfee">Customize Fee</span>
                        </div>
                    </div>
                    <button type="submit" class="btn walletpage_btn text-white mt-3 mt-lg-5">Continue</button>
                </div>
            </div>
        </div>
    </form>
</div>
