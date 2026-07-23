<?php

use App\Enums\AppPermissionEnum;
use App\Filament\Resources\Employees\Pages\ManageEmployees;
use App\Models\Employee;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->user->givePermissionTo(AppPermissionEnum::MANAGE_SETTINGS->value);
});

describe('EmployeeResource Filament Management', function () {
    it('can render employee resource index page', function () {
        $this->actingAs($this->user);

        Livewire::test(ManageEmployees::class)
            ->assertSuccessful();
    });

    it('can list employees with proper columns', function () {
        $this->actingAs($this->user);

        $employee = Employee::factory()->create([
            'name' => 'Johan Lince',
            'is_active' => true,
        ]);

        Livewire::test(ManageEmployees::class)
            ->assertCanSeeTableRecords([$employee])
            ->assertTableColumnExists('name')
            ->assertTableColumnExists('is_active');
    });

    it('can create employee with name only (HU-05)', function () {
        $this->actingAs($this->user);

        Livewire::test(ManageEmployees::class)
            ->mountAction('create')
            ->setActionData([
                'name' => 'Carlos Ruiz',
            ])
            ->callMountedAction()
            ->assertHasNoActionErrors();

        $this->assertDatabaseHas('employees', [
            'name' => 'Carlos Ruiz',
            'is_active' => true,
        ]);
    });

it('can edit employee status via edit action (CA-05.2)', function () {
        $this->actingAs($this->user);

        $employee = Employee::factory()->create([
            'name' => 'Alex Moreno',
            'is_active' => true,
        ]);

        Livewire::test(ManageEmployees::class)
            ->mountTableAction('edit', $employee)
            ->setTableActionData([
                'is_active' => false,
            ])
            ->callMountedTableAction()
            ->assertHasNoTableActionErrors();

        expect($employee->refresh()->is_active)->toBeFalse();
    });

    it('does not allow physical deletion of employees (RN-06 / CA-05.3)', function () {
        $this->actingAs($this->user);

        $employee = Employee::factory()->create();

        Livewire::test(ManageEmployees::class)
            ->assertTableActionDoesNotExist('delete');
    });
});
