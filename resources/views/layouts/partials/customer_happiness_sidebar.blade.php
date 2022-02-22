<ul class="list-unstyled menu-categories" id="accordionExample">
    <li class="menu {{ Route::currentRouteName() == 'customerHappiness.homepage' ? 'active' : '' }}">
        <a href="{{route('customerHappiness.homepage')}}"  aria-expanded="true" class="dropdown-toggle">
            <div class="">
                <ion-icon name="home-outline"></ion-icon>
                <span>Dashboard</span>
            </div>
        </a>
    </li>

    <li class="menu {{ Route::currentRouteName() == 'admin.transactions' ? 'active' : '' }}  ">
        <a href="{{route('customerHappiness.transactions')}}"  aria-expanded="false" class="dropdown-toggle">
            <div class="">
                <ion-icon name="stats-chart-outline"></ion-icon>
                <span>Transactions</span>
            </div>
        </a>
    </li>

    

    <li class="menu   ">
        <a href="{{route('customerHappiness.chatdetails',['status'=>'New'])}}"  aria-expanded="false" class="dropdown-toggle">
            <div class="">
                <ion-icon class="text-success" name="chatbubbles-outline"></ion-icon>
                <span>New Chat/Query<<span class="badge badge-warning">New</span></span>
            </div>
        </a>
    </li>

    <li class="menu ">
        <a href="{{route('customerHappiness.chatdetails',['status'=>'Close'])}}"  aria-expanded="false" class="dropdown-toggle">
            <div class="">
                <ion-icon class="text-danger" name="chatbubbles-outline"></ion-icon>
                <span>Closed Chat/Query <span class="badge badge-warning">New</span></span>
            </div>
        </a>
    </li>

   


  

    <li class="menu {{ Route::currentRouteName() == 'logout' ? 'active' : '' }}">
        <a href="{{route('logout')}}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" aria-expanded="false" class="dropdown-toggle">
            <div class="">
                <ion-icon name="log-out-outline"></ion-icon>
                <span>Logout</span>
            </div>
        </a>
    </li>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</ul>
