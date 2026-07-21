## **ÉPICA 1: PARAMETRIZACIÓN GLOBAL DE COSTOS OPERATIVOS (BACK-OFFICE)**

### **HU-01: Control de Gastos Fijos Mensuales (Overhead)**

**Como:** Dueño y Administrador de la Empresa

**Quiero:** Registrar, modificar y suspender de manera individual los costos fijos de operación de mi empresa (renta, suscripciones, seguros, telefonía)

**Para:** Que el sistema calcule de forma automática el Overhead Mensual activo de la empresa sin riesgo de alterar la contabilidad de propuestas comerciales ya cerradas.

#### **Criterios de Aceptación Comerciales:**

* **CA-01.1 (Campos del Gasto Fijo):** La interfaz administrativa en Filament proveerá un formulario sencillo para la tabla fixed\_expenses con los siguientes campos:  
  * **Concepto:** Nombre comercial del gasto (ej: "Renta de Bodega", "Seguro de Auto Liability").  
  * **Monto Mensual:** Valor monetario (decimal positivo mayor a cero).  
  * **Estado Activo (Toggle):** Interruptor para encender o apagar el gasto (por defecto activo).  
* **CA-01.2 (Suma Automática de Costos Operativos):** Cada vez que se guarde un cambio o se altere el estado de un gasto, el sistema actualizará dinámicamente el valor del Overhead total sumando únicamente los montos marcados como activos:  
  $$\\text{Overhead Mensual} \= \\sum (\\text{amount}) \\quad \\text{donde } \\text{is\\\_active} \= \\text{true}$$  
* **CA-01.3 (Protección Histórica \- Cero Eliminación en Interfaz):** En alineación con la regla de negocio **RN-06**, el recurso de Filament para gastos fijos (`FixedExpenseResource`) deshabilitará por completo las acciones `DeleteAction` y `BulkDeleteAction`. La baja de un gasto se gestionará únicamente apagando el interruptor `is_active`, garantizando que no existan borrados físicos en la base de datos. 

  ### **HU-02: Definición de Tarifas de Mano de Obra (Labor Roles) con Calculadora de Impuestos**

**Como:** Dueño y Administrador de la Empresa

**Quiero:** Configurar un catálogo de puestos de trabajo (pintor, carpintero, preparador) indicando su sueldo base por hora y el porcentaje de carga social/impuestos sobre nómina de la empresa

**Para:** Conocer el costo real cargado por hora de cada rol y utilizarlo como tarifa base en mis estimaciones, evitando cálculos manuales en papel u hojas de cálculo descentralizadas.

#### **Criterios de Aceptación Comerciales:**

* **CA-02.1 (Formulario de Costeo Reactivo):** El formulario de roles en Filament se actualizará en tiempo real en la pantalla a medida que el administrador digite los valores, sin necesidad de recargar la página:  
  * **Nombre del Rol:** Título único del perfil (ej: "Pintor Principal").  
  * **Salario Base por Hora:** Pago nominal por hora (decimal mayor a cero).  
  * **Impuestos sobre Nómina / Carga Social (%):** Porcentaje estimado de cargas federales/estatales de la empresa (ej: 15.00%).  
* **CA-02.2 (Cálculo Inmediato del Costo Cargado \- $C\_{ch}$):** El sistema mostrará de forma visual en un campo bloqueado de solo lectura el costo final real por hora para la empresa aplicando la fórmula:  
  $$C\_{ch} \= \\text{Salario Base} \\times \\left(1 \+ \\frac{\\text{\\% Carga Social}}{100}\\right)$$  
* **CA-02.3 (Persistencia de Tarifa de Cotización):** Al presionar "Guardar", el valor de $C\_{ch}$ se guardará permanentemente en el catálogo para ser consumido de inmediato como la tarifa predeterminada de mano de obra en los nuevos borradores de cotización.

### **HU-03: Calculador de Capacidad de Trabajo y Tasa de Overhead por Hora ($T\_{oh}$)**

**Como:** Dueño y Administrador de la Empresa

**Quiero:** Que el sistema calcule de forma automática y offline la capacidad estándar de horas laborables mensuales restando fines de semana y días feriados federales de EE. UU. (USA)

**Para:** Obtener una Tasa de Overhead por Hora ($T\_{oh}$) justa y balanceada, asegurando que cada hora de mano de obra estimada en una cotización absorba la porción exacta de los gastos operativos mensuales de la empresa.

#### **Criterios de Aceptación Comerciales:**

* **CA-03.1 (Algoritmo Calendario Offline):** El sistema calculará anualmente los días laborables restando del calendario de 365 días todos los fines de semana (sábados y domingos) y los días feriados oficiales de EE. UU. (calculados de forma local y offline utilizando la librería Yasumi).  
* **CA-03.2 (Estabilización de Capacidad Mensual):** Para evitar que la tasa de overhead varíe drásticamente en meses de pocos días hábiles (como febrero o diciembre) encareciendo artificialmente los presupuestos, el sistema utilizará una capacidad promedio constante dividiendo las horas hábiles anuales entre los 12 meses:  
  1. $$\\text{Días del Año} \- \\text{Fines de Semana} \- \\text{Feriados USA (Yasumi)} \= \\text{Días Hábiles Anuales}$$  
  2. $$\\text{Horas Hábiles Anuales} \= \\text{Días Hábiles Anuales} \\times 8 \\text{ horas/día}$$  
  3. $$\\text{Capacidad Estándar de Horas Mensuales} \= \\frac{\\text{Horas Hábiles Anuales}}{12}$$  
* **CA-03.3 (Cálculo Automático de Tasa de Overhead \- $T\_{oh}$):** El sistema dividirá los gastos fijos mensuales activos entre la capacidad promedio mensual estándar para generar la tasa operativa base por hora de la empresa:  
  $$T\_{oh} \= \\frac{\\text{Overhead Mensual Sumado}}{\\text{Capacidad Estándar de Horas Mensuales}}$$  
  *Este valor $T\_{oh}$ será persistido en las configuraciones globales (global\_settings) para su uso inmediato en las cotizaciones.*

### **HU-04: Panel de Control de Parámetros Globales**

**Como:** Administrador del Sistema

**Quiero:** Contar con un panel administrativo visual de solo lectura donde pueda auditar de un vistazo la suma de mis gastos fijos activos, la capacidad mensual de horas laborales calculada y la Tasa de Overhead por Hora ($T\_{oh}$) resultante

