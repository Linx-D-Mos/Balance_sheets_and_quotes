<?php

namespace App\Models;

use App\Enums\MaterialCategoryEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaterialCategory extends Model
{
    /** @use HasFactory<\Database\Factories\MaterialCategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'display_name',
        'code'
    ];

    protected $casts = [
        'code' => MaterialCategoryEnum::class,
    ];

    public function materialPurchases(): HasMany
    {
        return $this->hasMany(ProjectMaterialPurchase::class);
    }
}
