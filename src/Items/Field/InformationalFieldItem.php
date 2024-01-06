<?php

namespace Feadmin\Items\Field;

class InformationalFieldItem extends FieldItem
{
    protected string $body;

    public function body(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'body' => $this->body,
        ]);
    }
}
