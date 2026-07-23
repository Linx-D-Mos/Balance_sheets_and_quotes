<?php

namespace App\Filament\Resources\LaborRoles;

use App\Filament\Resources\LaborRoles\Pages\ManageLaborRoles;
use App\Models\LaborRole;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class LaborRoleResource extends Resource
{
    protected static ?string $model = LaborRole::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-briefcase';

    protected static UnitEnum|string|null $navigationGroup = 'BACK-OFFICE';

    protected static ?string $modelLabel = 'Role Técnico';

    protected static ?string $pluralModelLabel = 'Roles de Trabajo';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre del Rol')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('base_salary')
                    ->label('Salario Base (/HR)')
                    ->numeric()
                    ->prefix('$')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (callable $get, callable $set) {
                        $cost = LaborRole::calculateHourlyCost($get('base_salary'), $get('social_load_pct'));
                        $set('hourly_cost', number_format($cost, 2));
                    }),
                Forms\Components\TextInput::make('social_load_pct')
                    ->label('Carga Social (%)')
                    ->numeric()
                    ->suffix('%')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (callable $get, callable $set) {
                        $cost = LaborRole::calculateHourlyCost($get('base_salary'), $get('social_load_pct'));
                        $set('hourly_cost', number_format($cost, 2));
                    }),
                Forms\Components\TextInput::make('hourly_cost')
                    ->label('Costo Cargado Calculado (C_ch)')
                    ->prefix('$')
                    ->disabled()
                    ->dehydrated(false)
                    ->helperText('Valor calculado automáticamente por el sistema.'),
                Forms\Components\Toggle::make('is_active')
                    ->label('Disponible para Operaciones')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('base_salary')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('social_load_pct')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('hourly_cost')
                    ->money()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageLaborRoles::route('/'),
        ];
    }
}
