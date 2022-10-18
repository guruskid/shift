<ul class="list-unstyled menu-categories" id="accordionExample">
    <li class="menu {{ Route::currentRouteName() == 'admin.dashboard' ? 'active' : '' }}">
        <a href="{{route('admin.dashboard')}}"  aria-expanded="true" class="dropdown-toggle">
            <div class="">
                <ion-icon name="home-outline"></ion-icon>
                <span>Overview</span>
            </div>
        </a>
    </li>

    <li class="menu {{ Route::currentRouteName() == 'admin.sales.users_birthdays' ? 'active' : '' }}  ">
        <a href="{{route('admin.sales.users_birthdays')}}"  aria-expanded="false" class="dropdown-toggle">
            <div class="">
                <ion-icon name="cube-outline"></ion-icon>
                <span>Birthdays</span>
            </div>
        </a>
    </li>

    <li class="menu {{ Route::currentRouteName() == 'sales.oldUsers.salesAnalytics' ? 'active' : '' }}  ">
        <a href="{{route('sales.oldUsers.salesAnalytics')}}"  aria-expanded="false" class="dropdown-toggle">
            <div class="">
                <ion-icon name="analytics-outline"></ion-icon>
                <span>Sales Old Users</span>
            </div>
        </a>
    </li>


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
