<?php

use App\Enums\AppPermissionEnum;

return [
    AppPermissionEnum::MANAGE_SETTINGS->value => 'Administrar Configuración',
    AppPermissionEnum::WRITE_LOGS->value => 'Escribir Logs',
    AppPermissionEnum::VIEW_PROJECTS->value => 'Ver Proyectos',
    AppPermissionEnum::CREATE_PROJECTS->value => 'Crear Proyectos',
    AppPermissionEnum::EDIT_PROJECTS->value => 'Editar Proyectos',
    AppPermissionEnum::CLOSE_PROJECTS->value => 'Cerrar Proyectos',
    AppPermissionEnum::VIEW_ANY_QUOTES->value => 'Ver Todas las Cotizaciones',
    AppPermissionEnum::CREATE_QUOTES->value => 'Crear Cotizaciones',
    AppPermissionEnum::APPROVE_QUOTES->value => 'Aprobar Cotizaciones',
    AppPermissionEnum::EDIT_QUOTES->value => 'Editar Cotizaciones',
    AppPermissionEnum::CREATE_ENMIENDAS->value => 'Crear Enmiendas',
];
