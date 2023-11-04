<?php

namespace Database\Seeders;

use Feadmin\Models\Role;
use Illuminate\Database\Seeder;

class CreateDefaultRoles extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['name' => 'Super Admin']);
    }
}
