<?php

namespace Feadmin\Http\Requests\User;

use Feadmin\Facades\Extension;
use Feadmin\Facades\Preference;
use Feadmin\Services\FieldInputService;
use Feadmin\Services\FieldValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Weblebby\Extensions\Multilingual\Support\LocaleRules;

class UpdatePreferenceRequest extends FormRequest
{
    public readonly array $rulesAndAttributes;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $fields = Preference::fields($this->route('namespace'), $this->route('bag'));

        /** @var FieldInputService $fieldInputService */
        $fieldInputService = app(FieldInputService::class);
        /** @var FieldValidationService $fieldValidationService */
        $fieldValidationService = app(FieldValidationService::class);

        $fieldValues = $fieldInputService->getFieldValues($fields, $this->all());
        $this->rulesAndAttributes = $fieldValidationService->get($fields, $fieldValues);
    }

    public function rules(): array
    {
        $additionalRules = [];

        if (Extension::has('multilingual')) {
            $additionalRules['_locale'] = LocaleRules::get();
        }

        return [
            ...$this->rulesAndAttributes['rules'],
            ...$additionalRules,
            '_deleted_fields' => ['nullable', 'array'],
            '_deleted_fields.*' => ['required', 'string', 'max:191'],
            '_reordered_fields' => ['nullable', 'array'],
            '_reordered_fields.*' => ['required', 'string', 'max:191'],
        ];
    }

    public function attributes(): array
    {
        return $this->rulesAndAttributes['attributes'];
    }
}
