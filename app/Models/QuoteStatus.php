<?php

namespace App\Models;

use App\Enums\QuoteStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuoteStatus extends Model
{
    /** @use HasFactory<\Database\Factories\QuoteStatusFactory> */
    use HasFactory;

    protected $fillable = [
        'display_name',
        'code',
        'icon',
        'bg_color',
        'bg_text'
    ];

    protected $casts = [
        'code' => QuoteStatusEnum::class,
    ];

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }
}
