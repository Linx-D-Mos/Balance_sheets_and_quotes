<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FixedExpense extends Model
{
    /** @use HasFactory<\Database\Factories\FixedExpenseFactory> */
    use HasFactory;

    protected $fillable = [
        'concept',
        'amount',
        'is_active'
    ];

    protected $casts = [
        'amount' => 'decimal:4',
        'is_active' => 'boolean'
    ];
}
