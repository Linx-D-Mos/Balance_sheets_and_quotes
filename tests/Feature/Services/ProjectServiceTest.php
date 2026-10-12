<?php

use App\Enums\ProjectStatusEnum;
use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectStatus;
use App\Services\Project\ProjectService;

beforeEach(function () {
    $this->projectService = app(ProjectService::class);
});

describe('ProjectService: Creación de Proyectos (save)', function () {
    it('crea un proyecto con código autogenerado secuencial y estado borrador', function () {
        $client = Client::factory()->create();

        $project = $this->projectService->save([
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

    it('ignora intentos de inyectar fechas reales o alterar el código en la creación', function () {
        $client = Client::factory()->create();

        $project = $this->projectService->save([
            'client_id' => $client->id,
            'title' => 'Remodelación Oficinas Puerto',
            'code' => 'CODIGO-HACK-01',
            'actual_start_date' => '2026-01-01',
            'actual_end_date' => '2026-02-01',
            'project_status_id' => ProjectStatus::ofCode(ProjectStatusEnum::COMPLETED)->firstOrFail()->id,
        ]);

        expect($project->code)->not->toBe('CODIGO-HACK-01')
            ->and($project->code)->toMatch('/^PRJ-\d{4}-\d{4}$/')
            ->and($project->status->code)->toBe(ProjectStatusEnum::DRAFT)
            ->and($project->actual_start_date)->toBeNull()
            ->and($project->actual_end_date)->toBeNull();
    });

    it('genera códigos correlativos sin colisiones en el mismo año fiscal', function () {
        $client = Client::factory()->create();
        $year = now()->year;

        $project1 = $this->projectService->save([
            'client_id' => $client->id,
            'title' => 'Obra Alfa',
        ]);

        $project2 = $this->projectService->save([
            'client_id' => $client->id,
            'title' => 'Obra Beta',
        ]);

        expect($project1->code)->toBe("PRJ-{$year}-0001")
            ->and($project2->code)->toBe("PRJ-{$year}-0002");
    });
});

describe('ProjectService: Actualización Descriptiva (update)', function () {
    it('actualiza únicamente datos descriptivos sin alterar cliente, código ni fechas', function () {
        $clientA = Client::factory()->create();
        $clientB = Client::factory()->create();

        $project = $this->projectService->save([
            'client_id' => $clientA->id,
            'title' => 'Nombre Original',
            'city' => 'Buenaventura',
        ]);

        $originalCode = $project->code;

        $updatedProject = $this->projectService->update($project, [
            'client_id' => $clientB->id,
            'code' => 'PRJ-OVERWRITE',
            'title' => 'Nombre Actualizado',
            'city' => 'Cali',
            'address' => 'Avenida Colombia # 10-20',
            'actual_start_date' => '2026-05-01',
            'actual_end_date' => '2026-05-30',
        ]);

        expect($updatedProject->title)->toBe('Nombre Actualizado')
            ->and($updatedProject->city)->toBe('Cali')
            ->and($updatedProject->address)->toBe('Avenida Colombia # 10-20')
            ->and($updatedProject->client_id)->toBe($clientA->id)
            ->and($updatedProject->code)->toBe($originalCode)
            ->and($updatedProject->actual_start_date)->toBeNull()
            ->and($updatedProject->actual_end_date)->toBeNull();
    });
});
