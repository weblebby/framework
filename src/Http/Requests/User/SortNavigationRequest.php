<?php

namespace Feadmin\Http\Requests\User;

use Feadmin\Models\NavigationItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SortNavigationRequest extends FormRequest
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
            'items' => ['required', 'array'],
            'items.*.id' => ['required', Rule::exists(NavigationItem::class, 'id')],
        ];
    }
}