**Para:** Validar la salud financiera y la tasa de absorción de mis costos operativos antes de procesar cotizaciones comerciales.

#### **Criterios de Aceptación Comerciales:**

* **CA-04.1 (Métricas de Control en Filament):** El panel "Control de Parámetros Globales" mostrará de manera prominente mediante indicadores (*Widgets*) de solo lectura:  
  * **Overhead Mensual Activo:** Sumatoria en tiempo real de los gastos activos de la empresa.  
  * **Horas Laborables Promedio:** La capacidad mensual estándar del año en curso según la **HU-03**.  
  * **Tasa de Overhead Resultante ($T\_{oh}$):** El costo por hora que el negocio debe cobrar de forma indirecta para mantenerse a flote.  
* **CA-04.2 (Actualización Automatizada por Eventos):** El sistema no requerirá acciones manuales para actualizar la tasa. Mediante controladores de eventos de Laravel (*Eloquent Observers*), cualquier cambio en el catálogo de gastos fijos o la transición de año calendario disparará de inmediato el recálculo y la actualización automática del valor en la tabla global\_settings.

### **HU-05: Control y Disponibilidad del Roster de Personal (Catálogo de Empleados)**

*(Alineada al 100% con la base de datos)*

**Como:** Dueño y Administrador de la Empresa

**Quiero:** Registrar y mantener un listado centralizado con los nombres de mi personal disponible

**Para:** Poder llamarlos a trabajar en los proyectos e imputar sus horas reales en el campo, manteniendo la flexibilidad de asignarles cualquier función y tarifa técnica según las necesidades específicas de cada obra.

#### **Criterios de Aceptación Comerciales:**

* **CA-05.1 (Alta de Personal Flexible sin Roles Fijos):** El administrador podrá dar de alta a un nuevo trabajador ingresando únicamente su nombre completo como dato obligatorio. El sistema no exigirá ni asociará un puesto o salario fijo a nivel global, respetando la naturaleza multirol del personal en la empresa.  
* **CA-05.2 (Control de Disponibilidad Operativa):** Cada trabajador contará con un selector visual de estado ("Activo / Inactivo"). Al marcar a un empleado como "Inactivo", este dejará de aparecer inmediatamente en los listados de selección de nuevos proyectos o cotizaciones, evitando asignaciones erróneas de personal que ya no está disponible.  
* **CA-05.3 (Blindaje de Historial Financiero / Cero Eliminación en Interfaz):** Queda estrictamente prohibido el borrado físico de empleados en el sistema. El recurso de Filament `EmployeeResource` tendrá deshabilitadas las opciones `DeleteAction` y `BulkDeleteAction`. La desincorporación de un trabajador se gestionará exclusivamente a través del estado lógico `"Inactivo"`. 

## **ÉPICA 2: MOTOR DE COTIZACIÓN DINÁMICA**

### **HU-06: Estructuración de Propuestas Comerciales Vinculadas a Proyectos**

**Como:** Administrador y Dueño del Negocio

**Quiero:** Que el sistema me obligue a asociar cada propuesta económica (Cotización) a un Proyecto específico del cliente

**Para:** Mantener centralizado el historial de borradores, cotizaciones enviadas, aprobadas y futuras enmiendas de alcance bajo un único contrato maestro, evitando la existencia de cotizaciones "huérfanas" en el sistema.

#### **Criterios de Aceptación Comerciales:**

* **CA-06.1 (Asociación Obligatoria a Proyecto):** El formulario de creación de cotizaciones exigirá obligatoriamente seleccionar un Proyecto activo (project\_id). No se permitirá guardar ninguna cotización en el aire.  
* **CA-06.2 (Acceso Directo y Creación de Proyectos en Caliente):** Si el proyecto es nuevo, la interfaz en Filament proveerá un botón de acceso rápido dentro del selector que abrirá un modal para crear el Proyecto y asociarle el Cliente sin abandonar la pantalla de cotización ni perder la información digitada.  
* **CA-06.3 (Trazabilidad en el Contenedor de Proyectos):** Al visualizar la pantalla de un Proyecto en Filament, el sistema renderizará una tabla con el listado cronológico de todas las cotizaciones vinculadas, mostrando su código, fecha de creación, costo directo total y su estado actual.

### **HU-07A: Configuración Comercial y Margen de la Propuesta**

**Como:** Administrador del Negocio

**Quiero:** Inicializar una propuesta económica seleccionando el proyecto asociado, el cliente y definiendo el margen de ganancia comercial deseado

**Para:** Establecer la base del beneficio económico que pretendo percibir en la obra antes de proceder a la carga de costos directos.

#### **Criterios de Aceptación Comerciales:**

* **CA-07A.1 (Campos Base de Inicialización):** El sistema presentará la primera pestaña del formulario de Filament ("General") con los siguientes controles obligatorios: Selector de Cliente, Selector de Proyecto (project\_id) y campo numérico de Margen de Ganancia (margin\_applied).  
* **CA-07A.2 (Precarga Automatizada de Parámetros Globales):** Al abrir el formulario de una nueva cotización, el campo margin\_applied se inicializará automáticamente consumiendo el valor guardado en global\_settings.default\_profit\_margin (registro ID 1), pero se mantendrá 100% editable.

### **HU-07B: Calculador Reactivo de Fecha de Fin (Modo Duración)**

**Como:** Estimador del Negocio

**Quiero:** Que el sistema calcule dinámicamente la fecha estimada de finalización a partir de la fecha de inicio y una duración especificada, controlando la inclusión o exclusión de días no hábiles

**Para:** Ofrecer un plazo de entrega realista al cliente basándome en los días del calendario en que operará el equipo.

#### **Criterios de Aceptación Comerciales:**

* **CA-07B.1 (Cálculo Dinámico de Fecha de Fin):** Al ingresar la *Fecha de Inicio* y digitar una *Duración Estimada* (en días hábiles o semanas), Filament calculará automáticamente el campo end\_date bajo las siguientes reglas reactivas:  
  * **Si work\_weekends está APAGADO (False):** El sistema sumará los días de duración saltándose todos los sábados, domingos y días feriados federales de EE. UU. (calculados localmente con Yasumi), empujando la fecha de fin hacia adelante.  
  * **Si work\_weekends está ENCENDIDO (True):** El sistema sumará los días de duración de corrido (días naturales).

