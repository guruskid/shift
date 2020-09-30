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

    <li class="menu {{ Route::currentRouteName() == 'admin.rates' ? 'active' : '' }}">
        <a href="{{route('admin.rates')}}" aria-expanded="false" class="dropdown-toggle">
            <div class="">
                <ion-icon name="analytics-outline"></ion-icon>
                <span>Rates</span>
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
