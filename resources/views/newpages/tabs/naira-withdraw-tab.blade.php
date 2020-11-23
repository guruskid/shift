<div id="nairaWithdrawTab" style="display: none;">
    <span class="d-block text-center primary_text_color mb-1 mb-lg-4 mt-3 mt-md-0 nairawallet_wdText">Withdraw</span>
<form action="" method="post">
    @csrf
    <div class="px-2 px-lg-5 py-2" style="border: 1px solid rgba(0, 0, 112, 0.25);border-radius: 5px;">
        <div class="custom-control custom-radio">
            <input type="radio" id="bankname" name="bankname" class="custom-control-input">
            <label class="custom-control-label" for="bankname"
                style="color: #000070;font-size: 15px;font-weight:600;">Access Bank
                Plc</label>
            <span class="d-block" style="color: #000070;font-size: 15px;">000000000000, Maduka chuks
                chuks</span>
        </div>
    </div>
    <a href="#" class="d-block btn mt-2 mb-4 mb-md-0"
        style="color: #000070;float:right;border: 0.5px solid #000070;border-radius: 3px;">Add new account</a>
    <div class="form-group mt-5">
        <label for="amount" style="color: #000070;">Amount</label>
        <input type="text" class="form-control" name="" id="amount">
    </div>
    <span class="d-block" style="color: #000070;font-weight:600;font-size: 11px;position: relative;top:-11px;">A charge
        of ₦80.00 will be added for each transaction outside Dantown wallet.</span>
        <select class="custom-select my-3">
            <option selected>Open this select menu</option>
            <option value="1">One</option>
            <option value="2">Two</option>
            <option value="3">Three</option>
          </select>
    {{-- <div class="form-group mt-4">
        <label for="narration" style="color: #000070;font-size: 16px;">Narration</label>
        <textarea name="description" class="form-control" id="narration" cols="10" rows="3"
            style=resize:none;></textarea>
    </div> --}}
    <button type="submit" class="btn text-white w-100 py-2 mt-3" style="background-color: #000070">Confirm</button>
</form>
</div>