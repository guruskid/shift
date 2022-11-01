@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            {{ 'hi' }}
        @endcomponent
    @endslot


    {{-- Body --}}
    {{ $slot }}

    {{-- Subcopy --}}
    @isset($subcopy)
        @slot('subcopy')
            @component('mail::subcopy')
                {{ $subcopy }}
            @endcomponent
        @endslot
    @endisset

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            <strong>©</strong>@lang('All rights reserved.') | {{ config('app.name') }} {{ date('Y') }}
        @endcomponent
    @endslot
@endcomponent