### **HU-07C: Calculador de Días Hábiles Disponibles (Modo Rango Fijo)**

**Como:** Estimador del Negocio

**Quiero:** Que el sistema deduzca la cantidad de días hábiles reales de ejecución si el cliente impone un rango de fechas de inicio y fin inamovibles

**Para:** Alertar visualmente si el margen de tiempo es muy ajustado para el esfuerzo estimado.

#### **Criterios de Aceptación Comerciales:**

* **CA-07C.1 (Cálculo Dinámico de Capacidad de Días):** Si el usuario selecciona manualmente tanto la *Fecha de Inicio* como la *Fecha de Fin*, el sistema mostrará de inmediato un indicador visual de solo lectura con el total de **"Días Hábiles Disponibles para Ejecución"**:  
  * **Si work\_weekends está APAGADO (False):** Mostrará el conteo neto restando fines de semana y feriados USA (Yasumi).  
  * **Si work\_weekends está ENCENDIDO (True):** Mostrará el conteo total de días naturales del rango.  
  * 

### **HU-08: Estimador Reactivo de Mano de Obra (Esfuerzo Atómico)**

**Como:** Administrador del Negocio

**Quiero:** Estimar el esfuerzo de mano de obra agregando individualmente las posiciones de los pintores o instaladores, asignando sus roles y proyectando sus horas de trabajo

**Para:** Conocer instantáneamente el costo directo de nómina de la obra basado en las tarifas por hora vigentes de mi catálogo de roles.

#### **Criterios de Aceptación:**

* **CA-08.1:** El sistema proveerá la pestaña "Mano de Obra" estructurada como un sumador dinámico (Repeater) donde cada fila representa una asignación individual.  
* **CA-08.2:** Por cada fila, el usuario seleccionará el Rol de Trabajo (labor\_role\_id), pudiendo opcionalmente seleccionar un empleado real (employee\_id) o escribir un marcador de posición de texto libre (worker\_name\_placeholder, ej: "Pintor Auxiliar 1").  
* **CA-08.3:** Al ingresar las horas normales y extras estimadas en la fila, el sistema calculará en tiempo real el subtotal del costo de ese colaborador usando la tarifa por hora cargada de la empresa ($C\_{ch}$) y el multiplicador de hora extra vigente.

### **HU-09: Estimador de Materiales de Compra Directa (Insumos Libres)**

**Como:** Administrador del Negocio

**Quiero:** Listar libremente los materiales e insumos necesarios para la obra, especificando su descripción, cantidad y costo unitario estimado

**Para:** Computar el costo directo de materiales de la propuesta de forma flexible y adaptada a las necesidades específicas de la obra, sin depender de un catálogo rígido preexistente.

#### **Criterios de Aceptación:**

* **CA-09.1:** El sistema proveerá la pestaña "Materiales" estructurada como un sumador dinámico (Repeater) que permitirá agregar insumos mediante campos de texto y numéricos libres.  
* **CA-09.2:** Cada fila requerirá obligatoriamente: Concepto (ej. "Benjamin Moore 5 Galones", "Drywall Screws 1 Box"), Cantidad Estimada y Costo Unitario Estimado.  
* **CA-09.3:** El sistema calculará automáticamente en la pantalla el subtotal de cada fila multiplicando la cantidad estimada por el costo unitario estimado.

### **HU-10: Cálculo en Caliente del Costo Directo ($CD$)**

**Como:** Estimador de Proyectos

**Quiero:** Que el sistema sume automáticamente el subtotal acumulado de las pestañas de Mano de Obra y Materiales en el pie del formulario

**Para:** Conocer la base del costo directo de la obra instantáneamente a medida que añado filas a los repeaters.

#### **Criterios de Aceptación Comerciales:**

* **CA-10.1 (Suma Reactiva de Mano de Obra):** Al agregar, eliminar o modificar horas de un rol en el agregador de mano de obra, Filament recalculará el subtotal de nómina en pantalla sin recargar la página.  
* **CA-10.2 (Suma Reactiva de Materiales):** Al ingresar costos o cantidades de materiales directos, el subtotal se actualizará instantáneamente.  
* **CA-10.3 (Consolidación de Costo Directo):** El campo del Costo Directo ($CD$) mostrará la suma matemática en vivo:  
  $$CD \= \\sum(\\text{Subtotales de Mano de Obra}) \+ \\sum(\\text{Subtotales de Materiales})$$

### **HU-11: Cálculo en Caliente del Overhead Absorbido ($OH$)**

**Como:** Administrador y Dueño del Negocio

**Quiero:** Que el sistema multiplique en vivo las horas de esfuerzo estimadas por la Tasa de Overhead ($T\_{oh}$) activa

**Para:** Visualizar con precisión qué porción de los costos fijos de mi oficina está absorbiendo el proyecto en el campo.

#### **Criterios de Aceptación Comerciales:**

* **CA-11.1 (Detección de Horas de Esfuerzo):** El sistema sumará en tiempo real todas las horas (regulares y extras) de los trabajadores asignados en la pestaña de mano de obra.  
* **CA-11.2 (Cálculo del Overhead Absorbido):** Multiplicará el total de horas obtenidas en el CA-11.1 por la $T\_{oh}$ guardada en global\_settings:  
  $$OH \= \\text{Horas Totales Estimadas} \\times T\_{oh}$$  
* **CA-11.3 (Actualización Dinámica):** El valor de Overhead absorbido se actualizará en el formulario en vivo cada vez que se modifique una sola hora de esfuerzo en el roster.

### **HU-12: Cálculo del Costo de Equilibrio ($CE$) y Precio de Venta Sugerido ($PV$)**

**Como:** Administrador del Negocio

**Quiero:** Que el sistema combine los costos directos e indirectos para mostrarme el precio sugerido de venta en base al margen comercial deseado

**Para:** Presentar propuestas comerciales que garanticen la rentabilidad de la empresa y cubran el punto de equilibrio (Breakeven).

#### **Criterios de Aceptación Comerciales:**

* **CA-12.1 (Costo de Equilibrio):** El sistema sumará el Costo Directo ($CD$) y el Overhead Absorbido ($OH$) para pintar de solo lectura el Costo de Equilibrio ($CE$):  
  $$CE \= CD \+ OH$$  
