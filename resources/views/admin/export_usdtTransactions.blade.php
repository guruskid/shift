<table class="align-middle mb-4 table table-bordered table-striped transactions-table ">
    <thead>
        <tr>
            <th class="text-center">SN</th>
            <th class="text-center">First_name</th>
            <th class="text-center">Last_name</th>
            <th class="text-center">Email</th>
            <th class="text-center">Card</th>
            <th class="text-center">Type</th>
            <th class="text-center">Amount</th>
            <th class="text-center">Amount Paid</th>
            <th class="text-center">Status</th>
            <th class="text-center">Quantity</th>
            <th class="text-center">Card Price</th>
            <th class="text-center">Date</th>
        </tr>
    </thead>
    <tbody>
        @php
        $sn = 1; 
        $users;  
        @endphp

        @foreach ($transactions as $tnx)
        <tr>
            <td>{{$sn++}}</td>
            <td>{{ $tnx->user->first_name }}</td>
            <td>{{ $tnx->user->last_name }}</td>
            <td>{{ $tnx->user_email }}</td>
            <td>{{ $tnx->card }}</td>
            <td>{{ $tnx->type }}</td>
            <td>{{ number_format($tnx->amount) }}</td>
            <td>{{ number_format($tnx->amount_paid) }}</td>
            <td>{{ $tnx->status }}</td>
            <td>{{ $tnx->quantity }}</td>
            <td>{{ $tnx->card_price }}</td>
            <td>{{ $tnx->created_at->format('Y-m-d h:ia') }}</td>
        </tr> 
        @endforeach                                   
    </tbody>
</table>