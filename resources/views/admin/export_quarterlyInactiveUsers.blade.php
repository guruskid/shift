<table class="align-middle mb-0 table table-borderless table-striped table-hover transactons-table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($quarterlyInactive as $key => $qi)
        <tr>
            <td>{{ $qi->user->first_name .' '. $qi->user->last_name }}</td>
            <td>{{ $qi->user->email }}</td>
        </tr>
        @endforeach
    </tbody>
</table>