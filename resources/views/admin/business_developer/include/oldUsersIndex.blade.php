<div class="row layout-top-spacing">
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing"
    onclick="window.location = '{{ route('business-developer.user-category',['type'=>'Quarterly_Inactive']) }}'">
        <div class="widget widget-chart-one @if(isset($type) AND $type =='Quarterly_Inactive') bg-primary @endif">
            <div class="widget-heading">
                <div>
                    <h5 class="@if(isset($type) AND $type =='Quarterly_Inactive') text-white @endif">Quaterly Inactive Users </h5>
                </div>
                <div class="widget-n">
                    <h5>{{ $QuarterlyInactiveUsersCount }}</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing"
    onclick="window.location = '{{ route('business-developer.user-category',['type'=>'NoResponse']) }}'">
        <div class="widget widget-chart-one @if(isset($type) AND $type =='NoResponse') bg-primary @endif">
            <div class="widget-heading">
                <div>
                    <h5 class="@if(isset($type) AND $type =='NoResponse') text-white @endif">No Response </h5>
                </div>
                <div class="widget-n">
                    <h5>{{ $NoResponseCount }}</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing"
    onclick="window.location = '{{ route('business-developer.user-category',['type'=>'Called_Users']) }}'">
        <div class="widget widget-chart-one @if(isset($type) AND $type =='Called_Users') bg-primary @endif">
            <div class="widget-heading">
                <div>
                    <h5 class="@if(isset($type) AND $type =='Called_Users') text-white @endif">Called Users</h5>
                </div>
                <div class="widget-n">
                    <h5>{{ $calledUsersCount }}</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing"
    onclick="window.location = '{{ route('business-developer.user-category',['type'=>'Responded_Users']) }}'">
        <div class="widget widget-chart-one @if(isset($type) AND $type =='Responded_Users') bg-primary @endif">
            <div class="widget-heading">
                <div>
                    <h5 class="@if(isset($type) AND $type =='Responded_Users') text-white @endif">Responded Users</h5>
                </div>
                <div class="widget-n">
                    <h5>{{ $RespondedUsersCount }}</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing"
    onclick="window.location = '{{ route('business-developer.user-category',['type'=>'Recalcitrant_Users']) }}'">
        <div class="widget widget-chart-one @if(isset($type) AND $type =='Recalcitrant_Users') bg-primary @endif">
            <div class="widget-heading">
                <div>
                    <h5 class="@if(isset($type) AND $type =='Recalcitrant_Users') text-white @endif">Recalcitrant Users</h5>
                </div>
                <div class="widget-n">
                    <h5>{{ $RecalcitrantUsersCount }}</h5>
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
                                        <th><div class="">Name</div></th>
                                        <th><div class="">Username</div></th>
                                        @if ($type !="NoResponse")
                                        <th><div class="">Last Transaction Date</div></th>
                                        @endif
                                        @if ($type == "NoResponse")
                                        <th><div class="">No Response Cycle</div></th>
                                        @endif
                                        
                                        @if ($type == "Called_Users")
                                            <th><div class="">Called Date</div></th>
                                        @endif
                                        @if ($type == "Quarterly_Inactive")
                                            <th><div class="">Responded Cycle</div></th>
                                            <th><div class="">Recalcitrant Cycle</div></th>
                                            <th><div class="">Transaction no</div></th>
                                            <th><div class="">Priority</div></th>
                                        @endif
                                        @if($type == "Recalcitrant_Users")
                                            <th><div class="">Recalcitrant Date</div></th>
                                        @endif
                                        @if ($type == "Quarterly_Inactive")
                                            <th><div class="">Previous Cycle</div></th>
                                        @endif
                                        @if ($type == "Quarterly_Inactive" OR $type =="Responded_Users" OR $type == "Called_Users" OR $type =="NoResponse")
                                            <th><div class="">Action</div></th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data_table as $u)
                                    <tr>
                                        <td class="text-center"><div class="td-content customer-name">{{$u->user->first_name." ".$u->user->last_name}}</div></td>
                                        <td class="text-center">{{ $u->user->username }}</td>
                                        @if ($type !="NoResponse")
                                        <td class="text-center">{{ $u->last_transaction_date }}</td>
                                        @endif
                                        @if ($type == "NoResponse")
                                        <td class="text-center">{{ ($u->noResponse_streak == null) ? 0 : $u->noResponse_streak }}</td>
                                        @endif
                                        @if ($type == "Called_Users")
                                            <td class="text-center">{{ $u->called_date }}</td>
                                        @endif
                                        @if ($type == "Quarterly_Inactive")
                                            <td class="text-center">{{ ($u->Responded_Cycle == null) ? 0 : $u->Responded_Cycle }}</td>
                                            <td class="text-center">{{ ($u->Recalcitrant_Cycle == null) ? 0 : $u->Recalcitrant_Cycle  }}</td>
                                            <td class="text-center">{{ ($u->transactionCount) ? number_format($u->transactionCount) : 0 }}</td>
                                            <td class="text-center">{{ ($u->priority) ? $u->priority : null }}</td>
                                        @endif
                                        @if($type == "Recalcitrant_Users")
                                            <td class="text-center">{{ $u->updated_at->format('d M y, h:ia') }}</td>
                                        @endif
                                        @if ($type == "Quarterly_Inactive")
                                            <td class="text-center">{{ ($u->Previous_Cycle == null) ? 'none' : $u->Previous_Cycle }}
                                            @if ($u->Previous_Cycle == "Responded")
                                                @if ($u->Responded_streak != null)
                                                    ({{ $u->Responded_streak }})
                                                @endif
                                            @else
                                                @if ($u->Recalcitrant_streak != null)
                                                        ({{ $u->Recalcitrant_streak }})
                                                    @endif
                                            @endif
                                            </td>
                                        @endif
                                        @if ($type == "Quarterly_Inactive" OR $type =="Responded_Users" OR $type == "Called_Users" OR $type =="NoResponse")
                                        <td class="text-center">
                                            @if ($type == "Quarterly_Inactive" OR $type =="NoResponse")
                                            
                                            <div class="btn-group">
                                                <a href="#" class="my-2 mr-2" data-toggle="modal" data-target="#view-phone-number" onclick="showPhoneNumber({{$u->user}})">
                                                    <span class="btn btn btn-info">View</span>
                                                </a>

                                                {{-- <a href="#" class="my-2" data-toggle="modal" data-target="#add-call-log" onclick="AddResponse({{$u->user}})">
                                                    <span class="btn btn btn-info">Response</span>
                                                </a> --}}
                                            </div>
                                            
                                            @endif
                                            @if ($type =="Responded_Users" OR $type == "Called_Users")
                                                @if($u->call_log)
                                                <a href="#" class="my-2" data-toggle="modal" data-target="#view-call-log" onclick="ViewResponse({{$u->call_log}},{{ $u->user }},{{ $u->call_log->call_category }})">
                                                    <span class="btn btn btn-info">View</span>
                                                </a>
                                                @endif
                                            @endif
                                        </td>
                                        @endif
                                    </tr>
                                        
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="View More">
                                <a href="{{ route('business-developer.view-type',['type'=>$type]) }}">View all </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>