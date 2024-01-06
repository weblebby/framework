<?php

namespace Feadmin\Services\User;

use Feadmin\Contracts\Eloquent\PostInterface;
use Feadmin\Items\Field\FieldValueItem;
use Feadmin\Services\FieldInputService;
use Feadmin\Services\FieldValidationService;
use Illuminate\Support\Str;

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
