<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'tenants',
            'users',
            'customers',
            'projects',
            'quotation_items',
            'quotation_details',
            'progress_billings',
            'staff',
            'tools',
            'vehicles',
            'green_files',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (!Schema::hasColumn($table->getTable(), 'created_by')) {
                        $table->unsignedBigInteger('created_by')->nullable()->after('created_at');
                        $table->unsignedBigInteger('updated_by')->nullable()->after('updated_at');
                        $table->unsignedBigInteger('deleted_by')->nullable()->after('deleted_at');
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'tenants',
            'users',
            'customers',
            'projects',
            'quotation_items',
            'quotation_details',
            'progress_billings',
            'staff',
            'tools',
            'vehicles',
            'green_files',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn(['created_by', 'updated_by', 'deleted_by']);
                });
            }
        }
    }
};
