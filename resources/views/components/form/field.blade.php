@props(['field'])

@if ($field['type'] === \Feadmin\Enums\FieldTypeEnum::REPEATED)
    <x-feadmin::form.repeated :field="$field">
        @foreach ($field['fields'] as $field)
            <x-feadmin::form.field :field="$field" />
        @endforeach
    </x-feadmin::form.repeated>
@else
    @php($default ??= $field['default'] ?? null)

    <x-feadmin::form.group :name="$field['name']">
        <div>
            @if (!$field['type']->isLabelFree())
                <x-feadmin::form.label>{{ $field['label'] }}</x-feadmin::form.label>
            @endif
            @if (isset($field['hint']))
                <x-feadmin::form.hint>{{ $field['hint'] }}</x-feadmin::form.hint>
            @endif
        </div>
        @switch ($field['type'])
            @case(\Feadmin\Enums\FieldTypeEnum::PARAGRAPH)
                <p class="fd-text-zinc-600">{{ $field['default'] }}</p>
                @break
            @case(\Feadmin\Enums\FieldTypeEnum::TEXT)
            @case(\Feadmin\Enums\FieldTypeEnum::TEL)
            @case(\Feadmin\Enums\FieldTypeEnum::NUMBER)
                <x-feadmin::form.input :type="$field['type']->value" :default="$default" autofocus />
                @break
            @case(\Feadmin\Enums\FieldTypeEnum::RICH_TEXT)
                <x-feadmin::form.textarea :default="$default" data-ckeditor />
                @break
            @case(\Feadmin\Enums\FieldTypeEnum::IMAGE)
                <x-feadmin::form.image :image="$default" />
                @break
            @case(\Feadmin\Enums\FieldTypeEnum::TEXT_AREA)
                <x-feadmin::form.textarea :default="$default" rows="4" autofocus />
                @break
            @case(\Feadmin\Enums\FieldTypeEnum::CHECKBOX)
                <x-feadmin::form.checkbox :default="$default" :label="$field['label']" />
                @break
            @case(\Feadmin\Enums\FieldTypeEnum::SELECT)
                <x-feadmin::form.select :default="$default">
                    <option value="" selected disabled>{{ __('Bir öğe seçin') }}</option>
                    @foreach ($field['options'] as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </x-feadmin::form.select>
                @break
        @endswitch
    </x-feadmin::form.group>
@endif
