<?php

namespace Feadmin\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Feadmin\Http\Requests\User\StoreRoleRequest;
use Feadmin\Http\Requests\User\UpdateRoleRequest;
use Feadmin\Models\Role;
use Feadmin\Services\RoleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        $this->authorize('role:read');

        $roles = Role::paginate();

        seo()->title(t('Kullanıcı rolleri', 'panel'));

        return view('feadmin::user.roles.index', compact('roles'));
    }

    public function create(): View
    {
        $this->authorize('role:create');

        seo()->title(t('Kullanıcı rolü oluştur', 'panel'));

        return view('feadmin::user.roles.create');
    }

    public function store(StoreRoleRequest $request, RoleService $roleService): RedirectResponse
    {
        $role = Role::create(['name' => $request->name]);

        $roleService->createMissingPermissions($request->permissions);

        foreach ($request->permissions as $permission) {
            $role->givePermissionTo($permission);
        }

        return to_panel_route('roles.index')
            ->with('message', t('Rol oluşturuldu', 'panel'));
    }

    public function edit(Role $role): View
    {
        $this->authorize('role:update');

        seo()->title($role->name);

        return view('feadmin::user.roles.edit', compact('role'));
    }

    public function update(UpdateRoleRequest $request, Role $role, RoleService $roleService): RedirectResponse
    {
        $role->update(['name' => $request->name]);

        $roleService->createMissingPermissions($request->permissions);

        $role->syncPermissions($request->permissions);

        return back()->with('message', t('Rol güncellendi', 'panel'));
    }

    public function destroy(Role $role): RedirectResponse
    {
        $this->authorize('role:delete');

        if ($role->is_default) {
            abort(403);
        }

        $role->delete();

        return to_panel_route('roles.index')
            ->with('message', t('Rol silindi', 'panel'));
    }
}
