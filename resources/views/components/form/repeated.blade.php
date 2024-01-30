@props(['field', 'default' => null])

@php($dottedName = \Weblebby\Framework\Support\FormComponent::nameToDotted($field['name']))
@php($fieldItemName = \Weblebby\Framework\Support\FormComponent::nameToDottedWithoutEmptyWildcard($field['name']))
@php ($default = array_values(
    array_replace_recursive($default ?? [], $field['default'], old($dottedName, []) ?? [])
))

<div
        class="fd-p-3 fd-bg-white fd-border fd-rounded fd-space-y-3"
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
                    <x-weblebby::form.hint>{{ $field['hint'] }}</x-weblebby::form.hint>
                @endif
            </div>
        @endif
        <x-weblebby::button
                type="button"
                variant="light"
                icon="plus"
                size="sm"
                :data-repeated-field-add-row="true"
        >
            @lang('Yeni satır')
        </x-weblebby::button>
    </div>
    <div class="fd-hidden fd-space-y-3" data-repeated-field-rows></div>
    <template data-repeated-field-template>
        <div class="fd-bg-white fd-border fd-rounded fd-p-3 fd-relative fd-space-y-3" data-repeated-field-row>
            <div class="fd-flex fd-items-center fd-justify-between fd-gap-2">
                <div class="fd-flex fd-items-center fd-gap-1">
                    <x-weblebby::button
                            type="button"
                            icon="caret-right-fill"
                            class="fd-transition-transform"
                            data-repeated-field-collapse-row="true"
                    >
                    </x-weblebby::button>
                    <x-weblebby::button
                            type="button"
                            variant="light"
                            icon="grip-horizontal"
                            class="fd-cursor-grab active:fd-cursor-grabbing"
                            data-repeated-field-handle-row="true"
                    />
                    <div class="fd-flex fd-items-center fd-gap-3 fd-font-medium fd-text-lg fd-ms-3">
                        <span data-repeated-field-row-iteration>1.</span>
                        <div data-repeated-field-row-iteration-label></div>
                    </div>
                </div>
                <x-weblebby::button
                        type="button"
                        variant="red"
                        icon="x"
                        :data-repeated-field-remove-row="true"
                />
            </div>
            <div class="fd-space-y-3 fd-hidden" data-repeated-field-row-content>
                {{ $slot }}
            </div>
        </div>
    </template>
</div>

@pushonce('after_scripts')
    <template data-file-default-template>
        <x-weblebby::form.file-default :default="''" />
    </template>
@endpushonce

@push('after_scripts')
    <script>
      document.addEventListener("DOMContentLoaded", () => {
          @foreach ($default as $index => $value)
          window.Weblebby.RepeatedField.addRow({
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
