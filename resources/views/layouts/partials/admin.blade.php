<div class="scrollbar-sidebar">
    <div class="app-sidebar__inner">
        <ul class="vertical-nav-menu">
            <li class="app-sidebar__heading">User Dashboard</li>
            <li class="my-3 ">
                <a href="{{route('admin.dashboard')}}"
                    class=" {{ Route::currentRouteName() == 'admin.dashboard' ? 'mm-active' : '' }} ">
                    <i class="metismenu-icon pe-7s-home"></i>

                    Dashboard
                </a>
            </li>
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
                        <a href="{{route('admin.transactions-status', 'success')}}" >
                            <i class="metismenu-icon">
                            </i>Successful transactions
                        </a>
                    </li>
                    <li>
                        <a href="{{route('admin.transactions-status', 'approved')}}" >
                            <i class="metismenu-icon">
                            </i>Approved transactions
                        </a>
                    </li>
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

            @if (in_array(Auth::user()->role, [777] ))
                <li class="menu {{ Route::currentRouteName() == 'admin.naira-p2p' ? 'active' : '' }}  ">
                    <a href="{{route('admin.naira-p2p')}}">
                        <div class="">
                            <i class="metismenu-icon pe-7s-wallet"></i>
                            <span>My P2P Transactions</span>
                        </div>
                    </a>
                </li>
                
            @endif

            @if (in_array(Auth::user()->role, [999, 889, 777] ))
            <li class="my-3">
                <a href="{{route('admin.crypto-summary', 1)}}"
                    class=" {{ Route::currentRouteName() == 'admin.bitcoin-summary' ? 'mm-active' : '' }} ">
                    <i class="metismenu-icon pe-7s-wallet"></i>
                    Bitcoin Summary
                </a>
            </li>

            <li class="menu {{ Route::currentRouteName() == 'admin.junior-summary' ? 'active' : '' }}  ">
                <a href="{{route('admin.junior-summary')}}">
                    <div class="">
                        <i class="metismenu-icon pe-7s-graph2"></i>
                        <span>Summary</span>
                    </div>
                </a>
            </li>

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

            <li class="my-3">
                <a href="{{route('admin.rates')}}"
                    class=" {{ Route::currentRouteName() == 'admin.rates' ? 'mm-active' : '' }} ">
                    <i class="metismenu-icon pe-7s-graph1"></i>
                    Rates
                </a>
            </li>

            @if (!in_array(Auth::user()->role, [889, 777, 666]))
            <li class="my-3">
                <a href="{{route('admin.assigned-transactions')}}"
                    class=" {{ Route::currentRouteName() == 'admin.assigned-transactions' ? 'mm-active' : '' }} ">
                    <i class="metismenu-icon pe-7s-graph1"></i>
                    Assigned Transactions
                </a>
            </li>
            @endif

            @if (in_array(Auth::user()->role, [999, 666, 777, 889] ))
            <li class="my-3">
                <a href="{{route('admin.chat_agents')}}"
                    class=" {{ Route::currentRouteName() == 'admin.chat_agents' ? 'mm-active' : '' }} ">
                    <i class="metismenu-icon pe-7s-users"></i>
                    Chat Agents
                </a>
            </li>
            @endif

            @if (Auth::user()->role == 666)
            <li class="my-3">
                <a href="{{route('admin.user-verifications')}}"
                    class=" {{ Route::currentRouteName() == 'admin.user-verifications' ? 'mm-active' : '' }} ">
                    <i class="metismenu-icon pe-7s-user"></i>
                    Users' Verification
                </a>
            </li>
            @endif

            @if (in_array(Auth::user()->role, [999, 889, 777] ))
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
                <a href="{{route('admin.accountants')}}"
                    class=" {{ Route::currentRouteName() == 'admin.accountants' ? 'mm-active' : '' }} ">
                    <i class="metismenu-icon pe-7s-users"></i>
                    Accountants
                </a>
            </li>
            @endif

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
