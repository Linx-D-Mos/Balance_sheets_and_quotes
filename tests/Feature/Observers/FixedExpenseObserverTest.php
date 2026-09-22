<?php

use App\Models\FixedExpense;
use App\Models\GlobalSetting;

describe('FixedExpenseObserver and overhead rate calculus automatization', function () {
    beforeEach(function () {
        // Fijamos la capacidad mensual en 166.6667 y reseteamos la tasa inicial
        GlobalSetting::updateOrCreate(
            ['id' => 1],
            [
                'standard_monthly_hours' => 166.6667,
                'default_overhead_rate_applied' => 0.0000,
                'default_profit_margin' => 20.0000,
                'overtime_multiplier' => 1.5000,
            ]
        );

        // Limpiamos gastos fijos para pruebas deterministas
        FixedExpense::query()->delete();
    });

    it('recalculates and updates T_oh in global_settings upon creating an active fixed expense', function () {
        // 1666.6667 / 166.6667 = 10.0000
        FixedExpense::create([
            'concept' => 'Renta de Bodega Central',
            'amount' => 1666.6667,
            'is_active' => true,
        ]);

        $updatedSettings = GlobalSetting::find(1);

        expect((float) $updatedSettings->default_overhead_rate_applied)->toBe(10.0000);
    });

    it('ignores inactive fixed expenses when calculating T_oh (CA-01.2)', function () {
        FixedExpense::create([
            'concept' => 'Seguro Comercial Liability',
            'amount' => 1666.6667,
            'is_active' => true,
        ]);

        FixedExpense::create([
            'concept' => 'Software Secundario Desactivado',
            'amount' => 5000.0000,
            'is_active' => false,
        ]);

        $updatedSettings = GlobalSetting::find(1);

        expect((float) $updatedSettings->default_overhead_rate_applied)->toBe(10.0000);
    });

    it('recalculates T_oh automatically when toggling is_active status', function () {
        $expense = FixedExpense::create([
            'concept' => 'Servicio Telefónico y Datos',
            'amount' => 1666.6667,
            'is_active' => true,
        ]);

        expect((float) GlobalSetting::find(1)->default_overhead_rate_applied)->toBe(10.0000);

        $expense->update(['is_active' => false]);

        expect((float) GlobalSetting::find(1)->default_overhead_rate_applied)->toBe(0.0000);

        $expense->update(['is_active' => true]);

        expect((float) GlobalSetting::find(1)->default_overhead_rate_applied)->toBe(10.0000);
    });

    it('recalculates T_oh automatically when updating the amount', function () {
        $expense = FixedExpense::create([
            'concept' => 'Servidor y ERP Cloud',
            'amount' => 1666.6667,
            'is_active' => true,
        ]);

        expect((float) GlobalSetting::find(1)->default_overhead_rate_applied)->toBe(10.0000);

        // (1666.6667 * 2) / 166.6667 = 20.0000
        $expense->update(['amount' => 3333.3334]);

        expect((float) GlobalSetting::find(1)->default_overhead_rate_applied)->toBe(20.0000);
    });
});
