<?php

namespace App\Models;

use App\Enums\PaymentMethodEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectDeposit extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectDepositFactory> */
    use HasFactory;

    protected $fillable = [
        'project_id',
        'annulled_by_user_id',
        'amount',
        'payment_method',
        'received_at',
        'reference_number',
        'is_annulled',
        'annulled_at',
        'annulment_reason',
    ];

    protected $casts = [
        'received_at' => 'date',
        'is_annulled' => 'boolean',
        'annulled_at' => 'datetime',
        'payment_method' => PaymentMethodEnum::class,
        'amount' => 'decimal:4',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function annulledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'annulled_by_user_id');
    }
}
