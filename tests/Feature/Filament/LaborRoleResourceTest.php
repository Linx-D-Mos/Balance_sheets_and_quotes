<?php

use App\Enums\AppPermissionEnum;
use App\Filament\Resources\LaborRoles\Pages\ManageLaborRoles;
use App\Models\LaborRole;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->user->givePermissionTo(AppPermissionEnum::MANAGE_SETTINGS->value);
});

describe('LaborRole Domain and Resource Management', function () {
    it('calculates C_ch correctly using domain formula', function () {
        $calculated = LaborRole::calculateHourlyCost(20.00, 15.00);

        expect($calculated)->toBe(23.00);
    });

    it('automatically calculates and persists hourly_cost on model saving hook', function () {
        $role = LaborRole::create([
            'name' => 'Carpintero Principal',
            'base_salary' => 25.00,
            'social_load_pct' => 20.00,
            'is_active' => true,
        ]);

        expect((float) $role->fresh()->hourly_cost)->toBe(30.00);
    });

    it('can render labor role index page', function () {
        $this->actingAs($this->user);

        Livewire::test(ManageLaborRoles::class)
            ->assertSuccessful();
    });

    it('can list labor roles with proper columns', function () {
        $this->actingAs($this->user);

        $role = LaborRole::factory()->create([
            'name' => 'Pintor Principal',
            'base_salary' => 20.00,
            'social_load_pct' => 15.00,
            'is_active' => true,
        ]);

        Livewire::test(ManageLaborRoles::class)
            ->assertCanSeeTableRecords([$role])
            ->assertTableColumnExists('name')
            ->assertTableColumnExists('base_salary')
            ->assertTableColumnExists('social_load_pct')
            ->assertTableColumnExists('hourly_cost');
    });

    it('can create labor role via header action and compute C_ch', function () {
        $this->actingAs($this->user);

        Livewire::test(ManageLaborRoles::class)
            ->mountAction('create')
            ->setActionData([
                'name' => 'Pintor Auxiliar',
                'base_salary' => 15.00,
                'social_load_pct' => 20.00,
                'is_active' => true,
            ])
            ->callMountedAction()
            ->assertHasNoActionErrors();

        $this->assertDatabaseHas('labor_roles', [
            'name' => 'Pintor Auxiliar',
            'base_salary' => 15.0000,
            'social_load_pct' => 20.0000,
            'hourly_cost' => 18.0000,
        ]);
    });
});
