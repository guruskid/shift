@php
    $wallet = App\setting::find(1);
@endphp

<div class="modal fade  item-badge-rightm" id="wallet-id" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{route('admin.wallet')}}">
                {{ csrf_field() }}
                <div class="modal-header">
                    <div class="media">
                        <div class="media-body">
                            <h4 class="media-heading" id="card">Wallet id</h4>
                        </div>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label for="lead_message">Wallet Id</label>
                        <input type="hidden" class="form-control" name="name" value="wallet_id" >
                        <input type="text" class="form-control" name="value" value= "{{$wallet->value}} " >
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
