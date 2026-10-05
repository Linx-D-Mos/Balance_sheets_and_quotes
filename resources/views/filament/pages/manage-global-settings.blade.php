<x-filament-panels::page>
    <div class="space-y-6">
        {{-- 1. Métricas Superiores --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-cockpit.stat-card
                label="Gastos fijos"
                :value="'$' . number_format($this->getActiveOverheadSum(), 2)"
                :subtext="'Suma de ' . $this->getActiveOverheadCount() . ' gastos fijos activos'"
                icon="heroicon-o-document-text"
            />

            {{-- Métrica 2: Capacidad Laboral Promedio --}}
            <x-cockpit.stat-card
                label="Capacidad Laboral Promedio anual"
                :value="number_format($this->getStandardMonthlyHours(), 0)"
                unit="hrs/mes"
                :subtext="'Mes actual (' . ucfirst(now()->translatedFormat('F')) . '): ' . number_format($this->getCurrentMonthWorkingHours(), 0) . ' horas laborables'"
                icon="heroicon-o-clock"
                actionIcon="heroicon-o-arrow-path"
                actionWireClick="recalculateCapacity"
                actionTitle="Recalcular capacidad estándar anual y tasa T_oh para el año en curso"
            />

            <x-cockpit.stat-card
                label="Tasa de Overhead"
                :value="'$' . number_format($this->getOverheadRate(), 2)"
                unit="/ hr"
                subtext="Costo indirecto absorbido por hora hombre"
                icon="heroicon-o-calculator"
            />
        </div>

        {{-- 2. Cuerpo Principal --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

            {{-- Columna Izquierda --}}
            <div class="space-y-6">
                {{-- Gastos Fijos --}}
                <x-cockpit.card
                    title="Gastos Fijos Mensuales (Overhead)"
                    icon="heroicon-o-document-text"
                    :actionUrl="route('filament.admin.resources.fixed-expenses.index')"
                    actionIcon="heroicon-m-plus"
                >
                    <div class="grid grid-cols-12 text-[10px] font-bold text-gray-400 uppercase tracking-wider py-2.5 border-b border-gray-100 dark:border-gray-800">
                        <div class="col-span-6">CONCEPTO</div>
                        <div class="col-span-3 text-right">MONTO MENSUAL</div>
                        <div class="col-span-3 text-right">ESTADO</div>
                    </div>

                    <div class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($this->getFixedExpenses() as $expense)
                            <div class="py-3 grid grid-cols-12 items-center text-xs {{ ! $expense->is_active ? 'opacity-40' : '' }}">
                                <div class="col-span-6 font-medium text-gray-700 dark:text-gray-300 truncate pr-2">
                                    {{ $expense->concept }}
                                </div>
                                <div class="col-span-3 text-right font-bold text-gray-900 dark:text-white">
                                    ${{ number_format($expense->amount, 2) }}
                                </div>
                                <div class="col-span-3 flex justify-end">
                                    <x-cockpit.toggle
                                        :active="$expense->is_active"
                                        wire:click="toggleFixedExpense({{ $expense->id }})"
                                    />
                                </div>
                            </div>
                        @empty
                            <p class="py-4 text-xs text-gray-400 text-center">No hay gastos fijos registrados.</p>
                        @endforelse
                    </div>
                </x-cockpit.card>

                {{-- Roster de Personal --}}
                <x-cockpit.card
                    title="Roster de Personal (Employees)"
                    icon="heroicon-o-users"
                    :actionUrl="route('filament.admin.resources.employees.index')"
                    actionLabel="Ver catálogo"
                >
                    <div class="mb-3">
                        <a href="{{ route('filament.admin.resources.employees.index') }}" class="flex items-center gap-2 px-3 py-2 text-xs text-gray-400 bg-gray-50/70 hover:bg-gray-100/90 dark:bg-gray-800/40 rounded-lg border border-gray-200 dark:border-gray-700 transition">
                            <x-heroicon-o-user-plus class="w-4 h-4 text-gray-400" />
                            <span>+ Nuevo Trabajador (Buscar o agregar)</span>
                        </a>
                    </div>

                    <div class="space-y-2">
                        @forelse($this->getEmployees() as $employee)
                            <div class="flex items-center justify-between p-2.5 rounded-lg bg-gray-50/60 dark:bg-gray-800/50">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full {{ $employee->is_active ? 'bg-primary-100 text-primary-700 dark:bg-primary-950 dark:text-primary-400' : 'bg-gray-200 text-gray-500 dark:bg-gray-800 dark:text-gray-400' }} flex items-center justify-center font-bold text-xs">
                                        {{ strtoupper(substr($employee->name, 0, 2)) }}
                                    </div>
                                    <span class="text-xs font-semibold text-gray-800 dark:text-gray-200">{{ $employee->name }}</span>
                                </div>
                                <span class="text-[11px] font-semibold px-2.5 py-0.5 rounded border {{ $employee->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200/80 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800' : 'bg-gray-100 text-gray-500 border-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700' }}">
                                    {{ $employee->is_active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </div>
                        @empty
                            <p class="py-4 text-xs text-gray-400 text-center">No hay operarios registrados en el roster.</p>
                        @endforelse
                    </div>
                </x-cockpit.card>
            </div>

            {{-- Columna Derecha --}}
            <div class="space-y-6">
                {{-- Roles Técnicos --}}
                <x-cockpit.card
                    title="Catálogo de Roles & Tarifas Cargadas (C_ch)"
                    subtitle="Define el costo base y la carga patronal/social por tipo de operario."
                    icon="heroicon-o-briefcase"
                >
                    <div class="grid grid-cols-12 text-[10px] font-bold text-gray-400 uppercase tracking-wider py-2.5 border-b border-gray-100 dark:border-gray-800">
                        <div class="col-span-4">ROL</div>
                        <div class="col-span-3 text-right">SALARIO BASE</div>
                        <div class="col-span-2 text-right">CARGA (%)</div>
                        <div class="col-span-3 text-right">TARIFA RESULTANTE</div>
                    </div>

                    <div class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($this->getLaborRoles() as $role)
                            <div class="py-2.5 grid grid-cols-12 items-center text-xs gap-2">
                                <div class="col-span-4 font-semibold text-gray-800 dark:text-gray-200 truncate">
                                    {{ $role->name }}
                                </div>
                                <div class="col-span-3">
                                    <div class="px-2 py-1 rounded border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 font-mono text-xs text-right">
                                        ${{ number_format($role->base_salary, 2) }}
                                    </div>
                                </div>
                                <div class="col-span-2">
                                    <div class="px-2 py-1 rounded border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 font-mono text-xs text-right">
                                        {{ number_format($role->social_load_pct, 1) }}%
                                    </div>
                                </div>
                                <div class="col-span-3 flex justify-end">
                                    <div class="inline-flex items-center gap-1 px-2 py-1 rounded bg-primary-50 border border-primary-100 dark:bg-primary-950/40 dark:border-primary-900 text-primary-600 dark:text-primary-400 font-bold font-mono text-xs">
                                        <x-heroicon-s-lock-closed class="w-3 h-3 text-primary-500" />
                                        <span>${{ number_format($role->hourly_cost, 2) }}/h</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="py-4 text-xs text-gray-400 text-center">No hay roles técnicos configurados.</p>
                        @endforelse
                    </div>

                    <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-800 flex justify-end">
                        <a href="{{ route('filament.admin.resources.labor-roles.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold shadow-xs transition">
                            <x-heroicon-m-bookmark-square class="w-4 h-4" />
                            <span>Gestionar Catálogo de Roles</span>
                        </a>
                    </div>
                </x-cockpit.card>

                {{-- Multiplicadores Globales --}}
                <x-cockpit.card
                    title="Multiplicadores Globales del Negocio"
                    icon="heroicon-o-chart-bar"
                >
                    <form wire:submit="save" class="space-y-4">
                        {{ $this->form }}

                        <div class="pt-2 flex justify-end">
                            <x-filament::button type="submit" icon="heroicon-m-check" color="primary">
                                Guardar Multiplicadores
                            </x-filament::button>
                        </div>
                    </form>
                </x-cockpit.card>
            </div>
        </div>

        {{-- 3. Nota Informativa --}}
        <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 flex items-start gap-3">
            <x-heroicon-o-information-circle class="w-5 h-5 text-primary-500 shrink-0 mt-0.5" />
            <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed">
                <strong class="font-semibold text-gray-800 dark:text-gray-100">Nota:</strong> Las modificaciones en este panel no afectan retroactivamente a cotizaciones ni proyectos aprobados. Los nuevos valores se aplicarán únicamente a los nuevos borradores de cotización creados a partir de este momento.
            </p>
        </div>
    </div>
</x-filament-panels::page>
