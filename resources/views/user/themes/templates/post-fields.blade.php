<?php /** @var \Feadmin\Concerns\Fieldable $field */ ?>

<div class="fd-space-y-3">
    @foreach ($fields as $field)
        <x-feadmin::form.field :field="$field" />
    @endforeach
</div>
