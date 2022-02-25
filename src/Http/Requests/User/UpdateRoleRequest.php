<?php

namespace Feadmin\Http\Requests\User;

use Core\Facades\PermissionManager;
use Feadmin\Facades\Feadmin;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UpdateRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return !$this->role->is_default && $this->user()->can('role:update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => [
                'required', 'string', 'max:191',
                Rule::unique(Role::class)->ignore($this->route('role')),
            ],
            'permissions' => ['required', 'array'],
            'permissions.*' => [
                'required', 'string', 'max:191',
                Rule::in(Feadmin::panel()->permissions()->keys()),
            ],
        ];
    }
}
