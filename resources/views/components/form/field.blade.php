@props(['field', 'default' => null, 'withErrors' => true])

@if ($field['type'] === \Feadmin\Enums\FieldTypeEnum::REPEATED)
    <x-feadmin::form.repeated :field="$field" :default="$default">
        @foreach ($field['fields'] as $field)
            <x-feadmin::form.field :field="$field" :with-errors="false" />
        @endforeach
    </x-feadmin::form.repeated>
@elseif ($field['type'] === \Feadmin\Enums\FieldTypeEnum::CONDITIONAL)
    <x-feadmin::form.conditional :field="$field">
        @foreach ($field['fields'] as $field)
            <x-feadmin::form.field :field="$field" :with-errors="false" />
        @endforeach
    </x-feadmin::form.conditional>
@else
    @php($default ??= $field['default'] ?? null)

    <x-feadmin::form.group
            :name="$field['name'] ?? null"
            :data-form-field-key="$field['key']"
            :with-errors="$withErrors"
    >
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
                <p class="fd-text-zinc-600">{{ $field['body'] }}</p>
                @break
            @case(\Feadmin\Enums\FieldTypeEnum::TEXT)
            @case(\Feadmin\Enums\FieldTypeEnum::TEL)
            @case(\Feadmin\Enums\FieldTypeEnum::NUMBER)
                <x-feadmin::form.input
                        :attributes="$field['attributes']"
                        :type="$field['type']->value"
                        :default="$default"
                        :translatable="$field['translatable'] ?? false"
                />
                @break
            @case(\Feadmin\Enums\FieldTypeEnum::RICH_TEXT)
                <x-feadmin::form.textarea
                        :attributes="$field['attributes']"
                        :default="$default"
                        :translatable="$field['translatable'] ?? false"
                        data-ckeditor
                />
                @break
            @case(\Feadmin\Enums\FieldTypeEnum::CODE_EDITOR)
                <x-feadmin::form.code-editor
                        :attributes="$field['attributes']"
                        :default="$default"
                        :data-code-editor="json_encode($field['editor'])"
                />
                @break
            @case(\Feadmin\Enums\FieldTypeEnum::IMAGE)
                <x-feadmin::form.image
                        :attributes="$field['attributes']"
                        :image="$default"
                        :translatable="$field['translatable'] ?? false"
                />
                @break
            @case(\Feadmin\Enums\FieldTypeEnum::TEXT_AREA)
                <x-feadmin::form.textarea
                        rows="4"
                        :attributes="$field['attributes']"
                        :default="$default"
                        :translatable="$field['translatable'] ?? false"
                        autofocus
                />
                @break
            @case(\Feadmin\Enums\FieldTypeEnum::CHECKBOX)
                <x-feadmin::form.checkbox
                        :attributes="$field['attributes']"
                        :default="$default"
                        :label="$field['label']"
                        :use-hidden-input="true"
                        value="1"
                />
                @break
            @case(\Feadmin\Enums\FieldTypeEnum::SELECT)
                <x-feadmin::form.select :attributes="$field['attributes']" :default="$default">
                    <option value="" selected disabled>{{ __('Bir öğe seçin') }}</option>
                    @foreach ($field['options'] as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </x-feadmin::form.select>
                @break
        @endswitch
    </x-feadmin::form.group>
@endif
