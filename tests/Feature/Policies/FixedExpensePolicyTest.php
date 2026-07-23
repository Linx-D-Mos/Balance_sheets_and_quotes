<?php

use App\Enums\AppPermissionEnum;
use App\Models\FixedExpense;
use App\Models\User;

describe('FixedExpensePolicy Test', function () {
    it('denies access to fixed expenses without permission', function () {
        $user = User::factory()->create();
        $fixedExpense = FixedExpense::factory()->create();

        expect($user->can('viewAny', FixedExpense::class))->toBeFalse()
            ->and($user->can('create', FixedExpense::class))->toBeFalse()
            ->and($user->can('update', $fixedExpense))->toBeFalse();
    });

    it('allows viewing, creating and updating fixed expenses with manage_settings permission', function () {
        $user = User::factory()->create();
        $user->givePermissionTo(AppPermissionEnum::MANAGE_SETTINGS->value);
        $fixedExpense = FixedExpense::factory()->create();

        expect($user->can('viewAny', FixedExpense::class))->toBeTrue()
            ->and($user->can('create', FixedExpense::class))->toBeTrue()
            ->and($user->can('update', $fixedExpense))->toBeTrue();
    });

    it('strictly forbids physical delete of fixed expenses (RN-06 / CA-01.3)', function () {
        $user = User::factory()->create();
        $user->givePermissionTo(AppPermissionEnum::MANAGE_SETTINGS->value);
        $fixedExpense = FixedExpense::factory()->create();

        expect($user->can('delete', $fixedExpense))->toBeFalse()
            ->and($user->can('forceDelete', $fixedExpense))->toBeFalse();
    });
});