* **CA-12.2 (Precio de Venta Sugerido):** Aplicará la fórmula de margen sobre precio de venta, basándose en el porcentaje introducido en el input margin\_applied:  
  $$PV \= \\frac{CE}{1 \- \\left(\\frac{\\text{margin\\\_applied}}{100}\\right)}$$  
* **CA-12.3 (Reactividad del Margen):** Si el usuario altera el margen deseado (ej: de 20% a 35%), el precio sugerido se recalculará instantáneamente en pantalla.

### **HU-13: Alerta de Seguridad de Margen de Ganancia Mínimo**

**Como:** Dueño de la Empresa

**Quiero:** Que el sistema resalte visualmente en color rojo el precio de venta si el margen aplicado cae por debajo del 10% sobre el Costo de Equilibrio

**Para:** Evitar que mis estimadores o yo mismo vendamos proyectos por debajo del costo real operativo por un error de digitación.

#### **Criterios de Aceptación Comerciales:**

* **CA-13.1 (Gatillo de Alerta):** El sistema evaluará el precio final resultante frente al Costo de Equilibrio ($CE$). Si el margen de utilidad neta real proyectado es inferior al 10%, el campo de precio de venta se sombreará en rojo.  
* **CA-13.2 (Mensaje de Advertencia):** Se desplegará un texto dinámico de advertencia debajo del input que dirá: *"¡Alerta Financiera\! El precio configurado no cubre el margen mínimo de seguridad de la empresa (10%)."*

## **ÉPICA 3: PERSISTENCIA HISTÓRICA, CICLO DE VIDA E INMUTABILIDAD**

### **HU-14: Ciclo de Vida y Transición de Estados de Propuestas**

**Como:** Administrador del Negocio

**Quiero:** Controlar el flujo de transiciones de mis propuestas comerciales (Borrador, Enviada, Aprobada, Cancelada)

**Para:** Garantizar que las cotizaciones sigan un proceso ordenado y que las reglas de negocio de inmutabilidad se activen en los momentos correctos.

#### **Criterios de Aceptación:**

* **CA-14.1:** El sistema controlará estrictamente el flujo de estados: una propuesta nace en "Borrador", puede pasar a "Enviada", y de "Borrador" o "Enviada" puede transicionar a "Aprobada" o "Cancelada".  
* **CA-14.2:** No se permitirá revertir una propuesta a "Borrador" una vez que ha sido aprobada o cerrada por enmienda, protegiendo el flujo lógico del contrato.

### **HU-15: Transición Controlada al Estado "Aprobada"**

**Como:** Administrador de la Plataforma

**Quiero:** Que el sistema valide las reglas de negocio de consistencia antes de permitir cambiar el estado de una propuesta a "Aprobada"

**Para:** Evitar inconsistencies de estados, asegurando que un proyecto solo tenga un único baseline de comparación.

#### **Criterios de Aceptación Comerciales:**

* **CA-15.1 (Validación de Línea Base Única \- RN-01):** Al intentar aprobar una cotización, el sistema buscará si el proyecto ya tiene otra cotización aprobada. Si existe una activa, denegará la acción e indicará al usuario que debe archivarla o enmendarla primero.  
* **CA-15.2 (Flujo de Enmienda Directo \- RN-02):** Si la cotización que se aprueba es una Enmienda, el sistema transicionará de forma automática el registro padre anterior al estado "Cerrada por Enmienda".  
* **CA-15.3 (Activación de Estado de Proyecto Automático):** Al aprobarse la primera cotización de un proyecto (pasando de `draft` o `sent` a `approved`), el sistema disparará un evento para actualizar automáticamente el estado del proyecto padre (`projects.project_status_id`) al valor **"En Ejecución"** (`in_progress`), eliminando la necesidad de que el usuario lo modifique de forma manual en un panel secundario. 

### **HU-16: Persistencia Física del Snapshot Financiero (Congelamiento Contable)**

**Como:** Administrador Financiero

**Quiero:** Que al momento de aprobar la propuesta, el sistema guarde una copia inmutable de todas las tarifas globales utilizadas en las tablas de la transacción

**Para:** Proteger el presupuesto histórico del proyecto de futuros incrementos de precios de materiales o aumentos en la nómina general de la compañía.

#### **Criterios de Aceptación Comerciales:**

* **CA-16.1 (Transaccionalidad Atómica):** El proceso de congelamiento se ejecutará bajo una transacción de base de datos (DB::transaction). Si un solo guardado falla, se aplicará rollback de todo el proceso.  
* **CA-16.2 (Volcado de Tarifas):** Se copiará el valor de la $T\_{oh}$ y el $C\_{ch}$ actual a columnas dedicadas en la base de datos:  
  * quotes.overhead\_rate\_applied $\\leftarrow$ Tasa global del día de la aprobación.  
  * quote\_labor\_assignments.hourly\_rate\_at\_estimation $\\leftarrow$ Tarifa del rol del día de la aprobación.  
  * quote\_material\_items.estimated\_unit\_price $\\leftarrow$ Costo unitario estimado del día de la aprobación.

### **HU-17: Bloqueo Hermético de Interfaz (Modo Solo Lectura)**

**Como:** Administrador de la Plataforma

**Quiero:** Que el formulario completo de la cotización se desactive visualmente una vez que la propuesta comercial pase a estado "Enviada" o "Aprobada"

**Para:** Evitar que cualquier usuario pueda alterar de forma accidental los números que ya fueron enviados al cliente o aprobados para ejecución.

#### **Criterios de Aceptación Comerciales:**

* **CA-17.1 (Deshabilitación del Formulario):** En Filament, si el estado de la cotización es sent, approved o closed\_by\_amendment, todos los campos de texto, selectores y repeaters pasarán a modo .disabled().  
* **CA-17.2 (Ocultamiento de Acciones de Edición):** Los botones de "Agregar material", "Agregar pintor" y "Guardar" se removerán por completo de la vista de detalle para impedir llamadas innecesarias al backend.

### **HU-18: Bloqueo de Interfaz y Modo Solo Lectura de Propuestas**

**Como:** Administrador del Negocio

**Quiero:** Que el formulario de cotización se bloquee por completo en la pantalla una vez que la propuesta ha sido aprobada o enviada

**Para:** Impedir modificaciones accidentales en los datos del presupuesto por parte de los usuarios del sistema.

#### **Criterios de Aceptación:**

