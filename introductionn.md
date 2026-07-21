# **DOCUMENTO DE ELICITACIÓN DE REQUERIMIENTOS (DER)**

# **Visión y Alcance del Producto (PRD Foundation)**

Proyecto: Sistema de Simulación Financiera, Cotización Dinámica y Control de Márgenes Operativos (Ecosistema V1)

Versión del Alcance: MVP v1.1

Rol del Emisor: Analista de Producto y Arquitecto de Software

Fecha de emisión: Julio 2026

## **1\. Resumen Ejecutivo y Objetivos de Negocio**

El presente sistema está diseñado para reemplazar de forma definitiva el ecosistema descentralizado y propenso a errores humanos basado en hojas de cálculo mutables dentro del sector de la remodelación, pintura y *flooring*.

El software soporta el ciclo de vida completo de un proyecto: desde la parametrización de costos indirectos (Overhead) y la estimación de esfuerzos, hasta el registro de la ejecución real en el campo y la conciliación financiera automática. Esto permite al dueño de la empresa proteger su margen de ganancia neta, simular escenarios de negociación con los clientes y detectar desvíos económicos en tiempo real.

### Metas Métricas a Corto Plazo (MVP)

* Reducción a Cero de errores por mutabilidad accidental en cotizaciones históricas aprobadas.  
* Disminución del 80% en el tiempo de formulación y estructuración de presupuestos y enmiendas de obra.  
* Trazabilidad del 100% de los gastos de mano de obra y materiales aplicados directamente contra la línea base del proyecto.  
* Mitigar al 0% el error humano de vender proyectos por debajo de su costo de equilibrio (*Breakeven*) gracias al cálculo automático del Overhead absorbido.  
* Garantizar visibilidad inmediata del balance financiero de la obra, reportando desvíos mayores al 5% en mano de obra o materiales antes de que afecten la liquidez del negocio.

## **2\. Límites del Mercado y Público Objetivo**

### **S**ectores/Procesos Incluidos (In-Scope)

* Proyectos y obras de remodelación residencial y comercial, pintura de interiores/exteriores y preparación e instalación de acabados de pisos (*flooring*).  
* Control operativo interno y de nivel puramente administrativo (dueño y gestor de operaciones de la empresa).

### Sectores/Procesos Excluidos (Out-of-Scope)

* Gestión de relaciones con clientes (CRM): embudos de venta, captación de leads, notas de seguimiento comercial o agendas de visitas de preventa.  
* Facturación electrónica legal, nómina contable real e integraciones de pasarelas de pago bancarias directas.  
* Control de inventario físico en tiempo real de almacenes o bodegas.

## **3\. Estructura de Límites del MVP (In vs. Out)**

| Áreas / Funcionalidades | Dentro del Alcance (IN \- MVP V1) | Fuera del Alcance (OUT \- Fase 2\) |
| :---- | :---- | :---- |
| Configuración Financiera | Gastos fijos (Overhead) y tarifas horarias por rol. | Contabilidad general, depreciación de activos. |
| Catálogo de Personal | Roster de empleados activos/inactivos (multirol). | Liquidaciones de nómina, control de asistencia biométrico. |
| Estructuración | Creación de Proyectos y Clientes asociados. | CRM de leads, portal de autogestión para clientes. |
| Cotización | Estimador dinámico por pestañas de mano de obra e insumos. | Catálogo de materiales con control de stock de almacén. |
| Protección Contable | Snapshot transaccional de costos al aprobar la cotización. | Firmas digitales integradas de validez legal. |
| Enmiendas de Obra | Máximo de 2 niveles por proyecto (Sustitución Completa). | Árboles de enmiendas ilimitadas y acumulativas. |
| Ejecución de Obra | Bitácora de horas trabajadas, compras reales y anticipos. | Escaneo de facturas por OCR, conciliación bancaria automática. |
| Dashboard | Conciliación gráfica de desvíos "Estimado vs. Real". | Proyecciones predictivas de rentabilidad por IA. |

## **4\. Reglas de Negocio Críticas**

* RN-01 (Línea Base Única por Proyecto): Un proyecto puede tener asociadas múltiples propuestas comerciales en distintos estados, pero únicamente puede existir una cotización activa en estado "Aprobada" en un momento dado.  
* RN-02 (Regla de Sustitución por Enmienda): Al aprobarse formalmente una enmienda de obra, esta se convierte en la nueva y única línea base financiera del proyecto. El registro padre anterior transiciona de forma automática e irreversible al estado "Cerrada por Enmienda", congelando permanentemente su snapshot.  
* RN-03 (Consistencia de Análisis del Dashboard): Las métricas y desvíos financieros mostrados en el Dashboard de Conciliación se calcularán contrastando la ejecución real exclusivamente contra los valores de la cotización activa que cuente con el estado "Aprobada".  
* RN-04 (Integridad del Contenedor de Obra): Todo proyecto registrado en el sistema debe pertenecer obligatoriamente a un único cliente del catálogo.  
* RN-05 (Pertenencia de Propuestas): Toda cotización debe nacer, procesarse y consolidarse asociada estrictamente a un proyecto del sistema.  
* RN-06 (Inmutabilidad Financiera Extrema): Queda estrictamente prohibida la eliminación física (DELETE) de cualquier registro financiero aprobado o ejecutado (cotizaciones aprobadas, bitácoras de horas de personal, compras realizadas o anticipos). Para desactivar registros o corregir errores se aplicarán estados lógicos o anulaciones operativas.  
* RN-07 (Fórmula del Costo por Hora Cargado de Mano de Obra \- $C\_{ch}$):  
  $$C\_{ch} \= \\text{Salario Base} \\times \\left(1 \+ \\frac{\\text{\\% Carga Social}}{100}\\right)$$  
* RN-08 (Fórmula de la Tasa de Overhead por Hora \- $T\_{oh}$):  
  $$T\_{oh} \= \\frac{\\text{Sumatoria de Gastos Fijos Mensuales Activos}}{\\text{Capacidad Estándar de Horas Mensuales de la Empresa}}$$  
* RN-09 (Fórmula del Precio de Venta Sugerido \- $PV$):  
  $$PV \= \\frac{\\text{Costo de Equilibrio (Costo Directo \+ Overhead Absorbido)}}{1 \- \\left(\\frac{\\text{Margen Cotizado}}{100}\\right)}$$  
* **RN-10 (Filtro de Relación de Jornadas):** Todo registro en `project_labor_logs` que asocie un `quote_labor_assignment_id` debe pertenecer única y estrictamente a la cotización que actúe como **Línea Base Activa (estado "Aprobada")** del proyecto en la fecha del registro.  
* **RN-11 (Tratamiento de Mano de Obra Excedente):** Si una jornada real se registra con `quote_labor_assignment_id` en `null`, el sistema clasificará automáticamente el costo de ese subtotal real como **"Mano de Obra Excedente No Presupuestada"**. Se sumará al costo directo real del proyecto, pero reflejará una desviación negativa directa en el Dashboard de Conciliación.

