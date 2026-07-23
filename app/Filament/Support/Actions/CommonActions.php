<?php

namespace App\Filament\Support\Actions;

use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;

class CommonActions
{
    /**
     * Botón superior primario de creación (ej: "+ Nuevo Cliente", "+ Registrar Trabajador").
     */
    public static function createHeaderAction(string $label, string $icon = 'heroicon-m-user-plus'): CreateAction
    {
        return CreateAction::make()
            ->label($label)
            ->icon($icon);
    }

    /**
     * Acciones estándar de edición en fila de tabla.
     */
    public static function editRowAction(): EditAction
    {
        return EditAction::make();
    }

    /**
     * Accion secundaria de fila (ej: "Crear Proyecto" outlined con icono de carpeta).
     */
    public static function secondaryRowAction(string $name, string $label, string $icon = 'heroicon-o-folder-plus'): Action
    {
        return Action::make($name)
            ->label($label)
            ->icon($icon)
            ->outlined()
            ->color('primary');
    }
}
