<?php

namespace App\Enums;

enum PaymentMethodEnum: string
{
    case CASH = 'cash';
    case CHECK = 'check';
    case CREDIT_CARD = 'credit_card';
    case TRANSFER = 'transfer';
    case ZELLE = 'zelle';
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
        return __("Enums/PaymentMethod" . $this->value);
    }
}
