<?php

namespace App\Policies;

use App\Enums\AppPermissionEnum;
use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(AppPermissionEnum::MANAGE_SETTINGS->value);
    }

    public function view(User $user, Client $client): bool
    {
        return $user->can(AppPermissionEnum::MANAGE_SETTINGS->value);
    }

    public function create(User $user): bool
    {
        return $user->can(AppPermissionEnum::MANAGE_SETTINGS->value);
    }

    public function update(User $user, Client $client): bool
    {
        return $user->can(AppPermissionEnum::MANAGE_SETTINGS->value);
    }

    public function delete(User $user, Client $client): bool
    {
        return $user->can(AppPermissionEnum::MANAGE_SETTINGS->value);
    }
}
