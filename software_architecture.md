# **Documento de Arquitectura de Software**

**Proyecto:** Sistema de Simulación Financiera, Cotización Dinámica y Control de Márgenes Operativos (Ecosistema V1)

**Tecnología Core:** Laravel 13.x \+ Filament v4.x (PHP 8.3+)

**Patrón Arquitectónico:** Monolito Transaccional en Capas con Aislamiento de Lógica de Dominio

## **1\. Stack Tecnológico y Entorno de Ejecución**

* **Entorno de Ejecución:** PHP 8.3+ (exigido por Laravel 13, desbloqueando constantes de clase tipadas, la función nativa `json_validate()` y Attributes nativos de compilación).  
* **Framework Backend:** Laravel 13.x.  
* **Panel de Operación Administrativa:** Filament v4.x (componentes Blade interactivos con Livewire bajo el capó).  
* **Motor de Base de Datos:** PostgreSQL 16+.  
* **Gestor de Seguridad y Accesos:** Spatie Laravel Permission v6.x (autorización basada en Roles y Permisos granulares).  
* **Motor de Pruebas:** Pest PHP (v3) con estructura semántica de tests mediante bloques `describe` y `beforeEach`.  
* **Generación de Documentación de API:** Dedoc Scramble (OpenAPI 3.1), configurado para leer las firmas de los *JSON:API Resources* nativos de Laravel 13 sin anotaciones manuales.  
* **Procesamiento de Calendario Offline:** Librería local `azuyalabs/yasumi` para el cálculo matemático de festivos federales de EE. UU. (USA Federal Holidays) sin dependencias de red (RNF-1.1).

  ## **2\. Patrón de Flujo de Datos y Estructura de Capas**

El sistema procesará las solicitudes HTTP (tanto de la API de control como de las acciones internas de Filament) bajo el siguiente pipeline desacoplado:

\[Cliente HTTP / UI\] ➔ \[Request\] ➔ \[Policy\] ➔ \[Controller\] ➔ \[Service\] ➔ \[Resource\] 

### **A. Nomenclatura Estricta de Componentes (`ModeloAcciónTipodeArchivo`)**

Para garantizar la consistencia absoluta, cada archivo del sistema se nombrará bajo este formato. Ejemplo para la aprobación de una propuesta comercial:

* **Form Request:** `QuoteApproveRequest` (Valida que el input de aprobación sea correcto).  
* **Policy:** `QuotePolicy` (Valida que el usuario tenga el permiso Spatie `approve:quotes` y que la cotización esté en estado Borrador o Enviada).  
* **Controller:** `QuoteApproveController` (Controlador `__invoke` que recibe el request validado y llama al servicio).  
* **Service:** `QuoteApproveService` (Ejecuta la base de datos transaccional, genera el Snapshot y guarda los datos físicos).  
* **Resource:** `QuoteJsonResource` (Salida formateada de la cotización aprobada).

  ## **3\. Diccionarios de Dominio, Seguridad y Localización**

Para evitar los "strings mágicos" en el código, todos los estados, categorías y accesos se estructurarán en **Backed Enums** tipados, vinculados a archivos de traducción y control de seguridad.

app/Enums/

 ├── QuoteStatus.php        ➔ (draft, sent, approved, closed\_by\_amendment, canceled)

 ├── ProjectStatus.php      ➔ (draft, in\_progress, completed, canceled)

 ├── MaterialCategory.php   ➔ (budgeted, unbudgeted)

 ├── PaymentMethod.php      ➔ (cash, check, credit\_card, zelle)

 └── AppPermission.php      ➔ (manage\_settings, view\_any\_quotes, create\_quotes, approve\_quotes, edit\_quotes, write\_logs)

### **A. Catálogo de Permisos Granulares (`AppPermission`)**

Para evitar la dispersión de permisos en base de datos, toda acción sensible del sistema queda tipada de forma inmutable:

* `manage:settings` (Modificar tarifas base, multiplicadores de horas extras y costos fijos).  
* `approve:quotes` (Pasar cotizaciones a estado "Aprobada" y disparar el snapshot financiero).  
* `create:quotes` (Formular nuevas estimaciones y registrar materiales/mano de obra).  
* `create:enmiendas` (Generar enmiendas sobre cotizaciones aprobadas).  
* `write:logs` (Registrar gastos reales de campo, jornadas laborales y compras).  
* `close:projects` (Cambiar el estado de los proyectos a finalizados o cancelados).

  ### **B. Estrategia de Super-Administrador (Bypass de Desarrollo)**

