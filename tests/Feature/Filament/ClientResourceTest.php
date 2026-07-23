<?php

use App\Enums\AppPermissionEnum;
use App\Filament\Resources\ClientResource\Pages\ManageClients;
use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->user->givePermissionTo(AppPermissionEnum::MANAGE_SETTINGS->value);
});

describe('ClientResource Filament Management', function () {
    it('can render client resource index page', function () {
        $this->actingAs($this->user);

        Livewire::test(ManageClients::class)
            ->assertSuccessful();
    });

    it('can list clients with proper columns', function () {
        $this->actingAs($this->user);

        $client = Client::factory()->create([
            'company_name' => 'Constructora Alfa S.A.',
            'first_name' => 'Roberto',
            'last_name' => 'Gómez',
            'email' => 'rgomez@alfa.com',
            'phone_number' => '+1 (555) 019-2834',
        ]);

        Project::factory()->count(3)->create([
            'client_id' => $client->id,
        ]);

        Livewire::test(ManageClients::class)
            ->assertCanSeeTableRecords([$client])
            ->assertTableColumnExists('company_name')
            ->assertTableColumnExists('email')
            ->assertTableColumnExists('phone_number');
    });

    it('can create client via header action', function () {
        $this->actingAs($this->user);

        Livewire::test(ManageClients::class)
            ->mountPageAction('create')
            ->setActionData([
                'company_name' => 'Inmobiliaria Horizon Corp',
                'first_name' => 'Carlos',
                'last_name' => 'Ruiz',
                'email' => 'contacto@horizon.com',
                'phone_number' => '+1 (555) 048-9182',
            ])
            ->callMountedPageAction()
            ->assertHasNoPageActionErrors();

        $this->assertDatabaseHas('clients', [
            'company_name' => 'Inmobiliaria Horizon Corp',
            'email' => 'contacto@horizon.com',
            'phone_number' => '+1 (555) 048-9182',
        ]);
    });

    it('can edit client details via table action', function () {
        $this->actingAs($this->user);

        $client = Client::factory()->create([
            'company_name' => 'Nombre Antiguo S.A.',
        ]);

        Livewire::test(ManageClients::class)
            ->mountTableAction('edit', $client)
            ->setTableActionData([
                'company_name' => 'Nombre Actualizado S.A.S.',
            ])
            ->callMountedTableAction()
            ->assertHasNoTableActionErrors();

        expect($client->refresh()->company_name)->toBe('Nombre Actualizado S.A.S.');
    });

    it('can search clients by company name or email', function () {
        $this->actingAs($this->user);

        $clientA = Client::factory()->create(['company_name' => 'Desarrollos Norte S.A.']);
        $clientB = Client::factory()->create(['company_name' => 'Pinturas Sur S.A.']);

        Livewire::test(ManageClients::class)
            ->searchTable('Desarrollos')
            ->assertCanSeeTableRecords([$clientA])
            ->assertCannotSeeTableRecords([$clientB]);
    });
});
