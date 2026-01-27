<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Mattiverse\Userstamps\Traits\Userstamps;

class ProgressBilling extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\ProgressBillingFactory> */
    use HasFactory, BelongsToTenant, SoftDeletes, \OwenIt\Auditing\Auditable, Userstamps;

    protected $fillable = [
        'tenant_id',
        'project_id',
        'billing_round',
        'progress_rate',
        'amount_this_time',
        'retention_money',
        'status',
        'billing_number',
        'billing_date',
        'payment_date',
        'tax_amount',
        'note',
    ];

    protected $casts = [
        'billing_date' => 'date',
        'payment_date' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
