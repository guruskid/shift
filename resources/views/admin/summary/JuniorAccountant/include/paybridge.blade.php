
{{-- tabs --}}
<div class="row mb-2">
    <div class="col-md-3">
        {{-- bg-primary text-white --}}
        <a href="{{ route('admin.junior-summary-details', [ $month , $day, 'paybridge']) }}">
            <div class="card mb-1 widget-content @if ($show_category == "paybridge")
            bg-primary
             @endif">
                <div class="widget-content-wrapper">
                    <div class="widget-heading">
                        <h6 class="text-center @if ($show_category == "paybridge")
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
            <div class="card mb-1 widget-content @if ($show_category == "paybridgewithdrawal")
            bg-primary
             @endif">
                <div class="widget-content-wrapper">
                    <div class="widget-heading">
                        <h6 class="text-center @if ($show_category == "paybridgewithdrawal")
                        text-white
                         @endif">Withdrawal</h6>
                    </div>
                    
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        {{-- bg-primary text-white --}}
        <a href="{{ route('admin.junior-summary-details', [ $month , $day, 'paybridgeothers']) }}">
            <div class="card mb-1 widget-content @if ($show_category == "paybridgeothers")
            bg-primary
             @endif">
                <div class="widget-content-wrapper">
                    <div class="widget-heading">
                        <h6 class="text-center @if ($show_category == "paybridgeothers")
                        text-white
                         @endif">Others</h6>
                    </div>
                    
                </div>
            </div>
        </a>
    </div>
</div>
{{-- tabs end --}}
{{-- paybridge tnx --}}

