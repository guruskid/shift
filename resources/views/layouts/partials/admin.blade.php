<div class="scrollbar-sidebar">
    <div class="app-sidebar__inner">
        <ul class="vertical-nav-menu">
            @if (in_array(Auth::user()->role, [555] ))
                <li class="app-sidebar__heading">Customer Hapiness Dashboard</li>
            @else
                <li class="app-sidebar__heading">User Dashboard</li>
            @endif

            @if (in_array(Auth::user()->role, [555] ))
                <li class="my-3 ">
                    <a href="{{route('customerHappiness.homepage')}}"
                        class=" {{ Route::currentRouteName() == 'admin.dashboard' ? 'mm-active' : '' }} ">
                        <i class="metismenu-icon pe-7s-home"></i>

                        Go back
                    </a>
                </li>
            @else
                <li class="my-3 ">
                    <a href="{{route('admin.dashboard')}}"
                        class=" {{ Route::currentRouteName() == 'admin.dashboard' ? 'mm-active' : '' }} ">
                        <i class="metismenu-icon pe-7s-home"></i>

                        Dashboard
                    </a>
                </li>
            @endif

            @if (in_array(Auth::user()->role, [555] ))
            <li>
                <a href="#">
                    <i class="metismenu-icon pe-7s-timer"></i>
                    Transaction List
                    <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                </a>
                <ul>
                    <li>
                        <a href="{{route('customerHappiness.transactions')}}"
                            class=" {{ Route::currentRouteName() == 'customerHappiness.transactions' ? 'mm-active' : '' }} ">
                            <i class="metismenu-icon"></i>
                            All Transactions
                        </a>
                    </li>
                    <li>
                        <a href="{{route('customerHappiness.buy_transac')}}"
                            class=" {{ Route::currentRouteName() == 'customerHappiness.buy_transac' ? 'mm-active' : '' }} ">
                            <i class="metismenu-icon">
                            </i>Buy transactions
                        </a>
                    </li>
                    <li>
                        <a href="{{route('customerHappiness.sell_transac')}}"
                            class=" {{ Route::currentRouteName() == 'customerHappiness.sell_transac' ? 'mm-active' : '' }} ">
                            <i class="metismenu-icon">
                            </i>Sell transactions
                        </a>
                    </li>
                    <li>
                        <a href="{{route('customerHappiness.utility-transactions')}}"
                            class=" {{ Route::currentRouteName() == 'customerHappiness.utility-transactions' ? 'mm-active' : '' }} ">
                            <i class="metismenu-icon">
                            </i>Utility transactions
                        </a>
                    </li>
                    <li>
                        <a href="{{route('customerHappiness.asset-transactions', 0)}}"
                            class=" {{ Route::currentRouteName() == 'customerHappiness.asset-transactions' ? '' : '' }} ">
                            <i class="metismenu-icon">
                            </i>GiftCard transactions
                        </a>
                    </li>
                    <li>
                        <a href="{{route('customerHappiness.asset-transactions', 1)}}"
                            class=" {{ Route::currentRouteName() == 'customerHappiness.asset-transactions' ? '' : '' }} ">
                            <i class="metismenu-icon">
                            </i>Crypto transactions
                        </a>
                    </li>

                    <li>
                        <a href="{{route('customerHappiness.wallet-transactions')}}"
                            class=" {{ Route::currentRouteName() == 'customerHappiness.wallet-transactions' ? 'mm-active' : '' }} ">
                            <i class="metismenu-icon">
                            </i>Wallet transactions
                        </a>
                    </li>
                    <li>
                        <a href="{{route('customerHappiness.transactions-status', 'success')}}" >
                            <i class="metismenu-icon">
                            </i>Successful transactions
                        </a>
                    </li>
                    <li>
                        <a href="{{route('customerHappiness.transactions-status', 'approved')}}" >
                            <i class="metismenu-icon">
                            </i>Approved transactions
                        </a>
                    </li>
                    <li>
                        <a href="{{route('customerHappiness.transactions-status', 'in progress')}}" >
                            <i class="metismenu-icon">
                            </i>In Progress Transactions
                        </a>
                    </li>
                    <li>
                        <a href="{{route('customerHappiness.transactions-status', 'waiting')}}" >
                            <i class="metismenu-icon">
                            </i>Waiting transactions
                        </a>
                    </li>
                    <li>
                        <a href="{{route('customerHappiness.transactions-status', 'declined')}}" >
                            <i class="metismenu-icon">
                            </i>Declined transactions
                        </a>
                    </li>
                    <li>
                        <a href="{{route('customerHappiness.transactions-status', 'failed')}}" >
                            <i class="metismenu-icon">
                            </i>Failed transactions
                        </a>
                    </li>
                </ul>
            </li>
            @endif
            @if (!in_array(Auth::user()->role, [555,559,557,556] ))
            <li>
                <a href="#">
                    <i class="metismenu-icon pe-7s-timer"></i>
                    Transactions
                    <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                </a>
                <ul>
                    <li>
                        <a href="{{route('admin.transactions')}}"
                            class=" {{ Route::currentRouteName() == 'admin.transactions' ? 'mm-active' : '' }} ">
                            <i class="metismenu-icon"></i>
                            All Transactions
                        </a>
                    </li>
                    <li>
                        <a href="{{route('admin.buy_transac')}}"
                            class=" {{ Route::currentRouteName() == 'admin.buy_transac' ? 'mm-active' : '' }} ">
                            <i class="metismenu-icon">
                            </i>Buy transactions
                        </a>
                    </li>
                    <li>
                        <a href="{{route('admin.sell_transac')}}"
                            class=" {{ Route::currentRouteName() == 'admin.sell_transac' ? 'mm-active' : '' }} ">
                            <i class="metismenu-icon">
                            </i>Sell transactions
                        </a>
                    </li>
                    <li>
                        <a href="{{route('admin.currency_transactions', [143, 'USDT'])}}"
                            class=" {{ Route::currentRouteName() == 'admin.currency_transactions' ? 'mm-active' : '' }} ">
                            <i class="metismenu-icon">
                            </i>USDT transactions
                        </a>
                    </li>

                    <li>
                        <a href="{{route('admin.transactions-status', 'success')}}" >
                            <i class="metismenu-icon">
                            </i>Successful transactions
                        </a>
                    </li>
                    @if (!in_array(Auth::user()->role, [889, 777, 775, 666, 555, 449, 444, 556]))
                    <li>
                        <a href="{{route('admin.transactions-status', 'approved')}}" >
                            <i class="metismenu-icon">
                            </i>Approved transactions
                        </a>
                    </li>
                    @endif
                    <li>
                        <a href="{{route('admin.transactions-status', 'in progress')}}" >
                            <i class="metismenu-icon">
                            </i>In Progress Transactions
                        </a>
                    </li>
                    <li>
                        <a href="{{route('admin.transactions-status', 'waiting')}}" >
                            <i class="metismenu-icon">
                            </i>Waiting transactions
                        </a>
                    </li>
                    <li>
                        <a href="{{route('admin.transactions-status', 'declined')}}" >
                            <i class="metismenu-icon">
                            </i>Declined transactions
                        </a>
                    </li>
                    <li>
                        <a href="{{route('admin.transactions-status', 'failed')}}" >
                            <i class="metismenu-icon">
                            </i>Failed transactions
                        </a>
                    </li>
                </ul>
            </li>
            @endif

            @if (in_array(Auth::user()->role, [777,775] ))
                <li class="menu {{ Route::currentRouteName() == 'admin.naira-p2p' ? 'active' : '' }}  ">
                    <a href="{{route('admin.naira-p2p')}}">
                        <div class="">
                            <i class="metismenu-icon pe-7s-wallet"></i>
                            <span>My P2P Transactions</span>
                        </div>
                    </a>
                </li>

            @endif

            @if (in_array(Auth::user()->role, [999,666] ))
            <li class="my-3">
                <a href="{{route('admin.user-verifications-tracking')}}"
                    class=" {{ Route::currentRouteName() == 'admin.user-verifications-tracking' ? 'mm-active' : '' }} ">
                    <i class="metismenu-icon pe-7s-users"></i>
                    Verification Tracking
                </a>
            </li>
            @endif

            @if (in_array(Auth::user()->role, [999,559] ))
            <li class="my-3">
                <a href="{{route('admin.faq')}}"
                    class=" {{ Route::currentRouteName() == 'admin.faq' ? 'mm-active' : '' }} ">
                    <i class="metismenu-icon pe-7s-graph1"></i>
                    Faq
                </a>
            </li>

            <li class="my-3">
                <a href="{{route('faq.category.index')}}"
                    class=" {{ Route::currentRouteName() == 'faq.category.index' ? 'mm-active' : '' }} ">
                    <i class="metismenu-icon pe-7s-graph1"></i>
                    Faq Categories
                </a>
            </li>
            @endif

            @if (in_array(Auth::user()->role, [999, 889, 777, 775] ))
                <li class="my-3">
                    <a href="{{route('admin.crypto-summary', 1)}}"
                        class=" {{ Route::currentRouteName() == 'admin.bitcoin-summary' ? 'mm-active' : '' }} ">
                        <i class="metismenu-icon pe-7s-wallet"></i>
                        Bitcoin Summary
                    </a>
                </li>
            @if (!in_array(Auth::user()->role, [775] ))
            <li class="menu {{ Route::currentRouteName() == 'admin.junior-summary' ? 'active' : '' }}  ">
                <a href="{{route('admin.junior-summary')}}">
                    <div class="">
                        <i class="metismenu-icon pe-7s-graph2"></i>
                        <span>Summary</span>
                    </div>
                </a>
            </li>
            <li class="my-3">
                <a href="{{route('admin.account_officers')}}"
                    class=" {{ Route::currentRouteName() == 'admin.account_officers' ? 'mm-active' : '' }} ">
                    <i class="metismenu-icon pe-7s-users"></i>
                    Account Officers
                </a>
            </li>
            @endif


            <li class="my-3">
                <a href="{{route('admin.wallet-transactions')}}"
                    class=" {{ Route::currentRouteName() == 'admin.wallet-transactions' ? 'mm-active' : '' }} ">
                    <i class="metismenu-icon pe-7s-wallet"></i>
                    Naira Wallet Transactions
                </a>
            </li>

                <li>
                    <a href="{{ route('admin.bitcoin') }}">
                        <i class="metismenu-icon pe-7s-cash"></i>
                        Bitcoin Wallet
                        <i class="metismenu-state-icon "></i>
                    </a>
                </li>
            @endif
            @if (in_array(Auth::user()->role, [999, 559] ))

            <li class="my-3">
                <a href="{{route('sales.oldUsers.salesAnalytics')}}"
                    class=" {{ Route::currentRouteName() == 'sales.oldUsers.salesAnalytics' ? 'mm-active' : '' }} ">
                    <i class="metismenu-icon pe-7s-graph1"></i>
                    Sales Old Users
                </a>
            </li>

            <li class="my-3">
                <a href="{{route('sales.loadPriority')}}"
                    class=" {{ Route::currentRouteName() == 'sales.loadPriority' ? 'mm-active' : '' }} ">
                    <i class="metismenu-icon pe-7s-user"></i>
                    Sales Priority
                </a>
            </li>
            @endif
            {{-- Here --}}
            @if (in_array(Auth::user()->role, [888,999,666,777]))
                <li class="my-3">
                    <a href="{{route('admin.rates')}}"
                        class=" {{ Route::currentRouteName() == 'admin.rates' ? 'mm-active' : '' }} ">
                        <i class="metismenu-icon pe-7s-graph1"></i>
                        Rates
                    </a>
                </li>
            @endif

            @if (!in_array(Auth::user()->role, [889, 777,775 ,666, 555, 449, 444,559,557,556]))
                <li class="my-3">
                    <a href="{{route('admin.assigned-transactions')}}"
                        class=" {{ Route::currentRouteName() == 'admin.assigned-transactions' ? 'mm-active' : '' }} ">
                        <i class="metismenu-icon pe-7s-graph1"></i>
                        Assigned Transactions
                    </a>
                </li>
            @endif

            @if (in_array(Auth::user()->role, [889, 999, 777] ) )
            <li class="my-3">
                <a href="{{route('p2p.accounts')}}"
                    class=" {{ Route::currentRouteName() == 'p2p.accounts' ? 'mm-active' : '' }} ">
                    <i class="metismenu-icon pe-7s-wallet"></i>
                    Pay Birdge Accounts
                </a>
            </li>
            @endif
            @if (in_array(Auth::user()->role, [999, 666, 777,775, 889] ))
                <li class="my-3">
                    <a href="{{route('admin.chat_agents')}}"
                        class=" {{ Route::currentRouteName() == 'admin.chat_agents' ? 'mm-active' : '' }} ">
                        <i class="metismenu-icon pe-7s-users"></i>
                        Chat Agents
                    </a>
                </li>
            @endif

            @if (in_array(Auth::user()->role, [559,888] ))
                {{-- <li class="my-3">
                    <a href="{{route('admin.sales.users_verifications')}}"
                        class=" {{ Route::currentRouteName() == 'admin.sales.users_verifications' ? 'mm-active' : '' }} ">
                        <i class="metismenu-icon pe-7s-users"></i>
                         Users Verification
                    </a>
                </li> --}}
            @endif

            @if (in_array(Auth::user()->role, [559] ))
                <li class="my-3">
                    <a href="{{route('admin.sales.users_birthdays')}}"
                        class=" {{ Route::currentRouteName() == 'admin.sales.users_birthdays' ? 'mm-active' : '' }} ">
                        <i class="metismenu-icon pe-7s-users"></i>
                         Birthday
                    </a>
                </li>
            @endif

            @if (in_array(Auth::user()->role,[666,777]))
            <li class="my-3">
                <a href="{{route('admin.user-verifications')}}"
                    class=" {{ Route::currentRouteName() == 'admin.user-verifications' ? 'mm-active' : '' }} ">
                    <i class="metismenu-icon pe-7s-user"></i>
                    Users' Verification
                </a>
            </li>
            @endif    

            @if (in_array(Auth::user()->role,[666]))
            <li class="my-3">
                <a href="{{route('admin.users')}}"
                    class=" {{ Route::currentRouteName() == 'admin.users' ? 'mm-active' : '' }} ">
                    <i class="metismenu-icon pe-7s-users"></i>
                    Users
                </a>
            </li>

            <li class="my-3">
                <a href="{{route('admin.user-verifications')}}"
                    class=" {{ Route::currentRouteName() == 'admin.user-verifications' ? 'mm-active' : '' }} ">
                    <i class="metismenu-icon pe-7s-user"></i>
                    Users' Verification
                </a>
            </li>
            @endif
            @if (in_array(Auth::user()->role, [999, 559] ))
            <li class="my-3">
                <a href="{{route('sales.loadSales')}}"
                    class=" {{ Route::currentRouteName() == 'sales.loadSales' ? 'mm-active' : '' }} ">
                    <i class="metismenu-icon pe-7s-settings"></i>
                    Sales Setting
                </a>
            </li>
        @endif

            @if (in_array(Auth::user()->role, [999, 889, 777, 775] ))
                <li class="my-3">
                    <a href="{{route('admin.users')}}"
                        class=" {{ Route::currentRouteName() == 'admin.users' ? 'mm-active' : '' }} ">
                        <i class="metismenu-icon pe-7s-users"></i>
                        Users
                    </a>
                </li>
            @endif

            {{-- for Super Admin Only --}}
            @if (Auth::user()->role == 999 )
                <li class="my-3">
                    <a href="{{route('admin.notification')}}"
                        class=" {{ Route::currentRouteName() == 'admin.notification' ? 'mm-active' : '' }} ">
                        <i class="metismenu-icon pe-7s-volume"></i>
                        Notifications
                    </a>
                </li>

                <li class="my-3">
                    <a href="{{route('admin.user-verifications')}}"
                        class=" {{ Route::currentRouteName() == 'admin.user-verifications' ? 'mm-active' : '' }} ">
                        <i class="metismenu-icon pe-7s-user"></i>
                        Users' Verification
                    </a>
                </li>

                <li class="my-3">
                    <a href="{{route('admin.top-transfers')}}"
                        class=" {{ Route::currentRouteName() == 'admin.top-transfers' ? 'mm-active' : '' }} ">
                        <i class="metismenu-icon pe-7s-user"></i>
                        Top Trader's
                    </a>
                </li>

                <li class="my-3">
                    <a href="{{route('admin.accountants')}}"
                        class=" {{ Route::currentRouteName() == 'admin.accountants' ? 'mm-active' : '' }} ">
                        <i class="metismenu-icon pe-7s-users"></i>
                        Accountants
                    </a>
                </li>

                <li class="my-3">
                    <a href="{{route('admin.customerHappinessAgent')}}"
                        class=" {{ Route::currentRouteName() == 'admin.customerHappinessAgent' ? 'mm-active' : '' }} ">
                        <i class="metismenu-icon pe-7s-users"></i>
                        Customer Happiness
                    </a>
                </li>
            @endif
            @if(Auth::user()->role == 889 )
            <li class="my-3">
                <a href="{{route('admin.chinese_dashboard_page')}}"
                    class=" {{ Route::currentRouteName() == 'admin.chinese_dashboard_page' ? 'mm-active' : '' }} ">
                    <i class="fas fa-broom metismenu-icon"></i>
                    Chinese dashboard
                </a>
            </li>
            @endif

            @if(Auth::user()->role == 999 OR  Auth::user()->role == 444 OR  Auth::user()->role == 889 OR Auth::user()->role == 449)
            @if(Auth::user()->role == 999)
            <li class="my-3">
                <a href="{{route('admin.chinese_dashboard_page')}}"
                    class=" {{ Route::currentRouteName() == 'admin.chinese_dashboard_page' ? 'mm-active' : '' }} ">
                    <i class="fas fa-broom metismenu-icon"></i>
                    Chinese dashboard
                </a>
            </li>
            @endif

            <li class="my-3">
                <a href="{{route('admin.payout_transactions')}}"
                    class=" {{ Route::currentRouteName() == 'admin.payout_transactions' ? 'mm-active' : '' }} ">
                    <i class="fas fa-braille metismenu-icon"></i>
                    Payout page
                </a>
            </li>
            @if(Auth::user()->role == 449 OR Auth::user()->role == 999)
            <li class="my-3">
                <a href="{{route('admin.chinese_admins')}}"
                    class=" {{ Route::currentRouteName() == 'admin.chinese_admins' ? 'mm-active' : '' }} ">
                    <i class="metismenu-icon pe-7s-users"></i>
                    Chinese Admin's
                </a>
            </li>
            @endif
            @endif
            @if (in_array(Auth::user()->role, [999, 559] ))
            <li class="my-3">
                <a href="{{route('admin.call-categories')}}"
                    class=" {{ Route::currentRouteName() == 'admin.call-categories' ? 'mm-active' : '' }} ">
                    <i class="metismenu-icon pe-7s-users"></i>
                    Call Category
                </a>
            </li>
            @endif
            @if (in_array(Auth::user()->role, [999, 557] ))
            <li>
                <a href="#">
                    <i class="metismenu-icon pe-7s-users"></i>
                    Call Logs
                    <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                </a>
                <ul>
                    <li>
                        <a href="{{route('business-developer.call-log')}}"
                            class=" {{ Route::currentRouteName() == 'business-developer.call-log' ? 'mm-active' : '' }} ">
                            <i class="metismenu-icon"></i>
                            Old Users Call Log
                        </a>
                    </li>
                    <li>
                        <a href="{{route('business-developer.new-users.call-log')}}"
                            class=" {{ Route::currentRouteName() == 'business-developer.new-users.call-log' ? 'mm-active' : '' }} ">
                            <i class="metismenu-icon">
                            </i>New Users Call Log
                        </a>
                    </li>
                </ul>
            </li>

                <li class="my-3">
                    <a href="{{route('business-developer.user-profile')}}"
                        class=" {{ Route::currentRouteName() == 'business-developer.user-profile' ? 'mm-active' : '' }} ">
                        <i class="metismenu-icon pe-7s-users"></i>
                        User Profile
                    </a>
                </li>

                
            @endif
            @if (in_array(Auth::user()->role, [556] ))
                <li class="my-3">
                    <a href="{{route('sales.call-log')}}"
                        class=" {{ Route::currentRouteName() == 'sales.call-log' ? 'mm-active' : '' }} ">
                        <i class="metismenu-icon pe-7s-users"></i>
                        Call Log
                    </a>
                </li>
                <li class="my-3">
                    <a href="{{route('sales.user_profile')}}"
                        class=" {{ Route::currentRouteName() == 'sales.user_profile' ? 'mm-active' : '' }} ">
                        <i class="metismenu-icon pe-7s-users"></i>
                        User Profile
                    </a>
                </li>

            @endif
{{--
            @if(Auth::user()->role == 449 AND Auth::user()->role == 999)

            @endif --}}

            <li class="my-3">
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="metismenu-icon pe-7s-power"></i>
                    Logout
                </a>
            </li>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </ul>
    </div>
</div>
