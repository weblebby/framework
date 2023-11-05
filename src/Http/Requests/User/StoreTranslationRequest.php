<?php

namespace Feadmin\Http\Requests\User;

use Feadmin\Facades\Localization;
use Feadmin\Models\Locale;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTranslationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('locale:translate');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'code' => ['required', Rule::exists(Locale::class)],
            'key' => [
                'required',
                'string',
                Rule::in(array_keys(Localization::getTranslations())),
            ],
            'value' => ['required', 'string'],
        ];
    }
}
