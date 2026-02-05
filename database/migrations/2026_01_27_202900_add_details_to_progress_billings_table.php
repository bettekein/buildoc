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
        Schema::table('progress_billings', function (Blueprint $table) {
            $table->decimal('cumulative_amount', 15, 2)->default(0);
            $table->decimal('previous_billed_amount', 15, 2)->default(0);
            $table->decimal('retention_release_amount', 15, 2)->default(0);
            $table->decimal('offset_amount', 15, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('progress_billings', function (Blueprint $table) {
            //
        });
    }
};
