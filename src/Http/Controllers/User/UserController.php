<?php

namespace Feadmin\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Feadmin\Http\Requests\User\StoreUserRequest;
use Feadmin\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use Feadmin\Services\RoleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $this->authorize('user:read');

        $users = User::query()->paginate();

        seo()->title(__('Kullanıcılar'));

        return view('feadmin::user.users.index', compact('users'));
    }

    public function create(Request $request, RoleService $roleService): View
    {
        $this->authorize('user:create');

        $roles = $roleService->getAssignableRolesFor($request->user());

        seo()->title(__('Kullanıcı oluştur'));

        return view('feadmin::user.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = User::query()->create([
            ...$request->validated(),
            'password' => Hash::make($password = Str::random(8)),
        ]);

        $user->assignRole($request->role);

        /**
         * TODO: Send email to user with password.
         */
        info($password);

        return to_panel_route('users.index')->with('success', __('Kullanıcı oluşturuldu'));
    }

    public function edit(Request $request, RoleService $roleService, User $user): View
    {
        $this->authorize('user:update');

        $roles = $roleService->getAssignableRolesFor($request->user());

        seo()->title($user->name);

        return view('feadmin::user.users.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $user->update($request->validated());
        $user->syncRoles($request->role);

        return to_panel_route('users.index')
            ->with('message', __('Kullanıcı güncellendi'));
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('user:delete');

        $user->delete();

        return to_panel_route('users.index')
            ->with('message', __('Kullanıcı silindi'));
    }
}
