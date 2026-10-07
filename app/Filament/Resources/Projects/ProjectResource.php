<?php

namespace App\Filament\Resources\Projects;

use App\Enums\ProjectStatusEnum;
use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Projects\Pages\EditProject;
use App\Filament\Resources\Projects\Pages\ListProjects;
use App\Filament\Resources\Projects\Schemas\ProjectForm;
use App\Filament\Resources\Projects\Tables\ProjectsTable;
use App\Models\Client;
use App\Models\Project;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'CATÁLOGOS';

    protected static ?string $modelLabel = 'Proyecto';

    protected static ?string $pluralModelLabel = 'Proyectos';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('client_id')
                ->label('Cliente')
                ->relationship('client', 'name')
                ->getOptionLabelFromRecordUsing(fn(Client $record) => $record->company_name ?: "{$record->first_name} {$record->last_name}")
                ->searchable()
                ->preload()
                ->required(),
            TextInput::make('code')
                ->label('Código del proyecto')
                ->required()
                ->unique(Project::class, 'code', ignoreRecord: true)
                ->maxLength(50),
            TextInput::make('title')
                ->label('Nombre de obra')
                ->required()
                ->maxLength(255),
            TextInput::make('address')
                ->label('Dirección del proyecto')
                ->maxLength(255),
            TextInput::make('city')
                ->label('Ciudad del proyecto')
                ->maxLength(100),
            TextInput::make('state')
                ->label('Estado del proyecto')
                ->maxLength(100),
            Textarea::make('project_description')
                ->label('Descripción del proyecto')
                ->rows(3)
                ->columnSpanFull()
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('code')
                ->label('Código del proyecto')
                ->sortable()
                ->searchable(),
            TextColumn::make('title')
                ->label('Obra')
                ->weight('bold')
                ->sortable()
                ->searchable(),
            TextColumn::make('client')
                ->label('Cliente')
                ->formatStateUsing(fn(Project $record) => $record->client?->company_name ?: "{$record->client?->first_name} {$record->client?->last_name}")
                ->searchable(query: function (Builder $query, string $search): Builder {
                    return $query->whereHas('client', function (Builder $q) use ($search) {
                        $q->where('company_name', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
                }),
            TextColumn::make('approvedQuote.title')
                ->label('Línea base activa')
                ->placeholder('Sin contización aprobada')
                ->badge()
                ->color(fn($state) => $state ? 'success' : 'gray'),
            TextColumn::make('status.display_name')
                ->label('Estado')
                ->badge()
                ->color(fn(Project $record) => match ($record->status?->code) {
                    ProjectStatusEnum::IN_PROGRESS => ProjectStatusEnum::IN_PROGRESS->bgColor(),
                    ProjectStatusEnum::COMPLETED => ProjectStatusEnum::COMPLETED->bgColor(),
                    ProjectStatusEnum::CANCELED => ProjectStatusEnum::CANCELED->bgColor(),
                    default => ProjectStatusEnum::DRAFT->bgColor(),
                }),
        ])->recordActions([
            Action::make('createQuote')
                ->label('Formular cotización')
                ->icon('heroicon-o-document-plus')
                ->color('primary')
                ->visible(fn(Project $record): bool => ! in_array($record->status?->code, [
                    ProjectStatusEnum::COMPLETED,
                    ProjectStatusEnum::CANCELED,
                ], true)),
            EditAction::make(),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProjects::route('/'),
            'create' => CreateProject::route('/create'),
            'edit' => EditProject::route('/{record}/edit'),
        ];
    }
}
