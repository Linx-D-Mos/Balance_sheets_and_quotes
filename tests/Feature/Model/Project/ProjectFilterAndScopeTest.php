<?php

use App\Enums\ProjectStatusEnum;
use App\Enums\QuoteStatusEnum;
use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\Quote;
use App\Models\QuoteStatus;
use Illuminate\Database\QueryException;

describe('Creación e Integridad de Proyectos', function () {
    it('crea un proyecto asociado correctamente a un cliente con estado inicial borrador', function () {
        $client = Client::factory()->create();

        $project = Project::factory()->create([
            'client_id' => $client->id,
            'title' => 'Remodelación Residencial Beta',
        ]);

        expect($project->client_id)->toBe($client->id)
            ->and($project->client->id)->toBe($client->id)
            ->and($project->status->code)->toBe(ProjectStatusEnum::DRAFT);
    });

    it('falla por restricción de base de datos si el código de proyecto está duplicado', function () {
        $client = Client::factory()->create();

        Project::factory()->create([
            'client_id' => $client->id,
            'code' => 'PRJ-10001',
        ]);

        expect(fn () => Project::factory()->create([
            'client_id' => $client->id,
            'code' => 'PRJ-10001',
        ]))->toThrow(QueryException::class);
    });

    it('falla por restricción de base de datos si se intenta persistir sin cliente', function () {
        expect(fn () => Project::create([
            'client_id' => null,
            'project_status_id' => ProjectStatus::ofCode(ProjectStatusEnum::DRAFT)->first()->id,
            'code' => 'PRJ-99999',
            'title' => 'Proyecto Huérfano',
        ]))->toThrow(QueryException::class);
    });
});

describe('Filtrado y Búsqueda Operativa de Proyectos', function () {
    it('filtra proyectos exclusivamente por cliente asignado', function () {
        $clientA = Client::factory()->create();
        $clientB = Client::factory()->create();

        $projectsA = Project::factory()->count(2)->create(['client_id' => $clientA->id]);
        $projectB = Project::factory()->create(['client_id' => $clientB->id]);

        $results = Project::forClient($clientA->id)->get();

        expect($results)->toHaveCount(2)
            ->and($results->pluck('id')->all())->toEqualCanonicalizing($projectsA->pluck('id')->all())
            ->and($results->pluck('client_id')->unique()->first())->toBe($clientA->id);
    });

    it('filtra proyectos por estado operativo del Enum', function () {
        $client = Client::factory()->create();

        Project::factory()->count(2)->create([
            'client_id' => $client->id,
        ]);

        $inProgressProject = Project::factory()->inProgress()->create([
            'client_id' => $client->id,
        ]);

        $results = Project::ofStatus(ProjectStatusEnum::IN_PROGRESS)->get();

        expect($results)->toHaveCount(1)
            ->and($results->first()->id)->toBe($inProgressProject->id)
            ->and($results->first()->status->code)->toBe(ProjectStatusEnum::IN_PROGRESS);
    });

    it('filtra proyectos por coincidencia parcial en el título', function () {
        $client = Client::factory()->create();

        $project1 = Project::factory()->create([
            'client_id' => $client->id,
            'title' => 'Pintura Interior Casa Campestre',
        ]);

        $project2 = Project::factory()->create([
            'client_id' => $client->id,
            'title' => 'Instalación Pisos Laminados',
        ]);

        $project3 = Project::factory()->create([
            'client_id' => $client->id,
            'title' => 'Pintura Fachada Exterior',
        ]);

        $results = Project::searchByTitle('Pintura')->get();

        expect($results)->toHaveCount(2)
            ->and($results->pluck('id')->all())->toEqualCanonicalizing([$project1->id, $project3->id]);
    });

    it('filtra proyectos por mes y año de fecha de inicio real', function () {
        $client = Client::factory()->create();

        $projectMay = Project::factory()->create([
            'client_id' => $client->id,
            'actual_start_date' => '2026-05-15',
        ]);

        Project::factory()->create([
            'client_id' => $client->id,
            'actual_start_date' => '2026-06-01',
        ]);

        $results = Project::startedInMonth(2026, 5)->get();

        expect($results)->toHaveCount(1)
            ->and($results->first()->id)->toBe($projectMay->id);
    });
});

describe('Resolución de Línea Base Financiera (approvedQuote)', function () {
    it('resuelve estrictamente la cotización en estado aprobada como línea base activa', function () {
        $client = Client::factory()->create();
        $project = Project::factory()->create(['client_id' => $client->id]);

        $draftStatus = QuoteStatus::where('code', QuoteStatusEnum::DRAFT)->firstOrFail();
        $approvedStatus = QuoteStatus::where('code', QuoteStatusEnum::APPROVED)->firstOrFail();

        Quote::create([
            'project_id' => $project->id,
            'quote_status_id' => $draftStatus->id,
            'title' => 'Cotización Inicial Borrador',
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-15',
        ]);

        $approvedQuote = Quote::create([
            'project_id' => $project->id,
            'quote_status_id' => $approvedStatus->id,
            'title' => 'Cotización Oficial Aprobada',
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-15',
        ]);

        $project->refresh();

        expect($project->approvedQuote)->not->toBeNull()
            ->and($project->approvedQuote->id)->toBe($approvedQuote->id)
            ->and($project->approvedQuote->status->code)->toBe(QuoteStatusEnum::APPROVED);
    });

    it('devuelve null si el proyecto no tiene ninguna cotización en estado aprobada', function () {
        $client = Client::factory()->create();
        $project = Project::factory()->create(['client_id' => $client->id]);

        $draftStatus = QuoteStatus::where('code', QuoteStatusEnum::DRAFT)->firstOrFail();

        Quote::create([
            'project_id' => $project->id,
            'quote_status_id' => $draftStatus->id,
            'title' => 'Solo Borrador',
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-15',
        ]);

        $project->refresh();

        expect($project->approvedQuote)->toBeNull();
    });
});
