<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectLaborLog extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectLaborLogFactory> */
    use HasFactory;

    protected $fillable = [
        'project_id',
        'quote_labor_assignment_id',
        'employee_id',
        'labor_role_id',
        'annulled_by_user_id',
        'actual_hours_regular',
        'actual_hours_extra',
        'hourly_rate_actual',
        'overtime_multiplier_applied',
        'actual_subtotal',
        'logged_at',
        'is_annulled',
        'annulled_at',
        'annulment_reason',
    ];

    protected $casts = [
        'logged_at' => 'date',
        'is_annulled' => 'boolean',
        'annulled_at' => 'datetime',
        'hourly_rate_actual' => 'decimal:4',
        'overtime_multiplier_applied' => 'decimal:4',
        'actual_subtotal' => 'decimal:4',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function quoteLaborAssignment(): BelongsTo
    {
        return $this->belongsTo(QuoteLaborAssignment::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function laborRole(): BelongsTo
    {
        return $this->belongsTo(LaborRole::class);
    }

    public function annulledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'annulled_by_user_id');
    }
}
