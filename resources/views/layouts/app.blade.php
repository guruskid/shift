@auth
@php
/* $nots = App\Notification::where('user_id', 0)->get(); */
$nots = Auth::user()->notifications->take(3);
$not = $nots->last();
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
    <link href=" {{asset('main.css')}} " rel="stylesheet">
    <link href=" {{asset('custom.css')}} " rel="stylesheet">

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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" charset="utf-8"></script>

    {{-- Data tables --}}
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/v/bs4/dt-1.10.21/fh-3.1.7/r-2.2.5/datatables.min.css" />

    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.21/fh-3.1.7/r-2.2.5/datatables.min.js">
    </script>
</head>

<body >



    {{--  @auth
        @if (Auth::user()->role != 999)
        @include('layouts.partials.chat')
        @endif
        @endauth --}}
<div id="app" >
    <div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header closed-sidebar">
        <div class="app-header header-shadow header-text-light bg-night-sky " >
            <div class="app-header__logo">
                <a href="https://dantownms.com">
                    <div class="logo-src"></div>
                </a>
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
                <div class="btn-group">
                    @auth
                    <notifications-component :notifications = "{{$nots}}" :unread = "{{0}} " ></notifications-component>
                    {{-- <div class="dropdown">
                        <a data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="p-0 btn">
                            <i class="fa fa-bell mx-2 fa-2x text-warning "></i>
                        </a>
                        <div tabindex="-1" role="menu" aria-hidden="true" class="dropdown-menu dropdown-menu-left">
                            @foreach ($nots as $not)
                            <div class="media p-2">
                                <i class="fa fa-2x fa-bell mr-1 text-warning"></i>
                                <p class="media-body ">
                                    <strong>{{$not->title}}</strong>
                                    {{$not->body}}
                                </p>
                            </div>
                            @endforeach
                        </div>
                    </div> --}}
                    @endauth
                </div>
            </div>
            <div class="app-header__content" >

                <div class="app-header-right">
                    <div class="header-btn-lg pr-0">
                        <div class="widget-content p-0">
                            <div class="widget-content-wrapper">
                                <div class="widget-content-left">
                                    <div class="btn-group">
                                        @auth
                                        <notifications-component :notifications = "{{$nots}}" :unread = "{{0}} " ></notifications-component>
                                        {{-- <div class="dropdown">
                                            <a href="#" data-toggle="dropdown"><i
                                                    class="fa fa-bell mx-2 fa-2x text-white"></i></a>
                                            <div class="dropdown-menu">
                                                @foreach ($nots as $not)
                                                <div class="media p-2">
                                                    <i class="fa fa-2x fa-bell mr-1 text-warning"></i>
                                                    <p class="media-body">
                                                        <strong>{{$not->title}}</strong>
                                                        {{$not->body}}
                                                    </p>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div> --}}
                                        @if (Auth::user()->role == 999 OR Auth::user()->role == 888 )
                                        <div class="dropdown">
                                            <a data-toggle="dropdown" class="p-0 btn">
                                                <img width="42" class="rounded-circle"
                                                    src="{{asset('storage/avatar/'.Auth::user()->dp)}} " alt="">
                                                <i class="fa fa-angle-down ml-2 opacity-8"></i>
                                            </a>
                                            <div tabindex="-1" role="menu" aria-hidden="true"
                                                class="dropdown-menu dropdown-menu-left">
                                                <a href=" {{route('admin.dashboard')}} "><button type="button"
                                                        tabindex="0" class="dropdown-item">Dashboard</button></a>
                                                <a href=" {{route('admin.transactions')}} "><button type="button"
                                                        tabindex="0" class="dropdown-item">All
                                                        transactions</button></a>
                                                <a href=" {{route('admin.rates')}} "><button type="button" tabindex="0"
                                                        class="dropdown-item">Rates</button></a>
                                                <div tabindex="-1" class="dropdown-divider"></div>
                                                <a href="#"
                                                    onclick="event.preventDefault(); document.getElementById('logout-form2').submit();">
                                                    <button type="button" tabindex="0"
                                                        class="dropdown-item">Logout</button>
                                                </a>
                                                <form id="logout-form2" action="{{ route('logout') }}" method="POST"
                                                    style="display: none;">
                                                    @csrf
                                                </form>
                                            </div>
                                        </div>
                                        @else
                                        <div class="dropdown">
                                            <a data-toggle="dropdown" class="p-0 btn">
                                                <img width="42" class="rounded-circle"
                                                    src="{{asset('storage/avatar/'.Auth::user()->dp)}} " alt="">
                                                <i class="fa fa-angle-down ml-2 opacity-8"></i>
                                            </a>
                                            <div tabindex="-1" role="menu" aria-hidden="true"
                                                class="dropdown-menu dropdown-menu-left">
                                                <a href=" {{route('user.profile')}} "><button type="button" tabindex="0"
                                                        class="dropdown-item">My Account</button></a>
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
                                        @endif

                                        @endauth
                                    </div>
                                </div>
                                <div class="widget-content-left  ml-3 header-user-info">
                                    <div class="widget-heading">
                                        @auth
                                        {{Auth::user()->first_name." ".Auth::user()->last_name}}
                                        @endauth
                                    </div>
                                    <div class="widget-subheading">
                                        Hi there
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="notifications"></div>

            @yield('content')


    </div>
</div>
    <script src="/js/app.js"></script>
    <script src="{{asset('assets/scripts/main.js')}} "></script>
    <script src="{{asset('js/jquery-3.2.1.min.js')}} "></script>
    <script src="{{asset('js/popper.min.js')}} "></script>
    <script src="{{asset('js/bootstrap.min.js')}} "></script>
    <script src="{{asset('js/custom.js')}}"></script>
    <script src="{{asset('js/bootstrap-notify.js')}}"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>

    @auth
    @if (Auth::user()->role == 999 || Auth::user()->role == 888 )
    <script src="{{asset('js/sa.js')}}"></script>
    @endif
    @endauth

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
