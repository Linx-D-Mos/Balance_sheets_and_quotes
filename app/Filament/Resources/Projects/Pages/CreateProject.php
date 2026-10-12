<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use App\Services\Project\ProjectService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;


class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;

    public function getTitle(): string
    {
        return 'Crear Contenedor de Obra';
    }

    public function getSubheading(): ?string
    {
        return 'Radicación de nuevo proyecto y vinculación contractual con el cliente.';
    }

    protected function handleRecordCreation(array $data): Model
    {
        return app(ProjectService::class)->save($data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
