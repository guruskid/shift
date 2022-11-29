<div class="row layout-top-spacing">
    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-4 layout-spacing"
    onclick="window.location = '{{ route('business-developer.new-users.index',['type'=>'newInactiveUser']) }}'">
        <div class="widget widget-chart-one @if(isset($type) AND $type =='newInactiveUser') bg-primary @endif">
            <div class="widget-heading">
                <div>
                    <h5 class="@if(isset($type) AND $type =='newInactiveUser') text-white @endif">New Inactive User's </h5>
                </div>
                <div class="widget-n">
                    <h5>{{ $newInactiveUsersCount }}</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-4 layout-spacing"
    onclick="window.location = '{{ route('business-developer.new-users.index',['type'=>'calledNewUsers']) }}'">
        <div class="widget widget-chart-one @if(isset($type) AND $type =='calledNewUsers') bg-primary @endif">
            <div class="widget-heading">
                <div>
                    <h5 class="@if(isset($type) AND $type =='calledNewUsers') text-white @endif">Called New User's </h5>
                </div>
                <div class="widget-n">
                    <h5>{{ $newCalledUsersCount }}</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-4 layout-spacing"
    onclick="window.location = '{{ route('business-developer.new-users.index',['type'=>'newUnresponsiveUser']) }}'">
        <div class="widget widget-chart-one @if(isset($type) AND $type =='newUnresponsiveUser') bg-primary @endif">
            <div class="widget-heading">
                <div>
                    <h5 class="@if(isset($type) AND $type =='newUnresponsiveUser') text-white @endif">Unresponsive New User's</h5>
                </div>
                <div class="widget-n">
                    <h5>{{ $newUnresponsiveUsersCount }}</h5>
                </div>
            </div>
        </div>
    </div>


    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
        <div class="row">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="widget widget-table-two">
                    <div class="widget-heading">
                        <h5 class=""> </h5>
                    </div>

                    <div class="widget-content">
                        <div class="table-responsive">
                            <table class="table text-center">
                                <thead>
                                    <tr>
                                        <th><div class="text-center">Name</div></th>
                                        <th><div class="text-center">Username</div></th>
                                        @if($type == 'newInactiveUser')
                                        <th><div class="text-center">Last Transaction Date</div></th>
                                        <th><div class="text-center">Signup Date</div></th>
                                        @endif
                                        @if($type == 'calledNewUsers')
                                        <th><div class="text-center">Called Date</div></th>
                                        @endif
                                        <th><div class="text-center">Days left</div></th>
                                        <th><div class="text-center">Days in Cycle</div></th>
                                        <th><div class="text-center">Action</div></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data_table as $u)
                                    <tr>
                                        <td><div class="td-content customer-name">{{$u->user->first_name." ".$u->user->last_name}}</div></td>
                                        <td class="text-center">{{ $u->user->username }}</td>
                                        @if($type == 'newInactiveUser')
                                        <td class="text-center">{{ $u->last_transaction_date}}</td>
                                        <td class="text-center">{{ $u->signup }}</td>
                                        @endif
                                        @if($type == 'calledNewUsers')
                                        <td class="text-center">{{ $u->called_date}}</td>
                                        @endif
                                        <td class="text-center">{{ $u->daysLeft }}</td>
                                        <td class="text-center">{{ $u->daysLeftInCycle }}</td>
                                        <td class="text-center">
                                            @if($type == 'newInactiveUser')
                                            <a href="#" class="my-2 mr-2" data-toggle="modal" data-target="#view-phone-number" onclick="showPhoneNumber({{$u->user}})">
                                                <span class="btn btn btn-info">View</span>
                                            </a>
                                            @else
                                                @if($u->call_log)
                                                    <a href="#" class="my-2" data-toggle="modal" data-target="#view-call-log" onclick="ViewResponse({{$u->call_log}},{{ $u->user }},{{ $u->call_log->call_category }})">
                                                        <span class="btn btn btn-info">View</span>
                                                    </a>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                        
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="View More">
                                <a href="{{ route('business-developer.new-user.view-type',['type'=>$type]) }}">View all </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>