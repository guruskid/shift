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

    <li class="menu {{ Route::currentRouteName() == 'admin.rates' ? 'active' : '' }}">
        <a href="{{route('admin.rates')}}" aria-expanded="false" class="dropdown-toggle">
            <div class="">
                <ion-icon name="analytics-outline"></ion-icon>
                <span>Rates</span>
            </div>
        </a>
    </li>

    @if ( in_array(Auth::user()->role, [666, 999] ) )
    <li class="menu {{ Route::currentRouteName() == 'sales.newUsers.salesAnalytics' ? 'active' : '' }}  ">
        <a href="{{route('sales.newUsers.salesAnalytics')}}"  aria-expanded="false" class="dropdown-toggle">
            <div class="">
                <ion-icon name="analytics-outline"></ion-icon>
                <span>Sales New Users</span>
            </div>
        </a>
    </li>
    @endif

    @if ( in_array(Auth::user()->role, [666,999] ) )
    <li class="menu {{ Route::currentRouteName() == 'sales.oldUsers.salesAnalytics' ? 'active' : '' }}  ">
        <a href="{{route('sales.oldUsers.salesAnalytics')}}"  aria-expanded="false" class="dropdown-toggle">
            <div class="">
                <ion-icon name="analytics-outline"></ion-icon>
                <span>Sales Old Users</span>
            </div>
        </a>
    </li>
    @endif

    <li class="menu {{ Route::currentRouteName() == 'sales.loadSales' ? 'active' : '' }}  ">
        <a href="{{route('sales.loadSales')}}"  aria-expanded="false" class="dropdown-toggle">
            <div class="">
                <ion-icon name="settings"></ion-icon>
                <span>Sales Setting</span>
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

    <li class="menu {{ Route::currentRouteName() == 'admin.chat_agents' ? 'active' : '' }}">
        <a href="{{route('admin.chat_agents')}}" aria-expanded="false" class="dropdown-toggle">
            <div class="">
                <ion-icon name="people-circle-outline"></ion-icon>
                <span>Chat Agents</span>
            </div>
        </a>
    </li>

    <li class="menu {{ Route::currentRouteName() == 'admin.image_slider' ? 'active' : '' }}">
        <a href="{{route('admin.image_slider')}}" aria-expanded="false" class="dropdown-toggle">
            <div class="">
                <ion-icon name="people-circle-outline"></ion-icon>
                <span>Image Slider</span>
            </div>
        </a>
    </li>

    <li class="menu {{ Route::currentRouteName() == 'admin.users' ? 'active' : '' }}">
        <a href="{{route('admin.user-verifications')}}" aria-expanded="false" class="dropdown-toggle" >
            <div class="">
                <ion-icon name="shield-checkmark-outline"></ion-icon>
                <span>Users verification <span class="badge badge-warning">New</span></span>
            </div>
        </a>
    </li>

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
