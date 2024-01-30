<?php /** @var \Weblebby\Framework\Items\Field\Contracts\FieldInterface $field */ ?>

<div class="fd-space-y-3">
    @foreach ($fields as $field)
        <x-weblebby::form.field :field="$field" />
    @endforeach
</div>
