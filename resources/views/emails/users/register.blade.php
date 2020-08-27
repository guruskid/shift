@component('mail::message')
@component('mail::panel')
Welcome on Board
@endcomponent

Hi {{$name}},
thank you for signing up on Dantown multi services. Please login into your account to make a trade today.




Thanks,<br>
{{ config('app.name') }}
@endcomponent
