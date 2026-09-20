<?php

namespace App\Filament\Resources\FixedExpenses\Pages;

use App\Filament\Resources\FixedExpenses\FixedExpenseResource;
use App\Filament\Support\Actions\CommonActions;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Icons\Heroicon;

class ManageFixedExpenses extends ManageRecords
{
    protected static string $resource = FixedExpenseResource::class;

    public function getTitle(): string
    {
        return 'Catálogo de Gastos Fijos';
    }

    public function getSubheading(): ?string
    {
        return 'Gestión de alta rápida y disponibilidad de campo.';
    }

    protected function getHeaderActions(): array
    {
        return [
            CommonActions::createHeaderAction('Registrar gasto fijo', 'heroicon-o-plus'),
        ];
    }
}
