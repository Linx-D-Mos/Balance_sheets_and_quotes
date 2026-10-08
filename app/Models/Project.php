<?php

namespace App\Models;

use App\Enums\ProjectStatusEnum;
use App\Enums\QuoteStatusEnum;
use Illuminate\Database\Eloquent\Builder;
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
        'city',
        'state',
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

    //Scopes

    /**
     * Scope a query to only include projects for a given client.
     *
     * @param Builder $query
     * @param int $clientId
     * @return Builder
     */
    public function scopeForClient(Builder $query, int $clientId): Builder
    {
        return $query->where('client_id', $clientId);
    }

    /**
     * Scope a query to only include projects with a given status.
     *
     * @param Builder $query
     * @param ProjectStatusEnum $status
     * @return Builder
     */
    public function scopeOfStatus(Builder $query, ProjectStatusEnum $status): Builder
    {
        return $query->whereHas('status', function ($query) use ($status) {
            $query->where('code', $status);
        });
    }

    /**
     * Scope a query to search projects by title.
     *
     * @param Builder $query
     * @param string $term
     * @return Builder
     */
    public function scopeSearchByTitle(Builder $query, string $term): Builder
    {
        return $query->where('title', 'like', "%{$term}%");
    }

    /**
     * Scope a query to only include projects that started in a given month and year.
     *
     * @param Builder $query
     * @param int $year
     * @param int $month
     * @return Builder
     */
    public function scopeStartedInMonth(Builder $query, int $year, int $month): Builder
    {
        return $query->whereYear('actual_start_date', $year)
            ->whereMonth('actual_start_date', $month);
    }
}
