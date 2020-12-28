<div class="container-fluid mt-3 mt-lg-0 wallet_trx_tabs" id="bitcoin_wallet_receive_tab" style="display: none;">
    <form action="" method="post">
        @csrf
        <div class="row">
            <div class="col-10 col-md-6 mt-md-5 mx-auto">
                <span class="d-block mb-1" style="color: #000070;font-size: 16px;line-height: 22px;">Receiving wallet</span>
                <div class="py-3 px-2 text-center show_receiving_btc_address">
                    My tether wallet 090887543456HHJWHUKSJFYGKLKOKJO
                </div>
            </div>
            <div class="col-10 mx-auto text-center mt-4">
                <svg width="220" height="220" viewBox="0 0 247 247" fill="none" xmlns="http://www.w3.org/2000/svg"
                    xmlns:xlink="http://www.w3.org/1999/xlink">
                    <rect x="0.25" y="0.25" width="246.5" height="246.5" fill="url(#pattern0)" stroke="#7D7D7D"
                        stroke-width="0.5" />
                    <defs>
                        <pattern id="pattern0" patternContentUnits="objectBoundingBox" width="1" height="1">
                            <use xlink:href="#image0" transform="scale(0.000625)" />
                        </pattern>
                        <image id="image0" width="1600" height="1600"
                            xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAABkAAAAZAAQMAAAAbwhzkAAAABGdBTUEAALGPC/xhBQAAAAFzUkdCAK7OHOkAAAAGUExURQAAAP7+/soH9D0AAAYwSURBVHja7dyxcYNAEAVQPA4UugRKoTRcGqW4BIcKNMKBcABzXu8JhIXn/UwSJ/Zt/Oea8Z+kAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAXkiyGdTn77iaHt79nLHa1oQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEJA9IC+ZVvolhHS3b88hpE+130FAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQP4AMmQK7vNpruVme80OppxAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQECOCeluH1PdeBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQkGND4pvaQUBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBADgYJE0OG+TTh0TggICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB7QqqSgpS78TUBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQF5LGRF4h2MjwoICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICMhGkPI0izvTP6Ju/IosLnk/b1/yBwEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEB+RVyz1+mdjDlEu6gW4wKAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgKyB6Scc1hp76MdjCGk3I0f5jsAAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQHZBRJ348PhYkhNrb6rWTIICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICMi2kB8yPfTRJFKepnz07fbjdX40tT4QEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEJCtICuSqtWndjDllHotCAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAjIRpDPpj797B+umSOv08PvGcj0sQUBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAdkD8pJppV/mkMXRYT5VuRtf3kFXU8kHAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQF5DGQoltbbImQxzYpMkMVN7SAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAeDnMZEype8p46CgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIA8AeSem9pr0oKAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgOwJSU0zh3ynW7yvOM17ceRurAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICMi2kKr00dFTZgdTrjU7AAEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEB2RhyvICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICArMkXJlylJdANIpEAAAAASUVORK5CYII=" />
                    </defs>
                </svg>
            </div>
            <div class="col-12 col-md-8 mx-md-auto col-lg-6">
                <span class="address_input_label text-left">Address</span>
                <div class="input-group mb-3 mt-4">
                    <input type="text" class="form-control" id="receipientAddress" aria-label="Recipient's username"
                        aria-describedby="basic-addon2">
                    <div class="input-group-append">
                        <span class="input-group-text" onclick="copywalletaddress('receipientAddress')"
                            style="cursor:pointer;background: #000070;" id="basic-addon2">
                            <img src="/svg/copy_btn.svg"/>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <button type="submit" class="btn text-white mt-4 walletpage_btn">Confirm</button>
            </div>
        </div>
    </form>
</div>