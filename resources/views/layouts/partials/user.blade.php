{{-- Removed bg-custom-grey class --}}
<div class="scrollbar-sidebar  sidebar-text-light bg_custom_blue">
    <div class="app-sidebar__inner">
        <ul class="vertical-nav-menu">
            {{-- <li class="app-sidebar__heading">User Dashboard</li> --}}
            <li class="my-3 ">
                <a href="{{route('user.dashboard')}}"
                    class=" {{ Route::currentRouteName() == 'user.dashboard' ? 'mm-active' : '' }} ">
                    {{-- <i class="metismenu-icon fa fa-home"></i> --}}
                        {{-- {{dd( Route::currentRouteName())}}Route::currentRouteName() --}}
                        <i class="metismenu-icon">
                            <img src="{{ Route::currentRouteName() == "user.dashboard" ? asset('svg/dashboard_icon.svg'):asset('svg/dashboard_icon_inactive.svg')}}" alt="">
                        </i>
                        Dashboard
                </a>
            </li>

            <li class="my-3 ">
                <a href="{{route('user.portfolio')}}"
                    class="">
                    <i class="metismenu-icon ">
                        <img src="{{ Route::currentRouteName() == "user.portfolio" ? asset('svg/wallet_icon_inactive.svg'):asset('svg/wallet_icon_active.svg')}}" alt="">
                        {{-- <img src="{{asset('svg/wallet.svg')}} " alt=""> --}}
                    </i>
                    Wallet Portfolio
                </a>
            </li>

            <li class="my-3 ">
                <a href="{{route('user.bills')}}"
                    class="">
                    <i class="metismenu-icon ">
                        <img src="{{ Route::currentRouteName() == "user.bills" ? asset('svg/bills_icon_inactive.svg'):asset('svg/bills_icon_active.svg')}}" alt="">
                        {{-- <img src="{{asset('svg/recharge-sm.svg')}} " alt=""> --}}
                    </i>
                    Recharge
                </a>
            </li>

            <li class="my-3">
                <a href="{{route('user.assets')}}"
                    class=" {{ Route::currentRouteName() == 'user.assets' ? 'mm-active' : '' }} ">
                    {{-- <i class="metismenu-icon pe-7s-graph1"></i> --}}
                    <i class="metismenu-icon">
                        <img src="{{ Route::currentRouteName() == "user.assets" ? asset('svg/assets_icon_inactive.svg'):asset('svg/assets_icon_active.svg')}}" alt="">
                    </i>
                    Assets
                </a>
            </li>


            <li class="my-3 ">
                <a href="{{route('user.transactions')}}"
                    class=" {{ Route::currentRouteName() == 'user.transactions' ? 'mm-active' : '' }} ">
                    {{-- <i class="metismenu-icon pe-7s-timer"></i> --}}
                    <i class="metismenu-icon">
                        <img src="{{ Route::currentRouteName() == "user.transactions" ? asset('svg/transactions_icon_innactive.svg'):asset('svg/transactions_icon_active.svg')}}" alt="">
                    </i>
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
