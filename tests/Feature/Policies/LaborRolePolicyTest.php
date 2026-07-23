<?php

use App\Enums\AppPermissionEnum;
use App\Models\LaborRole;
use App\Models\User;

describe('LaborRolePolicy Test', function () {
    it('denies managing labor roles without permission', function () {
        $user = User::factory()->create();
        $laborRole = LaborRole::factory()->create();

        expect($user->can('viewAny', LaborRole::class))->toBeFalse()
            ->and($user->can('create', LaborRole::class))->toBeFalse()
            ->and($user->can('update', $laborRole))->toBeFalse();
    });

    it('allows managing labor roles with manage_settings permission', function () {
        $user = User::factory()->create();
        $user->givePermissionTo(AppPermissionEnum::MANAGE_SETTINGS->value);
        $laborRole = LaborRole::factory()->create();

        expect($user->can('viewAny', LaborRole::class))->toBeTrue()
            ->and($user->can('create', LaborRole::class))->toBeTrue()
            ->and($user->can('update', $laborRole))->toBeTrue();
    });
});
