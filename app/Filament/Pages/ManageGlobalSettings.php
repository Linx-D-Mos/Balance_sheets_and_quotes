<?php

namespace App\Filament\Pages;

use App\Enums\AppPermissionEnum;
use App\Models\Employee;
use App\Models\FixedExpense;
use App\Models\GlobalSetting;
use App\Models\LaborRole;
use App\Services\YasumiCalendarService;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Collection;
use UnitEnum;

use function Illuminate\Support\hours;

class ManageGlobalSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static UnitEnum|string|null $navigationGroup = 'BACK-OFFICE';

    protected static ?string $navigationLabel = 'Parámetros globales';

    protected static ?string $title = 'Parámetros Globales & Back-Office';

    protected string $view = 'filament.pages.manage-global-settings';

    protected ?string $subheading = 'Configuración de costos fijos (Overhead), roles de trabajo y capacidad laboral mensual.';

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
            ->columns(2)
            ->schema([
                TextInput::make('default_profit_margin')
                    ->label('Margen de Ganancia Predeterminado (%)')
                    ->numeric()
                    ->required()
                    ->suffix('%'),
                TextInput::make('overtime_multiplier')
                    ->label('Multiplicador Horas Extras (Overtime)')
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

    public function toggleFixedExpense(int $id): void
    {
        $expense = FixedExpense::findOrFail($id);
        $expense->update(['is_active' => !$expense->is_active]);

        Notification::make()
            ->title('Gasto fijo actualizado exitosamente')
            ->success()
            ->send();
    }

    public function getActiveOverheadSum(): float
    {
        return (float) FixedExpense::where('is_active', true)->sum('amount');
    }

    public function getActiveOverheadCount(): int
    {
        return FixedExpense::where('is_active', true)->count();
    }

    public function getStandardMonthlyHours(): float
    {
        $settings = GlobalSetting::find(1) ?? GlobalSetting::first();
        if ($settings && (float) $settings->standard_monthly_hours > 0) {
            return (float) $settings->standard_monthly_hours;
        }

        return (new YasumiCalendarService())->getMonthlyStandardCapacityHours((int) date('Y'));
    }

    public function getOverheadRate(): float
    {
        $settings = GlobalSetting::find(1) ?? GlobalSetting::first();
        if ($settings && (float) $settings->default_overhead_rate_applied > 0) {
            return (float) $settings->default_overhead_rate_applied;
        }

        $hours = $this->getStandardMonthlyHours();

        return $hours > 0 ? round($this->getActiveOverheadSum() / $hours, 4) : 0.0000;
    }

    public function getCurrentMonthWorkingHours(): float
    {
        $start = now()->startOfMonth();
        $end = now()->endOfMonth();

        return (float) ((new YasumiCalendarService)->getWorkingDaysBetween($start, $end) * 8);
    }

    public function recalculateCapacity(YasumiCalendarService $yasumiCalendarService): void
    {
        $year = (int) now()->format('Y');

        $monthlyCapacity = $yasumiCalendarService->getMonthlyStandardCapacityHours($year);

        $settings = GlobalSetting::find(1) ?? GlobalSetting::first();

        if (! $settings) {
            return;
        }

        $activeOverhead = $this->getActiveOverheadSum();

        $newRate = $monthlyCapacity > 0 ? round($activeOverhead / $monthlyCapacity, 4) : 00.0000;

        $settings->update([
            'standard_monthly_hours' => $monthlyCapacity,
            'default_overhead_rate_applied' => $newRate,
        ]);

        Notification::make()
            ->title("Capacidad y overhead recalculados para {$year}")
            ->body("Nueva capacidad base: {$monthlyCapacity} hrs/mes. Tasa de overhead: \${$newRate}/hr.")
            ->success()
            ->send();
    }
    public function getFixedExpenses(): Collection
    {
        return FixedExpense::orderBy('is_active', 'desc')->orderBy('concept')->get();
    }

    public function getLaborRoles(): Collection
    {
        return LaborRole::where('is_active', true)->orderBy('name')->get();
    }

    public function getEmployees(): Collection
    {
        return Employee::latest()->take(6)->get();
    }
}
