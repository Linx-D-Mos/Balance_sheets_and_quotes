<?php

namespace App\Enums;

enum RoleEnum : string
{
    case ADMINISTRATOR = 'administrator';

    public function label(): string
    {
        return __("Enums/Role". strtolower($this->value));
    }
}
