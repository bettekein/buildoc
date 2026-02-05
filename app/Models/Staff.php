<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Mattiverse\Userstamps\Traits\Userstamps;

class Staff extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\StaffFactory> */
    use HasFactory, BelongsToTenant, SoftDeletes, \OwenIt\Auditing\Auditable, Userstamps;

    protected $fillable = [
        'tenant_id',
        'name',
        'address',
        'blood_type',
        'social_insurance_status',
        'emergency_contact',
        'qualifications',
        'furigana',
        'birthday',
        'job_type',
        'experience_years',
        'hiring_date',
        'health_info',
        'insurance_details',
        // グリーンファイル追加項目
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
    ];

    protected $casts = [
        'qualifications' => 'array',
        'social_insurance_status' => 'array',
        'emergency_contact' => 'array',
        'health_info' => 'array',
        'insurance_details' => 'array',
        'birthday' => 'date',
        'hiring_date' => 'date',
        // グリーンファイル追加項目
        'last_medical_checkup' => 'date',
        'sendout_education_date' => 'date',
        'is_sole_proprietor' => 'boolean',
        'has_special_labor_insurance' => 'boolean',
        'qualification_details' => 'array',
        'skill_trainings' => 'array',
        'special_educations' => 'array',
        'driver_licenses' => 'array',
    ];

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_staff')
            ->withPivot('is_foreman', 'role', 'start_date', 'end_date')
            ->withTimestamps();
    }
}
