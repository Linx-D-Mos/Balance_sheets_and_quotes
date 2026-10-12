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
    public static function displayName(
        string $name = 'company_name',
        string $label = 'CLIENTE / RAZÓN SOCIAL',
        ?array $searchableColumns = null
    ): TextColumn {
        $column = TextColumn::make($name)
            ->label($label)
            ->sortable()
            ->weight('bold');

        if ($name === 'company_name') {
            $column->formatStateUsing(fn($state, $record): string => $state ?: trim("{$record->first_name} {$record->last_name}"));
        }

        $columnsToSearch = $searchableColumns ?? ($name === 'company_name' ? ['company_name', 'first_name', 'last_name', 'email'] : [$name]);

        return $column->searchable($columnsToSearch);
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
            ->formatStateUsing(fn($state) => "{$state} " . ($state == 1 ? $singularLabel : $pluralLabel))
            ->color(fn($state) => $state > 0 ? 'primary' : 'warning');
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

    /**
     * Columna de título de registro principal con código o identificador secundario en negrita.
     */
    public static function recordTitleWithCode(
        string $titleField = 'title',
        string $codeField = 'code',
        string $label = 'PROYECTO'
    ): TextColumn {
        return TextColumn::make($titleField)
            ->label($label)
            ->weight('bold')
            ->description(fn ($record): ?string => $record?->{$codeField})
            ->sortable()
            ->searchable();
    }

    /**
     * Columna estandarizada para clientes vinculados con formato corporativo y búsqueda insensible a mayúsculas.
     */
    public static function client(
        string $relationship = 'client',
        string $label = 'CLIENTE'
    ): TextColumn {
        return TextColumn::make($relationship)
            ->label($label)
            ->formatStateUsing(fn ($record): string => $record->{$relationship}?->company_name ?: trim("{$record->{$relationship}?->first_name} {$record->{$relationship}?->last_name}"))
            ->searchable(query: function ($query, string $search) use ($relationship) {
                return $query->whereHas($relationship, function ($q) use ($search) {
                    $q->where('company_name', 'ilike', "%{$search}%")
                        ->orWhere('first_name', 'ilike', "%{$search}%")
                        ->orWhere('last_name', 'ilike', "%{$search}%");
                });
            });
    }

    /**
     * Insignia de estado estandarizada que consume contratos nativos de Filament (HasLabel, HasColor, HasIcon).
     */
    public static function statusBadge(
        string $name = 'status.code',
        string $label = 'ESTADO'
    ): TextColumn {
        return TextColumn::make($name)
            ->label($label)
            ->badge();
    }

    /**
     * Insignia estandarizada para línea base activa o relación de cotización aprobada.
     */
    public static function activeBaseline(
        string $name = 'approvedQuote.title',
        string $label = 'LÍNEA BASE ACTIVA',
        string $placeholder = 'Sin cotización aprobada'
    ): TextColumn {
        return TextColumn::make($name)
            ->label($label)
            ->placeholder($placeholder)
            ->badge()
            ->color(fn ($state): string => $state ? 'success' : 'gray');
    }
}
