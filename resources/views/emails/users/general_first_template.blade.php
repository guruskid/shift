@component('mail::message')
@component('mail::panel')
{{-- {{$title}} --}}
@endcomponent


<h1 style="font-size: 24px">{{$title ?? ''}}</h1> <br>
{{-- 
@if (strlen($name) > 1)
    <h2 style="color: #000070; font-size:20px;"> Hello {{$name}},</h2>
@endif --}}
<h2 style="font-weight: normal"> Dear {{$name}},</h2>
<p style="font-size: 16px">
@php
    $pattern = array('<br/>');
    $nb = explode('<br>', $body);
    foreach ($nb as $b) {
        echo $b.'<br>';
    }
@endphp
</p>
@endcomponent
