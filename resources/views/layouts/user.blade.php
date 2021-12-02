@auth
@php
/* $nots = App\Notification::where('user_id', 0)->get(); */
$nots = Auth::user()->notifications;
foreach ($nots as $not ) {
$not->date = $not->created_at->diffForHumans();
}
$not = $nots->last();
$unread = Auth::user()->notifications()->where('is_seen', 0)->count();
$naira_balance = 0;
if (Auth::user()->nairaWallet) {
$naira_balance = Auth::user()->nairaWallet->amount;
}

$setting_withdrawal = \App\Http\Controllers\GeneralSettings::getSetting('NAIRA_WALLET_WITHDRAWALS');
$setting_airtime = \App\Http\Controllers\GeneralSettings::getSetting('AIRTIME_BUY');

@endphp
@endauth

<!doctype html>
<html lang="en">

<head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-158256173-2"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'UA-158256173-2');

    </script>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Language" content="en">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="{{asset('fav.png')}}">
    <link rel="shortcut icon" href="{{asset('fav.png')}}">
    <title> Dashboard - Dantown Multi Services</title>
    <meta name="viewport"
        content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" />
    <meta name="description" content="Dantown multi services">
    <meta name="msapplication-tap-highlight" content="no">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand&display=swap" rel="stylesheet">
    <link href="/css/app.css?v={{ env('APP_STATIC_FILES_VERSION') }}" rel="stylesheet">
    <link href=" {{asset('user_main.css')}} " rel="stylesheet">
    {{-- <link href=" {{asset('newpages/css/main.css')}} " rel="stylesheet"> --}}
    {{-- <link href=" {{asset('newpages/bootstrap/css/bootstrap.min.css')}} " rel="stylesheet"> --}}
    <link href=" {{asset('custom.css?v=3.0')}} " rel="stylesheet">
    <link href=" {{asset('user_assets/css/responsive-fixes.css')}} " rel="stylesheet">
    <link href=" {{asset('user_assets/css/main.css?v='.env('APP_STATIC_FILES_VERSION')  )}} " rel="stylesheet">
    {{-- <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css"> --}}
    <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>

    {{-- <link href=" {{asset('main.css')}} " rel="stylesheet"> --}}
    {{-- <link href=" {{asset('custom.css')}} " rel="stylesheet"> --}}

    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/v/bs4/dt-1.10.21/fh-3.1.7/r-2.2.5/datatables.min.css" />

    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.21/fh-3.1.7/r-2.2.5/datatables.min.js">
    </script>

    <script>
        window.Laravel = {!! json_encode([
            'csrfToken'=> csrf_token(),
            'user'=> [
                'authenticated' => auth()->check(),
                'id' => auth()->check() ? auth()->user()->id : null,
                'first_name' => auth()->check() ? auth()->user()->first_name : null,
                'last_name' => auth()->check() ? auth()->user()->last_name : null,
                'email' => auth()->check() ? auth()->user()->email : null,
                'naira_wallet_balance' => Auth::user()->nairaWallet->amount ?? 0,
                'bitcoin_wallet_balance' => Auth::user()->bitcoinWallet->balance ?? 0,
                ]
            ])
        !!};

    </script>

    <!-- Facebook Pixel Code -->
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '298624844814339');
    fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id=298624844814339&ev=PageView&noscript=1"
    /></noscript>
    <!-- End Facebook Pixel Code -->


    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-160759276-1"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-160759276-1');
    </script>

    <script src="//code.jivosite.com/widget/HKhMBc91QY" async></script>
    <style>
        #notifications {
            cursor: pointer;
            position: fixed;
            right: 0px;
            z-index: 9999;
            top: 0px;
            margin-bottom: 22px;
            margin-right: 15px;
            max-width: 300px;
        }

        .swal-text{
            color: black;
        }

        .swal-button {
            height: 40px;
            padding: 9px 20px;
            border: 0px solid #000070 ;
            background: #000070 ;
            color: #fff;
            border-radius: 5px;
            font-family: 'Roboto', sans-serif;
            font-weight: 500;
            display: inline-block;
        }

        .swal-button:not([disabled]):hover {
            background: #31318d;
            color: #fff !important;
        }
        .dantown-btn-init{
            background-color: #f2f2f2;
            /* border: 2px solid lightgray; */
            /* box-shadow: 3px 3px 3px #727272; */
            /* border: 1px solid gray */
        }
    </style>

