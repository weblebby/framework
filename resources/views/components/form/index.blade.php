@props(['bind', 'method' => 'POST'])

<form {{ $attributes->merge(['method' => $method === 'GET' ? $method : 'POST']) }}>
    @csrf
    @method($method)
    {{ $slot }}
</form>