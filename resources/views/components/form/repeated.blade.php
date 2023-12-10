@props(['field'])

@php($dottedName = \Feadmin\Support\FormComponent::nameToDotted($field['name']))
@php($fieldItemName = \Feadmin\Support\FormComponent::nameToDottedWithoutEmptyWildcard($field['name']))
@php ($default = array_values(
    array_replace_recursive($field['default'], old($dottedName, []))
))

<div
        class="fd-border fd-rounded fd-p-3 fd-space-y-3"
        data-repeated-field-item="{{ $fieldItemName }}"
        data-max-row="{{ $field['max'] ?? -1 }}"
>
    <div class="fd-flex fd-items-center fd-justify-between fd-gap-2">
        @if (filled($field['label']) || filled($field['hint']))
            <div>
                @if (filled($field['label']))
                    <h3 class="fd-font-medium fd-text-lg">{{ $field['label'] }}</h3>
                @endif
                @if (filled($field['hint']))
                    <x-feadmin::form.hint>{{ $field['hint'] }}</x-feadmin::form.hint>
                @endif
            </div>
        @endif
        <x-feadmin::button
                type="button"
                variant="light"
                icon="plus"
                size="sm"
                :data-repeated-field-add-row="true"
        >
            @lang('Yeni satır')
        </x-feadmin::button>
    </div>
    <div class="fd-hidden fd-space-y-3" data-repeated-field-rows></div>
    <template data-repeated-field-template>
        <div class="fd-border fd-rounded fd-p-3 fd-relative" data-repeated-field-row>
            <div class="fd-space-y-3">
                {{ $slot }}
            </div>
            <x-feadmin::button
                    class="fd-absolute fd-right-0 fd-top-0 -fd-translate-y-1/2 fd-translate-x-1/2"
                    type="button"
                    variant="danger"
                    icon="x"
                    :data-repeated-field-remove-row="true"
            />
        </div>
    </template>
</div>

@push('scripts')
    <script>
      document.addEventListener("DOMContentLoaded", () => {
          @foreach ($default as $index => $value)
          window.Feadmin.RepeatedField.addRow({
            itemContainer: @json($fieldItemName),
            fields: @json($value),
            errors: @json($errors->get(sprintf('%s.%d.*', $dottedName, $index))),
            dottedName: @json($dottedName),
            index: @json($index),
          });
          @endforeach
      });
    </script>
@endpush
