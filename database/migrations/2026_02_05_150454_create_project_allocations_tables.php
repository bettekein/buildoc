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
        // 案件×スタッフ 配置テーブル
        Schema::create('project_staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_foreman')->default(false); // 職長フラグ
            $table->string('role')->nullable(); // 役割・担当業務
            $table->date('start_date')->nullable(); // 入場開始日
            $table->date('end_date')->nullable(); // 入場終了日
            $table->timestamps();

            $table->unique(['project_id', 'staff_id']);
        });

        // 案件×車両 配置テーブル
        Schema::create('project_vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->date('start_date')->nullable(); // 使用開始日
            $table->date('end_date')->nullable(); // 使用終了日
            $table->timestamps();

            $table->unique(['project_id', 'vehicle_id']);
        });

        // 案件×工具 配置テーブル
        Schema::create('project_tools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tool_id')->constrained()->cascadeOnDelete();
            $table->date('start_date')->nullable(); // 使用開始日
            $table->date('end_date')->nullable(); // 使用終了日
            $table->timestamps();

            $table->unique(['project_id', 'tool_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_tools');
        Schema::dropIfExists('project_vehicles');
        Schema::dropIfExists('project_staff');
    }
};