* **CA-18.1:** Al visualizar una cotización en estado "Enviada", "Aprobada" o "Cerrada por Enmienda", el sistema deshabilitará todos los inputs de texto, selectores, botones de agregar y repeaters del formulario.  
* **CA-18.2:** El formulario mostrará una insignia visual clara en la parte superior que indique: "Solo Lectura \- Propuesta \[Estado\]".

### **HU-19: Clonación de Cotizaciones para Contrapropuestas**

**Como:** Administrador y Dueño del Negocio

**Quiero:** Duplicar de forma rápida una cotización existente que fue cancelada, rechazada o que aún está en borrador

**Para:** Generar una nueva propuesta modificable sin tener que digitar todo el presupuesto desde cero, recalculando de inmediato los costos de mano de obra y materiales con las tarifas y Overhead vigentes al día de hoy.

#### **Criterios de Aceptación Comerciales:**

* **CA-19.1 (Gatillo de Clonación):** La opción "Clonar Cotización" estará disponible visualmente en la barra de acciones de Filament para cualquier cotización que no esté en estado "Aprobada" o "Enviada" (es decir, permitida en Borrador, Rechazada o Cancelada).  
* **CA-19.2 (Estructuración del Clon y Reseteo Financiero):** El sistema creará una copia idéntica de la cotización original bajo las siguientes reglas:  
  * Hereda el cliente, el proyecto (project\_id), el rango de fechas, los roles asignados, cantidades de materiales y configuraciones de días festivos.  
  * El estado de la nueva cotización se establece obligatoriamente en "Borrador".  
  * Se descartan por completo los snapshots financieros de la cotización origen. El nuevo borrador aplica en caliente el costo cargado por hora ($C\_{ch}$) de los roles y la Tasa de Overhead ($T\_{oh}$) activos hoy en la configuración global.  
* **CA-19.3 (Redirección Administrativa):** Tras procesar la copia de forma segura en la base de datos, el sistema redirigirá automáticamente al administrador al formulario de edición de la nueva propuesta para que realice los ajustes solicitados por el cliente.

### **HU-20: Iniciación de Enmienda y Límite de Jerarquía**

**Como:** Administrador y Dueño del Negocio

**Quiero:** Iniciar un flujo formal de enmienda sobre una cotización aprobada y validar que el proyecto no exceda el límite administrativo de modificaciones

**Para:** Controlar los cambios de alcance de las obras y restringir las modificaciones a un historial máximo de dos niveles para mantener la estabilidad operativa del proyecto.

#### **Criterios de Aceptación:**

* **CA-20.1:** En la vista de detalle de una cotización "Aprobada", se habilitará la acción "Generar Enmienda".  
* **CA-20.2**: (Límite Jerárquico Directo por Nivel de Enmienda): El sistema utilizará la columna amendment\_level de la cotización para validar la profundidad del historial sin realizar consultas recursivas.Si la cotización padre tiene un amendment\_level mayor o igual a 2, el botón "Generar Enmienda" estará completamente deshabilitado en Filament.Al guardar la enmienda en borrador, el sistema asignará de manera automática al nuevo registro:$$\\text{Nuevo } amendment\\\_level \= \\text{amendment\\\_level del Padre} \+ 1$$  
* **CA-20.3:** Al procesar con éxito la acción, el sistema cambiará de forma automática el estado de la cotización padre a "Cerrada por Enmienda" (closed\_by\_amendment), congelando permanentemente su snapshot original.

### **HU-21: Ajuste Flexible de Personal y Materiales en Enmienda Borrador**

**Como:** Administrador del Negocio

**Quiero:** Que la nueva enmienda nazca como una copia del presupuesto anterior en estado de borrador, permitiéndome añadir, eliminar o modificar libremente a los pintores y materiales

**Para:** Rediseñar la estructura de costos y alcance de la obra basándome en las nuevas necesidades del cliente, recalculando los costos con las tarifas globales vigentes al día de hoy.

#### **Criterios de Aceptación:**

* **CA-21.1:** La nueva enmienda se creará en estado "Borrador", vinculada a la cotización padre mediante parent\_quote\_id y al proyecto maestro mediante project\_id.  
* **CA-21.2:** El sistema duplicará todas las filas de mano de obra y materiales del presupuesto padre en el nuevo formulario editable.  
* **CA-21.3:** El administrador podrá interactuar libremente con los repeaters para: agregar nuevos roles de pintores, eliminar asignaciones anteriores que ya no participarán en la obra, reajustar horas normales/extras y modificar conceptos de materiales. Las tarifas de los roles se actualizarán automáticamente con los costos cargados ($C\_{ch}$) vigentes hoy en la empresa.

### **HU-22: Activación de Nueva Línea Base (Aprobación de Enmienda)**

**Como:** Administrador y Dueño del Negocio

**Quiero:** Que al aprobar la nueva enmienda, esta sustituya por completo la línea base presupuestaria del proyecto

**Para:** Que el módulo de control real contrastre los gastos de campo exclusivamente contra esta nueva foto aprobada activa, simplificando el análisis de desvíos financieros.

#### **Criterios de Aceptación:**

* **CA-22.1:** Al pasar la cotización de enmienda al estado "Aprobada", el sistema la marcará como la única línea base activa del proyecto.  
* **CA-22.2:** El sistema ejecutará el snapshot financiero de la enmienda (**HU-16**), congelando físicamente sus tarifas y totales independientes en la base de datos.  
* **CA-22.3:** A partir de este momento, cualquier consulta del Dashboard de balance financiero ignorará los presupuestos anteriores y calculará las desviaciones de mano de obra y materiales usando únicamente los datos de esta enmienda aprobada.

### **HU-23: Visualización del Historial de Cambios (Trazabilidad)**

**Como:** Administrador y Dueño del Negocio

**Quiero:** Visualizar una pestaña con el historial cronológico y jerárquico de todas las modificaciones que ha sufrido el presupuesto del proyecto

**Para:** Auditar la evolución del precio del contrato con el cliente, viendo con claridad los montos de cada versión y quién la aprobó.

#### **Criterios de Aceptación:**

* **CA-23.1:** En la vista de detalle del Proyecto o de la Cotización activa, Filament renderizará una pestaña llamada "Historial de Cambios".  
* **CA-23.2:** Esta pestaña mostrará una tabla ordenada cronológicamente con la cadena de enmiendas vinculadas (Original, Enmienda 1, Enmienda 2), detallando por fila: Código de cotización, Fecha de registro, Estado, Horas Totales estimadas, Costo Directo y el Precio Final de Venta cobrado al cliente.

