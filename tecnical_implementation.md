# **Especificación Técnica de Implementación**

## **1\. Esquema Físico de Base de Datos y Restricciones**

Para cumplir estrictamente con las reglas de **Precisión Contable Decimal** (RNF-2.3, RNF-4.3) y la **Regla de Preservación Histórica Lógica** (Regla 5.3), las migraciones de Laravel se diseñarán bajo los siguientes estándares físicos:

### **A. Precisión Monetaria Estricta**

Todas las columnas de base de datos destinadas a precios unitarios, subtotales, totales, salarios, y tasas de overhead deben definirse con el tipo de dato decimal(12, 4\) en las migraciones de PostgreSQL:

PHP  
$table-\>decimal('hourly\_cost', 12, 4); // C\_ch de roles de trabajo  
$table-\>decimal('overhead\_rate\_applied', 12, 4); // T\_oh congelada del snapshot  
$table-\>decimal('actual\_subtotal', 12, 4); // Gastos reales de mano de obra

### **B. Estrategia de Borrado e Integridad Referencial**

Para evitar la eliminación accidental de datos con implicaciones contables, las claves foráneas de tablas maestras utilizarán la restricción restrictOnDelete() en lugar de eliminaciones en cascada:

PHP  
// En la migración de quotes:  
$table-\>foreignId('project\_id')-\>constrained()-\>restrictOnDelete();  
$table-\>foreignId('status\_id')-\>constrained('quote\_statuses')-\>restrictOnDelete();

// En la migración de project\_labor\_logs:  
$table-\>foreignId('employee\_id')-\>constrained()-\>restrictOnDelete();

*Si se intenta eliminar un empleado o proyecto que ya tiene registros reales de ejecución, la base de datos lanzará una excepción a nivel de base de datos, blindando la integridad.*

### **C. Indexación Estratégica para Agregaciones (\<50ms)**

Para cumplir con el requerimiento **RNF-4.1**, se definirán índices explícitos en las columnas utilizadas frecuentemente en cláusulas WHERE y operaciones de agregación SUM dentro de nuestro panel comparativo de conciliación:

PHP  
$table-\>index(\['project\_id', 'purchased\_at'\]); // Para sumas de materiales reales  
$table-\>index(\['project\_id', 'logged\_at'\]); // Para sumas de nóminas reales  
$table-\>index(\['project\_id', 'received\_at'\]); // Para sumas de anticipos reales

## **2\. Transacciones y Consistencia de Eventos**

### **A. Transaccionalidad Atómica (Snapshots y Enmiendas)**

Cualquier proceso que involucre cambios de estado financiero múltiple debe envolverse en transacciones de base de datos (DB::transaction). Si la inyección del Snapshot de tarifas en la tabla quote\_labor\_assignments falla, se debe aplicar un rollback total automático para impedir que la cotización quede en estado "Aprobada" con datos financieros incompletos o corruptos (RNF-2.2).

### **B. Observadores de Eloquent (Eloquent Observers) para Overhead**

El recálculo automático de la Tasa de Overhead Global ($T\_{oh}$) se resolverá de forma desacoplada mediante un Observador en el modelo de gastos fijos:

* **Gatillo:** Cambios en la tabla fixed\_expenses (creación, edición, activación/desactivación de gastos).  
* **Acción del Observer:** Invalida el valor cacheado de la tasa de overhead y dispara el recálculo matemático utilizando la capacidad promedio calculada por el motor de Yasumi, guardando el resultado de forma atómica en global\_settings (CA-04.2).

## **3\. Estándar de Pruebas Automatizadas (Pest PHP)**

Para cumplir con el **100% de cobertura en cálculos críticos** (RNF-1.3, RNF-2.4), las pruebas automatizadas se estructurarán de manera estrictamente metodológica bajo el estándar Pest:

### **A. Ciclo de Vida del Entorno de Pruebas**

