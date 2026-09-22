<?php

namespace App\Models;

use App\Observers\FixedExpenseObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([FixedExpenseObserver::class])]
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
