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
        'previous_billed_amount', // Added
        'cumulative_amount', // Added
        'amount_this_time',
        'retention_money',
        'retention_release_amount', // Added
        'offset_amount', // Added
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
        'progress_rate' => 'decimal:2',
        'amount_this_time' => 'decimal:2',
        'cumulative_amount' => 'decimal:2',
        'previous_billed_amount' => 'decimal:2',
        'retention_money' => 'decimal:2',
        'retention_release_amount' => 'decimal:2',
        'offset_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function calculate(array $input): array
    {
        $service = new \App\Services\BillingCalculationService();
        $project = $this->project;
        
        // Ensure inputs are present or default
        return $service->calculate(
            contractAmount: $input['contract_amount'] ?? $project->contract_amount ?? 0,
            progressRate: $input['progress_rate'] ?? 0,
            previousTotalProgressAmount: $input['previous_billed_amount'] ?? 0,
            retentionRate: $input['retention_rate'] ?? $project->retention_rate ?? 20.00,
            offsetAmount: $input['offset_amount'] ?? 0,
            retentionReleaseAmount: $input['retention_release_amount'] ?? 0,
            taxRate: 10.00 // Default or from settings
        );
    }
}
