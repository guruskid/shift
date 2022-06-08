<div class="modal fade  item-badge-rightm" id="add-bank-modal" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <form id="user-bank-details" class="mb-4">
                    {{ csrf_field() }}
                    <div class="form-row ">
                        <div class="col-md-12">
                            <div class="position-relative form-group">
                                <label>Bank Name</label>
                                <select name="bank_code" class="form-control">
                                    @foreach ($banks as $b)
                                    <option value="{{$b->code}}">{{$b->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="position-relative form-group">
                                <label>Account Number</label>
                                <input type="number" onKeyPress="if(this.value.length==10) return false;" required class="form-control" name="account_number">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="position-relative form-group col-md-6">
                                    <label>Bank Firstname</label>
                                    @if (Auth::user()->accounts->count() == 0)
                                    <input type="text" required class="form-control " name="first_name">
                                    @else
                                    <input type="text" required class="form-control" readonly value="{{ Auth::user()->first_name }}" name="first_name">
                                    @endif
                                </div>

                                <div class="position-relative form-group col-md-6">
                                    <label>Bank Lastname</label>
                                    @if (Auth::user()->accounts->count() == 0)
                                    <input type="text" required class="form-control " name="last_name">
                                    @else
                                    <input type="text" required class="form-control" readonly value="{{ Auth::user()->first_name }}" name="last_name">
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                    <button type="submit" id="sign-up-btn" class="mt-2 btn btn-outline-primary">
                        <i class="spinner-border spinner-border-sm" id="s-b" style="display: none;"></i>
                        Save</button>
                </form>
            </div>
        </div>
    </div>
</div>
