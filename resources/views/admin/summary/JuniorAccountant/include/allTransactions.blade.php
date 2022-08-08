
                           {{-- all tnx start --}}
                           <form action="{{ route('admin.junior-summary-sort-details') }}" method="POST">
                                @csrf
                                <div class="form-inline mb-3">
                                    <label class="mr-1">Start Date</label>
                                    <input type="datetime-local" name="startdate"  value="{{app('request')->input('startdate')}}"class="form-control mr-1" >

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
                            <div class="table-responsive">
                                <div class="card card-body mb-3">
                                    @if($show_summary == true)
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Total Transactions</th>
                                                <th class="text-center">Total Crypto Transactions</th>
                                                <th class="text-center">Total Giftcard Transactions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                    <td class="text-center">{{ isset($all_tnx_count) ? number_format($all_tnx_count) : 0 }}</td>
                                                    
                                                    <td>
                                                        <table class="table">
                                                            <thead>
                                                                <th class="text-center">Buy</th>
                                                                <th class="text-center">Sell</th>
                                                            </thead>
                                                            <tbody>
                                                                <td class="text-center">{{ isset($crypto_totaltnx_buy) ? number_format($crypto_totaltnx_buy) : 0 }} 
                                                                    [{{sprintf('%.8f', floatval($bitcoin_total_tnx_buy))}} BTC]</td>
                                                                <td class="text-center">{{ isset($crypto_totaltnx_sell) ? number_format($crypto_totaltnx_sell) : 0 }} 
                                                                    [{{sprintf('%.8f', floatval($bitcoin_total_tnx_sell))  }} BTC]</td>
                                                            </tbody>
                                                            <tfoot>
                                                                <td>
                                                                    <div>Total <h6 class="text-right">${{ $crypto_totaltnx_buy_amount }}</h6></div>
                                                                    <div>Total Naira <h6 class="text-right">N{{ $crypto_totaltnx_buy_amount_naira }}</h6></div>
                                                                </td>
                                                                <td>
                                                                    <div>Total <h6 class="text-right">${{ $crypto_totaltnx_sell_amount }}</h6></div>
                                                                    <div>Total Naira <h6 class="text-right">N{{ $crypto_totaltnx_sell_amount_naira }}</h6></div>
                                                                </td>
                                                            </tfoot>
                                                        </table>
                                                    </td>
                                                    <td>
                                                        <table class="table">
                                                            <thead>
                                                                <th class="text-center">Buy</th>
                                                                <th class="text-center">Sell</th>
                                                            </thead>
                                                            <tbody>
                                                                <td class="text-center">{{ isset($giftcards_totaltnx_buy) ? number_format($giftcards_totaltnx_buy) : 0 }}</td>
                                                                <td class="text-center">{{ isset($giftcards_totaltnx_sell) ? number_format($giftcards_totaltnx_sell) : 0 }}</td>
                                                            </tbody>
                                                            <tfoot>
                                                                <td>
                                                                    <div>Total <h6 class="text-right">${{ $giftcards_totaltnx_buy_amount }}</h6></div>
                                                                    <div>Total Naira <h6 class="text-right">N{{ $giftcards_totaltnx_buy_amount_naira }}</h6></div>
                                                                </td>
                                                                <td>
                                                                    <div>Total <h6 class="text-right">${{ $giftcards_totaltnx_sell_amount }}</h6></div>
                                                                    <div>Total Naira <h6 class="text-right">N{{ $giftcards_totaltnx_sell_amount_naira }}</h6></div>
                                                                </td>
                                                            </tfoot>
                                                        </table>
                                                    </td>
                                                    {{-- <td class="text-center">{{ isset($giftcards_totaltnx) ? number_format($giftcards_totaltnx) : 0 }}</td> --}}
                                            </tr>
                                        </tbody>
                                    </table>
                                    @endif
                                </div>
                                <table class="mb-2 table table-bordered transactions-table">
                                    <thead>
                                        <tr>
                                        <th class="text-center">ID</th>
                                        <th class="text-center">Asset</th>
                                        <th class="text-center">Trade type</th>
                                        <th class="text-center">Currency</th>
                                        <th class="text-center">Card type</th>
                                        <th class="text-center">Asset value</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-center">Card price</th>
                                        @if (in_array(Auth::user()->role, [444,449] ))
                                        <th class="text-center">Cash value</th>
                                        @endif
                                        @if (!in_array(Auth::user()->role, [449,444] ))
                                        <th class="text-center">User Amount</th>
                                        @endif
                                        @if (in_array(Auth::user()->role, [999] ))
                                            <th class="text-center">Commission</th>
                                            <th class="text-center">Chinese Amount</th>
                                        @endif
                                        @if (!in_array(Auth::user()->role, [449,444] ))
                                        <th class="text-center">Wallet ID</th>
                                        @endif
                                        <th class="text-center">User</th>
                                        <th class="text-center">Date</th>
                                        <th class="text-center">Status</th>
                                        @if (in_array(Auth::user()->role, [999, 889] ))
                                        <th class="text-center">Last Edit</th>
                                        <th class="text-center">Agent</th>
                                        <th class="text-center">Accountant</th>
                                        @endif
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @if (isset($all_tnx))
                                        @foreach ($all_tnx as $t)
                                        @php
                                        $c = $t->card;
                                        @endphp

                                        <tr>
                                            <td class="text-center text-muted">{{$t->uid}}</td>
                                            <td
                                                class="text-center  {{$c == 'perfect money' || $c == 'bitcoins' || $c == 'etherum' ? 'text-info   ': '' }} ">
                                                {{ucwords($t->card)}}</td>
                                            <td class="text-center text-capitalize">{{$t->type}}</td>
                                            <td class="text-center">{{$t->country}}</td>
                                            <td class="text-center">{{$t->card_type}}</td>
                                            <td class="text-center">{{$t->amount}}</td>

                                            @if ($t->asset->is_crypto)
                                            <td class="text-center">{{ sprintf('%.8f', floatval($t->quantity))}}</td>
                                            @else
                                            <td class="text-center">{{ $t->quantity}}</td>
                                            @endif
                                            <td class="text-center">{{$t->card_price}}</td>
                                            @if (in_array(Auth::user()->role, [444,449] ))
                                            <td class="text-center">N{{number_format($t->amount_paid)}}</td>
                                            @endif

                                            {{-- <td class="text-center">{{$t->wallet_id}}</td> --}}
                                            {{-- @if (isset($t->user))
                                            <td class="text-center">
                                                @if (in_array(Auth::user()->role, [555] ))
                                                    <a
                                                    href=" {{route('customerHappiness.user', [$t->user->id, $t->user->email] )}}">
                                                    {{$t->user->first_name." ".$t->user->last_name}}</a>
                                                @else
                                                    <a
                                                    href=" {{route('admin.user', [$t->user->id, $t->user->email] )}}">
                                                    {{$t->user->first_name." ".$t->user->last_name}}</a>
                                                @endif

                                            </td>
                                            @endif --}}

                                            @if (!in_array(Auth::user()->role, [449,444] ))
                                            <td class="text-center">N{{number_format($t->amount_paid - $t->commission)}}</td>
                                            @endif
                                            @if (in_array(Auth::user()->role, [999] ))
                                                <td class="text-center">{{$t->commission}}</td>
                                                <td class="text-center">N{{number_format($t->amount_paid)}}</td>

                                            @endif
                                            @if (!in_array(Auth::user()->role, [449,444] ))
                                            <td class="text-center">{{$t->wallet_id}}</td>
                                            @endif

                                            <td class="text-center">
                                                @if (isset($t->user))
                                                    @if (in_array(Auth::user()->role, [555] ))
                                                        <a
                                                        href=" {{route('customerHappiness.user', [$t->user->id, $t->user->email] )}}">
                                                        {{$t->user->first_name." ".$t->user->last_name}}</a>
                                                    @else
                                                        @if (in_array(Auth::user()->role, [449,444] ))
                                                        {{$t->user->first_name." ".$t->user->last_name}}
                                                        @else
                                                        <a
                                                        href=" {{route('admin.user', [$t->user->id, $t->user->email] )}}">
                                                        {{$t->user->first_name." ".$t->user->last_name}}</a>
                                                        @endif


                                                    @endif
                                                @endif
                                            </td>

                                            <td class="text-center">{{$t->created_at->format('d M, H:ia')}} </td>
                                            <td class="text-center">
                                                @switch($t->status)
                                                @case('success')
                                                <div class="text-success">{{$t->status}}</div>
                                                @break
                                                @case("failed")
                                                <div class="text-danger">{{$t->status}}</div>
                                                @break
                                                @case('declined')
                                                <div class="text-warning">{{$t->status}}</div>
                                                @break
                                                @case('waiting')
                                                <div class="text-info">{{$t->status}}</div>
                                                @break
                                                @default
                                                <div class="text-success">{{$t->status}}</div>

                                                @endswitch
                                            </td>
                                            @if (in_array(Auth::user()->role, [999, 889] ))
                                            <td class="text-center"> {{$t->last_edited}} </td>
                                            <td class="text-center"> {{$t->agent->first_name}} </td>
                                            <td class="text-center"> {{$t->accountant->first_name ?? 'None' }} </td>
                                            @endif

                                            
                                        </tr>
                                        
                                        @endforeach
                                    </tbody>
                                        
                                    {{-- {{ $all_tnx->links() }} --}}
                                    @endif


                                </table>
                            </div>
                            {{-- all tnx end --}}