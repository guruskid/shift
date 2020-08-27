@component('mail::message')

@component('mail::panel')
{{$title}}
@endcomponent

{{$body}}


{{ config('app.name') }}
@endcomponent
