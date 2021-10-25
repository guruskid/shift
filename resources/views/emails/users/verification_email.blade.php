@component('mail::message')
@component('mail::panel')
{{$title}}
@endcomponent


<span style="color: #000070; font-weight:bolder">{{$code}}</span> {{$body}}

{{-- @component('emails.users.general_first_template')

@endcomponent --}}



{{-- @component('mail::button', ['url' => env('APP_URL')])
Visit Us
@endcomponent --}}

@endcomponent
