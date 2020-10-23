<div class="scrollbar-sidebar  sidebar-text-light bg-custom-grey">
    <div class="app-sidebar__inner">
        <ul class="vertical-nav-menu">
            <li class="app-sidebar__heading">User Dashboard</li>
            <li class="my-3 ">
                <a href="{{route('user.dashboard')}}"
                    class=" {{ Route::currentRouteName() == 'user.dashboard' ? 'mm-active' : '' }} ">
                    <i class="metismenu-icon fa fa-home"></i>

                    Dashboard
                </a>
            </li>

            <li class="my-3 ">
                <a href="{{route('user.portfolio')}}"
                    class="">
                    <i class="metismenu-icon ">
                        <img src="{{asset('svg/wallet.svg')}} " alt="">
                    </i>
                    Wallet Portfolio
                </a>
            </li>

            <li class="my-3 ">
                <a href="{{route('user.bills')}}"
                    class="">
                    <i class="metismenu-icon ">
                        <img src="{{asset('svg/recharge-sm.svg')}} " alt="">
                    </i>
                    Recharge
                </a>
            </li>

            <li class="my-3">
                <a href="{{route('user.assets')}}"
                    class=" {{ Route::currentRouteName() == 'user.assets' ? 'mm-active' : '' }} ">
                    <i class="metismenu-icon pe-7s-graph1"></i>
                    Assets
                </a>
            </li>


            <li class="my-3 ">
                <a href="{{route('user.transactions')}}"
                    class=" {{ Route::currentRouteName() == 'user.transactions' ? 'mm-active' : '' }} ">
                    <i class="metismenu-icon pe-7s-timer"></i>
                    Transaction History
                </a>
            </li>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>


        </ul>
        <ul class="vertical-nav-menu" style="    position: absolute; right: 50%; bottom: 10%;">
            <hr style="border-top: 1px solid #00B9CD;" >
            <li class="" >
                <a href="{{route('user.profile')}}"
                    class=" {{ Route::currentRouteName() == 'user.profile' ? 'mm-active' : '' }} ">
                    <i class="metismenu-icon pe-7s-config"></i>
                    Account
                </a>
            </li>

            <li class="my-3">
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="metismenu-icon pe-7s-power"></i>
                    Logout
                </a>
            </li>

        </ul>
    </div>
</div>
