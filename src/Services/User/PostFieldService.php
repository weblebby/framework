<?php

namespace Weblebby\Framework\Services\User;

use Illuminate\Support\Str;
use Weblebby\Framework\Contracts\Eloquent\PostInterface;
use Weblebby\Framework\Items\Field\FieldValueItem;
use Weblebby\Framework\Services\FieldInputService;
use Weblebby\Framework\Services\FieldValidationService;

class PostFieldService
{
    public function sections(PostInterface $postable, array $input): array
    {
        /** @var FieldInputService $fieldInputService */
        $fieldInputService = app(FieldInputService::class);

        $sections = $postable::getPostSections()
            ->withTemplateSections($postable, $input['template'] ?? null)
            ->toArray();

        foreach ($sections as $key => $section) {
            $sections[$key]['field_values'] = $fieldInputService->getFieldValues($section['fields'], $input);
        }

        return $sections;
    }

    /**
     * @return array<string, array|FieldValueItem>
     */
    public function fieldValues(PostInterface $postable, array $input): array
    {
        $fields = [];
        $sections = $this->sections($postable, $input);

        foreach ($sections as $section) {
            $fields = array_replace_recursive($fields, $section['field_values']);
        }

        return $fields;
    }

    public function dottedFieldValues(PostInterface $postable, array $input): array
    {
        /** @var FieldInputService $fieldInputService */
        $fieldInputService = app(FieldInputService::class);

        return $fieldInputService->getDottedFieldValues(
            $this->fieldValues($postable, $input)
        );
    }

    /**
     * @return array<string, FieldValueItem>
     */
    public function dottedMetafieldValues(PostInterface $postable, array $input): array
    {
        $dottedMetafieldValues = [];
        $dottedFieldValues = $this->dottedFieldValues($postable, $input);

        foreach ($dottedFieldValues as $key => $value) {
            if (Str::startsWith($key, 'fields.')) {
                $key = Str::after($key, 'fields.');
                $dottedMetafieldValues[$key] = $value;
            }
        }

        return $dottedMetafieldValues;
    }

    public function rulesAndAttributes(PostInterface $postable, array $input): array
    {
        /** @var FieldValidationService $fieldValidationService */
        $fieldValidationService = app(FieldValidationService::class);

        $sections = $this->sections($postable, $input);
        $rulesAndAttributes = [];

        foreach ($sections as $section) {
            $rulesAndAttributes = array_replace_recursive(
                $rulesAndAttributes,
                $fieldValidationService->get($section['fields'], $section['field_values'])
            );
        }

        return $rulesAndAttributes;
    }
}
