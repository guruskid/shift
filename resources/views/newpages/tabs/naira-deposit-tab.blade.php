<div id="nairaDepositTab" style="display: none;">
    <div class="d-flex flex-column justify-content-center align-items-center mt-3 mt-lg-5 mb-2" style="max-width: 700px;">
        <span class="d-block text-center primary_text_color mb-4"
            style="font-size: 20px;font-weight:500;">Deposit</span>
        <div class="d-flex flex-column">
            <div class="my-1 my-lg-2">
                <span class="deposit_tab_bankdetailsText">Bank name:</span>
                <span style="color:#000070;font-size:16px;">{{ $n->bank_name }}</span>
            </div>
            <div class="my-1 my-lg-2">
                <span class="deposit_tab_bankdetailsText">Account name:</span>
                <span style="color:#000070;font-size:16px;">{{ $n->account_name }}</span>
            </div>
            <div class="my-1 my-lg-2">
                <span class="deposit_tab_bankdetailsText">Account number:</span>
                <input id="acct_number_input" class="deposit_tab_bankdetailsText" readonly style="border: none;width:80px;outline:none;" type="text" value="{{ $n->account_number }}" />
                {{-- <span id="dep_acct_number_text" class="deposit_tab_bankdetailsText">1245678928</span> --}}
                <span class=" ml-1 ml-lg-4" style="cursor: pointer;" onclick="copyAcctNumber('acct_number_input')">
                    <svg width="26" height="26" viewBox="0 0 26 26" fill="#000070" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M19.4993 8.66699H10.8327C9.63607 8.66699 8.66602 9.63704 8.66602 10.8337V19.5003C8.66602 20.6969 9.63607 21.667 10.8327 21.667H19.4993C20.696 21.667 21.666 20.6969 21.666 19.5003V10.8337C21.666 9.63704 20.696 8.66699 19.4993 8.66699Z"
                            stroke="#2C3E50" stroke-linecap="round" stroke-linejoin="round" />
                        <path
                            d="M17.334 8.66634V6.49967C17.334 5.92504 17.1057 5.37394 16.6994 4.96761C16.2931 4.56128 15.742 4.33301 15.1673 4.33301H6.50065C5.92602 4.33301 5.37492 4.56128 4.96859 4.96761C4.56226 5.37394 4.33398 5.92504 4.33398 6.49967V15.1663C4.33398 15.741 4.56226 16.2921 4.96859 16.6984C5.37492 17.1047 5.92602 17.333 6.50065 17.333H8.66732"
                            stroke="#2C3E50" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{-- <span style="color: #000070;">Copy</span> --}}
                </span>
            </div>
        </div>
    </div>
</div>
