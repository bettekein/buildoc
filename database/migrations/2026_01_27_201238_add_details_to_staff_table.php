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
        Schema::table('staff', function (Blueprint $table) {
            $table->string('furigana')->nullable();
            $table->date('birthday')->nullable();
            $table->string('job_type')->nullable();
            $table->integer('experience_years')->nullable();
            $table->date('hiring_date')->nullable();
            $table->json('health_info')->nullable();
            $table->json('insurance_details')->nullable();
            
            if (!Schema::hasColumn('staff', 'emergency_contact')) {
                $table->json('emergency_contact')->nullable();
            }
            if (!Schema::hasColumn('staff', 'blood_type')) {
                $table->string('blood_type')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            //
        });
    }
};