### **HU-24A: Generación de Propuesta Comercial Oculta (PDF Cliente)**

* ### **Como:** Administrador del Negocio

* ### **Quiero:** Abrir un modal en Filament que me permita descargar un PDF limpio de mi cotización orientado al cliente

* ### **Para:** Presentar un presupuesto profesional que proteja los costos internos y salarios de la empresa de miradas ajenas.

#### **Criterios de Aceptación Comerciales:**

* ### **CA-24A.1 (Gatillo Interactivo de Descarga):** El botón "Generar PDF" en Filament abrirá un modal de configuración. Por defecto, estará seleccionada la opción *"Propuesta Comercial (Cliente)"*. Al presionar "Descargar", se procesará el archivo sin alterar el estado de edición de la cotización en la plataforma.

* ### **CA-24A.2 (Maquetación y Censura Financiera de Cliente):** El archivo PDF generado mostrará el diseño ejecutivo de la obra incluyendo: Datos generales, fechas estimadas, desglose de roles con horas totales y conceptos de materiales con sus cantidades.

* ### **CA-24A.3 (Blindaje de Datos Internos \- RN-06):** El reporte **ocultará estrictamente** los salarios base de los trabajadores, los costos cargados reales ($C\_{ch}$), los precios unitarios estimados de los materiales y las tasas o costos de overhead absorbido. El único valor monetario visible será el **Precio Final de Venta** destacado en grande.

### **HU-24B: Variaciones del PDF para Campo y Administración (Filtros Dinámicos)**

* ### **Como:** Gestor de Operaciones y Administrador Financiero

* ### **Quiero:** Seleccionar en el modal de descarga si el PDF va dirigido al equipo de pintores en el campo o a la auditoría interna de la oficina

* ### **Para:** Disponer de reportes técnicos operativos o análisis contables consolidados según la necesidad del momento.

#### **Criterios de Aceptación Comerciales:**

* ### **CA-24B.1 (Inclusión de Perfiles en el Selector):** El selector del modal de la HU-24A incluirá dos nuevas opciones: *"Orden de Trabajo / Lista de Campo"* y *"Reporte Interno Consolidado"*.

* ### **CA-24B.2 (Filtro Técnico para Campo \- Orden de Trabajo):** Si se selecciona la opción de campo, el PDF se generará mostrando únicamente la lista de materiales (conceptos y cantidades netas para compra o retiro de bodega) y las horas de esfuerzo estimadas por rol. Este documento **censurará el 100% de los datos monetarios** (sin precios unitarios, subtotales ni precio final de venta).

* ### **CA-24B.3 (Transparencia Total para Administración):** Si se selecciona la opción interna, el PDF se descargará como una copia idéntica y sin restricciones de la simulación financiera en pantalla, detallando: costos directos ($CD$), overhead absorbido ($OH$), costo de equilibrio ($CE$), margen aplicado y precio final de venta.

### **HU-24C: Acción de Emisión Formal y Bloqueo de Edición (Cierre de Ciclo)**

### **Como:** Administrador del Negocio

### **Quiero:** Contar con una acción independiente en la interfaz para marcar formalmente la propuesta como emitida al cliente

* ### **Para:** Congelar el presupuesto de forma voluntaria, evitando que modificaciones accidentales en el borrador alteren las condiciones enviadas al cliente.

#### **Criterios de Aceptación Comerciales:**

* ### **CA-24C.1 (Gatillo de Cierre de Edición):** La barra de acciones de Filament mostrará un botón explícito llamado *"Emitir y Bloquear Propuesta"*. Esta acción requerirá una confirmación secundaria del usuario antes de ejecutarse.

* ### **CA-24C.2 (Transición de Estado Segura):** Al confirmar, el sistema traicionará el estado de la cotización de Borrador a Enviada en la base de datos de manera transaccional.

* ### **CA-24C.3 (Bloqueo Hermético de Interfaz):** En el instante en que el estado pase a Enviada, Filament activará el modo solo lectura de forma reactiva, deshabilitando todos los campos de texto, selectores y repeaters de mano de obra y materiales, removiendo además los botones de guardado.


## **ÉPICA 4: BALANCE Y CONCILIACIÓN DE GASTOS REALES**

### **HU-25A: Registro Atómico de Jornada de Trabajo**

**Como:** Administrador del Negocio

**Quiero:** Registrar de forma diaria o semanal las horas reales trabajadas por mi personal en un proyecto activo

**Para:** Imputar de forma atómica el esfuerzo de mano de obra en el campo.

#### **Criterios de Aceptación Comerciales:**

* **CA-25A.1 (Registro de Jornada Atómico):** En la pantalla de gestión del proyecto en ejecución, Filament proveerá un formulario de carga rápida para registrar jornadas individuales en la tabla `project_labor_logs`:  
  * **Trabajador:** Selector que consume la lista de empleados activos (*employees*).  
  * **Rol Desempeñado:** Selector para definir qué función realizó en esta jornada (*labor\_roles*).  
  * **Horas Regulares Reales:** Horas estándar trabajadas (mayor o igual a cero).  
  * **Horas Extras Reales:** Horas adicionales trabajadas (mayor o igual a cero).  
  * **Tarifa Real por Hora Pagada:** Campo monetario, precargado por defecto con el costo cargado ($C\_{ch}$) del rol seleccionado para ahorrar digitación, pero **100% editable**.  
* **CA-25A.2 (Selector de Asignación Presupuestada):** El formulario de Filament incluirá un selector opcional llamado "Posición/Línea Presupuestada". Este selector aplicará un filtro dinámico en caliente: *solo mostrará las filas del repeater de mano de obra de la cotización aprobada activa de este proyecto* (ej. "Pintor Auxiliar 1", "Carpintero Principal").  
* **CA-25A.3 (Precarga de Datos por Selección):** Al seleccionar una línea presupuestada, el formulario de Filament autocompletará de manera reactiva el labor\_role\_id y la hourly\_rate\_actual con el costo cargado ($C\_{ch}$) guardado en el snapshot de esa asignación, reduciendo el trabajo de digitación del usuario a cero.

### **HU-25B: Cálculo de Nómina Ejecutada e Inmutabilidad del Multiplicador**

**Como:** Administrador Financiero del Negocio

