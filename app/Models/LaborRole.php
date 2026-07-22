<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LaborRole extends Model
{
    /** @use HasFactory<\Database\Factories\LaborRoleFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'base_salary',
        'social_load_pct',
        'hourly_cost',
        'is_active',
    ];

    protected $casts = [
        'base_salary' => 'decimal:4',
        'social_load_pct' => 'decimal:4',
        'hourly_cost' => 'decimal:4',
        'is_active' => 'boolean'
    ];

    public function laborAssignments(): HasMany
    {
        return $this->hasMany(QuoteLaborAssignment::class);
    }

    public function laborLogs(): HasMany
    {
        return $this->hasMany(ProjectLaborLog::class);
    }
}
