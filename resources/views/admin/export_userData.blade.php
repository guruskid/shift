<table class="align-middle mb-4 table table-bordered table-striped transactions-table ">
    <thead>
        <tr>
            <th class="text-center">SN</th>
            <th class="text-center">First_name</th>
            <th class="text-center">Last_name</th>
            <th class="text-center">Email</th>
            <th class="text-center">Naira Balance</th>
            <th class="text-center">Date</th>
        </tr>
    </thead>
    <tbody>
        @php
        $sn = 1; 
        @endphp

        @foreach ($users as $user)
        <tr>
            <td>{{$sn++}}</td>
            <td>{{ $user->first_name }}</td>
            <td>{{ $user->last_name }}</td>
            <td>{{ $user->email }}</td>
            <td>₦{{$user->nairaWallet ? number_format($user->nairaWallet->amount) : 0 }} </td>
            <td>{{$user->created_at->format('d M y')}}</td>
        </tr> 
        @endforeach                                   
    </tbody>
</table>