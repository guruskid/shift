@component('mail::message')
@component('mail::panel')
{{$title}}
@endcomponent


<h1>{{$title}}</h1>

{{-- <h3>Ello {{$uname}}</h3> --}}

{{$title}}




{{$body ?? ''}}

@if(isset($para))
    @foreach ($para as $pr)
    <h3>{{$pr}}</h3>
    @endforeach
@endif

@endcomponent
