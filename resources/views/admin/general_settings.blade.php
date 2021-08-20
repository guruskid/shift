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
                            <i class="pe-7s-settings icon-gradient bg-sunny-morning">
                            </i>
                        </div>
                        <div>General System Settings</div>
                    </div>
                </div>
            </div>

            <div class="row">
               <div class="col-lg-12 card pt-3">
                    <div class="panel">
                        <div class="panel-body container-fluid">
                            <form action="{{route('admin.general_settings')}}" method="post"autocomplete="off">
                                @csrf
                                <fieldset>
                                    <legend>Withdrawal</legend>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group form-material" data-plugin="formMaterial">
                                                <div class="d-flex flex-row my-3">
                                                    <div class="float-left mr-20 p-box">
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" name="NAIRA_WALLET_WITHDRAWAL" id="naira-w"  onclick="" class="custom-control-input toggle-settings" {{($settings['NAIRA_WALLET_WITHDRAWALS']['settings_value'] == 1) ? 'checked' : ''}} data-notice="{{$settings['NAIRA_WALLET_WITHDRAWALS']['notice']}}" data-name="NAIRA_WALLET_WITHDRAWALS"> 
                                                            <label for="naira-w" class="custom-control-label">Naira Wallet Withdrawal</label>
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                         <div class="col-md-4">
                                            <div class="form-group form-material">
                                                <div class="d-flex flex-row my-3">
                                                    <div class="float-left mr-20">
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" name="BTC_WITHDRAWALS" id="btc-w" onclick="" class="custom-control-input toggle-settings" {{($settings['BTC_WITHDRAWALS']['settings_value'] == 1) ? 'checked' : ''}} data-notice="{{$settings['BTC_WITHDRAWALS']['notice']}}" data-name="BTC_WITHDRAWALS"> 
                                                            <label for="btc-w" class="custom-control-label">BTC Withdrawal</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                <fieldset>
                                    <legend>Buy/Sell BTC</legend>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group form-material">
                                                <div class="d-flex flex-row my-3">
                                                    <div class="float-left mr-20">
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" name="SELL_BTC" id="sell-btc" onclick="" class="custom-control-input toggle-settings" {{($settings['SELL_BTC']['settings_value'] == 1) ? 'checked' : ''}} data-notice="{{$settings['SELL_BTC']['notice']}}" data-name="SELL_BTC"> 
                                                            <label for="sell-btc" class="custom-control-label">Sell BTC</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                         <div class="col-md-4">
                                            <div class="form-group form-material">
                                                <div class="d-flex flex-row my-3">
                                                    <div class="float-left mr-20">
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" name="BUY_BTC" id="buy-btc" onclick="" class="custom-control-input toggle-settings" {{($settings['BUY_BTC']['settings_value'] == 1) ? 'checked' : ''}} data-notice="{{$settings['BUY_BTC']['notice']}}" data-name="BUY_BTC"> 
                                                            <label for="buy-btc" class="custom-control-label">Buy BTC</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                <fieldset>
                                    <legend>Buy/Sell Giftcard</legend>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group form-material">
                                                <div class="d-flex flex-row my-3">
                                                    <div class="float-left mr-20">
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" name="GIFTCARD_BUY" id="sell-gc" onclick="" class="custom-control-input toggle-settings" {{($settings['GIFTCARD_BUY']['settings_value'] == 1) ? 'checked' : ''}} data-notice="{{$settings['GIFTCARD_BUY']['notice']}}" data-name="GIFTCARD_BUY"> 
                                                            <label for="sell-gc" class="custom-control-label">Sell Giftcard</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                         <div class="col-md-4">
                                            <div class="form-group form-material">
                                                <div class="d-flex flex-row my-3">
                                                    <div class="float-left mr-20">
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" name="GIFTCARD_SELL" id="buy-gc" onclick="" class="custom-control-input toggle-settings" {{($settings['GIFTCARD_SELL']['settings_value'] == 1) ? 'checked' : ''}}  data-notice="{{$settings['GIFTCARD_SELL']['notice']}}" data-name="GIFTCARD_SELL"> 
                                                            <label for="buy-gc" class="custom-control-label">Buy Giftcard</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                <fieldset>
                                    <legend>Buy/Sell Airtime</legend>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group form-material">
                                                <div class="d-flex flex-row my-3">
                                                    <div class="float-left mr-20">
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" name="AIRTIME_BUY" id="buy-airtime"  class="custom-control-input toggle-settings s-active" {{($settings['AIRTIME_BUY']['settings_value'] == 1) ? 'checked' : ''}} data-notice="{{$settings['AIRTIME_BUY']['notice']}}" data-name="AIRTIME_BUY"> 
                                                            <label for="buy-airtime" class="custom-control-label">Buy Airtime</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group form-material">
                                                <div class="d-flex flex-row my-3">
                                                    <div class="float-left mr-20">
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" name="AIRTIME_SELL" id="sell-airtime"  class="custom-control-input toggle-settings s-active" {{($settings['AIRTIME_SELL']['settings_value'] == 1) ? 'checked' : ''}} data-notice="{{$settings['AIRTIME_SELL']['notice']}}" data-name="AIRTIME_SELL"> 
                                                            <label for="sell-airtime" class="custom-control-label">Sell Airtime</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                </fieldset>
                               
                                {{-- <div class="form-group form-material">
                                    <button type="submit" class="btn btn-primary waves-effect waves-classic">Update</button>
                                </div> --}}
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

{{-- Add card Modal --}}
<div class="modal fade  item-badge-rightm" id="settings-modal" role="dialog">
    <div class="modal-dialog modal-md " role="document">
        <div class="modal-content">
            <div class="modal-body">
                <form method="POST" id="save-setting">
                    @csrf
                    <input type="hidden" id="name" />
                    <input type="hidden" id="status" />
                    <div class="form-group form-material" data-plugin="formMaterial">
                        <label>Message (<span id="setting-name"></span>)</label>
                        <textarea name="NAIRA_WALLET_WITHDRAWALS" id="notice" rows="2" class="form-control notice"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Confirm freezze account --}}
<div class="modal fade " id="freeze-modal">
    <div class="modal-dialog modal-dialog-centered ">
        <form action="{{route('admin.freeze-account')}}" id="freeze-form" method="post" >
            @csrf
            <div class="modal-content  c-rounded">
                <!-- Modal Header -->
                <div class="modal-header bg-custom-gradient c-rounded-top p-4 ">
                    <h4 class="modal-title">Confirm Refund <i class="fa fa-paper-plane"></i></h4>
                    <button type="button" class="close bg-light rounded-circle " data-dismiss="modal">&times;</button>
                </div>
                <!-- Modal body -->

                <div class="modal-body p-4">
                    <p class="text-success">Enter your pin to confirm action</p>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Wallet pin </label>
                                <input type="password" name="pin" required class="form-control">
                                <input type="hidden" name="user_id" id="user-id" required class="form-control">
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-block c-rounded bg-custom-gradient txn-btn">
                        Confirm
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
