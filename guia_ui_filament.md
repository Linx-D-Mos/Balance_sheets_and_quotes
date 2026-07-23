# 📘 Guía de Arquitectura de UI y Componentes Reutilizables en Filament v4

Este documento sirve como guía oficial y manual de buenas prácticas para la creación y modificación de recursos (Resources) en el proyecto **BuildFin Pro**. 

Toda la interfaz de usuario está estandarizada conforme a las maquetas visuales ubicadas en la carpeta `/UI` (`Group 12.png`, `Frame 20.png`, `Frame 22.png`, `Frame 23.png`).

---

## 🏗️ 1. Filosofía de Arquitectura

Para evitar duplicar código Blade o definiciones de tabla en cada recurso nuevo (*Clientes*, *Personal/Roster*, *Roles de Trabajo*, *Gastos Fijos*), aplicamos el patrón de **Componentes Centralizados**:

```
app/Filament/
├── Support/                       <-- 🧠 Núcleo de Componentes Reutilizables
│   ├── Columns/
│   │   └── CommonColumns.php      <-- Columnas de tablas (nombres, badges, toggles, etc.)
│   └── Actions/
│       └── CommonActions.php      <-- Botones y acciones (crear, editar, outlined)
│
└── Resources/                     <-- 📂 Módulos de la Aplicación
    ├── Clients/
    │   ├── ClientResource.php
    │   └── Pages/ManageClients.php
    ├── Roles/ (Futuro)
    └── Employees/ (Futuro)
```

---

## 🛠️ 2. Guía Paso a Paso: Crear un Nuevo Recurso (Ejemplo: `EmployeeResource`)

Cuando vayas a implementar un nuevo módulo como **Personal / Roster (`EmployeeResource`)** o **Roles de Trabajo (`RoleResource`)**, sigue estos 3 pasos:

### Paso 1: Generar el Recurso
Ejecuta en consola o dentro de Sail:
```bash
./vendor/bin/sail artisan make:filament-resource Employee --simple
```

### Paso 2: Configurar la Tabla utilizando `CommonColumns` y `CommonActions`
Abre `app/Filament/Resources/Employees/EmployeeResource.php` y define las columnas importando el soporte reutilizable:

```php
namespace App\Filament\Resources\Employees;

use App\Filament\Support\Columns\CommonColumns;
use App\Filament\Support\Actions\CommonActions;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class EmployeeResource extends Resource
{
    protected static UnitEnum|string|null $navigationGroup = 'CATÁLOGOS';
    protected static ?string $modelLabel = 'Operario';
    protected static ?string $pluralModelLabel = 'Personal / Roster';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Columna Principal: Nombre en negrita + ID o código debajo
                CommonColumns::displayName('full_name', 'OPERARIO / NOMBRE COMPLETO'),

                // Badges de conteo (ej: "18 Jornadas")
                CommonColumns::countBadge('shifts', 'Jornada', 'Jornadas', 'JORNADAS REGISTRADAS'),

                // Interruptor de Disponibilidad / Estado Activo
                CommonColumns::availability('is_available', 'DISPONIBILIDAD OPERATIVA'),
            ])
            ->actions([
                // Acciones de fila estandarizadas
                CommonActions::editRowAction(),
            ]);
    }
}
```

### Paso 3: Configurar la Página con Título, Subtítulo y Botón Principal
Abre `app/Filament/Resources/Employees/Pages/ManageEmployees.php`:

```php
namespace App\Filament\Resources\Employees\Pages;

use App\Filament\Resources\Employees\EmployeeResource;
use App\Filament\Support\Actions\CommonActions;
use Filament\Resources\Pages\ManageRecords;

class ManageEmployees extends ManageRecords
{
    protected static string $resource = EmployeeResource::class;

    public function getTitle(): string
    {
        return 'Catálogo de Personal y Roster de Operarios';
    }

    public function getSubheading(): ?string
    {
        return 'Gestión de alta rápida multirol, disponibilidad de campo y protección de asignaciones históricas.';
    }

    protected function getHeaderActions(): array
    {
        return [
            // Botón morado primario superior (+ Registrar Trabajador)
            CommonActions::createHeaderAction('Registrar Trabajador', 'heroicon-m-user-plus'),
        ];
    }
}
```

---

## 🧰 3. Catálogo de Componentes Disponibles

### 📊 Columnas de Tabla (`CommonColumns`)

| Método | Descripción | Ejemplo de Uso |
| :--- | :--- | :--- |
| `displayName($name, $label)` | Nombre en negrita con búsqueda en múltiples campos | `CommonColumns::displayName()` |
| `email($name, $label)` | Correo con opción de copiado y marcador "Sin correo" | `CommonColumns::email()` |
| `phone($name, $label)` | Teléfono formateado | `CommonColumns::phone()` |
| `countBadge($relation, $sing, $plur, $label)` | Insignia con conteo de relaciones (ej: 3 Proyectos) | `CommonColumns::countBadge('projects', 'Proyecto', 'Proyectos')` |
| `availability($name, $label)` | Conmutador (Toggle switch) activo/inactivo | `CommonColumns::availability('is_active')` |

### 🔘 Botones y Acciones (`CommonActions`)

| Método | Descripción | Ejemplo de Uso |
| :--- | :--- | :--- |
| `createHeaderAction($label, $icon)` | Botón primario de creación superior | `CommonActions::createHeaderAction('Nuevo Cliente')` |
| `editRowAction()` | Botón/Icono de edición por fila | `CommonActions::editRowAction()` |
| `secondaryRowAction($name, $label, $icon)` | Botón secundario con borde (*outlined*) | `CommonActions::secondaryRowAction('create_project', 'Crear Proyecto')` |

---

## 🔄 4. Cómo Modificar o Extender Componentes en el Futuro

### Escenario A: Quieres modificar un componente global para TODOS los módulos
* **Ejemplo:** Cambiar el color de los Toggles o cambiar el icono por defecto de los botones de creación.
* **Solución:** Edita directamente el archivo `app/Filament/Support/Columns/CommonColumns.php` o `CommonActions.php`. El cambio se reflejará instantáneamente en todos los Resources del sistema.

### Escenario B: Quieres agregar un NUEVO componente reutilizable
* **Ejemplo:** Formateador de moneda para tarifas o salarios (ej: `$20.00 / hr` o `$2,500.00 / mes`).
* **Solución:** Agrega un nuevo método estático en `CommonColumns.php`:

```php
// En app/Filament/Support/Columns/CommonColumns.php
public static function currencyRate(string $name, string $unit = '/ hr', string $label = 'SALARIO BASE'): TextColumn
{
    return TextColumn::make($name)
        ->label($label)
        ->money('USD')
        ->formatStateUsing(fn ($state) => '$' . number_format($state, 2) . " {$unit}");
}
```

Y luego simplemente lo invocas en tus recursos como:
`CommonColumns::currencyRate('base_salary', '/ hr', 'SALARIO BASE')`.

---

## ✅ 5. Checklist de Calidad antes de Entregar un Nuevo Resource

- [ ] ¿El archivo Resource utiliza `CommonColumns` en lugar de definir columnas desde cero?
- [ ] ¿Las acciones principales usan `CommonActions`?
- [ ] ¿El título y subtítulo coinciden con las maquetas de la carpeta `/UI`?
- [ ] ¿Se ejecutaron las pruebas automatizadas (`./vendor/bin/sail test`) y pasan al 100%?
