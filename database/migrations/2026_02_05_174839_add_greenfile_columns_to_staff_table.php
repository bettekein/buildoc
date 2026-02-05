<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * グリーンファイル（安全書類）に必要な追加カラム
     */
    public function up(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            // 新規入場者調査票・作業員名簿に必要な項目
            $table->date('last_medical_checkup')->nullable()->comment('最終健康診断日');
            $table->date('sendout_education_date')->nullable()->comment('送り出し教育受講日');
            $table->string('employment_type')->nullable()->comment('雇用形態（正社員/契約/一人親方等）');
            $table->boolean('is_sole_proprietor')->default(false)->comment('一人親方・中小事業主フラグ');
            $table->boolean('has_special_labor_insurance')->default(false)->comment('労災保険特別加入フラグ');

            // 資格詳細（取得日・有効期限を含む）
            $table->json('qualification_details')->nullable()->comment('資格詳細（名称,取得日,有効期限の配列）');

            // 技能講習・特別教育
            $table->json('skill_trainings')->nullable()->comment('技能講習（作業主任者等）');
            $table->json('special_educations')->nullable()->comment('特別教育受講歴');

            // 運転免許
            $table->json('driver_licenses')->nullable()->comment('運転免許（種類,取得日,有効期限）');

            // 作業員名簿用追加
            $table->string('family_address')->nullable()->comment('家族連絡先住所');
            $table->string('nationality')->nullable()->comment('国籍（外国人対応）');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn([
                'last_medical_checkup',
                'sendout_education_date',
                'employment_type',
                'is_sole_proprietor',
                'has_special_labor_insurance',
                'qualification_details',
                'skill_trainings',
                'special_educations',
                'driver_licenses',
                'family_address',
                'nationality',
            ]);
        });
    }
};
