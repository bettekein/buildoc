<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Mattiverse\Userstamps\Traits\Userstamps;

class Subcontractor extends Model implements Auditable
{
    use HasFactory, BelongsToTenant, SoftDeletes, \OwenIt\Auditing\Auditable, Userstamps;

    protected $fillable = [
        'tenant_id',
        'name',
        'name_kana',
        'representative_name',
        'representative_title',
        'postal_code',
        'address',
        'phone',
        'fax',
        'email',
        'construction_licenses',
        'safety_officer',
        'employment_manager',
        'chief_engineer',
        'specialist_engineer',
        'specialist_qualification',
        'insurance_status',
    ];

    protected $casts = [
        'construction_licenses' => 'array',
        'insurance_status' => 'array',
    ];

    /**
     * この下請業者が担当するプロジェクト
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_subcontractors')
            ->withPivot('tier', 'parent_subcontractor_id', 'work_content', 'contract_amount', 'contract_date', 'period_start', 'period_end', 'safety_manager', 'site_chief_engineer')
            ->withTimestamps();
    }

    /**
     * 建設業許可をフォーマットして取得
     */
    public function getFormattedLicensesAttribute(): string
    {
        if (!is_array($this->construction_licenses)) {
            return '';
        }

        return collect($this->construction_licenses)->map(function ($license) {
            $type = $license['type'] ?? '';
            $number = $license['number'] ?? '';
            $date = $license['date'] ?? '';
            return "{$type} 第{$number}号 ({$date})";
        })->implode(', ');
    }
}
