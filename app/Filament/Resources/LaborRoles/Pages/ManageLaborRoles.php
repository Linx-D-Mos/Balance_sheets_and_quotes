<?php

namespace App\Filament\Resources\LaborRoles\Pages;

use App\Filament\Resources\LaborRoles\LaborRoleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageLaborRoles extends ManageRecords
{
    protected static string $resource = LaborRoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
