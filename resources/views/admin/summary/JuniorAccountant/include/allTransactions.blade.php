
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            {{-- bg-primary text-white --}}
                                <div class="card mb-1 widget-content ">
                                    <div class="widget-content-wrapper">
                                        <div class="widget-heading">
                                            <h6 class="text-center" id='revenue_growth_summary_name'>% Revenue Growth(Monthly)</h6>
                                            <div class="widget-n" style="justify-content: center; text-align: center;">
                                                <div id='revenue_growth_summary_a' class="d-block">
                                                    @if($revenueGrowth->revenueGrowth <= 0)
                                                    <h5 class="text-danger">{{ $revenueGrowth->revenueGrowth }} %</h5>
                                                    @else
                                                    <h5 class="text-success" >{{ $revenueGrowth->revenueGrowth }} %</h5>
                                                    @endif
                                                </div>

                                                <div id='revenue_growth_summary_b' class="d-none">
                                                    <h5 class="" id='revenue_growth_summary' >.......</h5>
                                                </div>
                                            </div>
                                            <div class="form-group mr-2">
                                                <select name="sortingType" id='revenue_growth_summary_sort' onchange="revenueGrowthSort()" class="form-control">
                                                    <option value="noData">SortingType</option>
                                                    <option value="weekly">Weeekly</option>
                                                    <option value="monthly">Monthly</option>
                                                    <option value="quarterly">Quaterly</option>
                                                    <option value="yearly">Yearly</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 mb-2">
                                {{-- bg-primary text-white --}}
                                    <div class="card mb-1 widget-content ">
                                        <div class="widget-content-wrapper">
                                            <div class="widget-heading">
                                                <h6 class="text-center" id='average_revenue_unique_summary_name'>Average Revenue Per Unique User(Monthly)</h6>
                                                <div class="widget-n" style="justify-content: center; text-align: center;">
                                                    <div id='average_revenue_unique_summary_a' class="d-block">
                                                        <h5 class="text-success" >${{ $averageRevenuePerUniqueUsers->averageRevenuePerUser }}</h5>
                                                    </div>
    
                                                    <div id='average_revenue_unique_summary_b' class="d-none">
                                                        <h5 class="" id='average_revenue_unique_summary' >.......</h5>
                                                    </div>
                                                </div>
                                                <div class="form-group mr-2">
                                                    <select name="sortingType" id='average_revenue_unique_summary_sort' onchange="averageRevenuePerUniqueUser()" class="form-control">
                                                        <option value="noData">SortingType</option>
                                                        <option value="weekly">Weeekly</option>
                                                        <option value="monthly">Monthly</option>
                                                        <option value="quarterly">Quaterly</option>
                                                        <option value="yearly">Yearly</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                           {{-- all tnx start --}}
                           <form action="{{ route('admin.junior-summary-sort-details') }}" method="POST">
                            @csrf
                            <div class="form-inline mb-3 mt-3">
                                <label class="mr-1">Start Date</label>
                                <input type="datetime-local" name="startdate"  value="{{app('request')->input('startdate')}}"class="form-control mr-1" >

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
                                                <option value="{{ $a->id }}">
                                                    @if($a->first_name)
                                                    {{ $a->first_name." ".$a->last_name }}
                                                    @else
                                                    {{ $a->email }}
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif

                                    <button class="btn btn-primary ml-1"><i class="fa fa-search"></i></button>
                                </div>
                            </form>
                            <div class="table-responsive">
                                <div class="card card-body mb-3">
                                    @if($showSummary == true)
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Total Transactions</th>
                                                <th class="text-center">Total BItcoin Transactions</th>
                                                <th class="text-center">Total USDT Transactions</th>
                                                <th class="text-center">Total Giftcard Transactions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                    <td class="text-center">{{ number_format($transactionCount) }}
                                                        <table class="table">
                                                            <thead>
                                                                <th class="text-center">Buy</th>
                                                                <th class="text-center">Sell</th>
                                                            </thead>
                                                            <tbody>
                                                                <td class="text-center">{{ number_format($transactionBuyCount) }} </td>
                                                                <td class="text-center">{{ number_format($transactionSellCount) }} </td>
                                                            </tbody>
                                                            <tfoot>
                                                                <td>
                                                                    <div>Total Naira <h6 class="text-center">₦{{ number_format($transactionBuyNairaValue) }}</h6></div>
                                                                </td>
                                                                <td>
                                                                    <div>Total Naira <h6 class="text-center">₦{{ number_format($transactionSellNairaValue) }}</h6></div>
                                                                </td>
                                                            </tfoot>
                                                        </table>

                                                    <td>
                                                        <table class="table">
                                                            <thead>
                                                                <th class="text-center">Buy</th>
                                                                <th class="text-center">Sell</th>
                                                            </thead>
                                                            <tbody>
                                                                <td class="text-center">{{ number_format($bitcoinBuyCount) }}
                                                                    [{{sprintf('%.8f', floatval($bitcoinBuyQuantity))}} BTC]</td>
                                                                <td class="text-center">{{ number_format($bitcoinSellCount) }}
                                                                    [{{sprintf('%.8f', floatval($bitcoinSellQuantity))  }} BTC]</td>
                                                            </tbody>
                                                            <tfoot>
                                                                <td>
                                                                    <div>Total <h6 class="text-right">${{ number_format($bitcoinBuyUsdValue) }}</h6></div>
                                                                    <div>Total Naira <h6 class="text-right">₦{{ number_format($bitcoinBuyNairaValue) }}</h6></div>
                                                                </td>
                                                                <td>
                                                                    <div>Total <h6 class="text-right">${{ number_format($bitcoinSellUsdValue) }}</h6></div>
                                                                    <div>Total Naira <h6 class="text-right">₦{{ number_format($bitcoinSellNairaValue) }}</h6></div>
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
                                                                <td class="text-center">{{ number_format($usdtBuyCount) }}
                                                                    [{{ $usdtBuyQuantity }} USDT]</td>
                                                                <td class="text-center">{{ number_format($usdtSellCount)}}
                                                                    [{{ $usdtSellQuantity }} USDT]</td>
                                                            </tbody>
                                                            <tfoot>
                                                                <td>
                                                                    <div>Total <h6 class="text-right">${{ number_format($usdtBuyUsdValue) }}</h6></div>
                                                                    <div>Total Naira <h6 class="text-right">₦{{ number_format($usdtBuyNairaValue) }}</h6></div>
                                                                </td>
                                                                <td>
                                                                    <div>Total <h6 class="text-right">${{ number_format($usdtSellUsdValue) }}</h6></div>
                                                                    <div>Total Naira <h6 class="text-right">₦{{ number_format($usdtSellNairaValue) }}</h6></div>
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
                                                                <td class="text-center">{{ number_format($giftCardBuyCount) }}</td>
                                                                <td class="text-center">{{ number_format($giftCardSellCount) }}</td>
                                                            </tbody>
                                                            <tfoot>
                                                                <td>
                                                                    <div>Total <h6 class="text-right">${{ $giftCardBuyUsdValue }}</h6></div>
                                                                </td>
                                                                <td>
                                                                    <div>Total <h6 class="text-right">${{ $giftCardSellUsdValue }}</h6></div>
                                                                    <div>Total Naira <h6 class="text-right">₦{{ $giftCardSellNairaValue }}</h6></div>
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
                                        <th class="text-center">Total Asset</th>
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
                                        @if (isset($transactions))
                                        @foreach ($transactions as $t)
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
                                        <td class="text-center">{{ $t->amount * $t->quantity}}</td>
                                        <td class="text-center">{{$t->card_price}}</td>
                                        @if (in_array(Auth::user()->role, [444,449] ))
                                        <td class="text-center">₦{{number_format($t->amount_paid)}}</td>
                                        @endif

                                        @if (!in_array(Auth::user()->role, [449,444] ))
                                        <td class="text-center">₦{{number_format($t->amount_paid, 2, '.', ',')}}</td>
                                        @endif
                                        @if (in_array(Auth::user()->role, [999] ))
                                            <td class="text-center">{{$t->commission}}</td>
                                            <td class="text-center">₦{{number_format($t->amount_paid + $t->commission,2, '.', ',')}}</td>

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

                                            <td class="text-center">{{$t->updated_at->format('d M, h:ia')}} </td>
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
