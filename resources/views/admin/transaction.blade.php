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
                        <div class="page-title-icon">
                            <i class="pe-7s-timer icon-gradient bg-warm-flame">
                            </i>
                        </div>
                        <div> Transaction (Status:: {{$transaction->status}})
                            <div class="page-title-subheading">
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-header">Transaction Images </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach ($transaction->batchPops as $pop)
                                    <div class="col-md-3">
                                        <img src="{{asset('storage/pop/'.$pop->path)}}" class="img-fluid" alt="image">
                                        <div class="d-flex justify-content-between">
                                            <a href="{{asset('storage/pop/'.$pop->path)}}">View</a>
                                            <i>By {{$pop->user->first_name}}
                                                @if(!Auth::user()->role == 444 OR !Auth::user()->role == 449)
                                                    ({{$pop->user->phone}})
                                                @endif
                                            </i>
                                        </div>
                                    </div>
                                @endforeach
                                @foreach ($transaction->pops as $pop)
                                    <div class="col-md-3">
                                        <img src="{{asset('storage/pop/'.$pop->path)}}" class="img-fluid" alt="image">
                                        <div class="d-flex justify-content-between">
                                            <a href="{{asset('storage/pop/'.$pop->path)}}">View</a>

                                                <i>By {{$pop->user->first_name}}
                                                    @if(!Auth::user()->role == 444 OR !Auth::user()->role == 449)
                                                        ({{$pop->user->phone}})
                                                    @endif
                                                </i>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @if(Auth::user()->role != 444)
                            <div class="card-footer">
                                <form action="{{route('transaction.add-image')}} " method="post" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <label for="">Upload Image(s)</label>
                                        <input type="hidden" name="transaction_id" value="{{$transaction->id}}" >
                                        <input type="file" name="pops[]" accept="image/*" required multiple >
                                    </div>
                                    <button type="submit" class="btn btn-success">Upload</button>
                                </form>
                            </div>
                        @endif
                        <div class="card-footer">
                            <form action="{{route('admin.edit_transaction')}} " method="POST" class="mb-3">
                                {{ csrf_field() }}

                                <div class="modal-body">

                                    <div class="form-group">
                                        <input type="hidden" readonly name="id" value="{{ $transaction->id }}">
                                    </div>

                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="">Card</label>
                                                <select name="card_id" class="form-control">
                                                    <option value="{{ $transaction->card_id }}">{{ $transaction->card }}</option>
                                                    @foreach ($cards as $card)
                                                    <option value="{{$card->id}}"> {{ ucfirst($card->name) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="">Country</label>
                                                <select name="country" class="form-control">
                                                    <option value="{{ $transaction->country }}" >{{ $transaction->country }}</option>
                                                    <option value="USD">USD</option>
                                                    <option value="EUR">EUR</option>
                                                    <option value="GBP">GBP</option>
                                                    <option value="AUD">AUD</option>
                                                    <option value="CAD">CAD</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="">Unit</label>
                                                <input type="text" placeholder="Value" class="form-control" value="{{ $transaction->amount }}" name="amount">
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="">Cash Value</label>
                                                <input type="text" placeholder="Amount paid" value="{{ $transaction->amount_paid + $transaction->commission }}" class="form-control" name="amount_paid">

                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        @if (Auth::user()->role != 888)

                                        <div class="col">
                                            <!-- ///////////// WORK IN PROGRESS ////////////// -->
                                            <div class="form-group">
                                                <label for="">Status</label>
                                                <select onchange="feedback_status()" id="f_status" name="status" class="form-control"
                                                @if (Auth::user()->role == 888)
                                                    {{ "disabled" }}
                                                @endif>
                                                    <option value="{{ $transaction->status }}">{{ $transaction->status }}</option>
                                                    @if (in_array(Auth::user()->role, [889, 777, 999, 444, 449]))
                                                    <option value="success">Success</option>
                                                    @endif
                                                    <option value="waiting">Waiting</option>
                                                    <option value="in progress">In progress</option>
                                                    <option value="failed">Failed</option>
                                                    <option value="declined">Declined</option>
                                                </select>
                                            </div>
                                        </div>
                                        @endif
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="">Transac Type</label>
                                                <select name="trade_type" class="form-control">
                                                    {{-- <option value="{{ $transaction->type }}">{{ $transaction->type }}</option> --}}
                                                    {{-- <option value="buy">Buy</option> --}}
                                                    <option value="sell">Sell</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="">Quantity</label>
                                                <input type="text" placeholder="Amount paid" value="{{ $transaction->quantity }}" class="form-control"
                                                    name="quantity">
                                            </div>
                                        </div>
                                        <!-- //////////////////////////////////// -->
                                        <div class="d-none col-12" id="yfailed">
                                            <div class="form-group">
                                            <label for="feedback">Feedback</label>
                                                <select name="failfeedbackstatus" class="form-control">
                                                    <option value="Your card was used">Your card was used</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="d-none col-12" id="ydeclined">
                                            <div class="form-group">
                                            <label for="feedback">Feedback</label>
                                                <select name="declinefeedbackstatus" class="form-control">
                                                    <option value="Your card/code was invalid">Your card/code was invalid</option>
                                                    <option value="The card/code was not clear"> The card/code was not clear  </option>
                                                    <option value="Your card/code needed more info"> Your card/code needed more info </option>
                                                    <option value="Multiple transaction was opened"> Multiple transaction was opened </option>
                                                    <option value="No image was uploaded">No image was uploaded</option>
                                                </select>
                                            </div>
                                        </div>
                                        <!-- /////////////////////////////////////// -->
                                    </div>
                                </div>
                                @if (!in_array($transaction->status,['success','declined','failed']))
                                    <button type="submit" class="btn btn-primary">Update</button>
                                @endif
                            </form>
                            {{-- <a href="#" class="my-2" data-toggle="modal" data-target="#edit-transac" onclick="editTransac({{$transaction}})">
                                <span class="btn btn btn-info">Edit</span> --}}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Edit transactions Modal --}}


@endsection
