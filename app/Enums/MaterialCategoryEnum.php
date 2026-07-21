<?php

namespace App\Enums;

enum MaterialCategoryEnum: string
{
    case BUDGETED = 'budgeted';
    case UNBUDGETED = 'unbudgeted';
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
        return __("Enums/MaterialCategory" . $this->value);
    }
}
