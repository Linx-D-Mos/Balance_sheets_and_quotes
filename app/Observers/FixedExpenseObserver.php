<?php

namespace App\Observers;

use App\Models\FixedExpense;
use App\Models\GlobalSetting;

class FixedExpenseObserver
{
    /**
     * Handle the FixedExpense "created" event.
     */
    public function saved(FixedExpense $fidexExpense): void
    {
        $this->recalculateOverheadRate();
    }

    /**
     * Handle the FixedExpense "updated" event.
     */
    public function deleted(FixedExpense $fidexExpense): void
    {
        $this->recalculateOverheadRate();
    }

    public function updated(FixedExpense $fidexExpense): void
    {
        $this->recalculateOverheadRate();
    }

    /**
     * Recalculates the overhead rate based on active fixed expenses.
     */
    private function recalculateOverheadRate(): void
    {
        $settings = GlobalSetting::find(1) ?? GlobalSetting::first();

        if (!$settings) {
            return;
        }

        $activeOverheadSum = (float) FixedExpense::query()
            ->where('is_active', true)
            ->sum('amount');

        $capacityHours = (float) $settings->standard_monthly_hours;

        $rate = $capacityHours > 0
            ? round($activeOverheadSum / $capacityHours, 4)
            : 0.0000;

        $settings->updateQuietly([
            'default_overhead_rate_applied' => $rate,
        ]);
    }
}
