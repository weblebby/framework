<?php

namespace Weblebby\Framework\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Weblebby\Framework\Http\Requests\User\StoreRoleRequest;
use Weblebby\Framework\Http\Requests\User\UpdateRoleRequest;
use Weblebby\Framework\Models\Role;
use Weblebby\Framework\Services\RoleService;

class RoleController extends Controller
{
    public function index(): View
    {
        $this->authorize('role:read');

        $roles = Role::query()->paginate();

        seo()->title(__('Kullanıcı rolleri'));

        return view('weblebby::user.roles.index', compact('roles'));
    }

    public function create(): View
    {
        $this->authorize('role:create');

        seo()->title(__('Kullanıcı rolü oluştur'));

        return view('weblebby::user.roles.create');
    }

    public function store(StoreRoleRequest $request, RoleService $roleService): RedirectResponse
    {
        /** @var Role $role */
        $role = Role::create(['name' => $request->name]);

        $roleService->createMissingPermissions($request->permissions);

        foreach ($request->permissions as $permission) {
            $role->givePermissionTo($permission);
        }

        return to_panel_route('roles.index')->with('message', __('Rol oluşturuldu'));
    }

    public function edit(Role $role): View
    {
        $this->authorize('role:update');

        seo()->title($role->name);

        return view('weblebby::user.roles.edit', compact('role'));
    }

    public function update(UpdateRoleRequest $request, Role $role, RoleService $roleService): RedirectResponse
    {
        $role->update(['name' => $request->name]);

        $roleService->createMissingPermissions($request->permissions);

        $role->syncPermissions($request->permissions);

        return back()->with('message', __('Rol güncellendi'));
    }

    public function destroy(Role $role): RedirectResponse
    {
        $this->authorize('role:delete');
        abort_if($role->is_default, 403);

        $role->delete();

        return to_panel_route('roles.index')->with('message', __('Rol silindi'));
    }
}
