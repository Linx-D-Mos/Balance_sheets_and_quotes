<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Override;

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

    /**
     * Calculates the hourly cost based on the base salary and social load percentage.
     */
    public static function calculateHourlyCost(float|int|string|null $baseSalary, float|int|string|null $socialLoadPct): float
    {
        $base = (float) ($baseSalary ?? 0);
        $load = (float) ($socialLoadPct ?? 0);

        return round($base * (1 + ($load / 100)), 4);
    }

    /**
     * Boot the model and set up event listeners.
     */
    protected static function booted(): void
    {
        static::saving(function (LaborRole $role) {
            $role->hourly_cost = static::calculateHourlyCost($role->base_salary, $role->social_load_pct);
        });
    }

    public function laborAssignments(): HasMany
    {
        return $this->hasMany(QuoteLaborAssignment::class);
    }

    public function laborLogs(): HasMany
    {
        return $this->hasMany(ProjectLaborLog::class);
    }
}
