<?php

namespace Feadmin\Http\Requests\User;

use Feadmin\Facades\Localization;
use Feadmin\Models\Locale;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLocaleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('locale:create');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $remainingLocales = Localization::getRemainingLocales()->keys();

        return [
            'code' => ['required', Rule::unique(Locale::class), Rule::in($remainingLocales)],
            'is_default' => ['nullable', 'boolean'],
        ];
    }
}
