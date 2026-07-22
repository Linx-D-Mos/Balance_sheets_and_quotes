<?php

namespace App\Models;

use App\Enums\ProjectStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectStatus extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectStatusFactory> */
    use HasFactory;

    protected $fillable = [
        'display_name',
        'code',
        'icon',
        'bg_color',
        'bg_text'
    ];

    protected $casts = [
        'code' => ProjectStatusEnum::class,
    ];

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
