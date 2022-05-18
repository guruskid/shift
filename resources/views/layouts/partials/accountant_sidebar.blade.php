<ul class="list-unstyled menu-categories" id="accordionExample">
    <li class="menu {{ Route::currentRouteName() == 'admin.dashboard' ? 'active' : '' }}">
        <a href="{{route('admin.dashboard')}}" aria-expanded="true" class="dropdown-toggle">
            <div class="">
                <ion-icon name="home-outline"></ion-icon>
                <span>Dashboard</span>
            </div>
        </a>
    </li>

    <li class="menu {{ Route::currentRouteName() == 'admin.transactions' ? 'active' : '' }}  ">
        <a href="{{route('admin.transactions')}}" aria-expanded="false" class="dropdown-toggle">
            <div class="">
                <ion-icon name="speedometer-outline"></ion-icon>
                <span>Transactions</span>
            </div>
        </a>
    </li>

    <li class="menu {{ Route::currentRouteName() == 'admin.wallet-transactions' ? 'active' : '' }}  ">
        <a href="{{route('admin.wallet-transactions')}}" aria-expanded="false" class="dropdown-toggle">
            <div class="">
                <ion-icon name="cash-outline"></ion-icon>
                <span>Naira Wallet Transactions</span>
            </div>
        </a>
    </li>

    <li class="menu {{ Route::currentRouteName() == 'admin.utility-transactions' ? 'active' : '' }}  ">
        <a href="{{route('admin.utility-transactions')}}"  aria-expanded="false" class="dropdown-toggle">
            <div class="">
                <ion-icon name="bulb"></ion-icon>
                <span>Utility Transactions</span>
            </div>
        </a>
    </li>

    @if (Auth::user()->role == 889)
    <li class="menu {{ Route::currentRouteName() == 'admin.naira-p2p' ? 'active' : '' }}  ">
        <a href="{{route('admin.trade-naira.index')}}"  aria-expanded="false" class="dropdown-toggle">
            <div class="">
                <ion-icon name="cash-outline"></ion-icon>
                <span>P2P Agents</span>
            </div>
        </a>
    </li>
    @endif

    @if (Auth::user()->role == 889)
        <li class="menu {{ Route::currentRouteName() == 'p2p.accounts' ? 'active' : '' }}  ">
            <a href="{{route('p2p.accounts')}}"  aria-expanded="false" class="dropdown-toggle">
                <div class="">
                    <ion-icon name="cash-outline"></ion-icon>
                    <span>Pay Birdge Accounts <span class="badge badge-warning">New</span></span>
                </div>
            </a>
        </li>
    @endif

    <li class="menu {{ Route::currentRouteName() == 'admin.naira-p2p' ? 'active' : '' }}  ">
        <a href="{{route('admin.naira-p2p')}}"  aria-expanded="false" class="dropdown-toggle">
            <div class="">
                <ion-icon name="cash-outline"></ion-icon>
                <span>My P2P Transactions</span>
            </div>
        </a>
    </li>
    @if ( in_array(Auth::user()->role, [889, 999, 777] ) )
    <li class="menu {{ Route::currentRouteName() == 'admin.junior-summary' ? 'active' : '' }}  ">
        <a href="{{route('admin.junior-summary')}}"  aria-expanded="false" class="dropdown-toggle">
            <div class="">
                <ion-icon name="analytics-outline"></ion-icon>
                <span>Summary</span>
            </div>
        </a>
    </li>
    @endif

    <li class="menu {{ Route::currentRouteName() == 'admin.bitcoin' ? 'active' : '' }}  ">
        <a href="{{route('admin.bitcoin')}}"  aria-expanded="false" class="dropdown-toggle">
            <div class="">
                <ion-icon name="logo-bitcoin"></ion-icon>
                <span>Bitcoin Wallet</span>
            </div>
        </a>
    </li>

    <li class="menu {{ Route::currentRouteName() == 'admin.bitcoin-wallets-transactions' ? 'active' : '' }}  ">
        <a href="{{route('admin.bitcoin-wallets-transactions')}}"  aria-expanded="false" class="dropdown-toggle">
            <div class="">
                <ion-icon name="cash-outline"></ion-icon>
                <span>Bitcoin Wallet Transactions</span>
            </div>
        </a>
    </li>

    <li class="menu {{ Route::currentRouteName() == 'admin.rates' ? 'active' : '' }}">
        <a href="{{route('admin.rates')}}" aria-expanded="false" class="dropdown-toggle">
            <div class="">
                <ion-icon name="analytics-outline"></ion-icon>
                <span>Rates</span>
            </div>
        </a>
    </li>

    <li class="menu {{ Route::currentRouteName() == 'admin.chat_agents' ? 'active' : '' }}">
        <a href="{{route('admin.chat_agents')}}" aria-expanded="false" class="dropdown-toggle">
            <div class="">
                <ion-icon name="analytics-outline"></ion-icon>
                <span>Chat Agents</span>
            </div>
        </a>
    </li>

    @if ( in_array(Auth::user()->role, [889, 999] ) )
    <li class="menu {{ Route::currentRouteName() == 'admin.accountants' ? 'active' : '' }}">
        <a href="{{route('admin.accountants')}}" aria-expanded="false" class="dropdown-toggle">
            <div class="">
                <ion-icon name="people-circle-outline"></ion-icon>
                <span>Junior Accountants</span>
            </div>
        </a>
    </li>

    <li class="menu {{ Route::currentRouteName() == 'admin.bitcoin' ? 'active' : '' }}  ">
        <a href="{{route('admin.crypto-summary', 1)}}"  aria-expanded="false" class="dropdown-toggle">
            <div class="">
                <ion-icon name="logo-bitcoin"></ion-icon>
                <span>Bitcoin Summary </span>
            </div>
        </a>
    </li>

    <li class="menu {{ Route::currentRouteName() == 'admin.chinese_dashboard_page' ? 'active' : '' }}">
        <a href="{{route('admin.chinese_dashboard_page')}}" aria-expanded="false" class="dropdown-toggle" >
            <div class="">
                <ion-icon name="people-circle-outline"></ion-icon>
                <span>Chinese dashboard</span>
            </div>
        </a>
    </li>

    <li class="menu {{ Route::currentRouteName() == 'admin.payout_transactions' ? 'active' : '' }}">
        <a href="{{route('admin.payout_transactions')}}" aria-expanded="false" class="dropdown-toggle" >
            <div class="">
                <ion-icon name="people-circle-outline"></ion-icon>
                <span>Payout Page</span>
            </div>
        </a>
    </li>

    <li class="menu {{ Route::currentRouteName() == 'admin.users' ? 'active' : '' }}">
        <a href="{{route('admin.users')}}" aria-expanded="false" class="dropdown-toggle">
            <div class="">
                <ion-icon name="person-outline"></ion-icon>
                <span>Users</span>
            </div>
        </a>
    </li>
    @endif

    <li class="menu {{ Route::currentRouteName() == 'logout' ? 'active' : '' }}">
        <a href="{{route('logout')}}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
            aria-expanded="false" class="dropdown-toggle">
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
