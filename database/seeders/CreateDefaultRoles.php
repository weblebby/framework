<?php

namespace Weblebby\Framework\Database\Seeders;

use Illuminate\Database\Seeder;
use Weblebby\Framework\Models\Role;

class CreateDefaultRoles extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Role::query()->count() > 0) {
            return;
        }

        Role::create(['name' => 'Super Admin']);
    }
}
