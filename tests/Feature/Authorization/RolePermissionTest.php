<?php

use App\Enums\AppPermissionEnum;
use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

describe('AUTHORIZATION SYSTEM AND RBAC TEST', function (){
    it('deny the access to a user without permissions', function(){
        $user = User::factory()->create();

        expect(Gate::forUser($user)->allows(AppPermissionEnum::MANAGE_SETTINGS->value))->toBeFalse();
    });

    it('allow the access if the user has the right permission', function(){
        $user = User::factory()->create();
        $user->givePermissionTo(AppPermissionEnum::MANAGE_SETTINGS->value);

        expect(Gate::forUser($user)->allows(AppPermissionEnum::MANAGE_SETTINGS->value))->toBeTrue();
        expect(Gate::forUser($user)->allows(AppPermissionEnum::APPROVE_QUOTES))->toBeFalse();
    });

    it('Give the automatic total bypass to the administrator rol through the gate interceptor ', function(){
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::ADMINISTRATOR->value);

        expect(Gate::forUser($user)->allows(AppPermissionEnum::MANAGE_SETTINGS->value))->toBeTrue();
        expect(Gate::forUser($user)->allows(AppPermissionEnum::APPROVE_QUOTES->value))->toBeTrue();
        expect(Gate::forUser($user)->allows('permiso_cualquiera_sin_existir'))->toBeTrue();
    });
});
