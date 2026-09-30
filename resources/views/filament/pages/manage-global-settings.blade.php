<x-filament-panels::page>
    <div class="space-y-6">
        {{-- 1. Tarjetas Superiores de Métricas --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="p-5 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <x-heroicon-o-document-text class="w-4 h-4 text-slate-400" />
                    Overhead Mensual Activo
                </div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">
                        ${{ number_format($this->getActiveOverheadSum(), 2) }}
                    </span>
                </div>
                <p class="mt-1 text-xs text-slate-400">
                    Suma de {{ $this->getActiveOverheadCount() }} gastos fijos activos
                </p>
            </div>

            <div class="p-5 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        <x-heroicon-o-clock class="w-4 h-4 text-slate-400" />
                        Capacidad Laboral Promedio
                    </div>
                    <x-heroicon-o-calendar class="w-4 h-4 text-slate-400" />
                </div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">
                        {{ number_format($this->getStandardMonthlyHours(), 0) }}
                    </span>
                    <span class="text-lg font-bold text-slate-700 dark:text-slate-300">hrs/mes</span>
                </div>
                <p class="mt-1 text-xs text-slate-400">
                    Deducido offline vía Yasumi (USA Holidays)
                </p>
            </div>
        </div>

        {{-- 2. Cuerpo Principal en Cuadrícula (6 / 6 Columnas) --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

            {{-- Columna Izquierda: Costos Fijos y Roster --}}
            <div class="space-y-6">
                {{-- Gastos Fijos --}}
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-document-text class="w-5 h-5 text-slate-400" />
                            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Gastos Fijos Mensuales (Overhead)</h3>
                        </div>
                        <a href="{{ route('filament.admin.resources.fixed-expenses.index') }}" title="Agregar / Gestionar Gastos Fijos" class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-indigo-600 dark:text-indigo-400 transition">
                            <x-heroicon-m-plus class="w-5 h-5" />
                        </a>
                    </div>

                    {{-- Cabecera de Tabla --}}
                    <div class="grid grid-cols-12 text-[10px] font-bold text-slate-400 uppercase tracking-wider py-2.5 border-b border-slate-100 dark:border-slate-800">
                        <div class="col-span-6">CONCEPTO</div>
                        <div class="col-span-3 text-right">MONTO MENSUAL</div>
                        <div class="col-span-3 text-right">ESTADO</div>
                    </div>

                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($this->getFixedExpenses() as $expense)
                            <div class="py-3 grid grid-cols-12 items-center text-xs {{ ! $expense->is_active ? 'opacity-40' : '' }}">
                                <div class="col-span-6 font-medium text-slate-700 dark:text-slate-300 truncate pr-2">
                                    {{ $expense->concept }}
                                </div>
                                <div class="col-span-3 text-right font-bold text-slate-900 dark:text-white">
                                    ${{ number_format($expense->amount, 2) }}
                                </div>
                                <div class="col-span-3 flex justify-end">
                                    <button
                                        type="button"
                                        wire:click="toggleFixedExpense({{ $expense->id }})"
                                        class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-hidden {{ $expense->is_active ? 'bg-emerald-500' : 'bg-slate-200 dark:bg-slate-700' }}"
                                    >
                                        <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow-xs transition duration-200 ease-in-out {{ $expense->is_active ? 'translate-x-4' : 'translate-x-0' }}"></span>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <p class="py-4 text-xs text-slate-400 text-center">No hay gastos fijos registrados.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Roster de Personal --}}
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-users class="w-5 h-5 text-slate-400" />
                            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Roster de Personal (Employees)</h3>
                        </div>
                        <a href="{{ route('filament.admin.resources.employees.index') }}" class="text-indigo-600 hover:text-indigo-500 text-xs font-semibold">
                            Ver catálogo →
                        </a>
                    </div>

                    {{-- Buscador / Agregar trabajador --}}
                    <div class="mt-3">
                        <a href="{{ route('filament.admin.resources.employees.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs text-slate-400 bg-slate-50/70 hover:bg-slate-100/90 dark:bg-slate-800/40 rounded-lg border border-slate-200 dark:border-slate-700 transition">
                            <x-heroicon-o-user-plus class="w-4 h-4 text-slate-400" />
                            <span>+ Nuevo Trabajador (Buscar o agregar)</span>
                        </a>
                    </div>

                    <div class="mt-3 space-y-2">
                        @forelse($this->getEmployees() as $employee)
                            <div class="flex items-center justify-between p-2.5 rounded-lg bg-slate-50/50 dark:bg-slate-800/50">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full {{ $employee->is_active ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-400' : 'bg-slate-200 text-slate-500 dark:bg-slate-800 dark:text-slate-400' }} flex items-center justify-center font-bold text-xs">
                                        {{ strtoupper(substr($employee->name, 0, 2)) }}
                                    </div>
                                    <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">{{ $employee->name }}</span>
                                </div>
                                <span class="text-[11px] font-semibold px-2.5 py-0.5 rounded border {{ $employee->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200/80 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800' : 'bg-slate-100 text-slate-500 border-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700' }}">
                                    {{ $employee->is_active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </div>
                        @empty
                            <p class="py-4 text-xs text-slate-400 text-center">No hay operarios registrados en el roster.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Columna Derecha: Roles de Trabajo y Multiplicadores --}}
            <div class="space-y-6">
                {{-- Roles Técnicos --}}
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-briefcase class="w-5 h-5 text-slate-400" />
                            <div>
                                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Catálogo de Roles & Tarifas Cargadas (C_ch)</h3>
                                <p class="text-[11px] text-slate-400 mt-0.5">Define el costo base y la carga patronal/social por tipo de operario.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Cabecera de Tabla Roles --}}
                    <div class="grid grid-cols-12 text-[10px] font-bold text-slate-400 uppercase tracking-wider py-2.5 border-b border-slate-100 dark:border-slate-800">
                        <div class="col-span-4">ROL</div>
                        <div class="col-span-3 text-right">SALARIO BASE</div>
                        <div class="col-span-2 text-right">CARGA (%)</div>
                        <div class="col-span-3 text-right">TARIFA RESULTANTE</div>
                    </div>

                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($this->getLaborRoles() as $role)
                            <div class="py-2.5 grid grid-cols-12 items-center text-xs gap-2">
                                <div class="col-span-4 font-semibold text-slate-800 dark:text-slate-200 truncate">
                                    {{ $role->name }}
                                </div>
                                <div class="col-span-3">
                                    <div class="px-2 py-1 rounded border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 font-mono text-xs text-right">
                                        ${{ number_format($role->base_salary, 2) }}
                                    </div>
                                </div>
                                <div class="col-span-2">
                                    <div class="px-2 py-1 rounded border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 font-mono text-xs text-right">
                                        {{ number_format($role->social_load_pct, 1) }}%
                                    </div>
                                </div>
                                <div class="col-span-3 flex justify-end">
                                    <div class="inline-flex items-center gap-1 px-2 py-1 rounded bg-indigo-50 border border-indigo-100 dark:bg-indigo-950/40 dark:border-indigo-900 text-indigo-600 dark:text-indigo-400 font-bold font-mono text-xs">
                                        <x-heroicon-s-lock-closed class="w-3 h-3 text-indigo-500" />
                                        <span>${{ number_format($role->hourly_cost, 2) }}/h</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="py-4 text-xs text-slate-400 text-center">No hay roles técnicos configurados.</p>
                        @endforelse
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                        <a href="{{ route('filament.admin.resources.labor-roles.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-xs transition">
                            <x-heroicon-m-bookmark-square class="w-4 h-4" />
                            <span>Gestionar Catálogo de Roles</span>
                        </a>
                    </div>
                </div>

                {{-- Multiplicadores Globales (Formulario Principal) --}}
                <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs">
                    <div class="flex items-center gap-2 pb-3 border-b border-slate-100 dark:border-slate-800 mb-4">
                        <x-heroicon-o-chart-bar class="w-5 h-5 text-slate-400" />
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Multiplicadores Globales del Negocio</h3>
                    </div>

                    <form wire:submit="save" class="space-y-4">
                        {{ $this->form }}

                        <div class="pt-2 flex justify-end">
                            <x-filament::button type="submit" icon="heroicon-m-check" color="primary">
                                Guardar Multiplicadores
                            </x-filament::button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- 3. Banner Informativo de Inmutabilidad --}}
        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 flex items-start gap-3">
            <x-heroicon-o-information-circle class="w-5 h-5 text-indigo-500 shrink-0 mt-0.5" />
            <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                <strong class="font-semibold text-slate-800 dark:text-slate-100">Nota:</strong> Las modificaciones en este panel no afectan retroactivamente a cotizaciones ni proyectos aprobados. Los nuevos valores se aplicarán únicamente a los nuevos borradores de cotización creados a partir de este momento.
            </p>
        </div>
    </div>
</x-filament-panels::page>

