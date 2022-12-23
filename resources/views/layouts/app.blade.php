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
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="{{ asset('fav.png') }}">
    <link rel="shortcut icon" href="{{ asset('fav.png') }}">
    <title> Dashboard - Dantown Multi Services</title>
    <meta name="viewport"
        content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" />
    <meta name="description" content="Dantown multi services">
    <meta name="msapplication-tap-highlight" content="no">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand&display=swap" rel="stylesheet">
    <link href=" {{ asset('main.css') }} " rel="stylesheet">
    <link href=" {{ asset('custom.css') }} " rel="stylesheet">

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

        .tooltip {
            max-width: 200px;
            padding: 3px 8px;
            color: #fff;
            text-align: center;
            background-color: #000070 !important;
            border-radius: .25rem;
        }

        .tooltip.bs-tooltip-auto[x-placement^=top] .arrow::before,
        .tooltip.bs-tooltip-top {
            margin-left: -3px;
            content: "";
            border-width: 5px 5px 0;
            border-top-color: #000070 !important;
        }

        .swal-button {
            background-color: #000070;
        }

        .swal-button:not([disabled]):hover {
            background-color: #020244;
        }


        .to_trans_page {
            cursor: pointer;
        }
    </style>
    <script>
        window.Laravel = {
            !!json_encode([
                'csrfToken' => csrf_token(),
                'user' => [
                    'authenticated' => auth() - > check(),
                    'id' => auth() - > check() ? auth() - > user() - > id : null,
                    'first_name' => auth() - > check() ? auth() - > user() - > first_name : null,
                    'last_name' => auth() - > check() ? auth() - > user() - > last_name : null,
                    'email' => auth() - > check() ? auth() - > user() - > email : null,
                ]
            ]) !!
        };
    </script>


    @yield('script-2')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" charset="utf-8"></script>

    {{-- Data tables --}}
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/v/bs4/dt-1.10.21/fh-3.1.7/r-2.2.5/datatables.min.css" />

    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.21/fh-3.1.7/r-2.2.5/datatables.min.js">
    </script>
    @auth
        @if (Auth::user()->role == 889 or Auth::user()->role == 777 or Auth::user()->role == 775)
            <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.css"
                integrity="sha512-DIW4FkYTOxjCqRt7oS9BFO+nVOwDL4bzukDyDtMO7crjUZhwpyrWBFroq+IqRe6VnJkTpRAS6nhDvf0w+wHmxg=="
                crossorigin="anonymous" referrerpolicy="no-referrer" />
            <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"
                integrity="sha512-Zq9o+E00xhhR/7vJ49mxFNJ0KQw1E1TMWkPTxrWcnpfEFDEXgUiwJHIKit93EW/XxE31HSI5GEOW06G6BF1AtA=="
                crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        @endif
    @endauth
</head>

