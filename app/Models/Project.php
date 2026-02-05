<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Mattiverse\Userstamps\Traits\Userstamps;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Project extends Model implements Auditable, HasMedia
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory, BelongsToTenant, SoftDeletes, \OwenIt\Auditing\Auditable, Userstamps, InteractsWithMedia;

    protected $fillable = [
        'tenant_id',
        'customer_id',
        'name',
        'period_start',
        'period_end',
        'status',
        'site_address',
        'work_description',
        'order_number',
        'project_number',
        'contract_date',
        'retention_rate',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'contract_date' => 'date',
        'retention_rate' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function quotationItems()
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function progressBillings()
    {
        return $this->hasMany(ProgressBilling::class);
    }
}
