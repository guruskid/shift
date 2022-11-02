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
                            <i class="pe-7s-users icon-gradient bg-sunny-morning">
                            </i>
                        </div>
                        <div>View Trade</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 pb-3 card">
                        <div class="table-responsive">
                            <div>
                                <div class="modal-dialog">
                                        <form action="{{route('admin.naira-p2p.update', $t)}}" id="freeze-form" method="post"> @method('put')
                                        @csrf
                                        <div class="modal-content c-rounded">
                                            <div class="modal-header bg-light c-rounded-top p-4 ">
                                                
                                            </div>
                                            <!-- Modal Header -->
                                            <div class="modal-header bg-light c-rounded-top p-4 ">
                                                <h4 class="modal-title">{{ ucwords($status)." ".ucwords($type) }}
                                                    @if($status == 'unresolved')
                                                    <p class="text-warning">{{ now()->diffForHumans($t->updated_at) }}</p>
                                                    @endif
                                                </h4>
                                                <button class="btn btn-primary"><a class="text-white" href="{{ route('admin.naira-p2p.type',['type'=>'withdrawal', 'status'=>'waiting']) }}">Back</a></button>
                                                
                                            </div>
                                            
                                            <!-- Modal body -->
                        
                                            <div class="modal-body p-4">
                                                <div class="row">
                                                    @if($t->is_flagged == 1)
                                                    <div class="col-md-12 mb-2">
                                                        <div class="bg-danger text-white text-center">This Transaction is flagged for Bulk Withdrawal<br>Contact the Junior Accountant to Confirm Action</div>
                                                    <div>
                                                    @endif
                                                    <div class="col-md-12 mt-2">
                                                        <label for="reason" >Name</label>
                                                        <input type="text" class="form-control mb-2" type="text" value="{{$t->user->first_name." ".$t->user->last_name}}" disabled>
                        
                                                        @php
                                                            $name = $t->user->first_name." ".$t->user->last_name;
                                                        @endphp
                        
                                                        @if($t->type == 'withdrawal')
                                                        <label class="mt-2" for="reason" >Account Number</label>
                                                        <div class="input-group mb-2">
                                                            <input type="text" class="form-control" type="text" id="accountNumbercopy" value="{{$t->account->account_number}}" readonly>
                                                            @if($t->status != 'success')
                                                            <div class="input-group-append">
                                                                <button data-clipboard-target="#accountNumbercopy" class="input-group-text" id="accNoCopy" onclick="copyData('accountNumbercopy', '{{ $name }} Account Number is')">
                                                                <img src="{{asset('svg/copy_btn.svg')}}" />
                                                                </button>
                                                            </div>
                                                            @endif
                                                        </div>
                        
                                                        <label class="mt-2" for="reason">Bank Name</label>
                                                        <input type="text" input class="form-control mb-2" type="text" id="bankName" value="{{$t->account->bank_name}}" disabled>
                                                        @endif
                        
                                                        <label class="mt-2" for="reason">Amount</label>
                                                        <div class="input-group mb-2">
                                                            <input type="text" input class="form-control" type="text" id="amountcopy" value="{{$t->amount}}" readonly>
                                                            @if($t->status != 'success')
                                                            <div class="input-group-append">
                                                                <button data-clipboard-target="#amountcopy" class="input-group-text" id="amountNoCopy" onclick="copyData('amountcopy','{{ $name }} Amount is')">
                                                                <img src="{{asset('svg/copy_btn.svg')}}" />
                                                                </button>
                                                            </div>
                                                            @endif
                                                        </div>
                        
                                                        @if($t->status == 'success' OR $t->status == 'cancelled')
                                                        <label for="reason" >Ref Num</label>
                                                        <input type="text" input class="form-control mb-2" type="text" value="{{$t->reference}}" disabled>
                        
                                                        <label for="reason" >Status</label>
                                                        <input type="text" input class="form-control mb-2" type="text" value="{{$t->status}}" disabled>
                                                        @endif
                                                        @if($t->status != 'success' AND $t->status != 'cancelled')
                                                        <div class="btn-toolbar float-right" role="toolbar" id="p2p_buttongroup-{{ $t->id }}">
                                                            <div class="btn-group mr-2 float-right" role="group" aria-label="First group">
                                                                <button type="button" onclick="buttonSelect('statusInput-{{ $t->id }}','approve',{{ $t->id }})" class="mt-2 btn btn-outline-primary c-rounded txn-btn">Approve</button>
                                                            </div>
                                                            @if($t->status != 'unresolved')
                                                            <div class="btn-group mr-2 float-right" role="group">
                                                                <button type="button" onclick="buttonSelect('statusInput-{{ $t->id }}','decline',{{ $t->id }})" class="mt-2 btn btn-outline-primary c-rounded txn-btn">Decline</button>
                                                            </div>
                                                            @endif
                        
                                                            @if($t->type == 'withdrawal' AND $t->status != 'unresolved')
                                                                <div class="btn-group mr-2 float-right" role="group">
                                                                <button type="button"onclick="buttonSelect('statusInput-{{ $t->id }}','unresolved',{{ $t->id }})" class="mt-2 btn btn-outline-primary c-rounded txn-btn">unresolved</button>
                                                                </div>
                                                            @endif
                                                            
                                                            <div class="btn-group mr-2 float-right" role="group">
                                                                <button type="button" data-dismiss="modal" class="mt-2 btn btn-outline-primary c-rounded txn-btn">Go Back</button>
                                                            </div>
                                                            </div>
                                                        </div>
                                                        @endif
                        
                                                    <input type="hidden" name="status" id="statusInput-{{ $t->id }}">
                                                    <input type="hidden" name="id" value="{{ $t->id }}">
                                                    <div class="col-md-12 d-none" id="p2p_dropdown-{{ $t->id }}">
                                                        <div class="form-group mt-1">
                                                            <label for="reason" class="text-danger">Reason for Declining </label>
                                                            <select name="reason" id="" class="form-control">
                                                                @if($t->type == 'withdrawal')
                                                                <option value="Bank network issues">Bank network issues</option>
                                                                <option value="Exceeded bank limit">Exceeded bank limit</option>
                                                                <option value="Incorrect bank details">Incorrect bank details</option>
                                                                <option value="A mismatch in name">A mismatch in name</option>
                                                                @else
                                                                <option value="payment not received">Payment Not Recieved</option>
                                                                @endif;
                                                            </select>
                                                        </div>
                                                    </div>
                        
                                                    <div class="col-md-12 d-none" id="p2p_pin-{{ $t->id }}">
                                                        <div class="form-group">
                                                            <label for="">Wallet pin </label>
                                                            <input type="password" name="pin" required class="form-control">
                                                        </div>
                                                        <button class="btn btn-block c-rounded bg-custom-gradient txn-btn">
                                                            Confirm
                                                        </button>
                                                    </div>
                        
                        
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{asset('assets/scripts/sweetalert.min.js')}} "></script>
<script>


    const __st_id = (activity) => document.getElementById(activity)

    const hideit = (ide) => {
        __st_id(ide).classList.remove("d-block")
        __st_id(ide).classList.add("d-none")
    }

    const showit = (ide) => {
        __st_id(ide).classList.remove("d-none")
        __st_id(ide).classList.add("d-block")
    }
    
    const buttonSelect = (id, status, uid) => {
        var approve = document.getElementById(id);
        approve.value = status;

        if(status == 'approve' || status == 'unresolved'){
            showit('p2p_pin-'+uid);
            hideit('p2p_buttongroup-'+uid);
        }

        if(status == 'decline') {
            showit('p2p_pin-'+uid);
            showit('p2p_dropdown-'+uid);
            hideit('p2p_buttongroup-'+uid);
        }
    }

    const copyData = (id, type) => {
        var copyText = document.getElementById(id);
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard
        .writeText(copyText.value)
        .then(() => {
            swal(type+" copied: " + copyText.value);
        })
        .catch(() => {
            swal("something went wrong");
        });
    }
@endsection
