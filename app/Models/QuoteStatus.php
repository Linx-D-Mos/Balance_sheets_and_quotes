<?php

namespace App\Models;

use App\Enums\QuoteStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class QuoteStatus
 *
 * Catalogue of quote statuses.
 *
 * @property int $id
 * @property string $display_name
 * @property QuoteStatusEnum $code
 * @property string|null $icon
 * @property string|null $bg_color
 * @property string|null $bg_text
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
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

    //Scopes
    public function scopeOfCode(Builder $query, QuoteStatusEnum $code){
        return $query->where('code', $code);
    }
}
