<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Mattiverse\Userstamps\Traits\Userstamps;

class QuotationDetail extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\QuotationDetailFactory> */
    use HasFactory, BelongsToTenant, SoftDeletes, \OwenIt\Auditing\Auditable, Userstamps;

    protected $fillable = [
        'tenant_id',
        'quotation_item_id',
        'name',
        'specification',
        'quantity',
        'unit',
        'unit_price',
        'cost_price',
        'total_price',
    ];

    public function item()
    {
        return $this->belongsTo(QuotationItem::class, 'quotation_item_id');
    }
}
