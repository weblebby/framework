<?php

namespace Weblebby\Framework\Items\Field;

use Weblebby\Framework\Items\Field\Enums\CodeEditorLanguageEnum;

class CodeEditorFieldItem extends TextFieldItem
{
    protected CodeEditorLanguageEnum $editorLanguage;

    public function editorLanguage(CodeEditorLanguageEnum $editorLanguage): self
    {
        $this->editorLanguage = $editorLanguage;

        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'editor' => [
                'language' => $this->editorLanguage->value,
            ],
        ]);
    }
}
