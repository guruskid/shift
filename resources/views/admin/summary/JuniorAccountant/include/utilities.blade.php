
                            {{-- util tnx start --}}
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
                                    
                                    <button class="btn btn-primary ml-1"><i class="fa fa-search"></i></button>
                                </div>
                            </form>
                            <div class="table-responsive">
                                        
                                <div class="card card-body mb-3">
                                    @if($show_summary == true)
                                    <table class="table ">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Total Transactions</th>
                                                <th class="text-center">Total Amount</th>
                                                <th class="text-center">Convenience fee Total</th>
                                                <th class="text-center">Total Amount Paid</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                    <td class="text-center">{{ isset($util_total_tnx) ? number_format($util_total_tnx) : 0 }}</td>
                                                    <td class="text-center">₦ {{ isset($util_tnx_amount) ? number_format($util_tnx_amount) : 0 }}</td>
                                                    <td class="text-center">₦ {{ isset($util_tnx_fee) ? number_format($util_tnx_fee) : 0 }}</td>
                                                    <td class="text-center">₦ {{ isset($util_amount_paid) ? number_format($util_amount_paid) : 0 }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    @endif
                                </div>
                                <table class="mb-2 table table-bordered transactions-table">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Reference ID</th>
                                            <th class="text-center">Date</th>
                                            <th class="text-center">User</th>
                                            <th class="text-center">Amount</th>
                                            <th class="text-center">Convenience fee</th>
                                            <th class="text-center">Total</th>
                                            <th class="text-center">Type</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Extras</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (isset($util_tnx))
                                            @foreach ($util_tnx as $t)
                                            <tr>
                                                <td class="text-center text-muted">{{$t->reference_id}}</td>
                                                <td class="text-center text-muted">{{$t->updated_at->format('d M, H:ia')}}</td>
                                                <td class="text-center">
                                                    <a
                                                        href="{{route('admin.user', [$t->user->id ?? ' ', $t->user->email ?? ' ' ] )}}">

                                                    @if(strlen($t->user->first_name) < 3 )
                                                        {{$t->user->email}}
                                                    @else
                                                        {{$t->user->first_name}}
                                                    @endif

                                                    </a>
                                                </td>
                                                {{-- <td class="text-center">{{$t->user->first_name}}</td> --}}

                                                <td class="text-center">{{$t->amount}}</td>
                                                <td class="text-center">{{$t->convenience_fee}}</td>
                                                <td class="text-center">{{$t->total}}</td>
                                                <td class="text-center">{{$t->type}}</td>
                                                <td class="text-center">
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
                                                <td class="text-center" style="word-wrap: break-word;min-width: 160px;max-width: 160px;">
                                                    <pre>{{$t->extras}}</pre>
                                                </td>
                                            </tr>
                                            @endforeach
                                        
                                            {{-- {{ $util_tnx->links() }} --}}
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            {{-- util tnx end --}}