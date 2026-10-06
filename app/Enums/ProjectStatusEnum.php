<?php

namespace App\Enums;

enum ProjectStatusEnum: string
{
    case DRAFT = 'draft';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

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
        return __("Enums/ProjectStatus" . $this->value);
    }

    public function icon(): string
    {
        return match ($this) {
            self::DRAFT => 'fa-solid fa-file',
            self::IN_PROGRESS => 'fa-solid fa-spinner',
            self::COMPLETED => 'fa-solid fa-check',
            self::CANCELLED => 'fa-solid fa-ban',
        };
    }

    public function bgColor(): string
    {
        return match ($this) {
            self::DRAFT => 'bg-secondary',
            self::IN_PROGRESS => 'bg-primary',
            self::COMPLETED => 'bg-success',
            self::CANCELLED => 'bg-danger',
        };
    }

    public function bgText(): string
    {
        return match ($this) {
            self::DRAFT => 'text-secondary',
            self::IN_PROGRESS => 'text-primary',
            self::COMPLETED => 'text-success',
            self::CANCELLED => 'text-danger',
        };
    }
}
