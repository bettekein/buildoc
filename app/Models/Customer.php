<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Mattiverse\Userstamps\Traits\Userstamps;

class Customer extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\CustomerFactory> */
    use HasFactory, BelongsToTenant, SoftDeletes, \OwenIt\Auditing\Auditable, Userstamps;

    protected $fillable = [
        'tenant_id',
        'name',
        'representative_name',
        'phone',
        'address',
        'customer_code',
        'zip_code',
        'fax',
    ];
}