</head>

<body >
    <div id="app">
        <div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header closed-sidebar">
            <div class="app-header header-shadow header-text-light">
                <div class="app-header__logo">
                    <a href="https://dantownms.com">
                        <div class="logo-src"></div>
                    </a>
                    <div class="header__pane ml-auto">
                        <div>
                            <button type="button" class="hamburger close-sidebar-btn hamburger--elastic"
                                data-class="closed-sidebar">
                                <span class="hamburger-box">
                                    <img src="{{asset('fav2.png')}}" class="img-fluid" alt="">
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="app-header__mobile-menu">
                    <div>
                        <button type="button" class="hamburger bg-custom p-2 hamburger--elastic mobile-toggle-nav">
                            <span class="hamburger-box">
                                <span class="hamburger-inner"></span>
                            </span>
                        </button>
                    </div>
                </div>
                <div class="app-header__content">

                    <div class="app-header-left">
                        <div class="col  ml-3 ">
                            <a href="{{route('user.portfolio')}}" title="View wallet">
                                <div class="widget-heading text-custom realtime-wallet-balance">
                                    @auth
                                    Loading . . .
                                    @endauth
                                </div>
                                <div class="widget-heading text-muted">
                                    Wallet Balance
                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="quick_menu_btns">
                        <ul class="nav d-flex">
                            <li data-toggle="modal" id="quickTopUpLink"
                                class="nav-item d-flex justify-content-center align-items-center mx-2"
                                style="font-size:14px;font-weight: 500;background: #000070;border-radius: 30px;height:40px;width:150px;">
                                <a class="nav-link text-white" href="#">Quick Top up</a>
                            </li>
                            {{-- @include('newpages.modals.quicktop-up',$setting_airtime) --}}
                            <li id="quickWithdrawalLink"
                                class="nav-item d-flex justify-content-center align-items-center mx-2"
                                style="font-size:14px;font-weight: 500;border: 1px solid #000070;border-radius: 30px;height:40px;width:150px;">
                                <a class="nav-link" style="color: #000070;font-weight: 500;">Quick Withdrawal</a>
                            </li>
                            {{-- @include('newpages.modals.quickwithdrawalmodal',$setting_withdrawal) --}}
                            <li id="quickWithdrawalLink"
                                class="nav-item d-flex justify-content-center align-items-center mx-2"
                                style="font-size:14px;font-weight: 500;background: #00B9CD;border-radius: 30px;height:40px;width:150px;">
                                <a class="nav-link text-white" href="#">Swap Bitcoins</a>
                            </li>
                        </ul>
                    </div>

                    <div class="app-header-right">
                        <div class="header-btn-lg pr-0">
                            <div class="widget-content p-0">
                                <div class="widget-content-wrapper">
                                    <div class="row align-items-center">
                                        <a href="{{route('user.profile')}} " class="text-right">
                                            <div class="col  ml-2 header-user-info">
                                                <div class="widget-heading text-custom">
                                                    @auth
                                                    {{Auth::user()->username ?? ''}}
                                                    @endauth
                                                </div>
                                                <div class="widget-subheading text-dark">
                                                    Hi there,
                                                </div>
                                                </div>
                                        </a>
                                        <div class="col ml-1">
                                            <div class="btn-group">
                                                @auth
                                                <div class="dropdown">
                                                    <a data-toggle="dropdown" class="p-0 btn">
                                                        <img width="35" class="rounded-circle"
                                                            src="{{asset('storage/avatar/'.Auth::user()->dp)}} " >
                                                        {{-- <i class="fa fa-angle-down ml-2 opacity-8"></i> --}}
                                                    </a>
                                                    <div tabindex="-1" role="menu" aria-hidden="true"
                                                        class="dropdown-menu dropdown-menu-left">
                                                        <a href=" {{route('user.profile')}} "><button type="button"
                                                                tabindex="0" class="dropdown-item">My
                                                                Account</button></a>
                                                        <a href=" {{route('user.transactions')}} "><button type="button"
                                                                tabindex="0" class="dropdown-item">My
                                                                transactions</button></a>
                                                        <a href=" {{route('user.assets')}} "><button type="button"
                                                                tabindex="0" class="dropdown-item">Trade</button></a>
                                                        <div tabindex="-1" class="dropdown-divider"></div>
                                                        <a href="#"
                                                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                            <button type="button" tabindex="0" class="dropdown-item"
                                                                style="color: red;">Logout</button>
                                                        </a>
                                                    </div>
                                                </div>

                                                <notifications-component :notifications="{{$nots}}"
                                                    :unread="{{$unread}} "></notifications-component>

                                                @endauth
                                            </div>
                                        </div>
                                        <div class="col ml-1">
                                            <span style="cursor: pointer;" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                <svg width="32" height="31" viewBox="0 0 32 31" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12.125 27.125H6.95833C6.27319 27.125 5.61611 26.8528 5.13164 26.3684C4.64717 25.8839 4.375 25.2268 4.375 24.5417V6.45833C4.375 5.77319 4.64717 5.11611 5.13164 4.63164C5.61611 4.14717 6.27319 3.875 6.95833 3.875H12.125"
                                                        stroke="#C9CED6" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path d="M21.167 21.9596L27.6253 15.5013L21.167 9.04297"
                                                        stroke="#C9CED6" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path d="M27.625 15.5H12.125" stroke="#C9CED6" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </span>
                                            {{-- <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                                style="display: none;">
                                                @csrf
                                            </form> --}}
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="app-header__menu">
                    <div class="btn-group">
                        @auth
                        <notifications-component :notifications="{{$nots}}" :unread="{{$unread}} ">
                        </notifications-component>
                        @endauth
                    </div>
                </div>
            </div>

            <div id="notifications"></div>


            @yield('content')

        </div>
    </div>

    <!-- Transaction details modal -->
    <div class="modal fade" id="txn-detail-modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content c-rounded">
                <!-- Modal Header -->
                <div class="modal-header bg-custom-gradient c-rounded-top p-4">
                    <h4 class="modal-title">
                        Transaction details
                        <i class="fa fa-rotate-180 fa-paper-plane"></i>
                    </h4>
                    <button type="button" class="close bg-light rounded-circle" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body p-4">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <td class="text-left"><strong>Id</strong></td>
                                <td class="text-right" id="d-txn-uid">XXXXX</td>
                            </tr>
                            <tr>
                                <td class="text-left"><strong>Asset Type</strong></td>
                                <td class="text-right" id="d-txn-asset-type">XXXXX</td>
                            </tr>
                            <tr>
                                <td class="text-left"><strong>Card Type</strong></td>
                                <td class="text-right" id="d-txn-card-type">XXXXX</td>
                            </tr>
                            <tr>
                                <td class="text-left"><strong>Transaction type</strong></td>
                                <td class="text-right text-capitalize" id="d-txn-txn-type">XXXXX</td>
                            </tr>
                            <tr>
                                <td class="text-left"><strong>Currency</strong></td>
                                <td class="text-right" id="d-txn-country">XXXXX</td>
                            </tr>
                            <tr>
                                <td class="text-left"><strong>Amount</strong></td>
                                <td class="text-right" id="d-txn-amount">XXXXX</td>
                            </tr>
                            <tr>
                                <td class="text-left"><strong>Rate</strong></td>
                                <td class="text-right" id="d-txn-rate">XXXXX</td>
                            </tr>
                            <tr>
                                <td class="text-left"><strong>Quantity</strong></td>
                                <td class="text-right" id="d-txn-quantity">XXXXX</td>
                            </tr>
                            <tr>
                                <td class="text-left"><strong>Naira Equiv.</strong></td>
                                <td class="text-right" id="d-txn-amt-paid">XXXXX</td>
                            </tr>
                            <tr>
                                <td class="text-left"><strong>Status</strong></td>
                                <td class="text-right text-capitalize" id="d-txn-status">XXXXX</td>
                            </tr>
                            <tr>
                                <td class="text-left"><strong>Date</strong></td>
                                <td class="text-right" id="d-txn-date">XXXXX</td>
                            </tr>
                        </tbody>
                    </table>
                    <button class="btn btn-block c-rounded bg-custom-gradient" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Naira wallet Transaction detail --}}
    <div class="modal fade" id="wallet-txn-modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content c-rounded">
                <!-- Modal Header -->
                <div class="modal-header bg-custom-gradient c-rounded-top p-4">
                    <h4 class="modal-title">
                        Transaction details
                        <i class="fa fa-rotate-180 fa-paper-plane"></i>
                    </h4>
                    <button type="button" class="close bg-light rounded-circle" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body p-4">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <td class="text-left"><strong>Reference</strong></td>
                                <td class="text-right" id="d-w-txn-ref">XXXXX</td>
                            </tr>
                            <tr>
                                <td class="text-left"><strong>Transaction type</strong></td>
                                <td class="text-right" id="d-w-txn-type">XXXXX</td>
                            </tr>
                            <tr>
                                <td class="text-left"><strong>Transaction Category</strong></td>
                                <td class="text-right" id="d-w-txn-cat">XXXXX</td>
                            </tr>
                            <tr>
                                <td class="text-left"><strong>Amount</strong></td>
                                <td class="text-right" id="d-w-txn-amount">XXXXX</td>
                            </tr>
                            <tr>
                                <td class="text-left"><strong>Charge</strong></td>
                                <td class="text-right" id="d-w-txn-charge">XXXXX</td>
                            </tr>
                            <tr>
                                <td class="text-left"><strong>Cr Account</strong></td>
                                <td class="text-right" id="d-w-txn-cr">XXXXX</td>
                            </tr>
                            <tr>
                                <td class="text-left"><strong>Dr Account</strong></td>
                                <td class="text-right" id="d-w-txn-dr">XXXXX</td>
                            </tr>
                            <tr>
                                <td class="text-left"><strong>Narration</strong></td>
                                <td class="text-right" id="d-w-txn-narration">XXXXX</td>
                            </tr>
                            <tr>
                                <td class="text-left"><strong>Status</strong></td>
                                <td class="text-right" id="d-w-txn-status">XXXXX</td>
                            </tr>
                            <tr>
                                <td class="text-left"><strong>Date</strong></td>
                                <td class="text-right" id="d-w-txn-date">XXXXX</td>
                            </tr>
                        </tbody>
                    </table>
                    <button class="btn btn-block c-rounded bg-custom-gradient" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @if (!Auth::user()->btcWallet)
    {{-- New BTC wallet --}}
    <div class="modal fade" id="new-btc-wallet">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content c-rounded">
                <!-- Modal body -->
                <div class="modal-body p-4">
                    <h4>New Wallet Pin</h4>
                    <p>Please enter a new wallet pin to migrate to the new BTC wallet. Please note that this pin will also be used as your Naira wallet pin</p>
                   <form action="{{ route('user.bitcoin-wallet.create') }}" class="disable-form" method="post">@csrf
                       <div class="form-group mt-5" >
                           <label for="">Wallet Pin</label>
                           <input type="password" name="pin" required maxlength="4" minlength="4" class="form-control">
                        </div>
                        <button class="btn btn-block c-rounded bg-custom-gradient" >Save</button>
                   </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    <script src="/js/app.js?v={{ env('APP_STATIC_FILES_VERSION') }}"></script>
    <script src="{{asset('assets/scripts/main.js')}} "></script>
    <script src="{{asset('js/jquery-3.2.1.min.js')}} "></script>
    <script src="{{asset('js/popper.min.js')}} "></script>
    <script src="{{asset('js/bootstrap.min.js')}} "></script>
    <script src="/js/custom.js?v={{ env('APP_STATIC_FILES_VERSION') }}"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="{{asset('js/wallet.js')}} "></script>


    {{-- Calculator scripts --}}
    <script src="/user_assets/js/calculator.js?v={{ env('APP_STATIC_FILES_VERSION') }}"></script>
    <script src="/user_assets/js/airtime.js"></script>
    @yield('scripts')


    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"> </script>

    @if (!Auth::user()->btcWallet)
    <script>
        $('#new-btc-wallet').modal('show');
    </script>
    @endif



    @if (session('error'))
    <script>
        swal(
        'Oops!',
        '{{ session("error") }}',
        'error'
        )
    </script>
    @endif

    @if (session('success'))
    <script>
        swal(
        'Good Job!',
        '{{ session("success") }}',
        'success'
        )
    </script>
    @endif

    @php
    $err_msg = '';
    foreach ($errors->all() as $err ) {
    $err_msg .= $err . '. ';
    if (strlen(trim($err_msg)) > 0) {
    echo '
    <script>
        swal({
            title: "Ooops!",
            text: "'.$err_msg .'",
            icon: "error",
            button: "OK",
        });

    </script>
    ';


    }
    }
    @endphp


</body>

</html>
