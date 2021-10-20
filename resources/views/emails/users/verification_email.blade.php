@component('mail::message')
@component('mail::panel')
{{$title}}
@endcomponent

{{$code}} {{$body}}



{{-- @component('mail::button', ['url' => env('APP_URL')])
Visit Us
@endcomponent --}}

@endcomponent
