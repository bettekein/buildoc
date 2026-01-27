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
            $table->string('billing_number')->nullable()->after('project_id'); // 請求書番号
            $table->date('billing_date')->nullable()->after('billing_round');
            $table->date('payment_date')->nullable()->after('billing_date');
            $table->decimal('tax_amount', 15, 2)->default(0)->after('amount_this_time');
            $table->text('note')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('progress_billings', function (Blueprint $table) {
            $table->dropColumn(['billing_number', 'billing_date', 'payment_date', 'tax_amount', 'note']);
        });
    }
};
