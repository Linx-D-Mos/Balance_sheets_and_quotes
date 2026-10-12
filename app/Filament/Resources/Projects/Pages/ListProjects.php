<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Enums\ProjectStatusEnum;
use App\Filament\Resources\Projects\ProjectResource;
use App\Models\Project;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Todos'),
            'draft' => Tab::make('Borrador')
                ->modifyQueryUsing(fn(Builder $query) => $query->ofStatus(ProjectStatusEnum::DRAFT))
                ->badge(fn() => Project::ofStatus(ProjectStatusEnum::DRAFT)->count())
                ->badgeColor(ProjectStatusEnum::DRAFT->getColor()),
            'in_progress' => Tab::make('En progreso')
                ->modifyQueryUsing(fn(Builder $query) => $query->ofStatus(ProjectStatusEnum::IN_PROGRESS))
                ->badge(fn() => Project::ofStatus(ProjectStatusEnum::IN_PROGRESS)->count())
                ->badgeColor(ProjectStatusEnum::IN_PROGRESS->bgColor()),
            'completed' => Tab::make('Completados')
                ->modifyQueryUsing(fn(Builder $query) => $query->ofStatus(ProjectStatusEnum::COMPLETED))
                ->badge(fn() => Project::ofStatus(ProjectStatusEnum::COMPLETED)->count())
                ->badgeColor(ProjectStatusEnum::COMPLETED->bgColor()),
            'canceled' => Tab::make('Cancelados')
                ->modifyQueryUsing(fn(Builder $query) => $query->ofStatus(ProjectStatusEnum::CANCELED))
                ->badge(fn() => Project::ofStatus(ProjectStatusEnum::CANCELED)->count())
                ->badgeColor(ProjectStatusEnum::CANCELED->bgColor()),
        ];
    }
}
