@props(['bind', 'bag' => 'default', 'method' => 'POST'])

@php($method = Str::upper($method))

<form {{ $attributes->merge(['method' => $method === 'GET' ? $method : 'POST']) }}>
    @if ($method !== 'GET')
        @csrf
        @method($method)
    @endif
    {{ $slot }}
</form>