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

    public function icon(): string
    {
        return match ($this) {
            self::BUDGETED => 'fa-solid fa-file-invoice-dollar',
            self::UNBUDGETED => 'fa-solid fa-file-invoice',
        };
    }

    public function bgColor(): string
    {
        return match ($this) {
            self::BUDGETED => 'bg-success',
            self::UNBUDGETED => 'bg-warning',
        };
    }

    public function bgText(): string
    {
        return match ($this) {
            self::BUDGETED => 'text-success',
            self::UNBUDGETED => 'text-warning',
        };
    }
}
