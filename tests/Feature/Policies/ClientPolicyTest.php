<?php

use App\Enums\AppPermissionEnum;
use App\Models\Client;
use App\Models\User;

describe('ClientPolicy Test', function () {
    it('denies access to view or manage clients for users without permission', function () {
        $user = User::factory()->create();
        $client = Client::factory()->create();

        expect($user->can('viewAny', Client::class))->toBeFalse()
            ->and($user->can('view', $client))->toBeFalse()
            ->and($user->can('create', Client::class))->toBeFalse()
            ->and($user->can('update', $client))->toBeFalse()
            ->and($user->can('delete', $client))->toBeFalse();
    });

    it('allows access to manage clients for users with proper permission', function () {
        $user = User::factory()->create();
        $user->givePermissionTo(AppPermissionEnum::MANAGE_SETTINGS->value);
        $client = Client::factory()->create();

        expect($user->can('viewAny', Client::class))->toBeTrue()
            ->and($user->can('view', $client))->toBeTrue()
            ->and($user->can('create', Client::class))->toBeTrue()
            ->and($user->can('update', $client))->toBeTrue();
    });
});
