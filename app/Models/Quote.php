<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quote extends Model
{
    /** @use HasFactory<\Database\Factories\QuoteFactory> */
    use HasFactory;

    protected $fillable = [
        'project_id',
        'quote_status_id',
        'parent_quote_id',
        'title',
        'start_date',
        'end_date',
        'work_weekends',
        'amendment_level',
        'total_hours',
        'direct_labor_cost',
        'direct_materials_cost',
        'direct_cost',
        'overhead_rate_applied',
        'overtime_multiplier_applied',
        'overhead_cost',
        'equilibrium_cost',
        'margin_applied',
        'total_price',
    ];

    protected $casts = [
        'amendment_level' => 'integer',
        'total_hours' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'work_weekends' => 'boolean',
        'direct_labor_cost' => 'decimal:4',
        'direct_materials_cost' => 'decimal:4',
        'direct_cost' => 'decimal:4',
        'overhead_rate_applied' => 'decimal:4',
        'overtime_multiplier_applied' => 'decimal:4',
        'overhead_cost' => 'decimal:4',
        'equilibrium_cost' => 'decimal:4',
        'margin_applied' => 'decimal:4',
        'total_price' => 'decimal:4',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(QuoteStatus::class, 'quote_status_id');
    }

    public function parentQuote(): BelongsTo
    {
        return $this->belongsTo(Quote::class, 'parent_quote_id');
    }

    public function amendments(): HasMany
    {
        return $this->hasMany(Quote::class, 'parent_quote_id');
    }

    public function laborAssignments(): HasMany
    {
        return $this->hasMany(QuoteLaborAssignment::class);
    }

    public function materialItems(): HasMany
    {
        return $this->hasMany(QuoteMaterialItem::class);
    }
}
