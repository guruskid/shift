@extends('layouts.admin')
@section('content')
<div id="content" class="main-content">
    <div class="layout-px-spacing" style="min-height: unset !important">
        <div class="row layout-top-spacing">

        </div>
        @foreach ($errors->all() as $err)
        <p class="text-danger">{{ $err }}</p>
        @endforeach
        <div class="row layout-top-spacing">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                <div class="widget widget-chart-one">
                    <div class="widget-heading">
                        <div>
                            <h3 class="">Profit Manager</h3>
                            <p>Total</p>
                        </div>

                        <div class="widget-n" style="justify-content: center; text-align: center;">
                            <h5>₦{{ number_format($charges) }}</h5>
                            <button class="btn" data-toggle="modal" data-target="#send-modal"
                                style="border-radius: 20px; background-color: #000070; color:white; border: 0px;">
                                Send All to Wallet
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="widget widget-chart-one">
                    <div class="widget-heading">
                        <div>

                            <h5 class="">Transfer Charges</h5>
                            <p>View All</p>
                        </div>

                        <div class="widget-n" style="justify-content: center; text-align: center;">
                            <h6>₦{{ number_format($transfer_charge) }}</h6>
                            <button class="btn " data-toggle="modal" data-target="#send-modal"
                                style="border-radius: 20px; background-color: #000070; color:white; border: 0px; ">
                                Send All to Wallet
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="widget widget-chart-one">
                    <div class="widget-heading">
                        <div>

                            <h5 class="">SMS Charges</h5>
                            <p>View All</p>
                        </div>

                        <div class="widget-n" style="justify-content: center; text-align: center;">
                            <h6>₦{{ number_format($sms_charge) }}</h6>
                            <button class="btn " data-toggle="modal" data-target="#send-modal"
                                style="border-radius: 20px; background-color: #000070; color:white; border: 0px; ">
                                Send All to Wallet
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!--  END CONTENT AREA  -->
        <!-- NEW SECTION HERE-->
       {{--  <div>
            <h4 style="margin-top: 20px; margin-bottom: -20px;">
                PayBill services
            </h4>

        </div> --}}
        <hr style="width:50%;text-align:left;margin-left:0">
    </div>
    {{-- <div class="row" style="margin: 0px; padding: 0px;">
        <div class="col-md-6">
            <div class="widget widget-chart-one">
                <div class="widget-heading">
                    <div>

                        <h5 class="">Airtime Sales</h5>
                        <p>View All</p>
                    </div>

                    <div class="widget-n" style="justify-content: center; text-align: center;">
                        <h6>₦5,758</h6>
                        <button class="btn "
                            style="border-radius: 20px; background-color: #000070; color:white; border: 0px; ">
                            Send All to Wallet
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="widget widget-chart-one">
                <div class="widget-heading">
                    <div>

                        <h5 class="">Data Sales</h5>
                        <p>View All</p>
                    </div>

                    <div class="widget-n" style="justify-content: center; text-align: center;">
                        <h6>₦6,758</h6>
                        <button class="btn "
                            style="border-radius: 20px; background-color: #000070; color:white; border: 0px; ">
                            Send All to Wallet
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="row col-md-12" style="margin: 0px; padding: 0px;">
            <div class="col-md-6">
                <div class="widget widget-chart-one">
                    <div class="widget-heading">
                        <div>

                            <h5 class="">TV subscription Sales</h5>
                            <p>View All</p>
                        </div>

                        <div class="widget-n" style="justify-content: center; text-align: center;">
                            <h6>₦5,758</h6>
                            <button class="btn "
                                style="border-radius: 20px; background-color: #000070; color:white; border: 0px; ">
                                Send All to Wallet
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="widget widget-chart-one">
                    <div class="widget-heading">
                        <div>

                            <h5 class="">Electricity Bills Sales</h5>
                            <p>View All</p>
                        </div>

                        <div class="widget-n" style="justify-content: center; text-align: center;">
                            <h6>₦6,758</h6>
                            <button class="btn "
                                style="border-radius: 20px; background-color: #000070; color:white; border: 0px; ">
                                Send All to Wallet
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
</div>

{{-- Send charges modal --}}
<form action="{{route('admin.send-charges')}}" method="POST" id="transfer-form">
    @csrf
    {{-- Send Modal --}}
    <div class="modal fade " id="send-modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content  c-rounded">
                <!-- Modal Header -->
                <div class="modal-header bg-custom-gradient c-rounded-top p-4 ">
                    <h4 class="modal-title tns-title ">Send NGN<i class="fa fa-paper-plane"></i></h4>
                    <button type="button" class="close bg-light rounded-circle " data-dismiss="modal">&times;</button>
                </div>
                <!-- Modal body -->

                <div class="modal-body p-4">
                    <input type="hidden" name="trans_type" id="trns-type">
                    <div id="add-acct-details">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="">Bank name </label>
                                    <select name="bank_code" id="bank-name" class="form-control">
                                        <option id="dantown-bank" value="">Select bank name</option>
                                        @foreach ($banks as $b)
                                        <option class="other-banks" value="{{$b->code}}">{{$b->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="">Account number </label>
                                    <input type="number" name="acct_num" id="account-number" class="form-control">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="">Account name </label>
                                    <input type="text" required name="acct_name" id="" readonly class="form-control acct-name">
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Account</label>
                               <select name="admin_account" id="" class="form-control">
                                   <option >Select account</option>
                                   @foreach ($admin_accounts as $account)
                                   <option value="{{ $account->id }}">{{ $account->account_name . ' - ₦'.$account->amount }}</option>
                                   @endforeach
                               </select>
                            </div>
                        </div>
                    </div>
                    <button type="button" id="sign-up-btn"  data-toggle="modal" data-target="#confirm-modal"
                        class="btn btn-block c-rounded bg-primary">
                        Confirm
                    </button>
                    <p class="text-custom t-info "></p>
                </div>
            </div>
        </div>
    </div>

    {{-- Confirm transfer modal --}}
    <div class="modal fade " id="confirm-modal">
        <div class="modal-dialog ">
            <div class="modal-content  c-rounded">
                <!-- Modal Header -->
                <div class="modal-header bg-custom-gradient c-rounded-top p-4 ">
                    <h4 class="modal-title">Confirm transfer <i class="fa fa-paper-plane"></i></h4>
                    <button type="button" class="close bg-light rounded-circle " data-dismiss="modal">&times;</button>
                </div>
                <!-- Modal body -->

                <div class="modal-body p-4">
                    <p class="text-success">Enter your pin to confirm the transfer</p>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Wallet pin </label>
                                <input type="password" name="pin" minlength="4" maxlength="4" required class="form-control wallet-pin"
                                    placeholder="- - - -">
                            </div>
                        </div>
                    </div>
                    <button id="transfer-btn" class="btn btn-block c-rounded bg-primary">
                        Send
                    </button>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="ref" value="{{$ref}}">
</form>
@endsection
