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
                    <form action="" method="post">
                        @csrf
                        <div class="form-row mb-3">
                            <div class="col-12 col-md">
                                <label for="bankname" style="color: #000070;font-size: 16px;">Bank name</label>
                                <input type="text" class="form-control" id="bankname">
                            </div>
                            <div class="col-12 col-md mt-2 md-md-0">
                                <label for="account_number" style="color: #000070;font-size: 16px;">Account
                                    number</label>
                                <input type="text" class="form-control" name="account_number" id="account_number" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="account_number" style="color: #000070;font-size: 16px;">Account name</label>
                            <input type="text" class="form-control" readonly name="account_number"
                                id="account_number" />
                        </div>
                        <div class="form-group">
                            <label for="account_number" style="color: #000070;font-size: 16px;">Amount</label>
                            <input type="text" class="form-control" name="account_number" placeholder="0000000000"
                                id="account_number" />
                        </div>
                        <div class="form-group">
                            <label for="narration" style="color: #000070;font-size: 16px;">Narration</label>
                            <textarea name="description" class="form-control" id="narration" cols="10" rows="3"
                                style=resize:none;></textarea>
                        </div>
                        <button type="submit" class="btn text-white w-100 py-2 mt-2"
                            style="background-color: #000070">Confirm</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>