@extends('layouts.user')
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
        @include('layouts.partials.user')
    </div>

    {{-- Content Starts here --}}
    <div class="app-main__outer">
        <div class="app-main__inner">

            <div id="content" class="main-content">
                <div class="layout-px-spacing">
                    <div class="row layout-top-spacing"></div>
                    <div class="row layout-top-spacing">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                            <div class="widget widget-chart-one">
                                <div class="widget-heading">
                                    <div>
                                        <span class="h3 giftcard-text" style="color: #000070;">Naira Wallet (₦) </span>
                                    </div>
                                    <div class="widget-n" style="justify-content: center; text-align: center;">
                                        <span class="d-block" style="h6 walletbalance-text">Wallet Balance</span>
                                        <span class="d-block price">₦{{ number_format($n->amount) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div id="content_bg" class="card card-body mb-4" style="height:550px;">
                                <div class="container px-4 d-flex justify-content-between align-items-center">
                                    <a href="{{ route('user.portfolio') }}">
                                        <div class="d-flex align-items-center">
                                            <div
                                                style="background: rgba(0, 0, 112, 0.25);width:24px;height:24px;border-radius:12px;">
                                                <span style="position: relative;left:33%;top:0;">
                                                    <svg width="8" height="12" viewBox="0 0 8 12" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M7.41 1.41L6 0L0 6L6 12L7.41 10.59L2.83 6L7.41 1.41Z"
                                                            fill="#000070" />
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="ml-2" style="color: #000070;font-size: 20px;">Back</div>
                                        </div>
                                    </a>
                                </div>
                                {{-- border line --}}
                                <div class="mt-4" style="width: 100%;border: 1px solid #C9CED6;"></div>
                                @foreach ($errors->all() as $err)
                                <p class="text-danger">{{ $err }}</p>
                                @endforeach

                                {{-- Wallet  menu  --}}
                                <div class="row">
                                    <div class="col-12 col-lg-6">
                                        <div class="mx-auto mt-4">
                                            <div class="walletpage_menu d-flex flex-column flex-lg-row justify-content-center justify-content-lg-between align-items-center flex-wrap flex-lg-nowrap">
                                                <div>
                                                    <span class="d-block" style="color: #565656;font-size: 16px;">Naira wallet
                                                        Balance</span>
                                                    <span class="d-block">
                                                        <span style="color: #000070;font-size: 30px;">₦{{ number_format($n->amount) }}</span>
                                                    </span>
                                                    {{-- <span class="d-block"
                                                        style="color: #565656;font-size: 16px;opacity: 0.5;">₦20,000</span> --}}
                                                </div>
                                                <div class="d-flex mt-3 mt-md-0">
                                                    <a id="naira_transfer" class="btn walletpage_menu-active naira_menu">
                                                        <span class="d-block">
                                                        <img src="{{asset('svg/bitcoin-send-icon.svg')}}" alt="">
                                                        </span>
                                                        <span class="d-block"
                                                            style="color: #000000;font-size: 14px;">Transfer</span>
                                                    </a>
                                                    <a id="naira_withdraw" class="btn naira_menu">
                                                        <span class="d-block">
                                                            <img src="{{asset('svg/naira-withdraw-icon.svg')}}" alt="">
                                                        </span>
                                                        <span class="d-block"
                                                            style="color: #000000;font-size: 14px;">Withdraw</span>
                                                    </a>
                                                    <a id="naira_deposit" class="btn naira_menu">
                                                        <span class="d-block">
                                                            <img src="{{asset('svg/naira-deposit-icon.svg')}}" alt="">
                                                        </span>
                                                        <span class="d-block" style="color: #000000;font-size: 14px;">Deposit</span>
                                                    </a>
                                                </div>
                                            </div>

                                                @include('newpages.tabs.naira-transfer-tab')
                                                @include('newpages.tabs.naira-withdraw-tab')
                                                @include('newpages.tabs.naira-deposit-tab')


                                                {{-- Naira transfer modal --}}
                                                @include('newpages.modals.dantownTodantown-modal')
                                                @include('newpages.modals.dantownToOther-modal')

                                        </div>
                                    </div>
                                    <div class="col-12 col-lg-6 pt-2 nairawalletmain__transactionhistory-container">
                                        <span class="d-block txn_history_title mt-3">Transaction history</span>
                                        <div class="mt-3" style="width: 100%;border: 1px solid #EFEFF8;"></div>
                                        <div class="table-responsive">
                                            <table class="table table-borderless">
                                                <tbody class="text-center wallet_trxs">
                                                  @foreach ($nts as $t)
                                                  <tr class="my-2">
                                                    <th scope="row">
                                                        <span class="d-block" style="font-size: 16px;">{{ $t->created_at->format('d M Y') }}</span>
                                                        <span class="d-block" style="font-size: 14px;font-weight: normal;">{{ $t->created_at->format('h:i a') }}</span>
                                                    </th>
                                                    <td>₦{{number_format($t->amount)}}</td>
                                                    <td>
                                                        <span style="color: #87676F;font-size: 16px;">{{ $t->transactionType->name }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="px-3 py-2" style="font-size: 12px;color: #219653;background: rgba(115, 219, 158, 0.3);border-radius: 15px;">{{ $t->status }}</span>
                                                    </td>
                                                  </tr>
                                                  @endforeach
                                                  {{ $nts->links() }}
                                                </tbody>
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
</div>

@endsection
