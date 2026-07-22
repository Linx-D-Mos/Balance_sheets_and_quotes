<?php

namespace App\Models;

use App\Enums\QuoteStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory;

    protected $fillable = [
        'client_id',
        'project_status_id',
        'code',
        'title',
        'address',
        'actual_start_date',
        'actual_end_date',
        'project_description'
    ];

    protected $casts = [
        'actual_start_date' => 'date',
        'actual_end_date' => 'date',
    ];

    /**
     * Get the cliente associet with the project
     *
     * @return BelongsTo
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the status that the project is on
     *
     * @return BelongsTo
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(ProjectStatus::class, 'project_status_id');
    }

    /**
     * Get the quotes associet with the project
     *
     * @return HasMany
     */
    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }

    /**
     * Get the approved quote associated with the project
     *
     * @return HasOne
     */
    public function approvedQuote(): HasOne
    {
        return $this->hasOne(Quote::class)->whereHas('status', function ($query) {
            $query->where('code', QuoteStatusEnum::APPROVED);
        });
    }

    /**
     * Get the labor logs associet with the project
     *
     * @return HasMany
     */
    public function laborLogs(): HasMany
    {
        return $this->hasMany(ProjectLaborLog::class);
    }

    /**
     * Get the material purchases associet with the project
     *
     * @return HasMany
     */
    public function materialPurchases(): HasMany
    {
        return $this->hasMany(ProjectMaterialPurchase::class);
    }

    /**
     * * Get the deposits associet with the project
     *
     * @return HasMany
     */
    public function deposits(): HasMany
    {
        return $this->hasMany(ProjectDeposit::class);
    }
}
