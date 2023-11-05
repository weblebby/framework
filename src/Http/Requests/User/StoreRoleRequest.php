<?php

namespace Feadmin\Http\Requests\User;

use Core\Facades\PermissionManager;
use Feadmin\Facades\Panel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class StoreRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('role:create');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:191', Rule::unique(Role::class)],
            'permissions' => ['required', 'array'],
            'permissions.*' => [
                'required', 'string', 'max:191',
                Rule::in(panel()->permission()->keys()),
            ],
        ];
    }
}
