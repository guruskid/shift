<div class="container-fluid mt-3 mt-lg-0 wallet_trx_tabs" id="bitcoin_wallet_receive_tab" style="display: none;">
    <form action="" method="post">
        @csrf
        <div class="row">
            <div class="col-10 col-md-6 mt-md-5 mx-auto">
                <span class="d-block mb-1" style="color: #000070;font-size: 16px;line-height: 22px;">Receiving wallet</span>
                <div class="py-3 px-2 text-center show_receiving_btc_address">
                    My Ethereum wallet {{ Auth::user()->bnbWallet->address }}
                </div>
            </div>
            <div class="col-10 mx-auto text-center mt-4">
                <img src="https://chart.googleapis.com/chart?chs=250x250&cht=qr&chl={{ Auth::user()->bnbWallet->address }}" alt="Qr Code">
            </div>
            <div class="col-md-5 mx-auto">
                <div class="row">
                    <div class="col-12  mx-md-auto ">
                        <span class="address_input_label text-left">Address</span>
                        <div class="input-group mb-3 mt-4">
                            <input type="text" class="form-control" value="{{ Auth::user()->bnbWallet->address }}" id="myAddress">
                            <div class="input-group-append">
                                <span class="input-group-text" onclick="copyAddress('myAddress')"
                                    style="cursor:pointer;background: #000070;" id="basic-addon2">
                                    <img src="/svg/copy_btn.svg"/>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12  mx-md-auto ">
                        <span class="address_input_label text-left">Memo</span>
                        <div class="input-group mb-3 mt-4">
                            <input type="text" class="form-control" value="{{ Auth::user()->bnbWallet->pin }}" id="myAddress">
                            <div class="input-group-append">
                                <span class="input-group-text" onclick="copyAddress('myAddress')"
                                    style="cursor:pointer;background: #000070;" id="basic-addon2">
                                    <img src="/svg/copy_btn.svg"/>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <button type="submit" class="btn text-white mt-4 walletpage_btn">Confirm</button>
            </div>
        </div>
    </form>
</div>
