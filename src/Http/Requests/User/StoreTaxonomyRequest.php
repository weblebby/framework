<?php

namespace Weblebby\Framework\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Weblebby\Extensions\Multilingual\Support\LocaleRules;
use Weblebby\Framework\Facades\Extension;
use Weblebby\Framework\Facades\PostModels;
use Weblebby\Framework\Items\TaxonomyItem;
use Weblebby\Framework\Services\FieldInputService;
use Weblebby\Framework\Services\FieldValidationService;

class StoreTaxonomyRequest extends FormRequest
{
    public readonly array $rulesAndAttributes;

    public readonly ?TaxonomyItem $taxonomyItem;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->taxonomyItem &&
            $this->user()->can($this->taxonomyItem->abilityFor('create'));
    }

    protected function prepareForValidation(): void
    {
        $this->taxonomyItem = PostModels::taxonomy($this->taxonomy);

        $fields = $this->taxonomyItem->fieldSections()->allFields();

        /** @var FieldInputService $fieldInputService */
        $fieldInputService = app(FieldInputService::class);
        /** @var FieldValidationService $fieldValidationService */
        $fieldValidationService = app(FieldValidationService::class);

        $fieldValues = $fieldInputService->getFieldValues($fields, $this->all());
        $this->rulesAndAttributes = $fieldValidationService->get($fields, $fieldValues);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $additionalRules = [];

        if (Extension::has('multilingual')) {
            $additionalRules['_locale'] = LocaleRules::get();
        }

        return [
            'title' => [
                'required', 'string', 'max:191',
                (new Unique('term_translations'))
                    ->when(
                        $this->route('taxonomy'),
                        fn (Unique $unique, $taxonomy) => $unique->ignore($taxonomy->term->translate($this->input('_locale')))
                    ),
            ],
            'slug' => [
                'nullable', 'string', 'max:191',
                (new Unique('term_translations'))
                    ->when(
                        $this->route('taxonomy'),
                        fn (Unique $unique, $taxonomy) => $unique->ignore($taxonomy->term->translate($this->input('_locale')))
                    ),
            ],
            'parent_id' => [
                'nullable', 'integer',
                Rule::exists('taxonomies', 'id')
                    ->where('taxonomy', $this->taxonomy),
            ],
            ...$additionalRules,
            ...$this->rulesAndAttributes['rules'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => __('Başlık'),
            'slug' => __('URL'),
            'parent_id' => __('Üst :taxonomy', ['taxonomy' => $this->taxonomyItem->singularName()]),
            '_locale' => __('Dil'),
            ...$this->rulesAndAttributes['attributes'],
        ];
    }
}
