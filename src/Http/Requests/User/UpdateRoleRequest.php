<?php

namespace Feadmin\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UpdateRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return ! $this->role->is_default && $this->user()->can('role:update');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required', 'string', 'max:191',
                Rule::unique(Role::class)->ignore($this->route('role')),
            ],
            'permissions' => ['required', 'array'],
            'permissions.*' => [
                'required', 'string', 'max:191',
                Rule::in(panel()->permission()->keys()),
            ],
        ];
    }
}
