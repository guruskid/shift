@auth
@php
/* $nots = App\Notification::where('user_id', 0)->get(); */
$nots = Auth::user()->notifications;
foreach ($nots as $not ) {
$not->date = $not->created_at->diffForHumans();
}
$not = $nots->last();
$unread = Auth::user()->notifications()->where('is_seen', 0)->count();

@endphp
@endauth

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
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
     @if(Auth::user()->role == 889 OR Auth::user()->role == 777 OR Auth::user()->role == 775)
     <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.css" integrity="sha512-DIW4FkYTOxjCqRt7oS9BFO+nVOwDL4bzukDyDtMO7crjUZhwpyrWBFroq+IqRe6VnJkTpRAS6nhDvf0w+wHmxg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
     <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js" integrity="sha512-Zq9o+E00xhhR/7vJ49mxFNJ0KQw1E1TMWkPTxrWcnpfEFDEXgUiwJHIKit93EW/XxE31HSI5GEOW06G6BF1AtA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
     @endif
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
                                <div class="btn-group">
                                    @auth
                                    <notifications-component :notifications="{{$nots}}" :unread="{{0}} "></notifications-component>

                                    @endauth
                                    {{-- <span class="badge bg-warning text-white">{{number_format($unread)}}</span> --}}
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
                        <p>
                            @switch(Auth::user()->role)
                                @case(999)
                                    Super Admin
                                    @break
                                @case(889)
                                    Senior Accountant
                                    @break
                                    @case(777)
                                    Junior Accountant
                                    @break
                                    @case(888)
                                    Sales Rep.
                                    @break
                                    @case(666)
                                    Manager
                                    @break
                                    @case(559)
                                    Marketing
                                    @break
                                    @case(444)
                                    Chinese
                                    @break
                                    @case(557)
                                    Business Developer
                                    @break
                                    @case(775)
                                    Account Officer
                                    @break
                                    @case(556)
                                    Sales
                                    @break
                                @default
                                Hi! there

                            @endswitch
                        </p>
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
                @if (Auth::user()->role == 999)
                @include('layouts.partials.super_admin_sidebar')
                @endif

                @if (Auth::user()->role == 666)
                @include('layouts.partials.manager_sidebar')
                @endif

                @if (Auth::user()->role == 889 || Auth::user()->role == 777 || Auth::user()->role == 775 )
                @include('layouts.partials.accountant_sidebar')
                @endif

                @if (Auth::user()->role == 559)
                @include('layouts.partials.marketing_sidebar')
                @endif
                @if (Auth::user()->role == 557)
                @include('layouts.partials.buisness_developer_sidebar')
                @endif
                @if (Auth::user()->role == 556)
                @include('layouts.partials.sales_sidebar')
                @endif

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
    <script src="/js/app.js?v=1"></script>

    <script src="{{asset('admin_assets/bootstrap/js/bootstrap.min.js')}} "></script>
    <script src="{{asset('admin_assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js')}} "></script>
    <script src="https://unpkg.com/ionicons@5.1.2/dist/ionicons.js"></script>
    <script src="{{asset('admin_assets/js/app.js')}} "></script>
    <script src="{{asset('js/bootstrap-notify.js')}}"></script>
    <script src="{{asset('js/sa.js?v=45')}}"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    {{-- Datatables --}}
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            App.init();
        });
    </script>
    <script src="{{asset('admin_assets/js/custom.js?v=45')}} "></script>

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
            alert('{{ session()->get("success") }}')
        });

    </script>
    @endif

    @if(session()->has('error'))
    <script>
        $(document).ready(function () {
            Notify("{{session()->get('error')}} ", null, null, 'danger');
            alert('{{ session()->get("error") }}')
        });

    </script>
    @endif

    <script>
        $('.btn-message').click(function () {
            $('#box-message').toggle();
            $('#scroll-msg').scrollTop($('#scroll-msg')[0].scrollHeight - $('#scroll-msg')[0].clientHeight);
        });

    </script>
     @auth
     @if (in_array(Auth::user()->role, [999, 889, 888, 777, 666, 444, 449,557,559,556] ))
     <script src="{{asset('js/sa.js?v=7')}}"></script>
     @endif

     @if(Auth::user()->role == 889 OR Auth::user()->role == 777 OR Auth::user()->role == 775)
     <script>

         var pusher = new Pusher('9a1545beffb83093b6cb', {
           cluster: 'eu'
         });

         var channel = pusher.subscribe('notify');
         channel.bind('transaction', function(data) {
           iziToast.success({
             timeout: 25000,
             position: 'topRight',
         title: '<a href="https://app.dantownms.com/admin/transnotifications" target="_blank">New Transaction </a>',
         message: data.info
     });


         });
       </script>
        @endif



     @endauth

<script type="text/javascript">
    const __st_id = (activity) => document.getElementById(activity)

    const hideit = (ide) => {
        __st_id(ide).classList.remove("d-block")
        __st_id(ide).classList.add("d-none")
    }

    const showit = (ide) => {
        __st_id(ide).classList.remove("d-none")
        __st_id(ide).classList.add("d-block")
    }

    const category_status = () => {
        const feedback = __st_id("category")
        if (!(feedback.value == "" || feedback.value =="NoResponse")) {
        showit("feedback-textarea")
        } else {
            hideit("feedback-textarea")
        }
    }

    const decline_reason = (selectedOption) => {
        showit(selectedOption)
    }

</script>

</body>
</html>