El archivo tests/Pest.php se configurará para utilizar el trait LazyRefreshDatabase de Laravel. Esto garantiza que la base de datos de pruebas se limpie de forma automática entre cada test individual.

### **B. Inicialización mediante Seeders en beforeEach**

Dado que el flujo de cotizaciones y proyectos depende de parámetros inmutables y catálogos estables, cada bloque de pruebas utilizará un gancho de preparación para asegurar un entorno real y constante:

* **beforeEach global o por grupo:** Se ejecutará el seeder maestro (DatabaseSeeder) que poblará los estados obligatorios (quote\_statuses, material\_categories) y los parámetros globales iniciales de la empresa (global\_settings) antes de correr las simulaciones.

### **C. Estructura de Pruebas Semánticas (describe)**

Los archivos de prueba estructurarán los casos de uso agrupando la funcionalidad por contextos semánticos mediante bloques describe() para facilitar la auditoría técnica. Ejemplo para el motor matemático:

PHP  
describe('Motor de Cálculo de Cotizaciones', function () {  
    beforeEach(function () {  
        $this-\>artisan('db:seed'); // Inicializa estados y settings de la empresa  
    });

    it('calcula la tasa de overhead por hora justa basada en Yasumi', function () {  
        // Simulación y aserciones  
    });

    it('bloquea la cotización al pasar a estado aprobada y genera snapshot', function () {  
        // Simulación y aserciones de snapshots físicos  
    });  
});  
# 📘 Manual Definitivo de Buenas Prácticas y Arquitectura Limpia en Filament v4
> **Proyecto:** BuildFin Pro / Lynx Construction (`balance_sheets_and_quotes`)  
> **Framework:** Laravel 13 + Filament v4 + Livewire 3 + PostgreSQL (Docker Sail)  
> **Propósito:** Guía técnica, análisis de causas raíz de errores recurrentes y estándar metodológico para el desarrollo de módulos consistentes, desacoplados y libres de deuda técnica.

---

## 🧭 Índice de Contenidos

