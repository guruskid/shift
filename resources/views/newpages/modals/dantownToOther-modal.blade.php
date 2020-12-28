<div class="modal dantownToOtherModal" id="dantownToOtherModal" tabindex="-1" style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content modal-content-custom quickrecharge_modal" style="margin-top:90px;">

            <div id="modal_container_content" class="container py-4">
                <div class="d-flex justify-content-between justify-content-lg-center mb-4">
                    <span class="d-block text-center">Dantown to other
                        Banks</span>
                    <span class="d-block closeBtn" id="closedantownToOtherModal" data-dismiss="modal">
                        <svg width="18" height="18" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g opacity="0.4">
                                <path
                                    d="M34 5.63477L28.3653 0L17 11.3652L5.63477 0L0 5.63477L11.3652 17L0 28.3652L5.63477 34L17 22.6348L28.3653 34L34 28.3652L22.6348 17L34 5.63477ZM31.1827 28.3652L28.3653 31.1826L17 19.8174L5.63477 31.1826L2.81742 28.3653L14.1826 17L2.81742 5.63477L5.63477 2.81742L17 14.1826L28.3653 2.81742L31.1827 5.63477L19.8174 17L31.1827 28.3652Z"
                                    fill="#000070" fill-opacity="0.75" />
                            </g>
                        </svg>
                    </span>
                </div>
                <div class="mx-3">
                    <form action="{{ route('user.transfer') }}" method="post">@csrf
                        <input type="hidden" name="ref" value="{{ $ref }}" >
                        <input type="hidden" name="trans_type" value="1" >
                        <div class="form-row mb-3">
                            <div class="col-12 col-md">
                                <label for="bankname" style="color: #000070;font-size: 16px;">Bank name</label>
                                <select class="custom-select" required id="bank-name" name="bank_code">
                                    <option >Select Bank</option>
                                    @foreach ($banks as $bank)
                                    <option class="text-capitalize" value="{{ $bank->code }}">{{ $bank->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md  md-md-0">
                                <label for="account_number" style="color: #000070;font-size: 16px;">Account
                                    number</label>
                                <input type="text" required id="account-number" class="form-control" name="acct_num"  />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="account_number" style="color: #000070;font-size: 16px;">Account name</label>
                            <input type="text" required class="form-control acct-name" readonly name="acct_name" />
                        </div>
                        <div class="form-group">
                            <label for="account_number" style="color: #000070;font-size: 16px;">Amount</label>
                            <input type="text" required class="form-control" name="amount" placeholder="0000000000" />
                        </div>
                        <div class="form-group">
                            <label for="narration" style="color: #000070;font-size: 16px;">Narration</label>
                            <select class="custom-select" required name="narration">
                                <option value="Personal">Personal</option>
                                <option value="Card Purchase">Card Purchase</option>
                                <option value="Bills">Bills</option>
                                <option value="Transport">Transport</option>
                                <option value="Transfers">Transfers</option>
                                <option value="Food">Food</option>
                                <option value="Family">Family</option>
                                <option value="Groceries">Groceries</option>
                                <option value="Shopping">Shopping</option>
                                <option value="Self Care">Self Care</option>
                                <option value="Holiday">Holiday</option>
                                <option value="Payroll">Payroll</option>
                                <option value="Enjoyment">Enjoyment</option>
                                <option value="Investments">Investments</option>
                                <option value="Charity">Charity</option>
                                <option value="Refund">Refund</option>
                                <option value="Household">Household</option>
                                <option value="Others">Others</option>
                            </select>
                        </div>
                        <div class="form-group mt-2">
                            <label for="amount" style="color: #000070;">Pin</label>
                            <input type="password" required max="4" placeholder="- - - -" class="form-control" name="pin" >
                        </div>
                        <button type="submit" id="sign-up-btn" class="btn text-white w-100 py-2 mt-2"
                            style="background-color: #000070">Confirm</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
