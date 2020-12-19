<div id="nairaWithdrawTab" style="display: none;">
    <span class="d-block text-center primary_text_color mb-1 mb-lg-4 mt-3 mt-md-0 nairawallet_wdText">Withdraw</span>
    <form action="{{ route('user.transfer') }}" method="post"> @csrf
        <input type="hidden" name="ref" value="{{ $ref }}">
        <input type="hidden" name="trans_type" value="2">
        @foreach (Auth::user()->accounts as $a)
        <div class="px-2 px-lg-5 py-2 mb-2" style="border: 1px solid rgba(0, 0, 112, 0.25);border-radius: 5px;">
            <div class="custom-control custom-radio">
                <input type="radio" id="account{{ $a->id }}" name="account_id" value="{{ $a->id }}" class="custom-control-input">
                <label class="custom-control-label" for="account{{ $a->id }}"
                    style="color: #000070;font-size: 15px;font-weight:600;">{{ $a->bank_name }} </label>
                <span class="d-block" style="color: #000070;font-size: 15px;">{{ $a->account_number }},
                    {{ $a->account_name }} </span>
            </div>
        </div>
        @endforeach
        <div class="form-group mt-2">
            <label for="amount" style="color: #000070;">Amount</label>
            <input type="number" class="form-control" name="amount" id="amount">
        </div>
        <span class="d-block" style="color: #000070;font-weight:600;font-size: 11px;position: relative;top:-11px;">A
            charge
            of ₦80.00 will be added for each transaction outside Dantown wallet.</span>
        <select class="custom-select my-3" name="narration">
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
        <div class="form-group mt-2">
            <label for="amount" style="color: #000070;">Pin</label>
            <input type="password" max="4" placeholder="- - - -" class="form-control" name="pin" >
        </div>
        <button type="submit" class="btn text-white w-100 py-2 mt-3" style="background-color: #000070">Confirm</button>
    </form>
</div>
