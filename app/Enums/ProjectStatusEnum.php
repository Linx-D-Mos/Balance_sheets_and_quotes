<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ProjectStatusEnum: string implements HasLabel, HasIcon, HasColor
{
    case DRAFT = 'draft';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELED = 'canceled';

    /**
     * Get the plain text with each string in each case.
     *
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getLabel(): string
    {
        return __("Enums/ProjectStatus.{$this->value}");
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::DRAFT => 'heroicon-m-document-text',
            self::IN_PROGRESS => 'heroicon-m-arrow-path',
            self::COMPLETED => 'heroicon-m-check-badge',
            self::CANCELED => 'heroicon-m-x-circle',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::IN_PROGRESS => 'info',
            self::COMPLETED => 'success',
            self::CANCELED => 'danger',
        };
    }

    public function bgText(): string
    {
        return match ($this) {
            self::DRAFT => 'secondary',
            self::IN_PROGRESS => 'primary',
            self::COMPLETED => 'success',
            self::CANCELED => 'danger',
        };
    }
}
