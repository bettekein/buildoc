<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create roles and assign existing permissions
        $role = \Spatie\Permission\Models\Role::create(['name' => 'Super Admin']);

        $user = \App\Models\User::where('email', 'admin@buildoc.test')->first();
        if ($user) {
            $user->assignRole($role);
        }
    }
}
