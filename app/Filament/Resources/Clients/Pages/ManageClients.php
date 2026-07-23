<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientResource;
use App\Filament\Support\Actions\CommonActions;
use Filament\Resources\Pages\ManageRecords;

class ManageClients extends ManageRecords
{
    protected static string $resource = ClientResource::class;

    public function getTitle(): string
    {
        return 'Directorio Maestro de Clientes';
    }

    public function getSubheading(): ?string
    {
        return 'Gestión de cuentas comerciales y vinculación directa de contenedores de obra.';
    }

    protected function getHeaderActions(): array
    {
        return [
            CommonActions::createHeaderAction('Nuevo Cliente'),
        ];
    }
}
