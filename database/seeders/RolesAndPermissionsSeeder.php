<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guard = 'web';

        Permission::firstOrCreate(
            ['name' => 'view audit logs', 'guard_name' => $guard]
        );
        Permission::firstOrCreate(
            ['name' => 'approve change requests', 'guard_name' => $guard]
        );

        $userRole = Role::firstOrCreate(
            ['name' => 'user', 'guard_name' => $guard]
        );
        $userRole->syncPermissions(['view audit logs']);

        $adminRole = Role::firstOrCreate(
            ['name' => 'admin', 'guard_name' => $guard]
        );
        $adminRole->syncPermissions(['view audit logs', 'approve change requests']);
    }
}
