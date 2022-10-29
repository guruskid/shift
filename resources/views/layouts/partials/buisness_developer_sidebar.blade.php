<ul class="list-unstyled menu-categories" id="accordionExample">
    <li class="menu {{ Route::currentRouteName() == 'admin.dashboard' ? 'active' : '' }}">
        <a href="{{route('admin.dashboard')}}" aria-expanded="true" class="dropdown-toggle">
            <div class="">
                <ion-icon name="home-outline"></ion-icon>
                <span>Dashboard</span>
            </div>
        </a>
    </li>

    <li class="menu {{ Route::currentRouteName() == 'business-developer.call-log' ? 'active' : '' }}  ">
        <a href="{{route('business-developer.call-log')}}" aria-expanded="false" class="dropdown-toggle">
            <div class="">
                <ion-icon name="speedometer-outline"></ion-icon>
                <span>Call Logs</span>
            </div>
        </a>
    </li>

    <li class="menu {{ Route::currentRouteName() == 'business-developer.user-profile' ? 'active' : '' }}  ">
        <a href="{{route('business-developer.user-profile')}}" aria-expanded="false" class="dropdown-toggle">
            <div class="">
                <ion-icon name="person-outline"></ion-icon>
                <span>Users Profile</span>
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
