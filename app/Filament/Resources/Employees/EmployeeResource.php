<?php

namespace App\Filament\Resources\Employees;

use App\Filament\Resources\Employees\Pages\ManageEmployees;
use App\Models\Employee;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use App\Filament\Support\Actions\CommonActions;
use App\Filament\Support\Columns\CommonColumns;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;;

use Filament\Tables\Table;
use Filament\Forms;
use Filament\Forms\Form;
use UnitEnum;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-identification';

    protected static UnitEnum|string|null $navigationGroup = 'CATÁLOGOS';

    protected static ?string $modelLabel = 'Operario';

    protected static ?string $pluralModelLabel = 'Personal / Roster';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre Completo')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_active')
                    ->label('Disponible para Operaciones')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->searchPlaceholder('Buscar operario por nombre...')
            ->recordTitleAttribute('name')
            ->columns([
                CommonColumns::displayName('name', 'OPERARIO / NOMBRE COMPLETO'),
                CommonColumns::availability('is_active', 'DISPONIBILIDAD OPERATIVA'),
            ])
            ->filters([
                //
            ])
            ->actions([
                CommonActions::editRowAction(),
            ])
            ->bulkActions([
                // RN-06 / CA-05.3: Prohibido borrado físico individual y masivo
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageEmployees::route('/'),
        ];
    }
}
