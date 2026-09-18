<?php

namespace App\Filament\Resources\LaborRoles\Pages;

use App\Filament\Resources\LaborRoles\LaborRoleResource;
use App\Filament\Support\Actions\CommonActions;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageLaborRoles extends ManageRecords
{
    protected static string $resource = LaborRoleResource::class;

    public function getTitle(): string
    {
        return 'Catálogo de Roles';
    }

    public function getSubheading(): ?string
    {
        return 'Gestión de alta rápida y disponibilidad de campo.';
    }

    protected function getHeaderActions(): array
    {
        return [
            CommonActions::createHeaderAction('Registrar Rol', 'heroicon-o-plus'),
        ];
    }
}