Para acelerar el desarrollo del MVP sin configurar asignaciones de permisos complejas en local, el sistema implementará un interceptor global en `App\Providers\AppServiceProvider.php` (estilo Laravel 13):

PHP

use Illuminate\\Support\\Facades\\Gate;

public function boot(): void

{

    // Bypass automático: El rol "administrator" tiene acceso absoluto

    Gate::before(function ($user, $ability) {

        return $user-\>hasRole('administrator') ? true : null;

    });

}

## **4\. Estándar de Pipeline y Flujo en Capas**

Cada acción del sistema de cotización y balances se estructurará bajo el flujo estricto de cinco capas:

1. **Request (`app/Http/Requests/ModeloAcciónRequest.php`):** Form Request dedicado con tipado estricto y sanitización de inputs (ej. `QuoteApproveRequest`, `ProjectLaborLogStoreRequest`).  
2. **Policy (`app/Policies/ModeloPolicy.php`):** Clase de Laravel para autorizar la acción evaluando tanto el estado inmutable del modelo como el permiso del usuario mediante Spatie:  
3. PHP  
   public function approve(User $user, Quote $quote): bool  
   {  
       // 1\. Regla de Negocio: Solo cotizaciones no aprobadas ni cerradas  
       if (\! in\_array($quote-\>status\_id, \[QuoteStatus::DRAFT, QuoteStatus::SENT\])) {  
           return false;  
       }  
       // 2\. Regla de Seguridad Spatie RBAC  
       return $user-\>can('approve:quotes');  
   }  
4. **Controller (`app/Http/Controllers/ModeloAcciónController.php`):** Controlador Invocable de Acción Única (`__invoke`). Recibe el Request validado, invoca al Policy (`$this->authorize`), delega la lógica de cómputo al servicio correspondiente y devuelve el Resource (ej. `QuoteApproveController`).  
5. **Service (`app/Services/ModeloAcciónService.php`):** Clase pura de PHP encargada del cerebro matemático y transaccional. Es la única capa autorizada para tocar la base de datos de manera transaccional (`DB::transaction`) y realizar redondeos (ej. `QuoteApproveService`).  
6. **Resource (`app/Http/Resources/ModeloJsonResource.php`):** Serializa la respuesta JSON utilizando el estándar nativo JSON:API (ej. `QuoteJsonResource`).

   ### **5\. Suite de Pruebas Automatizadas con Pest PHP**

Se configurará el entorno de pruebas automatizadas en `tests/Pest.php` centralizando las dependencias globales de infraestructura, permitiendo que cada archivo de prueba (`Feature/Unit`) contenga bloques `describe()` limpios dedicados exclusivamente al montaje de datos del dominio de negocio:

#### **A. Configuración Global de Infraestructura (`tests/Pest.php`)**

PHP

\<?php

use Tests\\TestCase;

use Illuminate\\Foundation\\Testing\\RefreshDatabase;

// Extensión del caso de prueba y Traits de limpieza de base de datos

pest()-\>extend(TestCase::class)

    \-\>use(RefreshDatabase::class)

    \-\>beforeEach(function () {

        // Inicialización obligatoria de catálogos e infraestructura inmutable del sistema

        $this-\>seed(RolesAndPermissionsSeeder::class); // Spatie Roles y Permisos Base

        $this-\>seed(ProjectStatusesSeeder::class);     // Catálogo de estados de proyectos

        $this-\>seed(QuoteStatusesSeeder::class);       // Catálogo de estados de cotizaciones

        $this-\>seed(GlobalSettingsSeeder::class);      // Registro inicial ID 1 con multiplicadores fijos

    })

    \-\>in('Feature', 'Unit');

#### **B. Estructura de Pruebas Semánticas de Dominio (Ejemplo de Implementación Local)**

PHP

\<?php

use App\\Enums\\QuoteStatus;

use App\\Models\\Quote;

use App\\Models\\LaborRole;

