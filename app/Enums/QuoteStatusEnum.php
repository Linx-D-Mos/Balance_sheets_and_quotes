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
}
