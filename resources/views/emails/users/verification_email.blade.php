@component('mail::message')
@component('mail::panel')
Verification Code
@endcomponent

{{$code}} is your verification code, valid for 5 minutes. to keep your account safe, do not share this code with anyone.



@component('mail::button', ['url' => env('APP_URL')])
Visit Us
@endcomponent

@endcomponent
