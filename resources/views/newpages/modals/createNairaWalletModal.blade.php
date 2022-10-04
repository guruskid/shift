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
                        @if (Auth::user()->accounts->count() == 0)
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
                                    <div class="position-relative form-group col-md-6">
                                        <label>Bank Firstname</label>
                                        @if (Auth::user()->accounts->count() == 0)
                                            <input type="text" required class="form-control " name="first_name" id="m_first_name" readonly>
                                        @else
                                            <input type="text" required class="form-control" value="{{ Auth::user()->first_name }}" name="first_name" id="m_first_name" readonly>
                                        @endif
                                    </div>

                                    <div class="position-relative form-group col-md-6">
                                        <label>Bank Lastname</label>
                                        @if (Auth::user()->accounts->count() == 0)
                                            <input type="text" required class="form-control " name="last_name" id="m_last_name" readonly>
                                        @else
                                            <input type="text" required class="form-control" readonly value="{{ Auth::user()->last_name }}" name="last_name" id="last_name" readonly>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if (Auth::user()->pin == '')
                            <div class="col-md-12">
                                <h6>--Create Wallet Pin--</h6>
                                <div class="row">
                                    <div class="position-relative form-group col-md-6">
                                        <label for="">Wallet Password (4 digits)</label>
                                        <input type="password" class="form-control " required name="password" minlength="4"
                                            maxlength="4" placeholder="- - - -">
                                    </div>
            
                                    <div class="position-relative form-group col-md-6">
                                        <label for="">Confirm password</label>
                                        <input type="password" class="form-control " required name="password_confirmation"
                                            placeholder="- - - -">
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <button type="submit" class="mt-2 btn btn-outline-primary">
                        <i class="spinner-border spinner-border-sm" id="s-b" style="display: none;"></i>
                        Save</button>
                </form>
            </div>
        </div>
    </div>
</div>
