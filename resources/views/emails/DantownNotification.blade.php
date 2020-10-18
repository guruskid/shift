@component('mail::message')

@component('mail::panel')
{{$title}}
@endcomponent

{{$body}}

@component('mail::button', ['url' => $btn_url])
{{$btn_text}}
@endcomponent


{{ config('app.name') }}
@endcomponent
