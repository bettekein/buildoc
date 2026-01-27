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
    ];

    protected $casts = [
        'qualifications' => 'array',
    ];
}
