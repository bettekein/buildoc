<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Mattiverse\Userstamps\Traits\Userstamps;

class GreenFile extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\GreenFileFactory> */
    use HasFactory, BelongsToTenant, SoftDeletes, \OwenIt\Auditing\Auditable, Userstamps;

    protected $fillable = [
        'tenant_id',
        'project_id',
        'staff_id',
        'tool_id',
        'vehicle_id',
        'safety_training_date',
    ];

    protected $casts = [
        'safety_training_date' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function tool()
    {
        return $this->belongsTo(Tool::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
