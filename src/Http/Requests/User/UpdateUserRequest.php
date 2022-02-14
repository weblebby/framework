<?php

namespace Feadmin\Http\Requests\User;

use Feadmin\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('user:update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'first_name' => ['required', 'string', 'max:191'],
            'last_name' => ['required', 'string', 'max:191'],
            'email' => ['required', 'string', 'email', 'max:191'],
        ];

        $rules['role'] = [
            'required',
            Rule::exists(Role::class, 'id')->when(
                !$this->user()->hasRole('Super Admin'),
                fn ($query) =>  $query->whereNot('name', 'Super Admin'),
            )
        ];

        return $rules;
    }
}
