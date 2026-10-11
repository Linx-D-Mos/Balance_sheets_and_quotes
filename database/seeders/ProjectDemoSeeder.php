<?php

namespace Database\Seeders;

use App\Enums\ProjectStatusEnum;
use App\Enums\QuoteStatusEnum;
use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\Quote;
use App\Models\QuoteStatus;
use Illuminate\Database\Seeder;

class ProjectDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Clientes de muestra (empresa y persona natural)
        $clientCorp = Client::firstOrCreate(
            ['email' => 'contacto@pacificlogistics.com'],
            [
                'first_name' => 'Mario',
                'last_name' => 'Gómez',
                'company_name' => 'Pacific Logistics S.A.S.',
                'phone_number' => '+57 312 4567890',
                'city' => 'Buenaventura',
                'state' => 'Valle del Cauca',
                'address' => 'Terminal Marítimo Muelle 2',
            ]
        );

        $clientPerson = Client::firstOrCreate(
            ['email' => 'claudia.valencia@example.com'],
            [
                'first_name' => 'Claudia',
                'last_name' => 'Valencia',
                'company_name' => null,
                'phone_number' => '+57 318 6543210',
                'city' => 'Cali',
                'state' => 'Valle del Cauca',
                'address' => 'Ciudad Jardín, Cl. 14 # 105-30',
            ]
        );

        // 2. Resolución de estados maestros
        $draftStatus = ProjectStatus::ofCode(ProjectStatusEnum::DRAFT)->firstOrFail();
        $inProgressStatus = ProjectStatus::ofCode(ProjectStatusEnum::IN_PROGRESS)->firstOrFail();
        $completedStatus = ProjectStatus::ofCode(ProjectStatusEnum::COMPLETED)->firstOrFail();
        $cancelledStatus = ProjectStatus::ofCode(ProjectStatusEnum::CANCELED)->firstOrFail();

        // 3. Proyecto 1: Borrador (Sin cotización aprobada, acción createQuote activa)
        Project::firstOrCreate(
            ['code' => 'PRJ-2026-0001'],
            [
                'client_id' => $clientCorp->id,
                'project_status_id' => $draftStatus->id,
                'title' => 'Adecuación Bodega de Carga Refrigerada',
                'city' => 'Buenaventura',
                'state' => 'Valle del Cauca',
                'address' => 'Zona Franca Km 3',
                'project_description' => 'Instalación de paneles térmicos y pintura epóxica de pisos.',
            ]
        );

        // 4. Proyecto 2: En Ejecución con Cotización Aprobada (Línea Base Activa)
        $projectInProgress = Project::firstOrCreate(
            ['code' => 'PRJ-2026-0002'],
            [
                'client_id' => $clientPerson->id,
                'project_status_id' => $inProgressStatus->id,
                'title' => 'Remodelación Interior Casa Campestre',
                'city' => 'Cali',
                'state' => 'Valle del Cauca',
                'address' => 'Condominio Pance Real Casa 12',
                'actual_start_date' => now()->subDays(10),
                'project_description' => 'Pintura general, acabados de madera y remodelación de cocina.',
            ]
        );

        $approvedQuoteStatus = QuoteStatus::where('code', QuoteStatusEnum::APPROVED)->first();
        if ($approvedQuoteStatus) {
            Quote::firstOrCreate(
                [
                    'project_id' => $projectInProgress->id,
                    'title' => 'COT-2026-001 (Presupuesto Base)',
                ],
                [
                    'quote_status_id' => $approvedQuoteStatus->id,
                    'start_date' => now()->subDays(10),
                    'end_date' => now()->addDays(20),
                    'work_weekends' => false,
                    'amendment_level' => 0,
                    'total_hours' => 160,
                    'direct_labor_cost' => 3680.0000,
                    'direct_materials_cost' => 4500.0000,
                    'direct_cost' => 8180.0000,
                    'overhead_rate_applied' => 12.5000,
                    'overhead_cost' => 2000.0000,
                    'equilibrium_cost' => 10180.0000,
                    'margin_applied' => 25.0000,
                    'total_price' => 13573.3333,
                ]
            );
        }

        // 5. Proyecto 3: Finalizado (Acción createQuote oculta)
        Project::firstOrCreate(
            ['code' => 'PRJ-2026-0003'],
            [
                'client_id' => $clientCorp->id,
                'project_status_id' => $completedStatus->id,
                'title' => 'Impermeabilización Cubierta Principal',
                'city' => 'Buenaventura',
                'state' => 'Valle del Cauca',
                'address' => 'Sede Portuaria Calle 1 # 2-40',
                'actual_start_date' => now()->subDays(45),
                'actual_end_date' => now()->subDays(5),
                'project_description' => 'Mantenimiento correctivo de filtraciones en losa de concreto.',
            ]
        );

        // 6. Proyecto 4: Cancelado (Acción createQuote oculta)
        Project::firstOrCreate(
            ['code' => 'PRJ-2026-0004'],
            [
                'client_id' => $clientPerson->id,
                'project_status_id' => $cancelledStatus->id,
                'title' => 'Construcción Kiosco y Zona BBQ',
                'city' => 'Cali',
                'state' => 'Valle del Cauca',
                'address' => 'Corregimiento La Buitrera',
                'project_description' => 'Desistimiento por parte del propietario por ajuste presupuestario.',
            ]
        );
    }
}
