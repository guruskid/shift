@component('mail::message')
<strong style="margin-left: 30%;" >Hi, {{$transaction->user->first_name}}</strong><br>
@if ($type == 'credit')
<strong style="text-align: center" > ₦{{number_format($transaction->amount)}} recieved from {{$transaction->dr_acct_name}} </strong>
@else
<strong style="text-align: center"> ₦{{number_format($transaction->amount)}} sent to {{$transaction->cr_acct_name}} </strong>
@endif

<img src="https://wpfystatic.b-cdn.net/rahul/billl.png" alt="invoice-icon" style="margin-left: 40%;" />

@component('mail::table')
| Details             | Value  |
| :------------- | --------:|
|Type   | {{ucwords($type)}}  |
|Amount   | ₦{{number_format($transaction->amount)}}  |
@if ($type == 'credit')
|Bank Name   | {{$transaction->dr_acct_name}}  |
@else
|Bank Name   | {{str_limit($transaction->cr_acct_name, 35)}}  |
@endif
|Transaction Id   | {{$transaction->reference}}  |
|Description   | {{$transaction->narration}}  |
|Balance Cash  | ₦{{number_format($transaction->current_balance)}}  |
@endcomponent

@endcomponent
