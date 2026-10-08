<?php

use App\Enums\ProjectStatusEnum;
use App\Models\Client;
use App\Models\Project;
use App\Services\Project\ProjectService;

beforeEach(function () {
    $this->projectService = app(ProjectService::class);
});

describe('ProjectService: Creación de Proyectos', function () {
    it('crea un proyecto con código autogenerado secuencial y estado borrador', function () {
        $client = Client::factory()->create();

        $project = $this->projectService->createProject([
            'client_id' => $client->id,
            'title' => 'Construcción Bodega Logística',
            'address' => 'Zona Portuaria Km 5',
            'city' => 'Buenaventura',
            'state' => 'Valle del Cauca',
            'project_description' => 'Estructura metálica y pisos de alta resistencia.',
        ]);

        expect($project)->toBeInstanceOf(Project::class)
            ->and($project->code)->toMatch('/^PRJ-\d{4}-\d{4}$/')
            ->and($project->status->code)->toBe(ProjectStatusEnum::DRAFT)
            ->and($project->actual_start_date)->toBeNull()
            ->and($project->actual_end_date)->toBeNull()
            ->and($project->city)->toBe('Buenaventura')
            ->and($project->state)->toBe('Valle del Cauca');
    });

    it('genera códigos correlativos sin colisiones en el mismo año fiscal', function () {
        $client = Client::factory()->create();
        $year = now()->year;

        $project1 = $this->projectService->createProject([
            'client_id' => $client->id,
            'title' => 'Obra Alfa',
        ]);

        $project2 = $this->projectService->createProject([
            'client_id' => $client->id,
            'title' => 'Obra Beta',
        ]);

        expect($project1->code)->toBe("PRJ-{$year}-0001")
            ->and($project2->code)->toBe("PRJ-{$year}-0002");
    });
});