<body>
    <div id="app">
        <div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header closed-sidebar">
            <div class="app-header header-shadow header-text-light bg-night-sky ">
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
                            <notifications-component :notifications="{{ $nots }}" :unread="{{ 0 }}">
                            </notifications-component>
                        @endauth
                    </div>
                </div>
                <div class="app-header__content">

                    <div class="app-header-right">
                        <div class="header-btn-lg pr-0">
                            <div class="widget-content p-0">
                                <div class="widget-content-wrapper">
                                    <div class="widget-content-left d-flex">

                                        <div class="btn-group">
                                            @auth
                                                @if (Auth::user()->role == 444 or Auth::user()->role == 449)
                                                    <div class="language mt-2">
                                                        <h4 class="text-light">改变语言
                                                        </h4>
                                                    </div>
                                                    <div class="m-2">
                                                        {{-- /////// Chinese Dashboard ///////// --}}

                                                        <div class="mr-5" id="google_translate_element"></div>
                                                        <script type="text/javascript">
                                                            function googleTranslateElementInit() {
                                                                new google.translate.TranslateElement({
                                                                    pageLanguage: 'en'
                                                                }, 'google_translate_element');
                                                            }
                                                        </script>
                                                        <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit">
                                                        </script>
                                                    </div>
                                                @endif
                                            @endauth
                                        </div>


                                        @auth
                                            <notifications-component :notifications="{{ $nots }}"
                                                :unread="{{ 0 }}">
                                            </notifications-component>


                                            @if (Auth::user()->role == 999 or
                                                Auth::user()->role == 888 or
                                                Auth::user()->role == 444 or
                                                Auth::user()->role == 449)
                                                <div class="dropdown">
                                                    <a data-toggle="dropdown" class="p-0 btn">
                                                        <img width="42" class="rounded-circle"
                                                            src="{{ asset('storage/avatar/' . Auth::user()->dp) }} "
                                                            alt="">
                                                        <i class="fa fa-angle-down ml-2 opacity-8"></i>
                                                    </a>


                                                    <div tabindex="-1" role="menu" aria-hidden="true"
                                                        class="dropdown-menu dropdown-menu-left">
                                                        <a href=" {{ route('admin.dashboard') }} "><button type="button"
                                                                tabindex="0" class="dropdown-item">Dashboard</button></a>
                                                        <a href=" {{ route('admin.transactions') }} "><button
                                                                type="button" tabindex="0" class="dropdown-item">All
                                                                transactions</button></a>

                                                        @if (Auth::user()->role != 444)
                                                            <a href=" {{ route('admin.rates') }} "><button type="button"
                                                                    tabindex="0" class="dropdown-item">Rates</button></a>
                                                        @endif

                                                        <div tabindex="-1" class="dropdown-divider"></div>
                                                        <a href="#"
                                                            onclick="event.preventDefault(); document.getElementById('logout-form2').submit();">
                                                            <button type="button" tabindex="0"
                                                                class="dropdown-item">Logout</button>
                                                        </a>
                                                        <div tabindex="-1" role="menu" aria-hidden="true"
                                                            class="dropdown-menu dropdown-menu-left">
                                                            <a href=" {{ route('admin.dashboard') }} "><button
                                                                    type="button" tabindex="0"
                                                                    class="dropdown-item">Dashboard</button></a>
                                                            <a href=" {{ route('admin.transactions') }} "><button
                                                                    type="button" tabindex="0"
                                                                    class="dropdown-item">All
                                                                    transactions</button></a>
                                                            <a href=" {{ route('admin.rates') }} "><button type="button"
                                                                    tabindex="0"
                                                                    class="dropdown-item">Rates</button></a>
                                                            <div tabindex="-1" class="dropdown-divider"></div>
                                                            <a href="#"
                                                                onclick="event.preventDefault(); document.getElementById('logout-form2').submit();">
                                                                <button type="button" tabindex="0"
                                                                    class="dropdown-item">Logout</button>
                                                            </a>
                                                            <form id="logout-form2" action="{{ route('logout') }}"
                                                                method="POST" style="display: none;">
                                                                @csrf
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="dropdown">
                                                    <a data-toggle="dropdown" class="p-0 btn">
                                                        <img width="42" class="rounded-circle"
                                                            src="{{ asset('storage/avatar/' . Auth::user()->dp) }} "
                                                            alt="">
                                                        <i class="fa fa-angle-down ml-2 opacity-8"></i>
                                                    </a>
                                                    <div tabindex="-1" role="menu" aria-hidden="true"
                                                        class="dropdown-menu dropdown-menu-left">
                                                        <a href=" {{ route('user.profile') }} "><button type="button"
                                                                tabindex="0" class="dropdown-item">My
                                                                Account</button></a>
                                                        <a href=" {{ route('user.transactions') }} "><button
                                                                type="button" tabindex="0" class="dropdown-item">My
                                                                transactions</button></a>

                                                        <a href=" {{ route('user.assets') }} "><button type="button"
                                                                tabindex="0" class="dropdown-item">Trade</button></a>
                                                        <div tabindex="-1" class="dropdown-divider"></div>
                                                        <a href="#"
                                                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                            <button type="button" tabindex="0"
                                                                class="dropdown-item">Logout</button>
                                                        </a>
                                                        <form id="logout-form" action="{{ route('logout') }}"
                                                            method="POST" style="display: none;">
                                                            @csrf
                                                        </form>
                                                    </div>
                                                </div>
                                            @endif

                                        @endauth

                                        <div class="widget-content-left  ml-3 header-user-info">
                                            <div class="widget-heading">
                                                @auth
                                                    {{ Auth::user()->first_name . ' ' . Auth::user()->last_name }}
                                                @endauth
                                            </div>
                                            <div class="widget-subheading">
                                                @auth
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

                                                        @case(555)
                                                            Customer Happiness
                                                        @break

                                                        @case(559)
                                                            Marketing
                                                        @break

                                                        @case(666)
                                                            Manager
                                                        @break

                                                        @case(444)
                                                            Chinese Operator
                                                        @break

                                                        @case(449)
                                                            Chinese Admin
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
                                                @endauth
                                            </div>
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
    <script src="/js/app.js?v=1"></script>
    <script src="{{ asset('assets/scripts/main.js') }} "></script>
    <script src="{{ asset('js/jquery-3.2.1.min.js') }} "></script>
    <script src="{{ asset('js/popper.min.js') }} "></script>
    <script src="{{ asset('js/bootstrap.min.js') }} "></script>
    <script src="{{ asset('js/custom.js') }}"></script>
    <script src="{{ asset('js/bootstrap-notify.js') }}"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    @auth
        @if (in_array(Auth::user()->role, [999, 889, 888, 777, 666, 444, 449, 557, 556, 559]))
            <script src="{{ asset('js/sa.js?v=7') }}"></script>
        @endif
    @endauth

    <script type="text/javascript">
        $(document).ready(function() {
            $('.transactions-table').DataTable({
                lengthMenu: [
                    [10, 100, 1000, -1],
                    [10, 100, 1000, 'All'],
                ],
                paging: true,
                order: [
                    [0, 'desc']
                ],
            });
        });
    </script>



    @if (session()->has('success'))
        <script>
            $(document).ready(function() {
                swal('Great', "{{ session()->get('success') }} ", 'success');
                // Notify("{{ session()->get('success') }} ", null, null, 'success');
            });
        </script>
    @endif

    @if (session()->has('error'))
        <script>
            $(document).ready(function() {
                // Notify("{{ session()->get('error') }} ", null, null, 'danger');
                swal('Oops!!', "{{ session()->get('error') }} ", 'error');
            });
        </script>
    @endif

    @yield('scripts')


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

        const feedback_status = () => {
            const feedback = __st_id("f_status")
            if (feedback.value == "failed") {
                showit("yfailed")
                hideit("ydeclined")
            } else if (feedback.value == "declined") {
                hideit("yfailed")
                showit("ydeclined")
            } else {
                hideit("ydeclined")
                hideit("yfailed")
            }
        }

        const category_status = () => {
            const feedback = __st_id("category")
            if (!(feedback.value == "" || feedback.value == "NoResponse")) {
                showit("feedback-textarea")
            } else {
                hideit("feedback-textarea")
            }
        }

        const decline_reason = (selectedOption) => {
            showit(selectedOption)
        }

        const pay_bridge_decline = (openOption, closeOption) => {
            showit(openOption);
            hideit(closeOption);
        }
    </script>

    <script>
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>

    <script>
        const __event = (activity) => document.getElementById(activity)
        const hidebuttons = () => {
            __event('ac1').classList.add("d-none")
            __event('ac2').classList.add("d-none")
            __event('ac3').classList.add("d-none")
        }

        const showbuttons = () => {
            __event('ac1').classList.remove("d-none")
            __event('ac2').classList.remove("d-none")
            __event('ac3').classList.remove("d-none")
        }
        const hideAll = () => {
            hidebuttons()
            __event('yearly').classList.add("d-none")
            __event('period_start').classList.add("d-none")
            __event('period_end').classList.add("d-none")
            __event('days').classList.add("d-none")
            __event('months').classList.add("d-none")
            __event('quaterly').classList.add("d-none")
        }
        const periodShowit = () => {
            showbuttons()
            __event('yearly').classList.add("d-none")
            __event('period_start').classList.remove("d-none")
            __event('period_end').classList.remove("d-none")
            __event('days').classList.add("d-none")
            __event('months').classList.add("d-none")
            __event('quaterly').classList.add("d-none")
        }
        const daysShowit = () => {
            showbuttons()
            __event('yearly').classList.add("d-none")
            __event('days').classList.remove("d-none")
            __event('period_start').classList.add("d-none")
            __event('period_end').classList.add("d-none")
            __event('months').classList.add("d-none")
            __event('quaterly').classList.add("d-none")

        }
        const monthShowit = () => {
            showbuttons()
            __event('months').classList.remove("d-none")
            __event('yearly').classList.remove("d-none")
            __event('period_start').classList.add("d-none")
            __event('period_end').classList.add("d-none")
            __event('days').classList.add("d-none")
        }

        const yearlyShowit = () => {
            showbuttons()
            __event('yearly').classList.remove("d-none")
            __event('period_start').classList.add("d-none")
            __event('period_end').classList.add("d-none")
            __event('days').classList.add("d-none")
            __event('months').classList.add("d-none")
            __event('quaterly').classList.add("d-none")
        }
        const sortingchange = () => {
            const feedback = __event("e_sort")
            switch (feedback.value) {
                case "noData":
                    hideAll()
                    break;
                case "period":
                    periodShowit()
                    break;
                case "days":
                    daysShowit()
                    break;
                case "monthly":
                    monthShowit()
                    break;
                case "quarterly":
                    monthShowit()
                    break;
                case "yearly":
                    yearlyShowit()
                    break;

                default:
                    periodShowit()
                    break;
            }
        }
    </script>

    <script>
        const copyData = (id, type) => {
            var copyText = document.getElementById(id);
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            navigator.clipboard
                .writeText(copyText.value)
                .then(() => {
                    swal(type + " copied: " + copyText.value);
                })
                .catch(() => {
                    swal("something went wrong");
                });
        }

        const buttonSelect = (id, status, uid) => {
            var approve = document.getElementById(id);
            approve.value = status;

            if (status == 'approve' || status == 'unresolved') {
                showit('p2p_pin-' + uid);
                hideit('p2p_buttongroup-' + uid);
            }

            if (status == 'decline') {
                showit('p2p_pin-' + uid);
                showit('p2p_dropdown-' + uid);
                hideit('p2p_buttongroup-' + uid);
            }
        }
        const hideNairaTradeButton = (id) => {
            var button = document.getElementById(id);
            hideit(id);
        }

        const disableNairaTradeButton = (id) => {
            var button = document.getElementById(id);
            var form = document.getElementById('p2pViewForm');
            form.submit();
            button.disabled = true;
        }
    </script>

    {{-- @if (Auth::user()->role == 444 or Auth::user()->role == 449 or Auth::user()->role == 999) --}}
    <script>
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
    @auth

        @if (Auth::user()->role == 444 or Auth::user()->role == 449 or Auth::user()->role == 999)
            <script>
                const _e = (e) => document.getElementById(e)

                const countInProgressTransaction = () => {
                    const ajax = new XMLHttpRequest()
                    const trans_url = "{{ url('/admin/get-transaction-count') }}"
                    ajax.open('GET', trans_url)
                    ajax.onload = () => {
                        const response = ajax.response
                        const resp = JSON.parse(response)
                        _e('waiting_count').innerHTML = resp.waiting_transaction
                        _e('in_progress_count').innerHTML = resp.in_progress_transactions
                        // console.log(resp)
                    }

                    ajax.onprogress = () => {
                        _e('waiting_count').innerHTML = '...'
                        _e('in_progress_count').innerHTML = '...'
                    }
                    ajax.send()
                }

                setInterval(() => {
                    countInProgressTransaction()
                }, 2000);
            </script>
        @endif

        @if (Auth::user()->role == 889 or Auth::user()->role == 777 or Auth::user()->role == 775)
            <script>
                var pusher = new Pusher('9a1545beffb83093b6cb', {
                    cluster: 'eu'
                });

                var channel = pusher.subscribe('notify');
                channel.bind('transaction', function(data) {
                    //   alert(JSON.stringify(data));

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


</body>

</html>
