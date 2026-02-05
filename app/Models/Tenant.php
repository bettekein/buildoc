<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Mattiverse\Userstamps\Traits\Userstamps;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Tenant extends Model implements Auditable, HasMedia
{
    /** @use HasFactory<\Database\Factories\TenantFactory> */
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable, Userstamps, InteractsWithMedia;

    public function getNameAttribute()
    {
        return $this->company_name;
    }

    protected $fillable = [
        'company_name',
        'license_number',
        'invoice_registration_number',
        'zip_code',
        'address',
        'phone',
        'fax',
        'representative_title',
        'license_details',
        'social_insurance',
        'invoice_number',
    ];

    protected $casts = [
        'license_details' => 'array',
        'social_insurance' => 'array',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
