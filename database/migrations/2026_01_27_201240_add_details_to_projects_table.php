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
        Schema::table('projects', function (Blueprint $table) {
            $table->string('site_address')->nullable();
            $table->text('work_description')->nullable();
            $table->string('order_number')->nullable();
            $table->string('project_number')->nullable();
            $table->date('contract_date')->nullable();
            $table->decimal('retention_rate', 5, 2)->default(20.00);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            //
        });
    }
};
