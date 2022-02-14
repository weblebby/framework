<?php

namespace Feadmin\Services;

use Feadmin\Models\Role;
use App\Models\User;
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

        $unexistingPermissions = array_diff($permissions, $existingPermissions);

        DB::table($table)
            ->insert(
                array_map(fn ($permission) => [
                    'name' => $permission,
                    'guard_name' => 'web',
                    'created_at' => $now = now(),
                    'updated_at' => $now,
                ], $unexistingPermissions)
            );
    }

    public function getAssignableRolesFor(User $user): Collection
    {
        return Role::query()
            ->when(!$user->hasRole('Super Admin'), function ($query) {
                return $query->whereNotIn('name', ['Super Admin']);
            })
            ->get();
    }
}
