@props(['field'])

@php($default = preference($field['name'], $field['default'] ?? null))

<x-feadmin::form.group :name="$field['name']">
    <div>
        @if ($field['type'] !== 'checkbox')
            <x-feadmin::form.label>{{ $field['label'] }}</x-feadmin::form.label>
        @endif
        @if (isset($field['hint']))
            <x-feadmin::form.hint>{{ $field['hint'] }}</x-feadmin::form.hint>
        @endif
    </div>
    @switch ($field['type'])
        @case('text')
        @case('tel')
        @case('number')
            <x-feadmin::form.input :type="$field['type']" :default="$default" autofocus />
            @break
        @case('richtext')
            <x-feadmin::form.textarea :default="$default" data-ckeditor />
            @break
        @case('image')
            <x-feadmin::form.image :image="$default" />
            @break
        @case('textarea')
            <x-feadmin::form.textarea :default="$default" rows="4" autofocus />
            @break
        @case('checkbox')
            <x-feadmin::form.checkbox :default="$default" :label="$field['label']" />
            @break
        @case('select')
            <x-feadmin::form.select :default="$default">
                <option value="" selected disabled>{{ t('Bir öğe seçin', 'admin') }}</option>
                @foreach ($field['options'] as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </x-feadmin::form.select>
            @break
    @endswitch
</x-feadmin::form.group>