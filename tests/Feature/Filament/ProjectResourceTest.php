<?php

use App\Enums\ProjectStatusEnum;
use App\Filament\Resources\ProjectResource\Pages\CreateProject;
use App\Filament\Resources\ProjectResource\Pages\ListProjects;
use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Livewire\Livewire;


beforeEach(function () {
    $this->admin = User::factory()->create();
    $this->admin->assignRole('administrator');
    $this->actingAs($this->admin);
});

describe('Listado y Visualización de Proyectos (S2)', function () {
    it('renderiza exitosamente la página de índice de proyectos', function () {
        Livewire::test(ListProjects::class)
            ->assertSuccessful();
    });

    it('muestra los proyectos registrados con su cliente y código en la tabla', function () {
        $client = Client::factory()->create(['company_name' => 'Constructora Bolivariana']);
        $project = Project::factory()->create([
            'client_id' => $client->id,
            'title' => 'Adecuación Oficinas Centro',
            'code' => 'PRJ-1044',
        ]);

        Livewire::test(ListProjects::class)
            ->assertCanSeeTableRecords([$project]);
    });

    it('filtra proyectos mediante las pestañas de estado operativo', function () {
        $draftProject = Project::factory()->create();
        $inProgressProject = Project::factory()->inProgress()->create();
        $completedProject = Project::factory()->completed()->create();

        // Pestaña: En Ejecución
        Livewire::test(ListProjects::class)
            ->set('activeTab', 'in_progress')
            ->assertCanSeeTableRecords([$inProgressProject])
            ->assertCanNotSeeTableRecords([$draftProject, $completedProject]);

        // Pestaña: Finalizados
        Livewire::test(ListProjects::class)
            ->set('activeTab', 'completed')
            ->assertCanSeeTableRecords([$completedProject])
            ->assertCanNotSeeTableRecords([$draftProject, $inProgressProject]);
    });
});

describe('Acción Contextual de Creación de Cotización', function () {
    it('habilita la acción createQuote en proyectos en borrador o en ejecución', function () {
        $draftProject = Project::factory()->create();
        $inProgressProject = Project::factory()->inProgress()->create();

        Livewire::test(ListProjects::class)
            ->assertTableActionVisible('createQuote', $draftProject)
            ->assertTableActionVisible('createQuote', $inProgressProject);
    });

    it('oculta la acción createQuote en proyectos finalizados o cancelados', function () {
        $completedProject = Project::factory()->completed()->create();
        $canceledProject = Project::factory()->canceled()->create();

        Livewire::test(ListProjects::class)
            ->assertTableActionHidden('createQuote', $completedProject)
            ->assertTableActionHidden('createQuote', $canceledProject);
    });
});

describe('Creación de Proyectos (Formulario Base)', function () {
    it('persiste un nuevo proyecto con cliente y código válidos', function () {
        $client = Client::factory()->create();

        Livewire::test(CreateProject::class)
            ->fillForm([
                'client_id' => $client->id,
                'title' => 'Remodelación Torre Central',
                'code' => 'PRJ-2090',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('projects', [
            'client_id' => $client->id,
            'title' => 'Remodelación Torre Central',
            'code' => 'PRJ-2090',
        ]);
    });

    it('rechaza el formulario si el cliente o el título no se suministran', function () {
        Livewire::test(CreateProject::class)
            ->fillForm([
                'client_id' => null,
                'title' => null,
            ])
            ->call('create')
            ->assertHasFormErrors([
                'client_id' => 'required',
                'title' => 'required',
            ]);
    });
});
