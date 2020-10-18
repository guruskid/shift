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
                            <i class="pe-7s-graph1 icon-gradient bg-warm-flame">
                            </i>
                        </div>
                        <div>Charges
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3 widget-content bg-amy-crisp">
                        <div class="widget-content-wrapper text-white">
                            <div class="widget-content-left">
                                <div class="widget-heading">
                                    <h5>Transfer Charges</h5>
                                    <button class="btn btn-danger" data-toggle="modal"
                                        data-target="#confirm-transfer-modal">Clear Balance</button>

                                </div>
                            </div>
                            <div class="widget-content-right">
                                <div class="widget-numbers text-white">
                                    <span>₦{{$transfer_charge->amount }}</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-3 widget-content bg-custom-accent">
                        <div class="widget-content-wrapper text-white">
                            <div class="widget-content-left">
                                <div class="widget-heading">
                                    <h5>SMS Charges</h5>
                                    <button class="btn btn-danger" data-toggle="modal"
                                    data-target="#confirm-sms-modal">Clear Balance</button>
                                </div>
                            </div>
                            <div class="widget-content-right">
                                <div class="widget-numbers text-white">
                                    <span>₦{{$sms_charge->amount}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                            @foreach ($errors->all() as $err)
                            <p class="text-danger">{{$err}} </p>
                            @endforeach
                            <ul class="nav nav-tabs nav-justified">
                                <li class="nav-item"><a data-toggle="tab" href="#tab-eg11-0"
                                        class="active nav-link">Transfer Charges</a></li>
                                <li class="nav-item"><a data-toggle="tab" href="#tab-eg11-1" class="nav-link">SMS
                                        Charges</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                {{-- Transfer Charges --}}
                                <div class="tab-pane active" id="tab-eg11-0" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="mb-2 transactions-table table">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Id</th>
                                                    <th class="text-center">Reference id</th>
                                                    <th class="text-center">Cr. Account</th>
                                                    <th class="text-center">Dr. Account</th>
                                                    <th class="text-center">Charge</th>
                                                    <th class="text-center">Narration</th>
                                                    <th class="text-center">Trans. Type</th>
                                                    <th class="text-center">Status</th>
                                                    <th class="text-center">Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($transfer_charges_txns as $t)
                                                <tr>
                                                    <td class="text-center">{{$t->id}}</td>
                                                    <td class="text-center">{{$t->reference}}</td>
                                                    <td class="text-center">{{$t->cr_acct_name}}</td>
                                                    <td class="text-center">{{$t->dr_acct_name}}</td>
                                                    <td class="text-center">₦{{$t->transfer_charge}}</td>
                                                    <td class="text-center">{{$t->narration}}</td>
                                                    <td class="text-center">{{ucwords($t->transactionType->name)}}</td>
                                                    <td class="text-center">{{ucwords($t->status)}} </td>
                                                    <td class="text-center">{{$t->created_at->format('d M y ')}}</td>
                                                </tr>
                                                @endforeach

                                            </tbody>
                                            {{$transfer_charges_txns->links()}}
                                        </table>
                                    </div>
                                </div>
                                {{-- Debit --}}
                                <div class="tab-pane" id="tab-eg11-1" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="mb-2 transactions-table table">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Id</th>
                                                    <th class="text-center">Reference id</th>
                                                    <th class="text-center">Cr. Account</th>
                                                    <th class="text-center">Dr. Account</th>
                                                    <th class="text-center">Charge</th>
                                                    <th class="text-center">Narration</th>
                                                    <th class="text-center">Trans. Type</th>
                                                    <th class="text-center">Status</th>
                                                    <th class="text-center">Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($sms_charges_txns as $t)
                                                <tr>
                                                    <td class="text-center">{{$t->id}}</td>
                                                    <td class="text-center">{{$t->reference}}</td>
                                                    <td class="text-center">{{$t->cr_acct_name}}</td>
                                                    <td class="text-center">{{$t->dr_acct_name}}</td>
                                                    <td class="text-center">₦{{$t->sms_charge}}</td>
                                                    <td class="text-center">{{$t->narration}}</td>
                                                    <td class="text-center">{{ucwords($t->transactionType->name)}}</td>
                                                    <td class="text-center">{{ucwords($t->status)}} </td>
                                                    <td class="text-center">{{$t->created_at->format('d M y ')}}</td>
                                                </tr>
                                                @endforeach

                                            </tbody>
                                            {{$sms_charges_txns->links()}}
                                        </table>
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

{{-- Transfer charges modal --}}
<div class="modal fade " id="confirm-transfer-modal">
    <div class="modal-dialog ">
        <div class="modal-content  c-rounded">
            <!-- Modal Header -->
            <div class="modal-header bg-custom-gradient c-rounded-top p-4 ">
                <h4 class="modal-title">Confirm Clear of Transfer Charges </h4>
                <button type="button" class="close bg-light rounded-circle " data-dismiss="modal">&times;</button>
            </div>
            <!-- Modal body -->

            <div class="modal-body p-4">
                <p class="text-success">Enter your account password to confirm action</p>
                <form action="{{route('admin.clear-transfer-charges')}}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Account Password </label>
                                <input type="password" name="password" required class="form-control wallet-pin"
                                    placeholder="- - - - - - - - - -">
                            </div>
                        </div>
                    </div>
                    <button  class="btn btn-block c-rounded bg-custom-gradient">
                        Clear
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Clear SMS Charge  --}}<div class="modal fade " id="confirm-sms-modal">
    <div class="modal-dialog ">
        <div class="modal-content  c-rounded">
            <!-- Modal Header -->
            <div class="modal-header bg-custom-gradient c-rounded-top p-4 ">
                <h4 class="modal-title">Confirm Clear of SMS Charges </h4>
                <button type="button" class="close bg-light rounded-circle " data-dismiss="modal">&times;</button>
            </div>
            <!-- Modal body -->

            <div class="modal-body p-4">
                <p class="text-success">Enter your account password to confirm action</p>
                <form action="{{route('admin.clear-sms-charges')}}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Account Password </label>
                                <input type="password" name="password" required class="form-control wallet-pin"
                                    placeholder="- - - - - - - - - -">
                            </div>
                        </div>
                    </div>
                    <button  class="btn btn-block c-rounded bg-custom-gradient">
                        Clear
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
