<?php

namespace Feadmin\Http\Requests\User;

use Core\Facades\Localization;
use Feadmin\Models\Locale;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTranslationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('locale:translate');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'code' => ['required', Rule::exists(Locale::class)],
            'group' => ['required', 'string', Rule::in(Localization::groups()->keys())],
            'key' => [
                'required',
                'string',
                Rule::exists('locale_translations')
                    ->where('group', $this->group)
                    ->where('locale_id', Localization::getDefaultLocaleId())
            ],
            'value' => ['required', 'string'],
        ];
    }
}
