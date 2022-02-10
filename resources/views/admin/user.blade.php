@extends('layouts.app')
@section('content')
<div class="app-main">
    <div class="app-sidebar sidebar-shadow">
        <div class="app-header__logo">
            <div class="logo-src"></div>
            <div class="header__pane ml-auto">
                <div>
                    <button type="button" class="hamburger close-sidebar-btn hamburger--elastic"
                        data-class="closed-sidebar">
                        <span class="hamburger-box">
                            <span class="hamburger-inner"></span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
        <div class="app-header__mobile-menu">
            <div>
                <button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
                    <span class="hamburger-box">
                        <span class="hamburger-inner"></span>
                    </span>
                </button>
            </div>
        </div>
        <div class="app-header__menu">
            <span>
                <button type="button" class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
                    <span class="btn-icon-wrapper">
                        <i class="fa fa-ellipsis-v fa-w-6"></i>
                    </span>
                </button>
            </span>
        </div>
        {{-- User Side bar --}}
        @include('layouts.partials.admin')
    </div>

    {{-- Content Starts here --}}
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="app-page-title">
                <div class="page-title-wrapper">
                    <div class="page-title-heading">
                        <div class="page-title-icon" style=" height: 150px; width: 150px " >
                            <a href="{{asset('storage/avatar/'.$user->dp)}}"><img
                                    src=" {{asset('storage/avatar/'.$user->dp)}} " height="120px" alt=""></a>
                        </div>
                        <div>{{$user->first_name." ".$user->last_name}} <br>{{$user->username}}<br>
                            <span class="text-custom">
                                @if ($user->nairaWallet)
                                ₦{{number_format($user->nairaWallet->amount ) }}
                                @else
                                No Naira wallet
                                @endif
                            </span>
                            <span class="text-custom">
                                @if ($btc_wallet)
                                <p>{{ number_format((float)$btc_wallet->balance,8) }} BTC</p>
                                <p>{{ $btc_wallet->address }}</p>
                                @else
                                No Bitcoin wallet
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                            <ul class="nav nav-tabs nav-justified">
                                <li class="nav-item"><a data-toggle="tab" href="#tab-eg11-0"
                                        class="active nav-link">Profile</a></li>
                                <li class="nav-item"><a data-toggle="tab" href="#tab-eg11-1" class="nav-link">Bank <div
                                            class="d-none d-md-block ml-1"> details</div> </a></li>
                                <li class="nav-item"><a data-toggle="tab" href="#tab-eg11-4"
                                        class="nav-link">Naira Wallet transactions</a></li>
                                <li class="nav-item"><a data-toggle="tab" href="#btc-txns"
                                    class="nav-link">Bitcoin Wallet transactions</a></li>
                                <li class="nav-item"><a data-toggle="tab" href="#tab-eg11-2"
                                    class="nav-link">Transactions</a></li>
                                <li class="nav-item"><a data-toggle="tab" href="#tab-eg11-3" class="nav-link">Id
                                        Card</a></li>
                            </ul>
                            <div class="tab-content">
                                {{-- Profile --}}
                                <div class="tab-pane active" id="tab-eg11-0" role="tabpanel">
                                    <div class="tab-pane active" id="tab-eg115-0" role="tabpanel">
                                        <form class="" id="user-profile-form">
                                            {{ csrf_field() }}
                                            <div class="form-row">
                                                <div class="col-md-6">
                                                    <div class="position-relative form-group">
                                                        <label>First Name</label>
                                                        <input type="text" class="form-control " name="first_name" {{ auth()->user()->role == 555 ? 'disabled' : '' }}
                                                            value="{{$user->first_name}}">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="position-relative form-group">
                                                        <label>Last Name</label>
                                                        <input type="text" class="form-control " name="last_name" {{ auth()->user()->role == 555 ? 'disabled' : '' }}
                                                            value="{{$user->last_name}}">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="col-md-6">
                                                    <div class="position-relative form-group">
                                                        <label>Your Email</label>
                                                        <input type="text" readonly class="form-control"
                                                            value="{{$user->email}}">
                                                    </div>


                                                </div>
                                                <div class="col-md-6">
                                                    <div class="position-relative form-group">
                                                        <label>Phone</label>
                                                        <input type="text" class="form-control" name="phone" {{ auth()->user()->role == 555 ? 'disabled' : '' }}
                                                            value="{{$user->phone}}">
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="submit" class="mt-2 btn btn-outline-primary" {{ auth()->user()->role == 555 ? 'disabled' : '' }}>
                                                <i class="spinner-border spinner-border-sm" id="s-p"
                                                    style="display: none;"></i>
                                                Save</button>
                                        </form>
                                    </div>
                                </div>

                                {{-- Bank details --}}
                                <div class="tab-pane" id="tab-eg11-1" role="tabpanel">
                                    <div class="form-row ">
                                        @foreach ($user->accounts as $acct)
                                        <div class="col-md-4">
                                            <div class="card card-body text-center">
                                                <h5 class="text-custom">{{$acct->account_name}} </h5>
                                                <h6>{{$acct->account_number}}</h6>
                                                <h6>{{$acct->bank_name}}</h6>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Transactions --}}
                                <div class="tab-pane" id="tab-eg11-2" role="tabpanel">
                                    <div class="table-responsive">
                                        <table
                                            class="align-middle mb-0 table table-borderless table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">#</th>
                                                    <th class="text-center">Asset Type</th>
                                                    <th class="text-center">Tran. type</th>
                                                    <th class="text-center">Asset Value</th>
                                                    <th class="text-center">Cash value</th>
                                                    <th class="text-center">Date</th>
                                                    <th class="text-center">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($transactions as $t)
                                                <tr>
                                                    <td class="text-center text-muted">{{$t->id}}</td>
                                                    <td class="text-center">{{ucwords($t->card)}}</td>
                                                    <td class="text-center">{{$t->type}}</td>
                                                    <td class="text-center">{{$t->amount}}</td>
                                                    <td class="text-center">{{number_format($t->amount_paid)}}</td>
                                                    <td class="text-center"> {{$t->created_at}} </td>
                                                    <td class="text-center">
                                                        @switch($t->status)
                                                        @case('success')
                                                        <div class="badge badge-success">{{$t->status}}</div>
                                                        @break
                                                        @case("failed")
                                                        <div class="badge badge-danger">{{$t->status}}</div>
                                                        @break
                                                        @case('declined')
                                                        <div class="badge badge-warning">{{$t->status}}</div>
                                                        @break
                                                        @case('waiting')
                                                        <div class="badge badge-info">{{$t->status}}</div>
                                                        @break
                                                        @default
                                                        <div class="badge badge-success">{{$t->status}}</div>

                                                        @endswitch
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                {{--Naira Wallet Transactions --}}
                                <div class="tab-pane" id="tab-eg11-4" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="mb-2 transactions-table table " id="nt-table">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Reference id</th>
                                                    <th class="text-center">Cr. Account</th>
                                                    <th class="text-center">Dr. Account</th>
                                                    <th class="text-center">Amount</th>
                                                    <th class="text-center">Narration</th>
                                                    <th class="text-center">Type</th>
                                                    <th class="text-center">Trans. Type</th>
                                                    <th class="text-center">Status</th>
                                                    <th class="text-center">Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($wallet_txns as $t)
                                                <tr>
                                                    <td class="text-center">{{$t->reference}}</td>
                                                    <td class="text-center">{{$t->cr_acct_name}}</td>
                                                    <td class="text-center">{{$t->dr_acct_name}}</td>
                                                    <td class="text-center">₦{{number_format($t->amount)}}</td>
                                                    <td class="text-center">{{$t->narration}}</td>
                                                    <td class="text-center">{{$t->transactionType->name}}</td>
                                                    <td class="text-center">{{ucwords($t->trans_type)}}</td>
                                                    <td class="text-center">{{ucwords($t->status)}} </td>
                                                    <td class="text-center">{{$t->created_at->format('d M y h:i a ')}}</td>
                                                </tr>
                                                @endforeach
                                                <tr>
                                                    <td class="text-center"><strong>Debit Total</strong></td>
                                                    <td class="text-center"><strong>₦{{number_format($dr_total)}} </strong></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center"><strong>Credit Total</strong></td>
                                                    <td class="text-center"><strong>₦{{number_format($cr_total)}} </strong></td>
                                                </tr>
                                            </tbody>
                                            {{$wallet_txns->links()}}
                                        </table>
                                    </div>
                                </div>

                                {{-- BTC Tranactions --}}
                                <div class="tab-pane" id="btc-txns" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="align-middle mb-4 table table-bordered table-striped transactions-table ">
                                            <thead>
                                                <tr>
                                                    {{-- <th>ID</th> --}}
                                                    <th>Trans. Type</th>
                                                    <th>Amount</th>
                                                    <th>USD</th>
                                                    <th>Date</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($btc_transactions as  $t)
                                                <tr>
                                                    {{-- <td>{{$key += 1}} </td> --}}
                                                    <td>{{ $t->transactionType }}</td>
                                                    <td>{{ number_format((float) $t->amount, 8) }}</td>
                                                    <td>{{ number_format($t->marketValue->amount, 2) }}</td>
                                                    <td>{{ $t->created->format('d M Y h:ia') }}</td>
                                                    <td>Completed</td>
                                                    <td class="transaction_content">
                                                        @if (isset($t->txId))
                                                            <a target="_blank" href="https://blockexplorer.one/btc/mainnet/tx/{{ $t->txId }}" class="">Explorer</a>

                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>

                                        </table>
                                    </div>
                                </div>

                                {{-- Verification --}}
                                <div class="tab-pane" id="tab-eg11-3" role="tabpanel">
                                    <div class="row ">
                                        <div class="col-md-8">
                                            <img src=" {{asset('storage/idcards/'.$user->id_card)}} " alt=""
                                                class="img-fluid">
                                        </div>

                                        <div class="colm4">
                                            <a href="#" 
                                            @if (!auth()->user()->role == 555)
                                                data-toggle="modal" data-target="#update" 
                                            @endif
                                            ><span
                                                 class="btn  btn-primary">Change Status</span></a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{--Update Status --}}
<div class="modal fade  item-badge-rightm" id="update" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action=" {{-- {{route('admin.verify_user')}} --}}" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="modal-header">
                    <div class="media">
                        <div class="media-body">
                            <h4 class="media-heading">{{$user->first_name}}</h4>
                        </div>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label for="lead_message">Status</label>
                        <select name="status" id="" class="form-control">
                            <option value=" {{$user->status}} ">{{$user->status}} </option>
                            <option value="verified">Verified</option>
                            <option value="not verified">Not verified</option>
                            <option value="waiting">Waiting</option>
                            <option value="declined">Declined</option>
                        </select>
                        <input type="hidden" name="id" value=" {{$user->id}}">
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
@endsection
