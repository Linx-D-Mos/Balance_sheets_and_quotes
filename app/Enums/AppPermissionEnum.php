<?php

namespace App\Enums;

enum AppPermissionEnum: string
{
    case MANAGE_SETTINGS = 'manage:settings';
    case VIEW_ANY_QUOTES = 'view_any_quotes';
    case CREATE_QUOTES = 'create:quotes';
    case APPROVE_QUOTES = 'approve:quotes';
    case EDIT_QUOTES = 'edit:quotes';
    case CREATE_ENMIENDAS = 'create:enmiendas';
    case WRITE_LOGS = 'write:logs';
    case CLOSE_PROJECTS = 'close:projects';

    /**
     * Get the plain text with each string in each case.
     *
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return __("Enums/AppPermission" . $this->value);
    }
}
