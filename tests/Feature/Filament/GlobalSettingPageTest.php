<?php

use App\Enums\AppPermissionEnum;
use App\Filament\Pages\ManageGlobalSettings;
use App\Models\FixedExpense;
use App\Models\GlobalSetting;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->user->givePermissionTo(AppPermissionEnum::MANAGE_SETTINGS->value);

    GlobalSetting::updateOrCreate(
        ['id' => 1],
        [
            'standard_monthly_hours' => 166.6667,
            'default_overhead_rate_applied' => 10.0000,
            'default_profit_margin' => 20.0000,
            'overtime_multiplier' => 1.5000,
        ]
    );
});

describe('Global Settings Filament Back-Office Page (P4 / HU-04)', function () {
    it('denies access to users without manage_settings permission', function () {
        $unauthorizedUser = User::factory()->create();

        $this->actingAs($unauthorizedUser);

        Livewire::test(ManageGlobalSettings::class)
            ->assertForbidden();
    });

    it('can render global settings page for authorized users', function () {
        $this->actingAs($this->user);

        Livewire::test(ManageGlobalSettings::class)
            ->assertSuccessful();
    });

    it('loads current global settings into form fields', function () {
        $this->actingAs($this->user);

        Livewire::test(ManageGlobalSettings::class)
            ->assertSchemaStateSet([
                'default_profit_margin' => '20.0000',
                'overtime_multiplier' => '1.5000',
            ]);
    });

    it('can update global configuration parameters', function () {
        $this->actingAs($this->user);

        Livewire::test(ManageGlobalSettings::class)
            ->fillForm([
                'default_profit_margin' => 25.0000,
                'overtime_multiplier' => 1.7500,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $settings = GlobalSetting::find(1);

        expect((float) $settings->default_profit_margin)->toBe(25.0000)
            ->and((float) $settings->overtime_multiplier)->toBe(1.7500);
    });

    it('can recalculate standard monthly capacity and overhead rate on demand', function () {
        $this->actingAs($this->user);

        // Forzamos un valor desactualizado en la capacidad
        GlobalSetting::updateOrCreate(
            ['id' => 1],
            [
                'standard_monthly_hours' => 150.0000,
                'default_overhead_rate_applied' => 0.0000,
            ]
        );

        FixedExpense::create([
            'concept' => 'Seguro Operativo Anual',
            'amount' => 1666.6667,
            'is_active' => true,
        ]);

        Livewire::test(ManageGlobalSettings::class)
            ->call('recalculateCapacity')
            ->assertHasNoErrors()
            ->assertNotified();

        $settings = GlobalSetting::find(1);

        // Certifica que recalculó la capacidad vía Yasumi (> 160 hrs) y actualizó T_oh
        expect((float) $settings->standard_monthly_hours)->toBeGreaterThan(160.0000)
            ->and((float) $settings->default_overhead_rate_applied)->toBeGreaterThan(0.0000);
    });
});
