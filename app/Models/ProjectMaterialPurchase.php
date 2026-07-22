<?php

namespace App\Models;

use App\Enums\PaymentMethodEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectMaterialPurchase extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectMaterialPurchaseFactory> */
    use HasFactory;

    protected $fillable = [
        'project_id',
        'material_category_id',
        'quote_material_item_id',
        'annulled_by_user_id',
        'concept',
        'store',
        'payment_method',
        'buyer_name',
        'actual_quantity',
        'actual_unit_price',
        'actual_subtotal',
        'purchased_at',
        'is_annulled',
        'annulled_at',
        'annulment_reason',
    ];

    protected $casts = [
        'purchased_at' => 'date',
        'is_annulled' => 'boolean',
        'annulled_at' => 'datetime',
        'payment_method' => PaymentMethodEnum::class,
        'actual_quantity' => 'decimal:4',
        'actual_unit_price' => 'decimal:4',
        'actual_subtotal' => 'decimal:4',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(MaterialCategory::class, 'material_category_id');
    }

    public function quoteMaterialItem(): BelongsTo
    {
        return $this->belongsTo(QuoteMaterialItem::class);
    }

    public function annulledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'annulled_by_user_id');
    }
}
