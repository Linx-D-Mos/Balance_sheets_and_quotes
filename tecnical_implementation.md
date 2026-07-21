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
