<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Models\Client;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Información General de la Obra')
                ->description('Datos de radicación descriptiva del proyecto y vinculación comercial.')
                ->columns(2)
                ->schema([
                    TextInput::make('code')
                        ->label('Código de Proyecto')
                        ->disabled()
                        ->dehydrated(false)
                        ->visibleOn('edit')
                        ->helperText('Identificador único inmutable generado por el sistema.'),

                    Select::make('client_id')
                        ->label('Cliente')
                        ->relationship('client', 'id')
                        ->getOptionLabelFromRecordUsing(fn (Client $record) => $record->company_name ?: "{$record->first_name} {$record->last_name}")
                        ->searchable()
                        ->preload()
                        ->required()
                        ->disabledOn('edit')
                        ->helperText('Vinculación contractual fija. No modificable tras la creación.'),

                    TextInput::make('title')
                        ->label('Nombre de Obra / Título')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(fn (string $operation): int => $operation === 'create' ? 2 : 1),

                    TextInput::make('address')
                        ->label('Dirección del Proyecto')
                        ->maxLength(255),

                    TextInput::make('city')
                        ->label('Ciudad')
                        ->maxLength(100),

                    TextInput::make('state')
                        ->label('Departamento / Estado')
                        ->maxLength(100),

                    Textarea::make('project_description')
                        ->label('Descripción / Alcance Inicial')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
