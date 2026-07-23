<?php

namespace App\Filament\Support\Columns;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\Str;

class CommonColumns
{
    /**
     * Columna de cliente / razón social principal con formato en negrita y búsqueda.
     */
    public static function displayName(string $name = 'company_name', string $label = 'CLIENTE / RAZÓN SOCIAL'): TextColumn
    {
        return TextColumn::make($name)
            ->label($label)
            ->formatStateUsing(fn ($state, $record) => $state ?: trim("{$record->first_name} {$record->last_name}"))
            ->searchable(['first_name', 'last_name', 'company_name', 'email'])
            ->sortable(['company_name', 'first_name'])
            ->weight(FontWeight::Bold);
    }

    /**
     * Columna de Correo Electrónico estandarizada.
     */
    public static function email(string $name = 'email', string $label = 'CORREO ELECTRÓNICO'): TextColumn
    {
        return TextColumn::make($name)
            ->label($label)
            ->searchable()
            ->copyable()
            ->placeholder('Sin correo');
    }

    /**
     * Columna de Teléfono estandarizada.
     */
    public static function phone(string $name = 'phone_number', string $label = 'TELÉFONO'): TextColumn
    {
        return TextColumn::make($name)
            ->label($label)
            ->placeholder('Sin teléfono');
    }

    /**
     * Columna de Insignia de Conteo (ej: "3 Proyectos", "18 Jornadas").
     */
    public static function countBadge(
        string $relation,
        string $singularLabel,
        string $pluralLabel,
        string $label = 'PROYECTOS VINCULADOS'
    ): TextColumn {
        return TextColumn::make("{$relation}_count")
            ->counts($relation)
            ->label($label)
            ->badge()
            ->formatStateUsing(fn ($state) => "{$state} " . ($state == 1 ? $singularLabel : $pluralLabel))
            ->color(fn ($state) => $state > 0 ? 'primary' : 'warning');
    }

    /**
     * Interruptor reutilizable de disponibilidad / estado activo.
     */
    public static function availability(string $name = 'is_active', string $label = 'DISPONIBILIDAD'): ToggleColumn
    {
        return ToggleColumn::make($name)
            ->label($label)
            ->onColor('primary')
            ->offColor('gray');
    }
}