**Quiero:** Que el sistema calcule automáticamente el costo total real de cada jornada utilizando un snapshot del multiplicador de horas extras del día del registro

**Para:** Evitar que los balances históricos acumulados de la obra se alteren retroactivamente si cambio el multiplicador de la empresa en el futuro.

#### **Criterios de Aceptación Comerciales:**

* **CA-25B.1 (Fórmula del Subtotal Real de Nómina):** Al guardar la jornada, el sistema multiplicará las horas reales por la tarifa real y por el multiplicador de horas extras ($M\_{he}$) vigente en ese instante de tiempo en global\_settings.overtime\_multiplier.  
* **CA-25B.2 (Volcado de Snapshot del Multiplicador):** El multiplicador utilizado se guardará físicamente en la columna project\_labor\_logs.overtime\_multiplier\_applied. El valor calculado se guardará de forma física y definitiva en la columna actual\_subtotal aplicando la fórmula:  
  $$\\text{actual\\\_subtotal} \= \\left( \\text{actual\\\_hours\\\_regular} \\times \\text{hourly\\\_rate\\\_actual} \\right) \+ \\left( \\text{actual\\\_hours\\\_extra} \\times \\text{hourly\\\_rate\\\_actual} \\times \\text{overtime\\\_multiplier\\\_applied} \\right)$$  
* **CA-25B.3 (Blindaje Histórico):** Si en el futuro un administrador modifica el multiplicador de horas extras en global\_settings, el sistema **no recalculará** las filas existentes en project\_labor\_logs, garantizando que el balance real acumulado de la obra no sufra alteraciones retroactivas.

### **HU-25C: Anulación Controlada de Logs de Trabajo (Audit Trail)**

**Como:** Administrador y Dueño de la Empresa

**Quiero:** Anular de forma lógica un registro incorrecto de horas reales de trabajo, registrando el motivo y el usuario responsable

**Para:** Corregir errores humanos de digitación sin violar la regla de inmutabilidad extrema (cero eliminación física).

#### **Criterios de Aceptación Comerciales:**

* **CA-25C.1 (Cero Borrados de Jornadas Reales):** En cumplimiento de la **RN-06**, el recurso de Filament `ProjectLaborLogResource` tendrá deshabilitada la eliminación física de registros. Las correcciones de horas se realizarán mediante un botón de acción llamado *"Anular Log"*.  
* **CA-25C.2 (Formulario de Anulación y Auditoría):** Al presionar "Anular Log", el sistema abrirá un modal interactivo que requerirá obligatoriamente ingresar un "Motivo de la Anulación". Tras la confirmación, el sistema ejecutará de manera transaccional:  
  * Cambiar `is_annulled` a *true*.  
  * Guardar `annulled_by_user_id` $\\leftarrow$ ID del usuario autenticado.  
  * Guardar `annulled_at` $\\leftarrow$ Timestamp actual.  
  * Guardar `annulment_reason` $\\leftarrow$ Motivo de la anulación ingresado por el usuario.  
* **CA-25C.3 (Exclusión de Agregados Financieros):** El sistema excluirá de forma inmediata el subtotal de este log (*actual\_subtotal*) de cualquier suma real en el Dashboard de Conciliación y en el Balance de Caja.

### **HU-26A: Registro de Compras Reales de Materiales (Control de Desviaciones)**

**Como:** Administrador y Dueño del Negocio

**Quiero:** Registrar cada ticket o factura de compra de materiales realizada en las tiendas, indicando el costo, la tienda, el pagador y el método de pago

**Para:** Mantener una bitácora detallada de los gastos directos de insumos, cruzando opcionalmente cada compra con los conceptos que estimé en la propuesta.

#### **Criterios de Aceptación Comerciales:**

* **CA-26A.1 (Formulario de Ingesta de Facturas):** El sistema proveerá una sección interactiva para registrar compras en la tabla `project_material_purchases` con los siguientes campos:  
  * **Insumo Estimado Vinculado (Opcional):** Un selector que listará únicamente los materiales estimados de la cotización aprobada activa (*quote\_material\_items*). Si se selecciona uno, el sistema clasificará el gasto bajo la categoría **"Presupuestado"**.  
  * **Concepto:** Descripción del material. Se precargará automáticamente si se vinculó a un insumo estimado.  
  * **Detalles de la Transacción:** Tienda, Forma de Pago (Efectivo, Cheque, Tarjeta de Crédito, Transferencia Zelle) y Comprador.  
  * **Métricas de Compra:** Cantidad Real Adquirida, Costo Unitario Real y Subtotal Real (calculado como Cantidad \* Costo Unitario en modo de solo lectura).  
* **CA-26A.2 (Manejo de Gastos Excedentes):** Si se realiza una compra de un material que no estaba previsto en la cotización, se dejará el campo "Insumo Estimado Vinculado" vacío. El sistema registrará el gasto y lo clasificará de forma automática bajo la categoría **"Excedente / No Presupuestado"**.

### **HU-26B: Anulación Controlada de Compras de Materiales (Audit Trail)**

**Como:** Administrador Financiero

**Quiero:** Anular de forma lógica una compra de materiales errónea para que deje de computar en los costos reales del proyecto, manteniendo el registro físico con su respectiva justificación

**Para:** Mantener una contabilidad exacta y transparente, asegurando que todos los registros anulados tengan una explicación auditable en el sistema.

#### **Criterios de Aceptación Comerciales:**

* **CA-26B.1 (Bloqueo de DELETE físico):** En cumplimiento de la **RN-06**, el recurso de Filament `ProjectMaterialPurchaseResource` no contará con botones de eliminación física.  
* **CA-26B.2 (Modal de Anulación de Compra):** La interfaz proveerá una acción de *"Anular Compra"*. Al activarse, solicitará obligatoriamente un "Motivo de la Anulación". Al confirmar, se actualizarán los campos:  
  * `is_annulled` a *true*.  
  * `annulled_by_user_id` $\\leftarrow$ ID del usuario autenticado.  
  * `annulled_at` $\\leftarrow$ Timestamp actual.  
  * `annulment_reason` $\\leftarrow$ Motivo de la anulación ingresado por el usuario.  
* **CA-26B.3 (Recálculo Automático):** El costo de la compra anulada se restará inmediatamente del Costo Directo Real consolidado del Dashboard y del Balance de Caja del proyecto.

### **HU-27: Control de Anticipos y Flujo de Caja del Proyecto**

