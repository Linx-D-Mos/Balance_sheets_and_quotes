<?php

use App\Enums\ProjectStatusEnum;

return [
    ProjectStatusEnum::DRAFT->value => 'Borrador',
    ProjectStatusEnum::IN_PROGRESS->value => 'En progreso',
    ProjectStatusEnum::COMPLETED->value => 'Completado',
    ProjectStatusEnum::CANCELED->value => 'Cancelado'
];
