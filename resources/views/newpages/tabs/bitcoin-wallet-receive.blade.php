<div class="container-fluid mt-3 mt-lg-0 wallet_trx_tabs" id="bitcoin_wallet_receive_tab" style="display: none;">
    @if($receive_btc_setting['settings_value'] == 1)
    <form action="" method="post">
        @csrf
        <div class="row">
            <div class="col-10 col-md-6 mt-md-5 mx-auto">
                <span class="d-block mb-1" style="color: #000070;font-size: 16px;line-height: 22px;">Receiving wallet</span>
                <div class="py-3 px-2 text-center show_receiving_btc_address">
                    My Bitcoin wallet {{ Auth::user()->btcWallet->address }}
                </div>
            </div>
            <div class="col-10 mx-auto text-center mt-4">
                <img src="https://chart.googleapis.com/chart?chs=250x250&cht=qr&chl={{ Auth::user()->btcWallet->address }}" alt="Qr Code">
            </div>
            <div class="col-12 col-md-8 mx-md-auto col-lg-6">
                <span class="address_input_label text-left">Address</span>
                <div class="input-group mb-3 mt-4">
                    <input type="text" class="form-control" value="{{ Auth::user()->btcWallet->address }}" id="myAddress" readonly>
                    <div class="input-group-append">
                        <span class="input-group-text" onclick="copyAddress('myAddress')"
                            style="cursor:pointer;background: #000070;" id="basic-addon2">
                            <img src="/svg/copy_btn.svg"/>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </form>
    @else
    <h4 class="text-center p-2 text-white" style="background-color: #000070"><i class="fas fa-info-circle"></i>
        {{$receive_btc_setting['notice']}}</h4>
    @endif
</div>
