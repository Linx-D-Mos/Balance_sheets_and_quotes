<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalSetting extends Model
{
    /** @use HasFactory<\Database\Factories\GlobalSettingFactory> */
    use HasFactory;

    protected $fillable = [
        'standard_monthly_hours',
        'default_overhead_rate_applied',
        'default_profit_margin',
        'overtime_multiplier'
    ];

    protected $casts = [
        'standard_monthly_hours' => 'decimal:4',
        'default_overhead_rate_applied' => 'decimal:4',
        'default_profit_margin' => 'decimal:4',
        'overtime_multiplier' => 'decimal:4'
    ];

}
