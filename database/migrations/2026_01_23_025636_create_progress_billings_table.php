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
        Schema::create('progress_billings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->integer('billing_round');
            $table->decimal('progress_rate', 5, 2)->default(0); // e.g., 50.00
            $table->decimal('amount_this_time', 15, 2)->default(0);
            $table->decimal('retention_money', 15, 2)->default(0);
            $table->string('status')->default('unbilled'); // unbilled, billed, paid
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress_billings');
    }
};
