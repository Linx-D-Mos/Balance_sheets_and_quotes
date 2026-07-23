<?php

use App\Enums\AppPermissionEnum;
use App\Models\Employee;
use App\Models\User;

describe('EmployeePolicy Test', function () {
    it('denies access to manage employees for users without permission', function () {
        $user = User::factory()->create();
        $employee = Employee::factory()->create();

        expect($user->can('viewAny', Employee::class))->toBeFalse()
            ->and($user->can('create', Employee::class))->toBeFalse()
            ->and($user->can('update', $employee))->toBeFalse();
    });

    it('allows viewing, creating and updating employees when user has permission', function () {
        $user = User::factory()->create();
        $user->givePermissionTo(AppPermissionEnum::MANAGE_SETTINGS->value);
        $employee = Employee::factory()->create();

        expect($user->can('viewAny', Employee::class))->toBeTrue()
            ->and($user->can('create', Employee::class))->toBeTrue()
            ->and($user->can('update', $employee))->toBeTrue();
    });

    it('strictly denies physical deletion of employees even with permission (RN-06)', function () {
        $user = User::factory()->create();
        $user->givePermissionTo(AppPermissionEnum::MANAGE_SETTINGS->value);
        $employee = Employee::factory()->create();

        expect($user->can('delete', $employee))->toBeFalse()
            ->and($user->can('forceDelete', $employee))->toBeFalse();
    });
});
