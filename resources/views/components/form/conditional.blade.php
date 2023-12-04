@props(['field'])

<div class="fd-space-y-3 fd-hidden" data-conditional-field-item="{{ json_encode($field['conditions']) }}">
    {{ $slot }}
</div>
