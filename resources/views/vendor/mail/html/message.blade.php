<x-mail::layout>
{{-- Header --}}
<x-slot:header>
<x-mail::header :url="config('app.url')">
    <img src="{{ Storage::disk('public')->url('logo/logo.png') }}" class="logo" alt="{{ config('app.name') }}" width="75" height="52">
</x-mail::header>
</x-slot:header>

{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
@isset($subcopy)
<x-slot:subcopy>
<x-mail::subcopy>
{{ $subcopy }}
</x-mail::subcopy>
</x-slot:subcopy>
@endisset

{{-- Footer --}}
<x-slot:footer>
<x-mail::footer>
© {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved.') }}
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
