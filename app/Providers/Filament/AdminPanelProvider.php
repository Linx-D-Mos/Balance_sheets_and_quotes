<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Support\Facades\Blade;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('Lynx construction')
            ->font('Inter')
            ->colors([
                'primary' =>  Color::hex('#FF8A00'),
                'secondary' => Color::hex('#06034F'),
                'gray' => Color::Slate,
                'danger' => Color::Rose,
                'warning' => Color::Amber,
                'success' => Color::Emerald,
                'info' => Color::Sky,
            ])
            ->sidebarCollapsibleOnDesktop() // Menú lateral plegable/desplegable
            ->maxContentWidth(Width::Full)
            ->navigationGroups([
                'OPERACIÓN',
                'CATÁLOGOS',
                'BACK-OFFICE',
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn(): string => Blade::render('
                @vite([\'resources/css/app.css\', \'resources/js/app.js\'])
                <style>
                    /* Densidad compacta en celdas de tablas */
                    .fi-ta-cell,
                    .fi-ta-header-cell {
                        padding-top: 0.55rem !important;
                        padding-bottom: 0.55rem !important;
                    }

                    /* Ajuste de padding en inputs de búsqueda y formularios */
                    .fi-input-wrp {
                        padding-top: 0.15rem !important;
                        padding-bottom: 0.15rem !important;
                    }

                    /* En dispositivos móviles (< 768px), escala compacta a 14px para que quepan todas las columnas y switches */
                    @media (max-width: 768px) {
                        html {
                            font-size: 14px !important;
                        }
                    }

                    /* 1. Anula la separación entre las tabs y la tabla, y alinea a la izquierda */
.fi-page:has(.fi-ta-ctn) .fi-tabs {
    width: 100% !important;
    display: flex !important;
    justify-content: flex-start !important;
    background-color: white !important;
    border: 1px solid rgb(229 231 235) !important;
    border-bottom: 1px solid rgb(229 231 235) !important;
    border-top-left-radius: 0.75rem !important;
    border-top-right-radius: 0.75rem !important;
    border-bottom-left-radius: 0 !important;
    border-bottom-right-radius: 0 !important;
    box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05) !important;
    margin-bottom: -1.5rem !important; /* Absorbe el gap vertical de Filament */
    padding: 0 1rem !important;
    gap: 1.5rem !important;
}

.dark .fi-page:has(.fi-ta-ctn) .fi-tabs {
    background-color: rgb(17 24 39) !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
}

/* 2. Estilo de Pestaña Plana (Underline Tab) */
.fi-page:has(.fi-ta-ctn) .fi-tabs-item {
    position: relative !important;
    background: transparent !important;
    border: none !important;
    border-radius: 0 !important;
    padding: 0.85rem 0.5rem !important;
    font-weight: 500 !important;
    color: rgb(107 114 128) !important;
    box-shadow: none !important;
}

.fi-page:has(.fi-ta-ctn) .fi-tabs-item:hover {
    color: rgb(17 24 39) !important;
}

.dark .fi-page:has(.fi-ta-ctn) .fi-tabs-item {
    color: rgb(156 163 175) !important;
}

/* 3. Indicador de Pestaña Activa con el Naranja Corporativo */
.fi-page:has(.fi-ta-ctn) .fi-tabs-item[aria-selected="true"] {
    color: #FF8A00 !important;
    font-weight: 600 !important;
}

.fi-page:has(.fi-ta-ctn) .fi-tabs-item[aria-selected="true"]::after {
    content: \'\' !important;
    position: absolute !important;
    bottom: -1px !important;
    left: 0 !important;
    right: 0 !important;
    height: 2px !important;
    background-color: #FF8A00 !important;
}

/* 4. Fusionar la tabla eliminando sus esquinas y borde superior */
.fi-page:has(.fi-tabs) .fi-ta-ctn {
    border-top-left-radius: 0 !important;
    border-top-right-radius: 0 !important;
    border-top: none !important;
}
                </style>
                ')
            )
            ->renderHook(
                PanelsRenderHook::SIDEBAR_NAV_START,
                fn(): string => Blade::render('
                    <div class="px-3 py-2 my-2">
                        <div class="p-3 bg-slate-100 dark:bg-slate-800 rounded-xl border border-slate-200/80 dark:border-slate-700/80">
                            <span class="block text-[10px] font-bold tracking-wider text-slate-400 dark:text-slate-500 uppercase"></span>
                            <div class="flex items-center justify-between mt-1">
                                <span class="text-sm font-semibold text-slate-800 dark:text-slate-200"></span>
                                <span class="h-2 w-2 rounded-full bg-emerald-500 ring-4 ring-emerald-500/20"></span>
                            </div>
                        </div>
                    </div>
                ')
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
