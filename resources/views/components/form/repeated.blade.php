@props(['field'])

@php ($default = old($field['name'], []))

<div
        class="fd-border fd-rounded fd-p-3 fd-space-y-3"
        data-repeated-field-item="{{ $field['name'] }}"
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
        <div class="fd-border fd-rounded fd-p-3 fd-space-y-3" data-repeated-field-row>
            {{ $slot }}
            <x-feadmin::button
                    type="button"
                    variant="danger"
                    icon="x"
                    size="sm"
                    :data-repeated-field-remove-row="true"
            >
                @lang('Kaldır')
            </x-feadmin::button>
        </div>
    </template>
</div>

@push('scripts')
    <script>
      document.addEventListener("DOMContentLoaded", () => {
          @foreach ($default as $index => $value)
          window.Feadmin.RepeatedField.addRow({
            container: @json($field['name']),
            fields: @json($value),
            errors: @json($errors->get(sprintf('%s.%d.*', $field['name'], $index))),
          });
          @endforeach
      });
    </script>
@endpush