<div class="table-responsive">
           @if ($show_category == "paybridge")
            <form action="{{ route('admin.junior-summary-sort-details') }}" method="POST">
                @csrf
                <div class="form-inline mb-3">
                    <label class="mr-1">Start Date</label>
                    <input type="datetime-local" name="startdate" value="{{app('request')->input('startdate')}}"  class="form-control mr-1" >

                    <label class="mr-1">End Date</label>
                    <input type="datetime-local" name="enddate" value="{{app('request')->input('enddate')}}" class="form-control mr-1" >
                    <input type="hidden" name="day" value="{{ $day }}">
                    <input type="hidden" name="month" value="{{ $month }}">
                    <input type="hidden" name="category" value="{{ $show_category }}">
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
                    
                    {{-- <input type="number" name="entries" class="form-control mr-1  ml-1" placeholder="Enteries"> --}}
                    <button class="btn btn-primary ml-1"><i class="fa fa-search"></i></button>
                </div>
            </form>
                <div class="card card-body mb-3">
                    @if($show_summary == true)
                            <table class="table ">
                                <thead>
                                    <tr>
                                        <th class="text-center">Total Deposit Transactions</th>
                                        <th class="text-center">Total Deposit Amount Paid</th>
                                        <th class="text-center">Total Deposit Charges</th>
                                        <th class="text-center">Total Deposit Amount</th>
                                        <th class="text-center">Pending Deposit Today</th>
                                        <th class="text-center">Pending Deposit Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                            <td class="text-center">{{ isset($nw_deposit_tnx_total) ? number_format($nw_deposit_tnx_total) : 0 }}</td>
                                            <td class="text-center">₦ {{ isset($nw_deposit_amount_paid) ? number_format($nw_deposit_amount_paid) : 0 }}</td>
                                            <td class="text-center">₦ {{ isset($nw_deposit_tnx_charges) ? number_format($nw_deposit_tnx_charges) : 0 }}</td>
                                            <td class="text-center">₦ {{ isset($nw_deposit_total_amount) ? number_format($nw_deposit_total_amount) :0 }}</td>
                                            <td class="text-center">[{{ isset($nw_deposit_pending_total) ? number_format($nw_deposit_pending_total) :0 }}] ₦ {{ isset($nw_deposit_pending_amount) ? number_format($nw_deposit_pending_amount) :0 }}</td>
                                            <td class="text-center">
                                                [{{ isset($deposit_total_pending) ? number_format($deposit_total_pending) :0 }}]
                                                 ₦ {{ isset($deposit_total_pending_amount) ? number_format($deposit_total_pending_amount) :0 }}
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
                            <th>Extras</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (isset($nw_deposit_tnx))
                            @foreach ($nw_deposit_tnx as $t)
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
                                    <td>{{$t->created_at->format('d M Y h:ia ')}} </td>
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
                                    <td>{{$t->extras}} </td>
                                </tr>
                            @endforeach
                            {{-- {{ $nw_deposit_tnx->links() }} --}}
                        @endif
                    </tbody>
                </table>
                
           @endif
        
           @if ($show_category == "paybridgewithdrawal")
            <form action="{{ route('admin.junior-summary-sort-details') }}" method="POST">
                    @csrf
                    <div class="form-inline mb-3">
                        <label class="mr-1">Start Date</label>
                        <input type="datetime-local" name="startdate" value="{{app('request')->input('startdate')}}" class="form-control mr-1" >

                        <label class="mr-1">End Date</label>
                        <input type="datetime-local" name="enddate" value="{{app('request')->input('enddate')}}" class="form-control mr-1" >
                        <input type="hidden" name="day" value="{{ $day }}">
                        <input type="hidden" name="month" value="{{ $month }}">
                        <input type="hidden" name="category" value="{{ $show_category }}">
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
                        
                        {{-- <input type="number" name="entries" class="form-control mr-1  ml-1" placeholder="Enteries"> --}}
                        <button class="btn btn-primary ml-1"><i class="fa fa-search"></i></button>
                    </div>
            </form>
            <div class="card card-body mb-3">
                @if($show_summary == true)
                        <table class="table ">
                            <thead>
                                <tr>
                                    <th class="text-center">Total Withdrawal Transactions</th>
                                    <th class="text-center">Total Withdrawal Amount Paid</th>
                                    <th class="text-center">Total Withdrawal Charges</th>
                                    <th class="text-center">Total Withdrawal Amount</th>
                                    <th class="text-center">Pending Withdrawal Today</th>
                                    <th class="text-center">Pending Withdrawal Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                        <td class="text-center">{{ isset($nw_withdrawal_tnx_total) ? number_format($nw_withdrawal_tnx_total) : 0 }}</td>
                                        <td class="text-center">₦ {{ isset($nw_withdrawal_amount_paid) ? number_format($nw_withdrawal_amount_paid) : 0 }}</td>
                                        <td class="text-center">₦ {{ isset($nw_withdrawal_tnx_charges) ? number_format($nw_withdrawal_tnx_charges) : 0 }}</td>
                                        <td class="text-center">₦ {{ isset($nw_withdrawal_total_amount) ? number_format($nw_withdrawal_total_amount) :0 }}</td>
                                        <td class="text-center">[{{ isset($nw_withdrawal_pending_total) ? number_format($nw_withdrawal_pending_total) :0 }}] ₦ {{ isset($nw_withdrawal_pending_amount) ? number_format($nw_withdrawal_pending_amount) :0 }}</td>
                                        <td class="text-center">
                                            [{{ isset($withdrawal_total_pending) ? number_format($withdrawal_total_pending) :0 }}]
                                             ₦ {{ isset($withdrawal_total_pending_amount) ? number_format($withdrawal_total_pending_amount) :0 }}
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
                        <th>Extras</th>
                    </tr>
                </thead>
                <tbody>
                    @if (isset($nw_withdrawal_tnx))
                        @foreach ($nw_withdrawal_tnx as $t)
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
                                <td>{{$t->created_at->format('d M Y h:ia ')}} </td>
                                <td>@switch($t->status)
                                    @case('success')
                                    <div class="text-success">{{$t->status}}</div>
                                    @break
                                    @case("failed")
                                    <div class="text-danger">{{$t->status}}</div>
                                    @break
                                    @default
                                    <div class="text-warning">{{$t->status}}</div>
                                    @endswitch </td>
                                <td>{{$t->extras}} </td>
                            </tr>
                        @endforeach
                        {{-- {{ $nw_withdrawal_tnx->links() }} --}}
                    @endif
                </tbody>
            </table>
          @endif

          @if ($show_category == "paybridgeothers")
             <form action="{{ route('admin.junior-summary-sort-details') }}" method="POST">
                @csrf
                <div class="form-inline mb-3">
                    <label class="mr-1">Start Date</label>
                    <input type="datetime-local" name="startdate" value="{{app('request')->input('startdate')}}" class="form-control mr-1">

                    <label class="mr-1">End Date</label>
                    <input type="datetime-local" name="enddate" value="{{app('request')->input('enddate')}}"class="form-control mr-1">
                    <input type="hidden" name="day" value="{{ $day }}">
                    <input type="hidden" name="month" value="{{ $month }}">
                    <input type="hidden" name="category" value="{{ $show_category }}">
                       
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
                    
                    {{-- <input type="number" name="entries" class="form-control mr-1  ml-1" placeholder="Enteries"> --}}
                    <button class="btn btn-primary ml-1"><i class="fa fa-search"></i></button>
                </div>
            </form>
            <div class="card card-body mb-3">
                @if($show_summary == true)
                        <table class="table ">
                            <thead>
                                <tr>
                                    <th class="text-center">Total Transactions</th>
                                    <th class="text-center">Total Amount Paid</th>
                                    <th class="text-center">Total Charges</th>
                                    <th class="text-center">Total Amount</th>
                                    <th class="text-center">Total Pending Today</th>
                                    <th class="text-center">Total Pending Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                        <td class="text-center">{{ isset($nw_other_tnx_total) ? number_format($nw_other_tnx_total) : 0 }}</td>
                                        <td class="text-center">₦ {{ isset($nw_other_amount_paid) ? number_format($nw_other_amount_paid) : 0 }}</td>
                                        <td class="text-center">₦ {{ isset($nw_other_tnx_charges) ? number_format($nw_other_tnx_charges) : 0 }}</td>
                                        <td class="text-center">₦ {{ isset($nw_other_total_amount) ? number_format($nw_other_total_amount) :0 }}</td>
                                        <td class="text-center">[{{ isset($nw_other_pending_total) ? number_format($nw_other_pending_total) :0 }}] ₦ {{ isset($nw_other_pending_amount) ? number_format($nw_other_pending_amount) :0 }}</td>
                                        <td class="text-center">
                                            [{{ isset($other_total_pending) ? number_format($other_total_pending) :0 }}]
                                             ₦ {{ isset($other_total_pending_amount) ? number_format($other_total_pending_amount) :0 }}
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
                        <th>Extras</th>
                    </tr>
                </thead>
                <tbody>
                    @if (isset($nw_other_tnx))
                        @foreach ($nw_other_tnx as $t)
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
                                <td>{{$t->created_at->format('d M Y h:ia ')}} </td>
                                <td>@switch($t->status)
                                    @case('success')
                                    <div class="text-success">{{$t->status}}</div>
                                    @break
                                    @case("failed")
                                    <div class="text-danger">{{$t->status}}</div>
                                    @break
                                    @default
                                    <div class="text-warning">{{$t->status}}</div>
                                    @endswitch  </td>
                                <td>{{$t->extras}} </td>
                            </tr>
                        @endforeach
                        {{-- {{ $nw_other_tnx->links() }} --}}
                    @endif
                </tbody>
            </table>
            @endif
      </div>
</div>

{{-- paybridge tnx end --}}