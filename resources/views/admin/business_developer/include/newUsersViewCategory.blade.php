<div class="table-responsive p-3">
    <table
        class="align-middle mb-0 table table-borderless table-striped table-hover text-center 
        {{-- transactions-table --}}
        ">
        <thead>
            <tr>
                <th><div class="text-center">Name</div></th>
                <th><div class="text-center">Username</div></th>
                @if(in_array($type,['callLog','newInactiveUser']))
                <th><div class="text-center">Last Transaction Date</div></th>
                <th><div class="text-center">Signup Date</div></th>
                @endif
                @if($type == 'calledNewUsers')
                <th><div class="text-center">Called Date</div></th>
                @endif
                @if(!in_array($type,['callLog']))
                <th><div class="text-center">Days left</div></th>
                <th><div class="text-center">Days in Cycle</div></th>
                <th><div class="text-center">Action</div></th>
                @else
                <th><div class="text-center">Category</div></th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($data_table as $u)
            <tr>
                <td><div class="td-content customer-name">{{$u->user->first_name." ".$u->user->last_name}}</div></td>
                <td class="text-center">{{ $u->user->username }}</td>
                @if(in_array($type,['callLog','newInactiveUser']))
                <td class="text-center">{{ $u->last_transaction_date}}</td>
                <td class="text-center">{{ $u->signup }}</td>
                @endif
                @if($type == 'calledNewUsers')
                <td class="text-center">{{ $u->called_date}}</td>
                @endif
                @if(!in_array($type,['callLog']))
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
                @else
                <td class="text-center">{{ $u->call_category->category }}</td>
                @endif
            </tr>
                
            @endforeach
        </tbody>
    </table>
    {{ $data_table->links() }}
</div>