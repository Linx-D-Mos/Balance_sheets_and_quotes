<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuoteMaterialItem extends Model
{
    /** @use HasFactory<\Database\Factories\QuoteMaterialItemFactory> */
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'concept',
        'estimated_quantity',
        'estimated_unit_price',
        'subtotal'
    ];

    protected $casts = [
        'estimated_quantity' => 'decimal:4',
        'estimated_unit_price' => 'decimal:4',
        'subtotal' => 'decimal:4'
    ];

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function materialPurchases(): HasMany
    {
        return $this->hasMany(ProjectMaterialPurchase::class);
    }
}
