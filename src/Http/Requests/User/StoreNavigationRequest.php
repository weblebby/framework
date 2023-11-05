<?php

namespace Feadmin\Http\Requests\User;

use Feadmin\Models\Navigation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreNavigationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('navigation:create');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:191'],
            'handle' => ['nullable', 'string', 'max:191', Rule::unique(Navigation::class)],
        ];
    }
}
