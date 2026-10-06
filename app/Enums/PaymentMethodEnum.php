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

    public function icon(): string
    {
        return match ($this) {
            self::CASH => 'fa-solid fa-money-bill',
            self::CHECK => 'fa-solid fa-money-check',
            self::CREDIT_CARD => 'fa-solid fa-credit-card',
            self::TRANSFER => 'fa-solid fa-university',
            self::ZELLE => 'fa-solid fa-mobile-screen-button',
        };
    }

    public function bgColor(): string
    {
        return match ($this) {
            self::CASH => 'bg-success',
            self::CHECK => 'bg-primary',
            self::CREDIT_CARD => 'bg-info',
            self::TRANSFER => 'bg-warning',
            self::ZELLE => 'bg-danger',
        };
    }

    public function bgText(): string
    {
        return match ($this) {
            self::CASH => 'text-success',
            self::CHECK => 'text-primary',
            self::CREDIT_CARD => 'text-info',
            self::TRANSFER => 'text-warning',
            self::ZELLE => 'text-danger',
        };
    }
}