describe('MOTOR DE SIMULACIÓN FINANCIERA \- REACTIVIDAD Y TOTALES', function () {

    beforeEach(function () {

        // Arrange Local: Creación de registros específicos para el contexto de este archivo

        $this-\>painterRole \= LaborRole::create(\[

            'name' \=\> 'Pintor Principal',

            'base\_salary' \=\> 20.0000,

            'social\_load\_pct' \=\> 15.0000, // Costo cargado automático C\_ch \= 23.0000

        \]);

        

        $this-\>quoteBorrador \= Quote::factory()-\>create(\[

            'status\_id' \=\> QuoteStatus::DRAFT-\>value,

            'margin\_applied' \=\> 20.0000

        \]);

    });

    test('el sistema calcula en caliente el costo directo sumando mano de obra y materiales', function () {

        // Act & Assert del cálculo reactivo financiero...

    });

});

## **6\. Integraciones y Documentación Automática**

* **Cálculo de Días Festivos (Yasumi ^2.11):** Integrado de forma offline en el cargador de servicios para determinar la capacidad promedio laboral de horas restando fines de semana y feriados de EE. UU.  
* **Documentación Viva de API (Dedoc Scramble):** Escanea por análisis estático la salida de tus controllers de acción única y firmas de `FormRequests` para exponer la especificación interactiva en OpenAPI 3.1 sin ensuciar tus clases con comentarios PHPDoc masivos.

  ## **7\. Requerimientos No Funcionales (RNF) \- Nivel Ingeniería**

* **RNF-1 (Precisión Aritmética Contable):** El sistema no debe utilizar variables de punto flotante nativas del hardware para cálculos agregados o comparativos. Todos los cálculos matemáticos del backend deben realizarse con precisión de cuatro decimales utilizando el estándar arbitrario de precisión decimal.  
* **RNF-2 (Rendimiento de Agregaciones en Caliente):** Las consultas sumatorias (`SUM`) necesarias para renderizar el Dashboard de Conciliación y el Balance de Caja deben ejecutarse en un tiempo inferior a 50 ms. La base de datos debe indexar físicamente las claves foráneas del proyecto (`project_id`) en todas las bitácoras de gastos y cobros.  
* **RNF-3 (Aislamiento de Modificación Financiera \- Snapshots):** Las actualizaciones en los catálogos de sueldos, el overhead mensual global de la empresa o las tarifas de materiales nunca deben recalcular o afectar los datos económicos persistidos físicamente en cotizaciones aprobadas, enviadas o cerradas por enmienda.  
* **RNF-4 (Seguridad Transaccional Multitabla):** Los flujos críticos que afecten la consistencia de múltiples tablas (como el Snapshot al aprobar o las transiciones en cascada de Enmiendas) deben estar envueltos en transacciones de base de datos con reversión automática (*rollback*) total ante cualquier error de red o base de datos.  
* **RNF-5 (Validación Hermética de Gastos):** La API y las interfaces de carga de datos en Filament deben desinfectar y validar las entradas impidiendo estrictamente el ingreso de montos monetarios o cantidades de insumos negativas, nulas o con caracteres inválidos.  
* **RNF-6 (Independencia Operativa Local):** El algoritmo de determinación del calendario laboral anual para estimaciones y tasas de overhead no debe depender de llamadas HTTP externas a APIs públicas, debiendo calcular la matriz de feriados federales de EE. UU. de forma 100% local en el servidor.  
* **RNF-7 (Cobertura de Pruebas Automatizadas):** El motor matemático financiero del sistema (clases de servicio de cálculo de cotizaciones y tasas) debe contar con una cobertura de pruebas de integración y unitarias automatizadas del 100%, garantizando la exactitud de los flujos de caja y márgenes de ganancia.  
* **RNF-8 (Estándar de Redondeo y Formato Visual):** Todas las cifras monetarias que se visualicen en el panel de control de Filament, tarjetas de métricas (*Widgets*), tablas administrativas y el documento PDF exportable de la propuesta comercial se formatearán de forma obligatoria a dos decimales utilizando el método de redondeo simétrico (*Half Up*).  
* **RNF-9 (Control de Acceso Basado en Roles y Permisos \- RBAC):** Toda acción que muta un estado financiero en base de datos o que modifique una configuración global debe estar validada por su respectivo `Policy` mapeado contra un permiso directo de Spatie. Filament debe estructurar sus recursos consumiendo de forma directa estas políticas, inhabilitando o escondiendo botones según los privilegios del usuario autenticado.

