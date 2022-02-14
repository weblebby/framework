<?php

namespace Feadmin\Http\Requests\User;

use Feadmin\Models\NavigationItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SortNavigationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'items' => ['required', 'array'],
            'items.*.id' => ['required', Rule::exists(NavigationItem::class, 'id')],
        ];
    }
}
