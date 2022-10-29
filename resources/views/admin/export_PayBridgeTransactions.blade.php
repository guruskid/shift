<table class="align-middle mb-0 table table-borderless table-striped table-hover transactons-table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Phone</th>
            <th>Amount</th>
            <th>Reference</th>
            <th>Type</th>
            <th>Prev Balance</th>
            <th>Current Balance</th>
            <th>Date</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($transactions as $key => $t)
        <tr>
            <td>{{ $t->user->first_name .' '. $t->user->last_name }}</td>
            <td>{{ $t->user->phone }}</td>
            @if($t->type == 'withdrawal' AND $t->status == 'waiting')
                <td class="text-danger">₦{{ number_format($t->amount) }}</td>
            @elseif($t->type == 'deposit' AND $t->status == 'waiting')
                <td class="text-success">₦{{ number_format($t->amount) }}</td>
            @else

            <td>₦{{ number_format($t->amount) }}</td>
            @endif
            <td>{{ $t->reference }}</td>
            <td>{{ $t->type }}
            @if($t->type == 'withdrawal' OR isset($t->acct_details))
                <br><br>
                {{ $t->acct_details }}
            @endif

            </td>
            <td>₦{{ number_format($t->prev_bal) }}</td>
            <td>₦{{ number_format($t->current_bal) }}</td>
            <td>{{ $t->created_at->format('d m y, h:ia') }}</td>
            <td>{{ $t->status }}</td>
        </tr>
        @endforeach
    </tbody>
</table>