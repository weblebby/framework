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

        $users = User::paginate();

        seo()->title(t('Kullanıcılar', 'panel'));

        return view('feadmin::user.users.index', compact('users'));
    }

    public function create(Request $request, RoleService $roleService): View
    {
        $this->authorize('user:create');

        $roles = $roleService->getAssignableRolesFor($request->user());

        seo()->title(t('Kullanıcı oluştur', 'panel'));

        return view('feadmin::user.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = User::create($request->validated() + [
            'password' => Hash::make($password = Str::random(8)),
        ]);

        $user->assignRole($request->role);

        /**
         * TODO: Send email to user with password.
         */
        info($password);

        return redirect()
            ->route('admin::users.index')
            ->with('success', t('Kullanıcı oluşturuldu', 'panel'));
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

        return redirect()
            ->route('admin::users.index')
            ->with('message', t('Kullanıcı güncellendi', 'panel'));
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('user:delete');

        $user->delete();

        return redirect()
            ->route('admin::users.index')
            ->with('message', t('Kullanıcı silindi', 'panel'));
    }
}
