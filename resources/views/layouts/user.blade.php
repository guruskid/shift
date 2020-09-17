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
    <link href=" {{asset('css/app.css')}} " rel="stylesheet">
    <link href=" {{asset('user_main.css')}} " rel="stylesheet">
    <link href=" {{asset('custom.css?v = 1.0')}} " rel="stylesheet">
    <link href=" {{asset('user_assets/css/responsive-fixes.css')}} " rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    {{-- <link rel="stylesheet" href="{{asset('main/css/calculator.css')}} "> --}}


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
                ]
            ])
        !!};

    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" charset="utf-8"></script>
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

    </style>

</head>

<body>
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
                           <a href="{{route('user.naira-wallet')}}" title="View wallet">
                            <div class="widget-heading text-custom">
                                @auth
                                ₦{{number_format($naira_balance)}}
                                @endauth
                            </div>
                            <div class="widget-heading text-muted">
                               Wallet Balance
                            </div>
                        </a>
                        </div>
                    </div>
                    <div class="app-header-right">
                        <div class="header-btn-lg pr-0">
                            <div class="widget-content p-0">
                                <div class="widget-content-wrapper">
                                    <div class="row align-items-center">
                                        <a href="{{route('user.profile')}} " class="text-right">
                                        <div class="col  ml-3 header-user-info">
                                                <div class="widget-heading text-custom">
                                                    @auth
                                                    {{Auth::user()->first_name}}
                                                    @endauth
                                                </div>
                                                <div class="widget-subheading text-dark">
                                                    Hi there,
                                                </div>
                                            </div>
                                        </a>
                                        <div class="col ml-2">
                                            <div class="btn-group">
                                                @auth
                                                <div class="dropdown">
                                                    <a data-toggle="dropdown" class="p-0 btn">
                                                        <img width="35" class="rounded-circle"
                                                            src="{{asset('storage/avatar/'.Auth::user()->dp)}} " alt="">
                                                        {{-- <i class="fa fa-angle-down ml-2 opacity-8"></i> --}}
                                                    </a>
                                                    <div tabindex="-1" role="menu" aria-hidden="true"
                                                        class="dropdown-menu dropdown-menu-left">
                                                        <a href=" {{route('user.profile')}} "><button type="button"
                                                                tabindex="0" class="dropdown-item">My Account</button></a>
                                                        <a href=" {{route('user.transactions')}} "><button type="button"
                                                                tabindex="0" class="dropdown-item">My
                                                                transactions</button></a>
                                                        <a href=" {{route('user.calculator')}} "><button type="button"
                                                                tabindex="0" class="dropdown-item">Trade</button></a>
                                                        <div tabindex="-1" class="dropdown-divider"></div>
                                                        <a href="#"
                                                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                            <button type="button" tabindex="0"
                                                                class="dropdown-item">Logout</button>
                                                        </a>
                                                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                                            style="display: none;">
                                                            @csrf
                                                        </form>
                                                    </div>
                                                </div>

                                                <notifications-component :notifications = "{{$nots}}" :unread = "{{$unread}} " ></notifications-component>

                                                @endauth
                                            </div>
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
                        <notifications-component :notifications = "{{$nots}}" :unread = "{{$unread}} " ></notifications-component>
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
              <table class="table table-borderless" >
                <tbody>
                    <tr>
                        <td class="text-left" ><strong>Id</strong></td>
                        <td class="text-right" id="d-txn-uid" >XXXXX</td>
                    </tr>
                    <tr>
                        <td class="text-left" ><strong>Asset Type</strong></td>
                        <td class="text-right" id="d-txn-asset-type" >XXXXX</td>
                    </tr>
                    <tr>
                        <td class="text-left" ><strong>Transaction type</strong></td>
                        <td class="text-right" id="d-txn-txn-type" >XXXXX</td>
                    </tr>
                    <tr>
                        <td class="text-left" ><strong>Currency</strong></td>
                        <td class="text-right" id="d-txn-country" >XXXXX</td>
                    </tr>
                    <tr>
                        <td class="text-left" ><strong>Amount</strong></td>
                        <td class="text-right" id="d-txn-amount" >XXXXX</td>
                    </tr>
                    <tr>
                        <td class="text-left" ><strong>Naira Equiv.</strong></td>
                        <td class="text-right" id="d-txn-amt-paid" >XXXXX</td>
                    </tr>
                    <tr>
                        <td class="text-left" ><strong>Status</strong></td>
                        <td class="text-right" id="d-txn-status" >XXXXX</td>
                    </tr>
                    <tr>
                        <td class="text-left" ><strong>Date</strong></td>
                        <td class="text-right" id="d-txn-date" >XXXXX</td>
                    </tr>
                </tbody>
              </table>
              <button class="btn btn-block c-rounded bg-custom-gradient" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>

    <script src="/js/app.js?v = 1.4"></script>
    <script src="{{asset('assets/scripts/main.js')}} "></script>
    <script src="{{asset('js/jquery-3.2.1.min.js')}} "></script>
    <script src="{{asset('js/popper.min.js')}} "></script>
    <script src="{{asset('js/bootstrap.min.js')}} "></script>
    <script src="{{asset('js/custom.js')}}"></script>
    <script src="{{asset('js/bootstrap-notify.js')}}"></script>
    <script src="{{asset('js/wallet.js')}} "></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js">
    </script>

    <script>
        $(document).ready(function () {
            $('.transactions-table').DataTable({
                paging: false,
                order: [[0, 'desc'] ]
            });
        });

    </script>

    @auth
    @if (Auth::user()->role == 999 || Auth::user()->role == 888 )
    <script src="{{asset('js/sa.js')}}"></script>
    @endif
    @endauth




    @if(session()->has('success'))
    <script>
        $(document).ready(function () {
            Notify("{{session()->get('success')}} ", null, null, 'success');
        });

    </script>
    @endif

    @if(session()->has('error'))
    <script>
        $(document).ready(function () {
            Notify("{{session()->get('error')}} ", null, null, 'danger');
        });

    </script>
    @endif

</body>

</html>
