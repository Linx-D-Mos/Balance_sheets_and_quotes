<?php

use App\Enums\AppPermissionEnum;
use App\Filament\Pages\ManageGlobalSettings;
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
});
