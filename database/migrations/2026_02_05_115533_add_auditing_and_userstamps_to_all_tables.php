<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'users',
            'tenants',
            'customers',
            'projects',
            'quotation_items',
            'quotation_details',
            'staff',
            'vehicles',
            'tools',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                // Userstamps
                if (!Schema::hasColumn($table->getTable(), 'created_by')) {
                    $table->unsignedBigInteger('created_by')->nullable()->after('created_at');
                }
                if (!Schema::hasColumn($table->getTable(), 'updated_by')) {
                    $table->unsignedBigInteger('updated_by')->nullable()->after('updated_at');
                }
                if (!Schema::hasColumn($table->getTable(), 'deleted_by')) {
                    $table->unsignedBigInteger('deleted_by')->nullable()->after('updated_by');
                }

                // SoftDeletes
                if (!Schema::hasColumn($table->getTable(), 'deleted_at')) {
                    $table->softDeletes()->after('updated_at');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Dropping columns is tricky if we don't know if they existed before,
        // but for this dev environment, we can optionally attempt to drop them or just leave them.
        // For safety/strictness, we might skip full rollback logic or implement it carefully.

        $tables = [
            'users',
            'tenants',
            'customers',
            'projects',
            'quotation_items',
            'quotation_details',
            'staff',
            'vehicles',
            'tools',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                // Check if columns exist before dropping to prevent errors on partial rollbacks
                if (Schema::hasColumn($table->getTable(), 'created_by')) {
                    $table->dropColumn('created_by');
                }
                if (Schema::hasColumn($table->getTable(), 'updated_by')) {
                    $table->dropColumn('updated_by');
                }
                if (Schema::hasColumn($table->getTable(), 'deleted_by')) {
                    $table->dropColumn('deleted_by');
                }
                if (Schema::hasColumn($table->getTable(), 'deleted_at')) {
                    $table->dropSoftDeletes(); // Laravel's way to drop softDeletes column
                }
            });
        }
    }
};
