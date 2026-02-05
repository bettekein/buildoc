<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * 下請業者マスター（一次〜三次下請対応）
     */
    public function up(): void
    {
        Schema::create('subcontractors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();

            // 会社基本情報
            $table->string('name')->comment('会社名');
            $table->string('name_kana')->nullable()->comment('会社名カナ');
            $table->string('representative_name')->nullable()->comment('代表者氏名');
            $table->string('representative_title')->nullable()->comment('代表者役職');

            // 住所・連絡先
            $table->string('postal_code')->nullable()->comment('郵便番号');
            $table->string('address')->nullable()->comment('住所');
            $table->string('phone')->nullable()->comment('電話番号');
            $table->string('fax')->nullable()->comment('FAX番号');
            $table->string('email')->nullable()->comment('メールアドレス');

            // 建設業許可（複数許可対応）
            $table->json('construction_licenses')->nullable()->comment('建設業許可情報');

            // 安全衛生関連
            $table->string('safety_officer')->nullable()->comment('安全衛生推進者');
            $table->string('employment_manager')->nullable()->comment('雇用管理責任者');
            $table->string('chief_engineer')->nullable()->comment('主任技術者');
            $table->string('specialist_engineer')->nullable()->comment('専門技術者');
            $table->string('specialist_qualification')->nullable()->comment('専門技術者資格');

            // 社会保険
            $table->json('insurance_status')->nullable()->comment('社会保険加入状況');

            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
        });

        // 案件×下請業者の中間テーブル
        Schema::create('project_subcontractors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subcontractor_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('tier')->default(1)->comment('下請階層（1=一次, 2=二次, 3=三次）');
            $table->foreignId('parent_subcontractor_id')->nullable()->constrained('subcontractors')->nullOnDelete()->comment('直上の下請業者');
            $table->string('work_content')->nullable()->comment('担当工事内容');
            $table->decimal('contract_amount', 15, 2)->nullable()->comment('契約金額');
            $table->date('contract_date')->nullable()->comment('契約日');
            $table->date('period_start')->nullable()->comment('工期開始');
            $table->date('period_end')->nullable()->comment('工期終了');
            $table->string('safety_manager')->nullable()->comment('安全衛生責任者（現場）');
            $table->string('site_chief_engineer')->nullable()->comment('主任技術者（現場）');
            $table->timestamps();

            $table->unique(['project_id', 'subcontractor_id', 'tier']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_subcontractors');
        Schema::dropIfExists('subcontractors');
    }
};
