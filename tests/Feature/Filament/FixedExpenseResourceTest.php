<?php

use App\Enums\AppPermissionEnum;
use App\Filament\Resources\FixedExpenses\Pages\ManageFixedExpenses;
use App\Models\FixedExpense;
use App\Models\User;
use Livewire\Livewire;

describe('FixedExpenseResource Filament Management', function () {

    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->user->givePermissionTo(AppPermissionEnum::MANAGE_SETTINGS->value);
    });

    it('can render fixed expense resource index page', function () {
        $this->actingAs($this->user);

        Livewire::test(ManageFixedExpenses::class)
            ->assertSuccessful();
    });

    it('can list fixed expenses with proper columns', function () {
        $this->actingAs($this->user);

        $expense = FixedExpense::factory()->create([
            'concept' => 'Renta de Bodega y Oficina',
            'amount' => 1800.00,
            'is_active' => true,
        ]);

        Livewire::test(ManageFixedExpenses::class)
            ->assertCanSeeTableRecords([$expense])
            ->assertTableColumnExists('concept')
            ->assertTableColumnExists('amount')
            ->assertTableColumnExists('is_active');
    });

    it('can create fixed expense via header action (HU-01 / CA-01.1)', function () {
        $this->actingAs($this->user);

        Livewire::test(ManageFixedExpenses::class)
            ->mountAction('create')
            ->setActionData([
                'concept' => 'Seguro Liability Comercial',
                'amount' => 450.00,
                'is_active' => true,
            ])
            ->callMountedAction()
            ->assertHasNoActionErrors();

        $this->assertDatabaseHas('fixed_expenses', [
            'concept' => 'Seguro Liability Comercial',
            'amount' => 450.0000,
            'is_active' => true,
        ]);
    });

    it('can edit fixed expense details via table action', function () {
        $this->actingAs($this->user);

        $expense = FixedExpense::factory()->create([
            'concept' => 'Suscripción Software Base',
            'amount' => 80.00,
            'is_active' => true,
        ]);

        Livewire::test(ManageFixedExpenses::class)
            ->mountTableAction('edit', $expense)
            ->setTableActionData([
                'concept' => 'Suscripción ERP y Licencias',
                'amount' => 120.00,
            ])
            ->callMountedTableAction()
            ->assertHasNoTableActionErrors();

        expect($expense->refresh()->concept)->toBe('Suscripción ERP y Licencias')
            ->and((float) $expense->amount)->toBe(120.00);
    });

    it('can toggle fixed expense active status (CA-01.1)', function () {
        $this->actingAs($this->user);

        $expense = FixedExpense::factory()->create([
            'concept' => 'Línea Celular de Operaciones',
            'is_active' => true,
        ]);

        Livewire::test(ManageFixedExpenses::class)
            ->mountTableAction('edit', $expense)
            ->setTableActionData([
                'is_active' => false,
            ])
            ->callMountedTableAction()
            ->assertHasNoTableActionErrors();

        expect($expense->refresh()->is_active)->toBeFalse();
    });

    it('strictly forbids physical deletion of fixed expenses (RN-06 / CA-01.3)', function () {
        $this->actingAs($this->user);

        $expense = FixedExpense::factory()->create();

        Livewire::test(ManageFixedExpenses::class)
            ->assertTableActionDoesNotExist('delete');
    });

    it('can search fixed expenses by concept', function () {
        $this->actingAs($this->user);

        $expenseA = FixedExpense::factory()->create(['concept' => 'Renta de Bodega Norte']);
        $expenseB = FixedExpense::factory()->create(['concept' => 'Servicio de Electricidad']);

        Livewire::test(ManageFixedExpenses::class)
            ->searchTable('Bodega')
            ->assertCanSeeTableRecords([$expenseA])
            ->assertCanNotSeeTableRecords([$expenseB]);
    });
});
