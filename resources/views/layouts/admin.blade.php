<!DOCTYPE html>
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
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title>Dashboard - Dantown Multi Services </title>
    <link rel="icon" type="image/x-icon" href="{{asset('admin_assets/img/fav2.png')}} "/>
    <link href="{{asset('admin_assets/css/loader.css')}} " rel="stylesheet" type="text/css" />
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="https://fonts.googleapis.com/css?family=Quicksand:400,500,600,700&display=swap" rel="stylesheet">
    <link href="{{asset('admin_assets/bootstrap/css/bootstrap.min.css')}} " rel="stylesheet" type="text/css" />
    <link href="{{asset('admin_assets/css/plugins.css')}} " rel="stylesheet" type="text/css" />
    <!-- END GLOBAL MANDATORY STYLES -->

    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM STYLES -->
    <link href="{{asset('admin_assets/plugins/apex/apexcharts.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('admin_assets/css/dashboard/dash_1.css')}}" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL PLUGINS/CUSTOM STYLES -->

    <script src="{{asset('admin_assets/js/loader.js')}} "></script>
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
</head>
<body class="sidebar-noneoverflow">
    <!-- BEGIN LOADER -->

    <!--  END LOADER -->

    <div id="app">
        <!--  BEGIN NAVBAR  -->
    <div class="header-container fixed-top">
        <header class="header navbar navbar-expand-sm">

            <ul class="navbar-nav theme-brand flex-row  text-center">
                <li class="nav-item theme-logo">
                    <a href="index.html">
                        <img src="{{asset('admin_assets/img/fav2.png')}} " class="navbar-logo" alt="logo">
                    </a>
                </li>
                <li class="nav-item theme-text">
                    <a href="index.html">
                        <img src="{{asset('admin_assets/img/logo.svg')}} " class="navbar-logo" alt="logo">
                    </a>
                </li>
                <li class="nav-item toggle-sidebar">
                    <a href="javascript:void(0);" class="sidebarCollapse" data-placement="bottom"><img src="{{asset('admin_assets/img/open-menu.svg')}} " alt="" style="width: 25px;"></a>
                </li>
            </ul>

            <ul class="navbar-item flex-row navbar-dropdown navbar-right">
                <li class="nav-item dropdown notification-dropdown">
                    <a href="javascript:void(0);" class="nav-link dropdown-toggle" id="notificationDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img class="bell" src="{{asset('admin_assets/img/bell.svg')}} " alt="">
                    </a>
                    <div class="dropdown-menu position-absolute animated fadeInUp" aria-labelledby="notificationDropdown">
                        <div class="notification-scroll">

                            <div class="dropdown-item">
                                <div class="media server-log">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-server"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect><rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect><line x1="6" y1="6" x2="6" y2="6"></line><line x1="6" y1="18" x2="6" y2="18"></line></svg>
                                    <div class="media-body">
                                        <div class="data-info">
                                            <h6 class="">Server Rebooted</h6>
                                            <p class="">45 min ago</p>
                                        </div>

                                        <div class="icon-status">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="dropdown-item">
                                <div class="media ">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-heart"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                                    <div class="media-body">
                                        <div class="data-info">
                                            <h6 class="">Licence Expiring Soon</h6>
                                            <p class="">8 hrs ago</p>
                                        </div>

                                        <div class="icon-status">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="dropdown-item">
                                <div class="media file-upload">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                    <div class="media-body">
                                        <div class="data-info">
                                            <h6 class="">Kelly Portfolio.pdf</h6>
                                            <p class="">670 kb</p>
                                        </div>

                                        <div class="icon-status">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="nav-item dropdown message-dropdown">
                    <a href="javascript:void(0);" class="nav-link dropdown-toggle" id="messageDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img class="avatar" src="{{asset('admin_assets/img/default.jpg')}} " alt="">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </a>
                    <div class="dropdown-menu p-0 position-absolute animated fadeInUp" aria-labelledby="messageDropdown">
                        <div class="">
                            <a class="dropdown-item">
                                <div class="">

                                    <div class="media">
                                        <div class="media-body">
                                            <div class="mb-3">
                                                <h5 class="msg-title">My Account</h5>
                                            </div>
                                            <div class="mb-3">
                                                <h5 class="msg-title">My Transaction</h5>
                                            </div>
                                            <div class="mb-2">
                                                <h5 class="msg-title">Trade</h5>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </a>
                            <a class="dropdown-item">
                                <div class="">
                                    <div class="media">
                                        <div class="media-body">
                                            <div class="mt-2">
                                                <h5 class="msg-title">Logout</h5>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </a>
                        </div>
                    </div>
                </li>

                <li class="nav-item dropdown user-profile-dropdown  order-lg-0 order-1">
                    <div class="media-body">
                        <h5>{{Auth::user()->first_name}} </h5>
                        <p>Hi there</p>
                    </div>
                </li>
            </ul>
        </header>
    </div>
    <!--  END NAVBAR  -->

    <!--  BEGIN MAIN CONTAINER  -->
    <div class="main-container" id="container">

        <div class="search-overlay"></div>

        <!--  BEGIN SIDEBAR  -->
        <div class="sidebar-wrapper sidebar-theme">

            <nav id="sidebar">
                <ul class="list-unstyled menu-categories" id="accordionExample">
                    <li class="menu {{ Route::currentRouteName() == 'admin.dashboard' ? 'active' : '' }}">
                        <a href="{{route('admin.dashboard')}}"  aria-expanded="true" class="dropdown-toggle">
                            <div class="">
                                <ion-icon name="home-outline"></ion-icon>
                                <span>Dashboard</span>
                            </div>
                        </a>
                    </li>

                    <li class="menu {{ Route::currentRouteName() == 'admin.transactions' ? 'active' : '' }}  ">
                        <a href="{{route('admin.transactions')}}"  aria-expanded="false" class="dropdown-toggle">
                            <div class="">
                                <ion-icon name="speedometer-outline"></ion-icon>
                                <span>Transactions</span>
                            </div>
                        </a>
                    </li>

                    <li class="menu {{ Route::currentRouteName() == 'admin.wallet-transactions' ? 'active' : '' }}  ">
                        <a href="{{route('admin.wallet-transactions')}}"  aria-expanded="false" class="dropdown-toggle">
                            <div class="">
                                <ion-icon name="cash-outline"></ion-icon>
                                <span>Naira Wallet Transactions</span>
                            </div>
                        </a>
                    </li>

                    <li class="menu {{ Route::currentRouteName() == 'admin.rates' ? 'active' : '' }}">
                        <a href="{{route('admin.rates')}}"  aria-expanded="false" class="dropdown-toggle">
                            <div class="">
                                <ion-icon name="analytics-outline"></ion-icon>
                                <span>Rates</span>
                            </div>
                        </a>
                    </li>

                    <li class="menu {{ Route::currentRouteName() == 'admin.assigned-transactions' ? 'active' : '' }}">
                        <a href="{{route('admin.assigned-transactions')}}"  aria-expanded="false" class="dropdown-toggle">
                            <div class="">
                                <ion-icon name="radio-button-on-outline"></ion-icon>
                                <span>Assigned Trnsactions</span>
                            </div>
                        </a>
                    </li>


                    @if (Auth::user()->role == 999 )

                    <li class="menu {{ Route::currentRouteName() == 'admin.users' ? 'active' : '' }}">
                        <a href="{{route('admin.users')}}" aria-expanded="false" class="dropdown-toggle" >
                            <div class="">
                                <ion-icon name="people-outline"></ion-icon>
                                <span>Users</span>
                            </div>
                        </a>
                    </li>

                    <li class="menu {{ Route::currentRouteName() == 'admin.cards' ? 'active' : '' }}">
                        <a href="{{route('admin.cards')}}" aria-expanded="false" class="dropdown-toggle">
                            <div class="">
                                <ion-icon name="cube-outline"></ion-icon>
                                <span>All Assets</span>
                            </div>
                        </a>
                    </li>

                    <li class="menu {{ Route::currentRouteName() == 'admin.notification' ? 'active' : '' }}">
                        <a href="{{route('admin.notification')}}" aria-expanded="false" class="dropdown-toggle" >
                            <div class="">
                                <ion-icon name="volume-high-outline"></ion-icon>
                                <span>Notifications</span>
                            </div>
                        </a>
                    </li>

                    <li class="menu {{ Route::currentRouteName() == 'admin.verify' ? 'active' : '' }}">
                        <a href="{{route('admin.verify')}}" aria-expanded="false" class="dropdown-toggle" >
                            <div class="">
                                <ion-icon name="shield-checkmark-outline"></ion-icon>
                                <span>Verify Users</span>
                            </div>
                        </a>
                    </li>

                    <li class="menu {{ Route::currentRouteName() == 'admin.chat_agents' ? 'active' : '' }}">
                        <a href="{{route('admin.chat_agents')}}" aria-expanded="false" class="dropdown-toggle" >
                            <div class="">
                                <ion-icon name="people-circle-outline"></ion-icon>
                                <span>Trade Agents</span>
                            </div>
                        </a>
                    </li>
                    @endif

                    <li class="menu {{ Route::currentRouteName() == 'logout' ? 'active' : '' }}">
                        <a href="{{route('logout')}}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" aria-expanded="false" class="dropdown-toggle">
                            <div class="">
                                <ion-icon name="radio-button-on-outline"></ion-icon>
                                <span>Logout</span>
                            </div>
                        </a>
                    </li>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </ul>
            </nav>

        </div>
        <!--  END SIDEBAR  -->

        <!--  BEGIN CONTENT AREA  -->
        @yield('content')
        <!--  END CONTENT AREA  -->


    </div>
    <!-- END MAIN CONTAINER -->
    </div>

    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <script src="{{asset('admin_assets/js/libs/jquery-3.1.1.min.js')}} "></script>
    <script src="{{asset('admin_assets/bootstrap/js/popper.min.js')}} "></script>
    {{-- Vue JS --}}
    <script src="/js/app.js"></script>

    <script src="{{asset('admin_assets/bootstrap/js/bootstrap.min.js')}} "></script>
    <script src="{{asset('admin_assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js')}} "></script>
    <script src="https://unpkg.com/ionicons@5.1.2/dist/ionicons.js"></script>
    <script src="{{asset('admin_assets/js/app.js')}} "></script>
    <script src="{{asset('js/bootstrap-notify.js')}}"></script>
    <script src="{{asset('js/sa.js')}}"></script>

    {{-- Datatables --}}
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            App.init();
        });
    </script>
    <script src="{{asset('admin_assets/js/custom.js')}} "></script>
    <!-- END GLOBAL MANDATORY SCRIPTS -->

    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->
    <script src="{{asset('admin_assets/plugins/apex/apexcharts.min.js')}} "></script>
    <script src="{{asset('admin_assets/js/dashboard/dash_1.js')}} "></script>
    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->

    <script>
        $(document).ready(function () {
            $('.transactions-table').DataTable({
                paging: true,
                order: [[0, 'desc'] ]
            });
        });

    </script>



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

    <script>
        $('.btn-message').click(function () {
            $('#box-message').toggle();
            $('#scroll-msg').scrollTop($('#scroll-msg')[0].scrollHeight - $('#scroll-msg')[0].clientHeight);
        });

    </script>

</body>
</html>
