<?php

namespace App\Policies;

use App\Enums\AppPermissionEnum;
use App\Models\Employee;
use App\Models\User;

class EmployeePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(AppPermissionEnum::MANAGE_SETTINGS->value);
    }

    public function view(User $user, Employee $employee): bool
    {
        return $user->can(AppPermissionEnum::MANAGE_SETTINGS->value);
    }

    public function create(User $user): bool
    {
        return $user->can(AppPermissionEnum::MANAGE_SETTINGS->value);
    }

    public function update(User $user, Employee $employee): bool
    {
        return $user->can(AppPermissionEnum::MANAGE_SETTINGS->value);
    }

    public function delete(User $user, Employee $employee): bool
    {
        return false; // RN-06: Prohibido borrado físico
    }

    public function forceDelete(User $user, Employee $employee): bool
    {
        return false; // RN-06: Prohibido borrado físico
    }
}
