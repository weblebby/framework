<?php

namespace Weblebby\Framework\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Weblebby\Framework\Models\Role;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('user:update');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:191'],
            'email' => ['required', 'string', 'email', 'max:191'],
        ];

        $rules['role'] = [
            'required',
            Rule::exists(Role::class, 'id')->when(
                ! $this->user()->hasRole('Super Admin'),
                fn ($query) => $query->whereNot('name', 'Super Admin'),
            ),
        ];

        return $rules;
    }
}