1. [Diagnóstico: ¿Por qué sientes que estás fallando con Filament? (Causas Raíz)](#1-diagnóstico-por-qué-sientes-que-estás-fallando-con-filament-causas-raíz)
2. [Arquitectura y Organización de Archivos en Filament v4](#2-arquitectura-y-organización-de-archivos-en-filament-v4)
3. [Simple Resources vs. Full Resources (La Primera Gran Decisión)](#3-simple-resources-vs-full-resources-la-primera-gran-decisión)
4. [La Capa de Servicios (Service Layer) y el Ciclo de Vida de Filament](#4-la-capa-de-servicios-service-layer-y-el-ciclo-de-vida-de-filament)
5. [Enums y Contratos Nativos de Filament (`HasLabel`, `HasColor`, `HasIcon`)](#5-enums-y-contratos-nativos-de-filament-haslabel-hascolor-hasicon)
6. [Formularios Reactivos y Limpios (`Schemas`)](#6-formularios-reactivos-y-limpios-schemas)
7. [Tablas de Alto Rendimiento y Prevención de N+1 (`Tables`)](#7-tablas-de-alto-rendimiento-y-prevención-de-n1-tables)
8. [Consistencia UI: Uso Correcto de `CommonColumns` y `CommonActions`](#8-consistencia-ui-uso-correcto-de-commoncolumns-y-commonactions)
9. [Políticas, Autorización y Blindaje de Integridad](#9-políticas-autorización-y-blindaje-de-integridad)
10. [Refactorización Guiada del Módulo de Proyectos (Antes vs. Después)](#10-refactorización-guiada-del-módulo-de-proyectos-antes-vs-después)
11. [Guía de Pruebas Automatizadas con Pest en Filament](#11-guía-de-pruebas-automatizadas-con-pest-en-filament)
12. [Checklist de Calidad Pre-Merge](#12-checklist-de-calidad-pre-merge)

---

## 1. Diagnóstico: ¿Por qué sientes que estás fallando con Filament? (Causas Raíz)

Desarrollar en Filament puede sentirse frustrante al inicio no por falta de habilidad en Laravel, sino por **choques de paradigma**. Los siguientes son los 6 motivos exactos por los cuales has experimentado fricción en este proyecto:

### 1.1. Mezcla de paradigmas Filament v3 y Filament v4
- **Lo que ocurrió:** En Filament v3, todo el formulario y la tabla se definían obligatoriamente dentro del archivo `Resource.php` con `public static function form(Form $form)` y `table(Table $table)`. En Filament v4, el generador crea clases desacopladas: `Schemas/ProjectForm.php` y `Tables/ProjectsTable.php`.
- **La consecuencia:** Generaste el recurso con el generador v4, pero luego programaste todo en `ProjectResource.php`. Los archivos en `Schemas/` y `Tables/` quedaron vacíos (código muerto), dejándote la sensación de tener un proyecto desordenado e inconcluso.

### 1.2. Choque entre Recursos Simples (`--simple`) y Recursos Completos
- Los módulos iniciales (*Clientes*, *Empleados*, *Gastos Fijos*, *Roles*) se construyeron como **Simple Resources** (`ManageRecords`), donde todo se crea y edita en modales sobre la misma tabla.
- Al llegar a **Proyectos**, necesitabas páginas dedicadas (`ListProjects`, `CreateProject`, `EditProject`). Al cambiar de tipo de recurso, las acciones de cabecera (`getHeaderActions()`) y los hooks de persistencia funcionan de forma diferente. Intentar reutilizar componentes de modal en páginas completas causó inconsistencias.

### 1.3. Desconexión de los Contratos Nativos de Enums
- Filament está diseñado para resolver de forma automática etiquetas, colores e iconos si los Enums de PHP implementan sus contratos.
- En `ProjectStatusEnum` creaste métodos manuales (`bgColor()`, `icon()`), lo que te obligó a escribir closures largos y complejos (`fn ($record) => match(...)`) en las columnas de la tabla. Además, usaste iconos FontAwesome (`fa-solid fa-...`) en lugar de Heroicons de Blade, lo que no renderiza en Filament.

### 1.4. Inconsistencia entre Políticas de Acceso y la Interfaz Visual
- En `ProjectPolicy` definiste `delete() => false` (siguiendo la regla de no borrado físico).
- Pero en `EditProject.php` colocaste `DeleteAction::make()`. Esto crea una contradicción: la interfaz ofrece un botón que la política deniega o que no debería existir según las reglas de negocio.

### 1.5. Fuga de Lógica en PostgreSQL (Condiciones de Carrera y N+1)
- Filament oculta gran parte de las consultas Eloquent. Si no se declara explícitamente el eager loading (`with([...])`), Filament ejecuta consultas N+1 en cada fila de la tabla.
- En la generación del código secuencial (`PRJ-2026-XXXX`), `lockForUpdate()` no bloquea nada en PostgreSQL si la tabla está vacía o es el primer proyecto del año, abriendo la puerta a colisiones de clave única en concurrencia.

### 1.6. Parcheo visual mediante CSS inyectado
- En `AdminPanelProvider.php` agregaste más de 60 líneas de CSS inyectado con `!important` para forzar la apariencia de las pestañas (`.fi-tabs`). Filament v4 ofrece configuración nativa y compilación de temas vía Tailwind para evitar fragilidad en el diseño.

---

## 2. Arquitectura y Organización de Archivos en Filament v4

Para mantener el proyecto limpio, modular y congruente, cada recurso debe estructurarse bajo el siguiente estándar estricto:

```
app/
├── Enums/                               <-- 🏷️ Enums con contratos Filament
│   └── ProjectStatusEnum.php
├── Filament/
│   ├── Pages/                           <-- 📄 Páginas aisladas (Settings, etc.)
│   ├── Resources/
│   │   └── Projects/
│   │       ├── Pages/                   <-- 📑 Ciclo de vida de páginas
│   │       │   ├── CreateProject.php
│   │       │   ├── EditProject.php
│   │       │   └── ListProjects.php
│   │       ├── Schemas/                 <-- 📝 Definición del formulario
│   │       │   └── ProjectForm.php
│   │       ├── Tables/                  <-- 📊 Definición de columnas y filtros
│   │       │   └── ProjectsTable.php
│   │       └── ProjectResource.php      <-- 🎛️ Registro maestro, navegación y permisos
│   └── Support/                         <-- 🧠 Componentes compartidos
│       ├── Actions/
│       │   └── CommonActions.php
│       └── Columns/
│           └── CommonColumns.php
├── Models/                              <-- 🏛️ Modelos Eloquent limpios
│   └── Project.php
├── Policies/                            <-- 🛡️ Reglas de autorización
│   └── ProjectPolicy.php
└── Services/                            <-- ⚙️ Lógica de negocio transaccional
    └── Project/
        └── ProjectService.php
```

### Regla de Oro de `ProjectResource.php`:
El archivo del recurso **no debe contener cientos de líneas de campos ni columnas**. Su única responsabilidad es ser el coordinador:
```php
namespace App\Filament\Resources\Projects;

use App\Filament\Resources\Projects\Schemas\ProjectForm;
use App\Filament\Resources\Projects\Tables\ProjectsTable;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    public static function form(Schema $schema): Schema
    {
        return ProjectForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProjectsTable::configure($table);
    }
}
```

---

## 3. Simple Resources vs. Full Resources (La Primera Gran Decisión)

Antes de crear un recurso con `./vendor/bin/sail artisan make:filament-resource`, decide según esta regla:

| Tipo | Cuándo Usarlo | Comando | Ejemplo en tu proyecto |
| :--- | :--- | :--- | :--- |
| **Simple Resource** | Entidades de catálogo, con menos de 6 campos, sin relaciones complejas ni flujos de múltiples pasos. Toda la interacción cabe en un modal. | `make:filament-resource Client --simple` | `ClientResource`, `EmployeeResource`, `FixedExpenseResource` |
| **Full Resource** | Entidades transaccionales centrales con formularios extensos, múltiples pestañas, relaciones hijas (Repeaters), dashboards o transiciones de estado. | `make:filament-resource Project` | `ProjectResource`, `QuoteResource` |

### Error común:
Querer que un Full Resource use los mismos métodos que un `ManageRecords`. En Full Resources:
- El botón de crear en `ListProjects` navega a `/projects/create`.
- El guardado se procesa en `CreateProject::handleRecordCreation()`.
- La actualización se procesa en `EditProject::handleRecordUpdate()`.

---

## 4. La Capa de Servicios (Service Layer) y el Ciclo de Vida de Filament

Filament **no debe contener lógica de negocio crítica**. Si tu aplicación mañana expone una API REST o ejecuta un comando Artisan, la lógica no debe estar atrapada dentro de un componente Livewire de Filament.

### 4.1. Cómo integrar el `ProjectService` con Filament

#### En `CreateProject.php`:
```php
namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use App\Services\Project\ProjectService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Delega la transacción y generación de código al servicio
        return app(ProjectService::class)->save($data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
```

#### En `EditProject.php`:
```php
namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use App\Services\Project\ProjectService;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return app(ProjectService::class)->update($record, $data);
    }

    protected function getHeaderActions(): array
    {
        // NO incluir DeleteAction si el Policy lo prohíbe
        return [];
    }
}
```

### 4.2. Generación segura del código de proyecto (Sin condiciones de carrera)

El código actual en `ProjectService` tiene una vulnerabilidad de concurrencia:
```php
// ❌ INSEGURO en PostgreSQL si no hay registros previos del año
$lastProject = Project::where('code', 'like', "{$prefix}%")
    ->lockForUpdate()
    ->first();
```

**Implementación limpia y a prueba de fallos:**
```php
namespace App\Services\Project;

use App\Enums\ProjectStatusEnum;
use App\Models\Project;
use App\Models\ProjectStatus;
use Illuminate\Support\Facades\DB;

class ProjectService
{
    public function save(array $data): Project
    {
        return DB::transaction(function () use ($data) {
            $draftStatus = ProjectStatus::ofCode(ProjectStatusEnum::DRAFT)->firstOrFail();

            $code = $this->generateNextProjectCode();

            return Project::create([
                'client_id' => $data['client_id'],
                'project_status_id' => $draftStatus->id,
                'code' => $code,
                'title' => trim($data['title']),
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
                'state' => $data['state'] ?? null,
                'project_description' => $data['project_description'] ?? null,
                'actual_start_date' => null,
                'actual_end_date' => null,
            ]);
        });
    }

    public function update(Project $project, array $data): Project
    {
        return DB::transaction(function () use ($project, $data) {
            $project->update([
                'title' => trim($data['title']),
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
                'state' => $data['state'] ?? null,
                'actual_start_date' => $data['actual_start_date'] ?? null,
                'actual_end_date' => $data['actual_end_date'] ?? null,
                'project_description' => $data['project_description'] ?? null,
            ]);

            return $project;
        });
    }

    protected function generateNextProjectCode(): string
    {
        $year = now()->year;
        $prefix = "PRJ-{$year}-";

        // Obtenemos el valor máximo numérico extrayendo la secuencia
        $maxSequence = Project::where('code', 'like', "{$prefix}%")
            ->lockForUpdate()
            ->selectRaw("COALESCE(MAX(CAST(SUBSTRING(code FROM '[0-9]+$') AS INTEGER)), 0) as max_seq")
            ->value('max_seq');

        $nextSeq = ((int) $maxSequence) + 1;

        return sprintf('%s%04d', $prefix, $nextSeq);
    }
}
```

---

## 5. Enums y Contratos Nativos de Filament (`HasLabel`, `HasColor`, `HasIcon`)

Esta es la mejora con mayor impacto en reducción de código y legibilidad.

### 5.1. Implementación del Enum
Al implementar los contratos de Filament en tu Enum, **Filament sabe exactamente cómo pintar badges, filtros e iconos sin una sola línea de código extra en la tabla**.

```php
namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ProjectStatusEnum: string implements HasLabel, HasColor, HasIcon
{
    case DRAFT = 'draft';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELED = 'canceled';

    public function getLabel(): string
    {
        return match ($this) {
            self::DRAFT => 'Borrador',
            self::IN_PROGRESS => 'En Ejecución',
            self::COMPLETED => 'Finalizado',
            self::CANCELED => 'Cancelado',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::IN_PROGRESS => 'info',
            self::COMPLETED => 'success',
            self::CANCELED => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::DRAFT => 'heroicon-m-document-text',
            self::IN_PROGRESS => 'heroicon-m-arrow-path',
            self::COMPLETED => 'heroicon-m-check-badge',
            self::CANCELED => 'heroicon-m-x-circle',
        };
    }
}
```

### 5.2. Impacto en la Tabla
**Antes (Closure manual repetitivo):**
```php
TextColumn::make('status.display_name')
    ->badge()
    ->color(fn(Project $record): string => match ($record->status?->code) {
        ProjectStatusEnum::IN_PROGRESS => ProjectStatusEnum::IN_PROGRESS->bgColor(),
        ...
    })
```

**Después (100% nativo y automático):**
```php
TextColumn::make('status.code')
    ->label('ESTADO')
    ->badge();
// Filament lee getLabel(), getColor() y getIcon() automáticamente
```

---

## 6. Formularios Reactivos y Limpios (`Schemas`)

Extrae tu formulario a [`app/Filament/Resources/Projects/Schemas/ProjectForm.php`](file:///home/linx_d_mos/code/balance_sheets_and_quotes/app/Filament/Resources/Projects/Schemas/ProjectForm.php):

```php
namespace App\Filament\Resources\Projects\Schemas;

use App\Models\Client;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Información Principal')
                ->columns(2)
                ->schema([
                    TextInput::make('code')
                        ->label('Código de Proyecto')
                        ->disabled()
                        ->dehydrated(false)
                        ->visibleOn('edit')
                        ->helperText('Código secuencial generado automáticamente.'),

                    Select::make('client_id')
                        ->label('Cliente')
                        ->relationship('client', 'company_name')
                        ->getOptionLabelFromRecordUsing(fn (Client $record) => $record->company_name ?: "{$record->first_name} {$record->last_name}")
                        ->searchable()
                        ->preload()
                        ->required()
                        ->disabledOn('edit'), // Los proyectos no deben cambiar de cliente para preservar contratos

                    TextInput::make('title')
                        ->label('Nombre de Obra / Título')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(fn ($operation) => $operation === 'create' ? 2 : 1),

                    TextInput::make('address')
                        ->label('Dirección')
                        ->maxLength(255),

                    TextInput::make('city')
                        ->label('Ciudad')
                        ->maxLength(100),

                    TextInput::make('state')
                        ->label('Estado / Región')
                        ->maxLength(100),

                    DatePicker::make('actual_start_date')
                        ->label('Fecha Real de Inicio')
                        ->visibleOn('edit'),

                    DatePicker::make('actual_end_date')
                        ->label('Fecha Real de Cierre')
                        ->visibleOn('edit')
                        ->afterOrEqual('actual_start_date'),

                    Textarea::make('project_description')
                        ->label('Descripción / Alcance Inicial')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
```

---

## 7. Tablas de Alto Rendimiento y Prevención de N+1 (`Tables`)

Extrae tu tabla a [`app/Filament/Resources/Projects/Tables/ProjectsTable.php`](file:///home/linx_d_mos/code/balance_sheets_and_quotes/app/Filament/Resources/Projects/Tables/ProjectsTable.php):

```php
namespace App\Filament\Resources\Projects\Tables;

use App\Enums\ProjectStatusEnum;
use App\Filament\Support\Actions\CommonActions;
use App\Models\Project;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // 🚀 EAGER LOADING: Elimina consultas N+1 en cada carga
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['client', 'status', 'approvedQuote']))
            ->searchPlaceholder('Buscar obra por título o código...')
            ->columns([
                TextColumn::make('title')
                    ->label('PROYECTO')
                    ->weight('bold')
                    ->description(fn (Project $record): string => $record->code)
                    ->sortable()
                    ->searchable(),

                TextColumn::make('client')
                    ->label('CLIENTE')
                    ->formatStateUsing(fn (Project $record) => $record->client?->company_name ?: "{$record->client?->first_name} {$record->client?->last_name}")
                    // Búsqueda insensible a mayúsculas/minúsculas para PostgreSQL
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('client', function (Builder $q) use ($search) {
                            $q->where('company_name', 'ilike', "%{$search}%")
                                ->orWhere('first_name', 'ilike', "%{$search}%")
                                ->orWhere('last_name', 'ilike', "%{$search}%");
                        });
                    }),

                TextColumn::make('approvedQuote.code')
                    ->label('LÍNEA BASE ACTIVA')
                    ->placeholder('Sin cotización aprobada')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'gray'),

                TextColumn::make('status.code')
                    ->label('ESTADO')
                    ->badge(),
            ])
            ->filters([
                SelectFilter::make('client_id')
                    ->label('Cliente')
                    ->relationship('client', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->company_name ?: "{$record->first_name} {$record->last_name}")
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                // 🔗 Acción contextual completa hacia el módulo de cotizaciones
                Action::make('createQuote')
                    ->label('Formular cotización')
                    ->icon('heroicon-m-document-plus')
                    ->color('primary')
                    ->url(fn (Project $record): string => url("/admin/quotes/create?project_id={$record->id}"))
                    ->visible(fn (Project $record): bool => ! in_array($record->status?->code, [
                        ProjectStatusEnum::COMPLETED,
                        ProjectStatusEnum::CANCELED,
                    ], true)),

                CommonActions::editRowAction(),
            ])
            ->toolbarActions([]);
    }
}
```

---

## 8. Consistencia UI: Uso Correcto de `CommonColumns` y `CommonActions`

La guía oficial [`guia_ui_filament.md`](file:///home/linx_d_mos/code/balance_sheets_and_quotes/guia_ui_filament.md) se creó específicamente para que todos los recursos se vean y comporten como parte de la misma aplicación.

### Reglas indispensables:
1. **Acción de Creación Superior:**  
   Siempre usar `CommonActions::createHeaderAction('Nuevo Proyecto', 'heroicon-m-plus')` en `getHeaderActions()`.
2. **Acción de Edición de Fila:**  
   Siempre usar `CommonActions::editRowAction()`.
3. **Títulos y Subtítulos:**  
   Cada página de lista o gestión debe implementar:
   ```php
   public function getTitle(): string
   {
       return 'Contenedores de Proyectos y Obras';
   }

   public function getSubheading(): ?string
   {
       return 'Gestión de expedientes maestros, líneas base aprobadas y estado operativo de campo.';
   }
   ```
4. **Encabezados de Tabla en Mayúsculas Sostenidas:**  
   `PROYECTO`, `CLIENTE`, `LÍNEA BASE ACTIVA`, `ESTADO`. Esto asegura alineación visual con los mockups.

---

## 9. Políticas, Autorización y Blindaje de Integridad

En [`ProjectPolicy.php`](file:///home/linx_d_mos/code/balance_sheets_and_quotes/app/Policies/ProjectPolicy.php):
```php
public function delete(User $user, Project $project): bool
{
    return false; // Prohibido el borrado físico de proyectos por trazabilidad contable
}
```

### Regla de consistencia:
Si una política retorna `false` permanente para una acción:
- **No instancies la acción en la vista:** En `EditProject.php`, remueve `DeleteAction::make()`.
- **En la tabla:** Deja `toolbarActions([])` sin `DeleteBulkAction`.
- Agrega un comentario en el código indicando la regla de negocio: `// RN-XX: Prohibido borrado físico individual y masivo para preservar integridad contable`.

---

## 10. Refactorización Guiada del Módulo de Proyectos (Antes vs. Después)

### 10.1. `ListProjects.php`

**Antes:**
```php
class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
    // Sin getTitle(), sin getSubheading()
}
```

**Después (Limpio y alineado a la guía):**
```php
namespace App\Filament\Resources\Projects\Pages;

use App\Enums\ProjectStatusEnum;
use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Support\Actions\CommonActions;
use App\Models\Project;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;

    public function getTitle(): string
    {
        return 'Contenedores de Proyectos y Obras';
    }

    public function getSubheading(): ?string
    {
        return 'Administración de obras activas, seguimiento de líneas base y transición de estados.';
    }

    protected function getHeaderActions(): array
    {
        return [
            CommonActions::createHeaderAction('Nuevo Proyecto', 'heroicon-m-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Todos'),
            'draft' => Tab::make('Borrador')
                ->modifyQueryUsing(fn (Builder $query) => $query->ofStatus(ProjectStatusEnum::DRAFT))
                ->badge(fn () => Project::ofStatus(ProjectStatusEnum::DRAFT)->count())
                ->badgeColor(ProjectStatusEnum::DRAFT->getColor()),
            'in_progress' => Tab::make('En Ejecución')
                ->modifyQueryUsing(fn (Builder $query) => $query->ofStatus(ProjectStatusEnum::IN_PROGRESS))
                ->badge(fn () => Project::ofStatus(ProjectStatusEnum::IN_PROGRESS)->count())
                ->badgeColor(ProjectStatusEnum::IN_PROGRESS->getColor()),
            'completed' => Tab::make('Finalizados')
                ->modifyQueryUsing(fn (Builder $query) => $query->ofStatus(ProjectStatusEnum::COMPLETED))
                ->badge(fn () => Project::ofStatus(ProjectStatusEnum::COMPLETED)->count())
                ->badgeColor(ProjectStatusEnum::COMPLETED->getColor()),
            'canceled' => Tab::make('Cancelados')
                ->modifyQueryUsing(fn (Builder $query) => $query->ofStatus(ProjectStatusEnum::CANCELED))
                ->badge(fn () => Project::ofStatus(ProjectStatusEnum::CANCELED)->count())
                ->badgeColor(ProjectStatusEnum::CANCELED->getColor()),
        ];
    }
}
```

---

## 11. Guía de Pruebas Automatizadas con Pest en Filament

Para validar que los recursos de Filament funcionen sin depender de pruebas manuales en el navegador, Pest provee aserciones dedicadas. Ejecuta siempre con Sail:

```bash
./vendor/bin/sail artisan test --filter=ProjectResourceTest
```

### Estructura estándar de pruebas para recursos de Filament:

```php
use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Projects\Pages\ListProjects;
use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->admin = User::factory()->create();
    $this->admin->assignRole('administrator');
    $this->actingAs($this->admin);
});

describe('Listado de Proyectos', function () {
    it('renderiza la tabla y columnas principales', function () {
        $project = Project::factory()->create();

        Livewire::test(ListProjects::class)
            ->assertSuccessful()
            ->assertCanSeeTableRecords([$project]);
    });

    it('no muestra botón de eliminar por regla de negocio', function () {
        $project = Project::factory()->create();

        Livewire::test(ListProjects::class)
            ->assertTableActionHidden('delete', $project);
    });
});

describe('Creación de Proyectos', function () {
    it('valida campos requeridos en el formulario', function () {
        Livewire::test(CreateProject::class)
            ->fillForm([
                'title' => null,
                'client_id' => null,
            ])
            ->call('create')
            ->assertHasFormErrors(['title' => 'required', 'client_id' => 'required']);
    });

    it('crea el proyecto delegando al servicio', function () {
        $client = Client::factory()->create();

        Livewire::test(CreateProject::class)
            ->fillForm([
                'title' => 'Construcción Bodega Beta',
                'client_id' => $client->id,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('projects', [
            'title' => 'Construcción Bodega Beta',
            'client_id' => $client->id,
        ]);
    });
});
```

---

## 12. Checklist de Calidad Pre-Merge

Antes de dar por terminado cualquier módulo nuevo o refactorizado en Filament, verifica esta lista:

- [ ] **1. Tipo de Recurso:** ¿Es un Simple Resource (`ManageRecords`) o Full Resource (`Pages`) según la complejidad de la entidad?
- [ ] **2. Archivos en su sitio:** ¿El formulario está en `Schemas/` y la tabla en `Tables/` sin código muerto en el recurso?
- [ ] **3. Eager Loading:** ¿La tabla tiene `->modifyQueryUsing(fn ($q) => $q->with([...]))` para evitar N+1?
- [ ] **4. Enums:** ¿Los Enums implementan `HasLabel`, `HasColor` y `HasIcon` con Heroicons válidos?
- [ ] **5. Soporte Compartido:** ¿Las acciones y columnas comunes usan `CommonActions` y `CommonColumns`?
- [ ] **6. PostgreSQL Safe:** ¿Las búsquedas usan `ilike` en lugar de `like`?
- [ ] **7. Títulos:** ¿La página implementa `getTitle()` y `getSubheading()` conforme a la guía de UI?
- [ ] **8. Políticas:** ¿Las acciones visibles en Filament coinciden al 100% con los permisos de la Policy (ej. sin botón de eliminar si la policy retorna `false`)?
- [ ] **9. Pruebas:** ¿Se ejecutó `./vendor/bin/sail artisan test` y el 100% de las pruebas pasan en verde?
