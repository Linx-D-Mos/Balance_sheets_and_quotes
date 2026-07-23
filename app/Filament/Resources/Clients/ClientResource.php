<?php

namespace App\Filament\Resources\Clients;

use App\Filament\Resources\Clients\Pages\ManageClients;
use App\Filament\Support\Actions\CommonActions;
use App\Filament\Support\Columns\CommonColumns;
use App\Models\Client;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use UnitEnum;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-user-group';

    protected static UnitEnum|string|null $navigationGroup = 'CATÁLOGOS';

    protected static ?string $modelLabel = 'Cliente';

    protected static ?string $pluralModelLabel = 'Clientes';

    protected static ?string $recordTitleAttribute = 'company_name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('first_name')
                    ->label('Nombre')
                    ->maxLength(255),
                Forms\Components\TextInput::make('last_name')
                    ->label('Apellido')
                    ->maxLength(255),
                Forms\Components\TextInput::make('company_name')
                    ->label('Empresa / Razón Social (Opcional)')
                    ->maxLength(255)
                    ->helperText('Dejar en blanco si el cliente es una persona natural.'),
                Forms\Components\TextInput::make('email')
                    ->label('Correo Electrónico')
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone_number')
                    ->label('Teléfono')
                    ->maxLength(255),
                Forms\Components\TextInput::make('address')
                    ->label('Dirección')
                    ->maxLength(255),
                Forms\Components\TextInput::make('city')
                    ->label('Ciudad')
                    ->maxLength(255),
                Forms\Components\TextInput::make('state')
                    ->label('Estado / Provincia')
                    ->maxLength(255),
                Forms\Components\TextInput::make('zip_code')
                    ->label('Código Postal')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                CommonColumns::displayName(),
                CommonColumns::email(),
                CommonColumns::phone(),
                CommonColumns::countBadge('projects', 'Proyecto', 'Proyectos'),
            ])
            ->filters([
                //
            ])
            ->actions([
                CommonActions::editRowAction(),
                CommonActions::secondaryRowAction('create_project', 'Crear Proyecto'),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageClients::route('/'),
        ];
    }
}
