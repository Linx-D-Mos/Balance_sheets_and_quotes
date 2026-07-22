<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuoteLaborAssignment extends Model
{
    /** @use HasFactory<\Database\Factories\QuoteLaborAssignmentFactory> */
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'labor_role_id',
        'employee_id',
        'worker_name_placeholder',
        'estimated_hours_regular',
        'estimated_hours_extra',
        'hourly_rate_at_estimation',
        'estimated_subtotal',
    ];

    protected $casts = [
        'hourly_rate_at_estimation' => 'decimal:4',
        'estimated_subtotal' => 'decimal:4',
    ];

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function laborRole(): BelongsTo
    {
        return $this->belongsTo(LaborRole::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function laborLogs(): HasMany
    {
        return $this->hasMany(ProjectLaborLog::class);
    }
}
