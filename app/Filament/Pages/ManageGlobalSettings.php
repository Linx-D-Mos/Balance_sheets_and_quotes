<?php

namespace App\Filament\Pages;

use App\Enums\AppPermissionEnum;
use App\Models\GlobalSetting;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use UnitEnum;

class ManageGlobalSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static UnitEnum|string|null $navigationGroup = 'BACK-OFFICE';

    protected static ?string $navigationLabel = 'Parámetros globales';

    protected static ?string $title = 'Parámetros Globales & Back-Office';

    protected string $view = 'filament.pages.manage-global-settings';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->can(AppPermissionEnum::MANAGE_SETTINGS->value) ?? false;
    }

    public function mount(): void
    {
        $settings = GlobalSetting::find(1) ?? GlobalSetting::first();

        $this->form->fill($settings?->toArray() ?? []);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
        ->schema([
            TextInput::make('default_profit_margin')
            ->label('Margen de Ganancia Predeterminado (%)')
            ->numeric()
            ->required()
            ->suffix('%'),
            TextInput::make('overtime_multiplier')
            ->label('Multiplicador Horas Extras')
            ->numeric()
            ->required()
            ->suffix('x'),
        ])->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        GlobalSetting::updateOrCreate(
            ['id' => 1],
            [
                'default_profit_margin' => $state['default_profit_margin'],
                'overtime_multiplier' => $state['overtime_multiplier'],
            ]
        );

        Notification::make()
            ->title('Configuración guardada exitosamente')
            ->success()
            ->send();
    }
}
