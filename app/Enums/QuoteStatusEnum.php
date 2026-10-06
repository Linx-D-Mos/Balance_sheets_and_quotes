<?php

namespace App\Enums;

enum QuoteStatusEnum : string
{
    case DRAFT = 'draft';
    case SENT = 'sent';
    case APPROVED = 'approved';
    case CLOSED_BY_AMENDMENT = 'closed_by_amendment';
    case CANCELED = 'canceled';

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
        return __("Enums/QuoteStatus" . $this->value);
    }

    public function icon(): string
    {
        return match ($this) {
            self::DRAFT => 'fa-solid fa-file',
            self::SENT => 'fa-solid fa-paper-plane',
            self::APPROVED => 'fa-solid fa-check',
            self::CLOSED_BY_AMENDMENT => 'fa-solid fa-ban',
            self::CANCELED => 'fa-solid fa-times',
        };
    }

    public function bgColor(): string
    {
        return match ($this) {
            self::DRAFT => 'bg-secondary',
            self::SENT => 'bg-primary',
            self::APPROVED => 'bg-success',
            self::CLOSED_BY_AMENDMENT => 'bg-warning',
            self::CANCELED => 'bg-danger',
        };
    }

    public function bgText(): string
    {
        return match ($this) {
            self::DRAFT => 'text-secondary',
            self::SENT => 'text-primary',
            self::APPROVED => 'text-success',
            self::CLOSED_BY_AMENDMENT => 'text-warning',
            self::CANCELED => 'text-danger',
        };
    }
}
