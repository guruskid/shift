
{{-- tabs --}}
<div class="row mb-2">
    <div class="col-md-3">
        {{-- bg-primary text-white --}}
        <a href="{{ route('admin.junior-summary-details', [ $month , $day, 'paybridge']) }}">
            <div class="card mb-1 widget-content @if ($showCategory == "paybridge")
            bg-primary
             @endif">
                <div class="widget-content-wrapper">
                    <div class="widget-heading">
                        <h6 class="text-center @if ($showCategory == "paybridge")
                        text-white
                         @endif">Deposit</h5>
                    </div>
                    
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        {{-- bg-primary text-white --}}
        <a href="{{ route('admin.junior-summary-details', [ $month , $day, 'paybridgewithdrawal']) }}">
            <div class="card mb-1 widget-content @if ($showCategory == "paybridgewithdrawal")
            bg-primary
             @endif">
                <div class="widget-content-wrapper">
                    <div class="widget-heading">
                        <h6 class="text-center @if ($showCategory == "paybridgewithdrawal")
                        text-white
                         @endif">Withdrawal</h6>
                    </div>
                    
                </div>
            </div>
        </a>
    </div>
</div>
{{-- tabs end --}}
{{-- paybridge tnx --}}

<div class="table-responsive">
    <form action="{{ route('admin.junior-summary-sort-details') }}" method="POST">
        @csrf
        <div class="form-inline mb-3">
            <label class="mr-1">Start Date</label>
            <input type="datetime-local" name="startdate" value="{{app('request')->input('startdate')}}"  class="form-control mr-1" >

            <label class="mr-1">End Date</label>
            <input type="datetime-local" name="enddate" value="{{app('request')->input('enddate')}}" class="form-control mr-1" >
            <input type="hidden" name="day" value="{{ $day }}">
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="category" value="{{ $showCategory }}">
            {{-- @if (isset($accountant))
                @foreach ($accountant as $a)
                    <input type="hidden" name="name" value="{{ $a->first_name }}" class="form-control mr-4">
                @endforeach
            @endif --}}

            @if (isset($accountant))
                <select name="Accountant" class="ml-1 form-control">
                    <option value="null">Accountant</option>
                    @foreach ($accountant as $a) 
                        <option value="{{ $a->id }}">{{ $a->first_name ?:$a->email }}</option>
                    @endforeach
                </select>
            @endif
            
            <button class="btn btn-primary ml-1"><i class="fa fa-search"></i></button>
        </div>
    </form>
    <div class="card card-body mb-3">
        @if($showSummary == true)
                <table class="table ">
                    <thead>
                        @if($showCategory == "paybridgewithdrawal")
                        <tr>
                            <th class="text-center">Total Withdrawal Transactions</th>
                            <th class="text-center">Total Withdrawal Amount Paid</th>
                            <th class="text-center">Total Withdrawal Charges</th>
                            <th class="text-center">Total Withdrawal Amount</th>
                            <th class="text-center">Average Response Time</th>
                            <th class="text-center">Pending Withdrawal Today</th>
                            <th class="text-center">Pending Withdrawal Total</th>
                        </tr>
                        @else
                        <tr>
                            <th class="text-center">Total Deposit Transactions</th>
                            <th class="text-center">Total Deposit Amount Paid</th>
                            <th class="text-center">Total Deposit Charges</th>
                            <th class="text-center">Total Deposit Amount</th>
                            <th class="text-center">Average Response Time</th>
                            <th class="text-center">Pending Deposit Today</th>
                            <th class="text-center">Pending Deposit Total</th>
                        </tr>
                        @endif
                    </thead>
                    <tbody>
                        <tr>
                                <td class="text-center">{{ isset($payBridgeTransactionsCount) ? number_format($payBridgeTransactionsCount) : 0 }}</td>
                                <td class="text-center">₦ {{ isset($payBridgeTransactionsAmountPaid) ? number_format($payBridgeTransactionsAmountPaid) : 0 }}</td>
                                <td class="text-center">₦ {{ isset($payBridgeTransactionsCharges) ? number_format($payBridgeTransactionsCharges) : 0 }}</td>
                                <td class="text-center">₦ {{ isset($payBridgeTransactionsAmount) ? number_format($payBridgeTransactionsAmount) :0 }}</td>
                                <td class="text-center">{{ $averageResponseTime }}</td>
                                <td class="text-center">[{{ isset($payBridgeTransactionsPendingAmount) ? number_format($payBridgeTransactionsPendingAmount) :0 }}] ₦ {{ isset($nw_deposit_pending_amount) ? number_format($nw_deposit_pending_amount) :0 }}</td>
                                
                                <td class="text-center">
                                    [{{ isset($pendingTotalCount) ? number_format($pendingTotalCount) :0 }}]
                                        ₦ {{ isset($pendingTotalAmount) ? number_format($pendingTotalAmount) :0 }}
                                </td>
                        </tr>
                    </tbody>
                </table>
            @endif
    </div>  
                
        <table class="mb-2 table table-bordered transactions-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Reff</th>
                    <th>User Name</th>
                    <th>Trans. Type</th>
                    <th>Amount Paid</th>
                    <th>Total Charge</th>
                    <th>Total</th>
                    <th>Prev. Bal </th>
                    <th>Cur. Bal</th>
                    <th>Cr. Acct.</th>
                    <th>Debit Acct.</th>
                    <th>Narration</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Response Time</th>
                    <th>Extras</th>
                </tr>
            </thead>
            <tbody>
                @if (isset($payBridgeTransactions))
                    @foreach ($payBridgeTransactions as $t)
                        <tr>
                            <td>{{$t->id}} </td>
                            <td>{{$t->reference}} </td>
                            <td>
                                <a
                                    href="{{route('admin.user', [$t->user->id ?? ' ', $t->user->email ?? ' ' ] )}}">

                                @if(strlen($t->user->first_name) < 3 )
                                    {{$t->user->email}}
                                @else
                                    {{$t->user->first_name}}
                                @endif

                                </a>
                            </td>
                            <td>{{$t->transactionType->name}} </td>
                            <td>₦{{number_format($t->amount_paid) }} </td>
                            <td>₦{{number_format($t->charge) }} </td>
                            <td>₦{{number_format($t->amount) }} </td>
                            <td>₦{{number_format($t->previous_balance) }}</td>
                            <td>₦{{number_format($t->current_balance) }} </td>
                            <td>{{$t->cr_acct_name}} </td>
                            <td>{{$t->dr_acct_name}} </td>
                            <td>{{$t->narration}} </td>
                            <td>{{$t->updated_at->format('d M Y h:ia ')}} </td>
                            <td>
                                @switch($t->status)
                                @case('success')
                                <div class="text-success">{{$t->status}}</div>
                                @break
                                @case("failed")
                                <div class="text-danger">{{$t->status}}</div>
                                @break
                                @default
                                <div class="text-warning">{{$t->status}}</div>
                                @endswitch
                            </td>
                            @if ($t->status == 'pending')
                                <td>{{ now()->diffForHumans($t->created_at) }}</td>
                                @else
                                <td>{{ $t->updated_at->diffForHumans($t->created_at) }}</td>
                            @endif
                            <td>{{$t->extras}} </td>
                        </tr>
                    @endforeach
                    {{-- {{ $nw_deposit_tnx->links() }} --}}
                @endif
            </tbody>
        </table>
                
      </div>
</div>

{{-- paybridge tnx end --}}