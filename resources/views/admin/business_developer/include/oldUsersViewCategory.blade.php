<div class="table-responsive p-3">
    <table
        class="align-middle mb-0 table table-borderless table-striped table-hover text-center 
        {{-- transactions-table --}}
        ">
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
                @if ($type =="callLog")
                <th><div class="">Category</div></th>
                @endif
                @if ($type == "Called_Users")
                    <th><div class="">Called Date</div></th>
                @endif
                @if ($type == "Quarterly_Inactive")
                    <th><div class="">Responded Cycle</div></th>
                    <th><div class="">Recalcitrant Cycle</div></th>
                    <th><div class="">Transaction no</div></th>
                    <th><div class="">Priority</div></th>
                    <th><div class="">Transaction Amount</div></th>
                @endif
                @if($type == "Recalcitrant_Users")
                    <th><div class="">Recalcitrant Date</div></th>
                @endif
                @if ($type == "Quarterly_Inactive")
                    <th><div class="">Previous Cycle</div></th>
                @endif
                @if ($type == "Quarterly_Inactive" OR $type =="Responded_Users" OR $type == "Called_Users" OR $type =="callLog" OR $type =="NoResponse")
                    <th><div class="">Action</div></th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($data_table as $u)
            <tr>
                <td><div class="td-content customer-name">{{$u->user->first_name." ".$u->user->last_name}}</div></td>
                <td>{{ $u->user->username }}</td>
                @if ($type !="NoResponse")
                <td>{{ $u->last_transaction_date }}</td>
                @endif
                @if ($type == "NoResponse")
                <td>{{ ($u->noResponse_streak == null) ? 0 : $u->noResponse_streak }}</td>
                @endif
                @if ($type =="callLog")
                <td>{{ ($u->call_category) ? $u->call_category->category : 'none' }}</td>
                @endif
                @if ($type == "Called_Users")
                    <td>{{ $u->call_log->created_at->format('d M y, h:ia') }}</td>
                @endif
                @if ($type == "Quarterly_Inactive")
                <td>{{ ($u->Responded_Cycle == null) ? 0 : $u->Responded_Cycle }}</td>
                <td>{{ ($u->Recalcitrant_Cycle == null) ? 0 : $u->Recalcitrant_Cycle  }}</td>
                <td>{{ ($u->transactionCount) ? number_format($u->transactionCount) : 0 }}</td>
                <td>{{ ($u->priority) ? $u->priority : null }}</td>
                <td class="text-center">{{ ($u->transactionAmount) ? number_format($u->transactionAmount) : 0 }}</td>
                @endif
                @if($type == "Recalcitrant_Users")
                    <td>{{ $u->updated_at->format('d M y, h:ia') }}</td>
                @endif
                @if ($type == "Quarterly_Inactive")
                <td>{{ ($u->Previous_Cycle == null) ? 'none' : $u->Previous_Cycle }}
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
                @if ($type == "Quarterly_Inactive" OR $type =="Responded_Users" OR $type == "Called_Users" OR $type =="callLog" OR $type =="NoResponse")
                <td>
                    @if ($type == "Quarterly_Inactive" OR $type =="NoResponse")
                    <a href="#" class="my-2 mr-2" data-toggle="modal" data-target="#view-phone-number" onclick="showPhoneNumber({{$u->user}})">
                        <span class="btn btn btn-info">View</span>
                    </a>
                    
                    @endif
                    @if ($type == "Called_Users")
                        @if ($type =="Responded_Users" OR $type == "Called_Users")
                        <a href="#" class="my-2" data-toggle="modal" data-target="#view-call-log" onclick="ViewResponse({{$u->call_log}},{{ $u->user }},{{ $u->call_log->call_category }})">
                            <span class="btn btn btn-info">View</span>
                        </a>
                        @endif 
                    @else
                        @if ($type =="Responded_Users" OR $type == "Called_Users" OR $type =="callLog")
                        <a href="#" class="my-2" data-toggle="modal" data-target="#view-call-log" onclick="ViewResponse({{$u}},{{ $u->user }},{{ $u->call_category }})">
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
    {{ $data_table->links() }}
</div>