<div class="modal fade  item-badge-rightm" id="add-bank-modal" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                @if (Auth::user()->pin == '')
                    <h4>Authenticate Naira Wallet</h4>
                @endif
                <form id="user-bank-details" class="mb-4">
                    {{ csrf_field() }}
                    <div class="form-row">
                        <div class="col-md-12">
                            <h6>--Bank Details--</h6>
                            <div class="position-relative form-group">
                                <label>Bank Name</label>
                                <select name="bank_code" id="m_bank_code" class="form-control">
                                    @foreach ($banks as $b)
                                        <option value="{{$b->code}}">{{$b->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="position-relative form-group">
                                <label>Account Number</label>
                                <input type="number" onKeyPress="if(this.value.length==10) return false;" required class="form-control" name="account_number" id="acct_numb">
                            </div>
                            <div class="row">
                                <div class="position-relative form-group col-md-12">
                                    <label>Account Name</label>
                                    <input type="text" required class="form-control " name="account_name" id="account_name" readonly required>
                                </div>
                            </div>
                        </div>

                    </div>
                    <button type="submit" class="mt-2 btn btn-outline-primary" disabled id="m_save">
                        <i class="spinner-border spinner-border-sm" id="s-b" style="display: none;"></i>
                        Save</button>
                </form>
            </div>
        </div>
    </div>
</div>
