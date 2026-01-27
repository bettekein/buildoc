<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Customer;
use App\Models\Project;

class ProjectSeeder extends Seeder
{
    public function run()
    {
        // Get the first tenant (created by DatabaseSeeder)
        $tenant = Tenant::first();
        
        if (!$tenant) {
            $this->command->error('No tenant found. Please run DatabaseSeeder first.');
            return;
        }

        // Login as a user of this tenant to pass the Global Scope if active, 
        // OR manually set tenant_id. Since we are in seeder, we can manually set it, 
        // but GlobalScope applies on queries. Creating is fine if we set tenant_id.
        // However, the BelongsToTenant trait AUTO-SETS tenant_id if auth check passes.
        // If no auth, we must set it manually.

        $customer = Customer::create([
            'tenant_id' => $tenant->id,
            'name' => '株式会社 サンプル建設',
            'representative_name' => '山田 太郎',
            'phone' => '03-1234-5678',
            'address' => '東京都千代田区1-1-1',
        ]);

        $project = Project::create([
            'tenant_id' => $tenant->id,
            'customer_id' => $customer->id,
            'name' => 'テスト案件：オフィス改修工事',
            'period_start' => now(),
            'period_end' => now()->addMonths(3),
            'status' => '見積中',
        ]);

        $this->command->info('Test Project Created: ' . $project->name);
    }
}
