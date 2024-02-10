@props(['field', 'default' => null, 'withErrors' => true])

@if ($field['type'] === \Weblebby\Framework\Enums\FieldTypeEnum::REPEATED)
    <x-weblebby::form.repeated :field="$field" :default="$default">
        @foreach ($field['fields'] as $field)
            <x-weblebby::form.field :field="$field" :with-errors="false" />
        @endforeach
    </x-weblebby::form.repeated>
@elseif ($field['type'] === \Weblebby\Framework\Enums\FieldTypeEnum::CONDITIONAL)
    <x-weblebby::form.conditional :field="$field">
        @foreach ($field['fields'] as $field)
            <x-weblebby::form.field :field="$field" :with-errors="false" />
        @endforeach
    </x-weblebby::form.conditional>
@else
    @php($default ??= $field['default'] ?? null)

    <x-weblebby::form.group
            :name="$field['name'] ?? null"
            :data-form-field-key="$field['key']"
            :with-errors="$withErrors"
            :hidden="$field['type']->isHidden()"
    >
        <div>
            @if (!$field['type']->isLabelFree())
                <x-weblebby::form.label>{{ $field['label'] }}</x-weblebby::form.label>
            @endif
            @if (isset($field['hint']))
                <x-weblebby::form.hint>{{ $field['hint'] }}</x-weblebby::form.hint>
            @endif
        </div>
        @switch ($field['type'])
            @case(\Weblebby\Framework\Enums\FieldTypeEnum::PARAGRAPH)
                <p class="fd-text-zinc-600">{{ $field['body'] }}</p>
                @break
            @case(\Weblebby\Framework\Enums\FieldTypeEnum::HIDDEN)
            @case(\Weblebby\Framework\Enums\FieldTypeEnum::TEXT)
            @case(\Weblebby\Framework\Enums\FieldTypeEnum::TEL)
            @case(\Weblebby\Framework\Enums\FieldTypeEnum::NUMBER)
                <x-weblebby::form.input
                        :attributes="$field['attributes']"
                        :type="$field['type']->value"
                        :default="$default"
                        :translatable="$field['translatable'] ?? false"
                />
                @break
            @case(\Weblebby\Framework\Enums\FieldTypeEnum::COLOR)
                <x-weblebby::form.color :attributes="$field['attributes']" :default="$default" />
                @break
            @case(\Weblebby\Framework\Enums\FieldTypeEnum::FILE)
                <x-weblebby::form.file
                        :attributes="$field['attributes']"
                        :default="$default"
                        :translatable="$field['translatable'] ?? false"
                />
                @break
            @case(\Weblebby\Framework\Enums\FieldTypeEnum::RICH_TEXT)
                <x-weblebby::form.textarea
                        :attributes="$field['attributes']"
                        :default="$default"
                        :translatable="$field['translatable'] ?? false"
                        data-ckeditor
                />
                @break
            @case(\Weblebby\Framework\Enums\FieldTypeEnum::CODE_EDITOR)
                <x-weblebby::form.code-editor
                        :attributes="$field['attributes']"
                        :default="$default"
                        :data-code-editor="json_encode($field['editor'])"
                        class="fd-h-60"
                />
                @break
            @case(\Weblebby\Framework\Enums\FieldTypeEnum::IMAGE)
                <x-weblebby::form.image
                        :attributes="$field['attributes']"
                        :image="$default"
                        :translatable="$field['translatable'] ?? false"
                />
                @break
            @case(\Weblebby\Framework\Enums\FieldTypeEnum::TEXT_AREA)
                <x-weblebby::form.textarea
                        rows="4"
                        :attributes="$field['attributes']"
                        :default="$default"
                        :translatable="$field['translatable'] ?? false"
                        autofocus
                />
                @break
            @case(\Weblebby\Framework\Enums\FieldTypeEnum::CHECKBOX)
                <x-weblebby::form.checkbox
                        :attributes="$field['attributes']"
                        :default="$default"
                        :label="$field['label']"
                        :use-hidden-input="true"
                        value="1"
                />
                @break
            @case(\Weblebby\Framework\Enums\FieldTypeEnum::SELECT)
                <x-weblebby::form.select :attributes="$field['attributes']" :default="$default">
                    <option value="" selected disabled>{{ __('Bir öğe seçin') }}</option>
                    @foreach ($field['options'] as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </x-weblebby::form.select>
                @break
        @endswitch
    </x-weblebby::form.group>
@endif
