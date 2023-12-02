<?php

namespace Feadmin\Services;

use App\Models\User;
use Feadmin\Models\Role;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RoleService
{
    public function createMissingPermissions(array $permissions): void
    {
        $table = config('permission.table_names.permissions');

        $existingPermissions = DB::table($table)
            ->whereIn('name', $permissions)
            ->pluck('name')
            ->toArray();

        $missingPermissions = array_diff($permissions, $existingPermissions);

        DB::table($table)->insert(
            array_map(fn ($permission) => [
                'name' => $permission,
                'guard_name' => 'web',
                'created_at' => $now = now(),
                'updated_at' => $now,
            ], $missingPermissions)
        );
    }

    public function getAssignableRolesFor(User $user): Collection
    {
        return Role::query()
            ->when(! $user->hasRole('Super Admin'), function ($query) {
                return $query->whereNotIn('name', ['Super Admin']);
            })
            ->get();
    }
}
