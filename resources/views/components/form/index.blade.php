@props(['bind', 'bag' => 'default', 'method' => 'POST'])

@php($method = \Illuminate\Support\Str::upper($method))

<form {{ $attributes->merge(['method' => $method === 'GET' ? $method : 'POST']) }}>
    @if ($method !== 'GET')
        @csrf
        @method($method)
    @endif
    @hook(panel()->nameWith('before_form_fields'))
    {{ $slot }}
    @hook(panel()->nameWith('after_form_fields'))
</form>