<?php

namespace App\Filament\Resources\FixedExpenses;

use App\Filament\Resources\FixedExpenses\Pages\ManageFixedExpenses;
use App\Filament\Support\Columns\CommonColumns;
use App\Models\FixedExpense;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class FixedExpenseResource extends Resource
{
    protected static ?string $model = FixedExpense::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static UnitEnum|string|null $navigationGroup = 'BACK-OFFICE';

    protected static ?string $modelLabel = 'Gasto Fijo';

    protected static ?string $pluralModelLabel = 'Gastos Fijos';

    protected static ?string $recordTitleAttribute = 'concept';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('concept')
                    ->label('Concepto')
                    ->required()
                    ->maxLength(255),
                TextInput::make('amount')
                    ->label('Monto mensual')
                    ->numeric()
                    ->prefix('$')
                    ->required()
                    ->minValue(0.01),
                Toggle::make('is_active')
                    ->label('Activo para Overhead')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->searchPlaceholder('Buscar gasto fijo por concepto...')
            ->recordTitleAttribute('concept')
            ->columns([
                TextColumn::make('concept')
                    ->label('CONCEPTO')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('amount')
                    ->label('MONTO MENSUAL')
                    ->money('USD')
                    ->sortable(),
                CommonColumns::availability('is_active', 'ESTADO'),
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
            ])
            ->toolbarActions([
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageFixedExpenses::route('/'),
        ];
    }
}
