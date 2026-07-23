<?php

namespace App\Policies;

use App\Enums\AppPermissionEnum;
use App\Models\LaborRole;
use App\Models\User;

class LaborRolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(AppPermissionEnum::MANAGE_SETTINGS->value);
    }

    public function view(User $user, LaborRole $laborRole): bool
    {
        return $user->can(AppPermissionEnum::MANAGE_SETTINGS->value);
    }

    public function create(User $user): bool
    {
        return $user->can(AppPermissionEnum::MANAGE_SETTINGS->value);
    }

    public function update(User $user, LaborRole $laborRole): bool
    {
        return $user->can(AppPermissionEnum::MANAGE_SETTINGS->value);
    }

    public function delete(User $user, LaborRole $laborRole): bool
    {
        return $user->can(AppPermissionEnum::MANAGE_SETTINGS->value);
    }
}
