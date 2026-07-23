<?php

namespace App\Filament\Resources\Employees\Pages;

use App\Filament\Resources\Employees\EmployeeResource;
use App\Filament\Support\Actions\CommonActions;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\Support\Htmlable;
use Override;

class ManageEmployees extends ManageRecords
{
    protected static string $resource = EmployeeResource::class;

    public function getTitle(): string
    {
        return 'Catálogo de Personal';
    }

    public function getSubheading(): ?string
    {
        return 'Gestión de alta rápida y disponibilidad de campo.';
    }

    protected function getHeaderActions(): array
    {
        return [
            CommonActions::createHeaderAction('Registrar Trabajador', 'heroicon-o-plus'),
        ];
    }
}
