<?php

namespace App\Policies;

use App\Enums\AppPermissionEnum;
use App\Models\FixedExpense;
use App\Models\User;

class FixedExpensePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(AppPermissionEnum::MANAGE_SETTINGS->value);
    }

    public function view(User $user, FixedExpense $fixedExpense): bool
    {
        return $user->can(AppPermissionEnum::MANAGE_SETTINGS->value);
    }

    public function create(User $user): bool
    {
        return $user->can(AppPermissionEnum::MANAGE_SETTINGS->value);
    }

    public function update(User $user, FixedExpense $fixedExpense): bool
    {
        return $user->can(AppPermissionEnum::MANAGE_SETTINGS->value);
    }

    public function delete(User $user, FixedExpense $fixedExpense): bool
    {
        return false; // RN-06 / CA-01.3: Prohibido borrado físico
    }

    public function forceDelete(User $user, FixedExpense $fixedExpense): bool
    {
        return false; // RN-06 / CA-01.3: Prohibido borrado físico
    }
}