**Como:** Administrador y Dueño del Negocio

**Quiero:** Registrar cada depósito, abono o pago que el cliente me entrega a lo largo de la obra

**Para:** Monitorear en tiempo real el flujo de caja del proyecto, conociendo cuánta liquidez operativa tengo y cuánto dinero resta por cobrar de la cotización aprobada.

#### **Criterios de Aceptación Comerciales:**

* **CA-27.1 (Registro Contable de Abonos):** El sistema proveerá un formulario limpio para registrar transacciones de ingresos en la tabla project\_deposits:  
  * **Monto Recibido:** Valor decimal positivo.  
  * **Método de Pago:** Cash, Check, Credit Card, Zelle.  
  * **Fecha de Recibo:** Fecha de ingreso (received\_at).  
  * **Referencia:** Campo de texto libre opcional para registrar el número de cheque o el ID de la transferencia bancaria.  
* **CA-27.2 (Cálculo del Balance de Caja de la Obra):** El sistema calculará en tiempo real el dinero líquido disponible en la caja del proyecto aplicando la fórmula financiera:  
  $$\\text{Balance de Caja del Proyecto} \= \\sum(\\text{project\\\_deposits.amount}) \- \\left\[ \\sum(\\text{project\\\_labor\\\_logs.actual\\\_subtotal}) \+ \\sum(\\text{project\\\_material\\\_purchases.actual\\\_subtotal}) \\right\]$$  
  *Este valor se mostrará de forma prominente en el tablero del proyecto, alertándome si la caja operativa cae a números negativos (es decir, cuando estoy gastando más en nómina y materiales de lo que el cliente me ha depositado).*

### **HU-28: Panel de Conciliación "Estimado vs. Real" (La Prueba del Nueve del Negocio)**

**Como:** Administrador y Dueño del Negocio

**Quiero:** Visualizar una pantalla de control y auditoría centralizada dentro de mi proyecto

**Para:** Contrastar de forma gráfica y numérica las desviaciones de costos directos, el Overhead real absorbido y la ganancia neta obtenida contra las proyecciones originales de la cotización aprobada.

#### **Criterios de Aceptación Comerciales:**

* **CA-28.1 (Establecimiento de la Línea Base Activa \- Escenario B):** Para realizar los cálculos comparativos, el sistema identificará la **única propuesta comercial que se encuentre en estado "Aprobada"** y vinculada al proyecto (projects.id). Cualquier cotización anterior en estado "Cerrada por Enmienda" o "Cancelada" será ignorada, garantizando que la comparación se realice siempre contra el último contrato legal vigente acordado con el cliente.  
* **CA-28.2 (Métricas de Desviación Financiera):** El panel de control en Filament renderizará tarjetas de indicadores (*Widgets*) que calcularán y contrastarán en tiempo real:  
  1. **Costo Directo ($CD$):**  
     * **Estimado ($CD\_{est}$):** El campo direct\_cost registrado en el snapshot de la cotización aprobada.  
     * **Real ($CD\_{real}$):** La suma consolidada del costo de nómina (project\_labor\_logs.actual\_subtotal) y compras (project\_material\_purchases.actual\_subtotal).  
  2. **Overhead Absorbido ($OH$):**  
     * **Estimado ($OH\_{est}$):** El campo overhead\_cost del snapshot de la cotización aprobada.  
     * **Real ($OH\_{real}$):** Las horas totales reales de esfuerzo trabajadas multiplicadas por la tasa de overhead que se congeló en el snapshot al aprobar el contrato:  
       $$OH\_{real} \= \\sum(\\text{Horas Reales de Jornadas}) \\times \\text{quotes.overhead\\\_rate\\\_applied}$$  
  3. **Utilidad Neta Real ($UN$):**  
     * **Estimada ($UN\_{est}$):** El margen bruto proyectado en dólares en el contrato:  
       $$UN\_{est} \= \\text{quotes.total\\\_price} \- (\\text{quotes.direct\\\_cost} \+ \\text{quotes.overhead\\\_cost})$$  
     * **Real ($UN\_{real}$):** La ganancia real de dinero que la empresa percibirá una vez absorbidos todos los costos directos e indirectos reales en el campo:  
       $$UN\_{real} \= \\text{quotes.total\\\_price} \- (CD\_{real} \+ OH\_{real})$$  
* **CA-28.3 (Cálculo de Desviación Atómica de Mano de Obra):** El widget de mano de obra ya no solo comparará totales por rol, sino que restará las horas reales acumuladas directamente de la asignación correspondiente:  
  1. $$\\Delta \\text{Horas} \= \\left( \\sum \\text{project\\\_labor\\\_logs.actual\\\_hours\\\_regular} \\right) \- \\text{quote\\\_labor\\\_assignments.estimated\\\_hours\\\_regular}$$

### **HU-29: Cierre Operativo de Proyectos y Bloqueo de Bitácoras**

**Como:** Dueño y Administrador de la Empresa

**Quiero:** Cambiar el estado de un proyecto a "Finalizado" o "Cancelado"

**Para:** Impedir de forma automática e irreversible cualquier registro posterior de horas de mano de obra, compras de materiales o anticipos, protegiendo la veracidad de la auditoría financiera final del proyecto.

#### **Criterios de Aceptación Comerciales:**

* **CA-29.1 (Transición de Estado Manual):** El administrador podrá actualizar el estado del proyecto (`project_status_id`) de "En Ejecución" a **"Finalizado"** o **"Cancelado"** a través de Filament.  
* **CA-29.2 (Cierre Hermético de Bitácoras):** Al transicionar el proyecto a "Finalizado" o "Cancelado", el sistema bloqueará la base de datos (`PostgreSQL`) e impedirá la creación de nuevos registros en las tablas:  
  * `project_labor_logs`  
  * `project_material_purchases`  
  * `project_deposits`  
* **CA-29.3 (Bloqueo en Interfaz \- Filament):** Si un proyecto está finalizado o cancelado, los botones de "Registrar Jornada", "Registrar Compra" y "Registrar Anticipo" se ocultarán por completo de la interfaz de Filament de ese proyecto.  
* **CA-29.4 (Validación de API / Backend):** Si se intenta inyectar un gasto o anticipo a través de la API para un proyecto cerrado, el sistema responderá con un error HTTP `422 Unprocessable Content` con el mensaje: *"Acción denegada. El proyecto se encuentra Finalizado o Cancelado"*.

