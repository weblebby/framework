<?php

namespace Feadmin\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreGeneralSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'general::name' => ['required', 'string', 'max:191'],
            'general::url' => ['required', 'url'],
            'general::copyright' => ['required', 'string', 'max:191'],
        ];
    }
}
