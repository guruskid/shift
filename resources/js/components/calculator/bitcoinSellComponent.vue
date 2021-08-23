<template>
    <div class="container py-4">
      <div
        class="container my-3 mt-lg-5 wallet_trx_tabs"
        id="bitcoin_wallet_send_tab"
      >
        <form
          class="disable-form"
        >
          <div class="row">
            <div
              class="col-12 col-md-10 col-lg-8 mx-auto"
              style="border: 1px solid rgba(0, 0, 112, 0.25)"
            >
              <div class="input-group">
                <input
                  type="number"
                  step="any"
                  id="usd-amount"
                  required
                  class="form-control"
                  placeholder="0.00"
                  style="border: 0px"
                />
                <div class="input-group-append">
                  <span class="input-group-text usd_bg_text pr-1">USD</span>
                </div>
                <input
                  type="number"
                  step="any"
                  name="amount"
                  id="btc-amount"
                  placeholder="0"
                  class="form-control"
                  style="border: 0px; border-right: 0px"
                />
                <div class="input-group-prepend">
                  <span class="input-group-text usd_bg_text">0 BTC</span>
                </div>
              </div>
            </div>
            <div class="container mt-3m mt-lg-5">
              <div class="row">
                <div class="col-md-8 mx-auto row my-4">
                  <div class="col-6 col-md">
                    <div class="form-group">
                      <label for="" class="networkfee_text">Network fee</label>
                      <select
                        class="custom-select"
                        style="height: 42px; border-radius: 0px"
                      >
                        <option selected>Network fee</option>
                        {{--
                        <option value="1">Regular</option>
                        --}}
                      </select>
                    </div>
                  </div>
                  <div class="col-6 col-md">
                    <div
                      class="d-flex flex-column mx-auto networkfee_container"
                    >
                      <span class="d-block align-self-end btctext fee-text">
                        BTC</span
                      >
                      <span class="d-block align-self-end customfee"
                        >Transaction Fee</span
                      >
                    </div>
                  </div>
                </div>
                <div class="col-md-7 mx-auto row my-4 form-group">
                  <label for="receipientAddress">Address</label>
                  <div class="input-group">
                    <input
                      type="text"
                      class="form-control"
                      id="receipientAddress"
                      name="address"
                    />
                    <input
                      type="hidden"
                      class="fee-input"
                      name="fees"
                    />
                    <div class="input-group-append">
                      <span
                        class="input-group-text"
                        style="cursor: pointer; background: #000070"
                        id="basic-addon2"
                        ><svg
                          width="17"
                          height="19"
                          viewBox="0 0 17 19"
                          fill="none"
                          xmlns="http://www.w3.org/2000/svg"
                        >
                          <path
                            d="M12.0909 0H1.72727C0.777273 0 0 0.777273 0 1.72727V13.8182H1.72727V1.72727H12.0909V0ZM14.6818 3.45455H5.18182C4.23182 3.45455 3.45455 4.23182 3.45455 5.18182V17.2727C3.45455 18.2227 4.23182 19 5.18182 19H14.6818C15.6318 19 16.4091 18.2227 16.4091 17.2727V5.18182C16.4091 4.23182 15.6318 3.45455 14.6818 3.45455ZM14.6818 17.2727H5.18182V5.18182H14.6818V17.2727Z"
                            fill="white"
                          />
                        </svg>
                      </span>
                    </div>
                  </div>
                </div>

                <div class="col-md-7 mx-auto row my-4 form-group">
                  <label for="receipientAddress">Pin</label>
                  <input
                    type="password"
                    name="pin"
                    required
                    class="form-control"
                    id="receipientAddress"
                  />
                </div>
                <div class="col-md-8 mx-auto row my-4">
                  <button
                    style="background-color: #000070"
                    type="submit"
                    class="btn mx-auto text-white"
                  >
                    Continue
                  </button>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
</template>
