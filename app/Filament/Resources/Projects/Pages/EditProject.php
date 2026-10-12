<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use App\Services\Project\ProjectService;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    public function getTitle(): string
    {
        return 'Editar Expediente de Obra';
    }

    public function getSubheading(): ?string
    {
        return 'Ajuste de parámetros descriptivos y trazabilidad del proyecto.';
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return app(ProjectService::class)->update($record, $data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        // RN-XX: Prohibido borrado físico individual y masivo para preservar integridad contable
        return [];
    }
}
