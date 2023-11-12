<?php /** @var \Feadmin\Items\Field\FieldItem $field */ ?>

<div class="fd-space-y-3">
    @foreach ($fields as $field)
        @php($field->name("metafields[{$field['name']}]"))
        <x-feadmin::form.field :field="$field" />
    @endforeach
</div>
