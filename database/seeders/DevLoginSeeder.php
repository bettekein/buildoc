<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DevLoginSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure Super Admin Role exists
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);

        // 1. Super Admin
        // We might want to attach them to a tenant or make them tenant-less if allowed.
        // Assuming they belong to the first tenant or a system tenant.
        $tenant = Tenant::firstOrCreate([
            'company_name' => 'Buildoc System',
        ], [
            'license_number' => '000000',
        ]);

        $admin = User::firstOrCreate([
            'email' => 'admin@buildoc.test',
        ], [
            'name' => 'Super Admin',
            'password' => Hash::make('password'),
            'tenant_id' => $tenant->id,
        ]);

        if (!$admin->hasRole('Super Admin')) {
            $admin->assignRole('Super Admin');
        }

        // 2. Tenant Admin (General User)
        $userTenant = Tenant::firstOrCreate([
            'company_name' => 'General Construction Corp',
        ], [
            'license_number' => '999999',
            'zip_code' => '100-0001',
            'address' => 'Tokyo, Chiyoda',
            'phone' => '03-1234-5678',
        ]);

        User::firstOrCreate([
            'email' => 'user@buildoc.test',
        ], [
            'name' => 'Tenant User',
            'password' => Hash::make('password'),
            'tenant_id' => $userTenant->id,
        ]);
    }
}
