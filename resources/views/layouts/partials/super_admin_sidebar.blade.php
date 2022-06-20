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

    <li class="menu {{ Route::currentRouteName() == 'admin.utility-transactions' ? 'active' : '' }}  ">
        <a href="{{route('admin.utility-transactions')}}"  aria-expanded="false" class="dropdown-toggle">
            <div class="">
                <ion-icon name="bulb"></ion-icon>
                <span>Utility Transactions</span>
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

    <li class="menu {{ Route::currentRouteName() == 'admin.trade-naira.index' ? 'active' : '' }}  ">
        <a href="{{route('admin.trade-naira.index')}}"  aria-expanded="false" class="dropdown-toggle">
            <div class="">
                <ion-icon name="cash-outline"></ion-icon>
                <span>Trade Naira <span class="badge badge-warning">New</span></span>
            </div>
        </a>
    </li>

    <li class="menu {{ Route::currentRouteName() == 'p2p.accounts' ? 'active' : '' }}  ">
        <a href="{{route('p2p.accounts')}}"  aria-expanded="false" class="dropdown-toggle">
            <div class="">
                <ion-icon name="cash-outline"></ion-icon>
                <span>Pay Bridge Accounts <span class="badge badge-warning">New</span></span>
            </div>
        </a>
    </li>

    <li class="menu {{ Route::currentRouteName() == 'admin.bitcoin' ? 'active' : '' }}  ">
        <a href="{{route('admin.bitcoin')}}"  aria-expanded="false" class="dropdown-toggle">
            <div class="">
                <ion-icon name="logo-bitcoin"></ion-icon>
                <span>Bitcoin Wallet</span>
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

    @if ( in_array(Auth::user()->role, [889, 999] ) )
    <li class="menu {{ Route::currentRouteName() == 'sales.newUsers.salesAnalytics' ? 'active' : '' }}  ">
        <a href="{{route('sales.newUsers.salesAnalytics')}}"  aria-expanded="false" class="dropdown-toggle">
            <div class="">
                <ion-icon name="analytics-outline"></ion-icon>
                <span>Sales New Users</span>
            </div>
        </a>
    </li>
    @endif

    @if ( in_array(Auth::user()->role, [889, 999] ) )
    <li class="menu {{ Route::currentRouteName() == 'sales.oldUsers.salesAnalytics' ? 'active' : '' }}  ">
        <a href="{{route('sales.oldUsers.salesAnalytics')}}"  aria-expanded="false" class="dropdown-toggle">
            <div class="">
                <ion-icon name="analytics-outline"></ion-icon>
                <span>Sales Old Users</span>
            </div>
        </a>
    </li>
    @endif

    <li class="menu {{ Route::currentRouteName() == 'admin.tron' ? 'active' : '' }}  ">
        <a href="{{route('admin.tron')}}"  aria-expanded="false" class="dropdown-toggle">
            <div class="">
                <img src="/crypto/tron.png" height="20px" class="mr-4" alt="">
                <span>Tron Wallet <span class="badge badge-warning">New</span></span>
            </div>
        </a>
    </li>

    <li class="menu {{ Route::currentRouteName() == 'admin.tron' ? 'active' : '' }}  ">
        <a href="{{route('admin.tether')}}"  aria-expanded="false" class="dropdown-toggle">
            <div class="">
                <img src="/svg/tetherwallet_logo.svg" height="20px" class="mr-4" alt="">
                <span>Tether Wallet <span class="badge badge-warning">New</span></span>
            </div>
        </a>
    </li>

    <li class="menu {{ Route::currentRouteName() == 'sales.loadSales' ? 'active' : '' }}  ">
        <a href="{{route('sales.loadSales')}}"  aria-expanded="false" class="dropdown-toggle">
            <div class="">
                <ion-icon name="settings"></ion-icon>
                <span>Target Setting</span>
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

    <li class="menu {{ Route::currentRouteName() == 'admin.users' ? 'active' : '' }}">
        <a href="{{route('admin.user-verifications')}}" aria-expanded="false" class="dropdown-toggle" >
            <div class="">
                <ion-icon name="shield-checkmark-outline"></ion-icon>
                <span>Users verification </span>
            </div>
        </a>
    </li>

    <li class="menu {{ Route::currentRouteName() == 'admin.verification_limit' ? 'active' : '' }}">
        <a href="{{route('admin.verification_limit')}}" aria-expanded="false" class="dropdown-toggle">
            <div class="">
                <ion-icon name="people-circle-outline"></ion-icon>
                <span>Verification limit</span>
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

    <li class="menu {{ Route::currentRouteName() == 'admin.call-categories' ? 'active' : '' }}">
        <a href="{{route('admin.call-categories')}}" aria-expanded="false" class="dropdown-toggle">
            <div class="">
                <ion-icon name="cube-outline"></ion-icon>
                <span>Call Categories</span>
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



    <li class="menu {{ Route::currentRouteName() == 'admin.chat_agents' ? 'active' : '' }}">
        <a href="{{route('admin.chat_agents')}}" aria-expanded="false" class="dropdown-toggle" >
            <div class="">
                <ion-icon name="people-circle-outline"></ion-icon>
                <span>Trade Agents</span>
            </div>
        </a>
    </li>

    <li class="menu {{ Route::currentRouteName() == 'admin.accountants' ? 'active' : '' }}">
        <a href="{{route('admin.accountants')}}" aria-expanded="false" class="dropdown-toggle" >
            <div class="">
                <ion-icon name="people-circle-outline"></ion-icon>
                <span>Accountants</span>
            </div>
        </a>
    </li>


    <li class="menu {{ Route::currentRouteName() == 'admin.referral' ? 'active' : '' }}">
        <a href="{{route('admin.referral')}}" aria-expanded="false" class="dropdown-toggle" >
            <div class="">
                <ion-icon name="people-circle-outline"></ion-icon>
                <span>Referral Settings</span>
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


     <li class="menu {{ Route::currentRouteName() == 'admin.accountants' ? 'active' : '' }}">
        <a href="{{route('admin.general_settings')}}" aria-expanded="false" class="dropdown-toggle" >
            <div class="">
                <ion-icon name="people-circle-outline"></ion-icon>
                <span>General Settings</span>
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
