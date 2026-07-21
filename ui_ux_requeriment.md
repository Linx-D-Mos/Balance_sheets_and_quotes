# **Documento de Especificación de UX/UI y Diseño de Interacción (Definitivo \- MVP v1.1)**

**Proyecto:** Sistema de Simulación Financiera, Cotización Dinámica y Control de Márgenes Operativos

**Ecosistema Base:** Filament v4 \+ Livewire \+ Alpine.js \+ Tailwind CSS (Laravel 13 \+ PostgreSQL)

**Target Adaptativo:** Dispositivos Multi-pantalla (Desktop, Tablets de Campo y Plegables en Widescreen 2316 × 904 px y Canvas Cuadrado 2176 × 1812 px)

## **1\. Arquitectura de Navegación y Mapa de Pantallas**

### **1.1 Inventario Maestro de Interfaces**

El sistema consolida el flujo operativo a través de un catálogo optimizado de interfaces estructuradas jerárquicamente para garantizar una navegación sin flujos huérfanos:

* **Interfaces de Soporte e Índices de Acceso:**  
  * **S1: Catálogo Maestro y Buscador de Clientes:** Resource de acceso para gestionar las cuentas comerciales de la compañía.  
  * **S2: Índice General de Contenedores de Proyectos:** Vista de control para buscar, filtrar y seleccionar los proyectos activos.  
* **Interfaces Prioritarias del Núcleo Operativo (Core MVP):**  
  * **P1: Dashboard de Conciliación e Ingesta de Datos (Pantalla Principal):** Panel analítico de control de desvíos económicos en vivo y centro de operaciones rápidas de campo.  
  * **P2: Motor de Cotización Dinámica, Enmiendas y Acciones Comerciales:** Formulario estructurado para simulaciones de costos, acciones comerciales (clonación, cancelación) e inyección de snapshots.  
  * **P3: Bitácoras de Ejecución de Campo (Log Hub):** Centro operativo de registro masivo de jornadas, tickets de materiales y control de anticipos.  
  * **P4: Panel de Control de Parámetros Globales (Back-Office):** Módulo de parametrización de costos fijos, salarios base y tasa corporativa de Overhead.  
  * **P5: Gestor de Contenedores de Proyectos y Clientes:** Expediente maestro del proyecto que despliega la genealogía y el árbol jerárquico de enmiendas contractualmente aprobadas.

### **1.2 Glosario de Consistencia Terminológica**

Para erradicar variaciones conceptuales entre interfaces, se aplica la siguiente nomenclatura técnica inalterable en todas las pantallas y exportables:

| Término Oficial de Interfaz | Símbolo Técnico | Definición Conceptual para el Usuario |
| :---- | :---- | :---- |
| **Costo Directo Estimado** | $CD\_{est}$ | Suma proyectada de mano de obra y materiales en la cotización activa. |
| **Costo Directo Real** | $CD\_{real}$ | Suma acumulada de las jornadas de trabajo y compras de materiales válidas en el campo. |
| **Overhead Absorbido Estimado** | $OH\_{est}$ | Costo indirecto proyectado (Horas Estimadas $\\times$ Tasa de Overhead aplicada). |
| **Overhead Absorbido Real** | $OH\_{real}$ | Costo indirecto real (Horas Reales Válidas $\\times$ Tasa de Overhead congelada). |
| **Costo de Equilibrio** | $CE$ | Punto de balance (*Breakeven*) requerido para cubrir costos directos e indirectos ($CD \+ OH$). |
| **Precio Final de Venta** | $PV$ | Monto total definitivo facturado y cobrado al cliente final. |
| **Utilidad Neta Real** | $UN\_{real}$ | Beneficio económico líquido retenido por la empresa ($PV \- (CD\_{real} \+ OH\_{real})$). |
| **Balance de Caja de la Obra** | \- | Liquidez operativa disponible en el proyecto (Anticipos Recibidos \- Gastos Reales Totales). |

## **2\. Planos de Interacción y Diseño de Interfaces (Blueprints Adaptativos)**

### **2.1 Dashboard de Conciliación e Ingesta de Datos (P1)**

#### **A. Estructura de Layout y Componentes UI**

Diseñado bajo una cuadrícula (*Grid*) de **12 columnas** de Tailwind CSS optimizada para alta densidad informativa.

* **Bloque de Cabecera (Ancho Completo \- 12 Columnas):** Despliega el nombre del proyecto (projects.title) acompañado por un *Badge* cromático de estado. Incluye un cintillo informativo superior que indica la Línea Base Activa (Código de cotización aprobada, nivel de enmienda y enlace de auditoría en modo solo lectura).  
* **Fila de Indicadores Clave (Stats Overview \- 4 Tarjetas en Grid):**  
  * *Balance de Caja de la Obra:* Cifra destacada de liquidez operativa.  
  * *Costo Directo ($CD$):* Doble lectura que expone $CD\_{real}$ en tipografía principal y $CD\_{est}$ como subtexto atenuado.  
  * *Overhead Absorbido ($OH$):* Indicador en tiempo real que contrasta $OH\_{real}$ frente a $OH\_{est}$.  
  * *Utilidad Neta Real ($UN\_{real}$):* Proyección de ganancia neta reajustada en caliente.  
* **Cuerpo Principal Dividido (Layout Asimétrico 8 / 4 Columnas):**  
  * *Zona Izquierda (8 Columnas):* Pestañas de desvíos. Pestaña 1: Mano de Obra Real por rol técnico. Pestaña 2: Materiales (discriminando insumos presupuestados de compras excedentes no presupuestadas).  
  * *Zona Derecha (4 Columnas \- Quick Action Hub):* Panel vertical fijo con botones prominentes para invocar modales de inserción rápida: *"Registrar Jornada"*, *"Registrar Compra"* y *"Registrar Anticipo"*.

**Adaptación Responsive por Dispositivo (Target Breakpoints):**

* **Panorámica Comprimida (2316 × 904 px \- Widescreen Corto):** Alinear las 4 tarjetas de métricas en una fila única horizontal (3 columnas cada una). Las pestañas izquierdas y el *Quick Action Hub* derecho fijan alturas máximas con desplazamiento vertical independiente (max-h-\[50vh\] overflow-y-auto), evitando scroll general en pantallas cortas.  
* **Cuadrada de Alta Densidad (2176 × 1812 px \- Canvas Cuadrado Largo):** Reorganiza las 4 tarjetas superiores en una matriz compacta de 2 × 2\. Elimina restricciones de altura en el cuerpo principal, permitiendo que las tablas de desvíos expongan hasta 20 filas sin paginación inmediata.

#### **B. Comportamiento Dinámico y Reactividad**

* **Refresco Asíncrono de Métricas:** Al guardar cualquier modal del *Quick Action Hub*, el Dashboard intercepta el evento de éxito y recalcula los totales en el servidor en menos de 50 ms, actualizando las tarjetas superiores sin recargar la página.  
* **Semáforo Financiero:** Si los gastos superan los depósitos, *Balance de Caja* cambia a fondo rojo suave con texto carmesí. Si $UN\_{real}$ cae más de un 5% por debajo del margen contratado, la tarjeta de utilidad se tiñe de rojo de seguridad.  
* **Bloqueo por Cierre de Obra:** En estado "Finalizado" o "Cancelado", los botones de ingesta rápida se ocultan automáticamente.

#### **C. Auditoría de UX Smells y Ajustes de Requerimientos**

* **UX Smell 1: Estado vacío en proyectos nuevos (sin línea base aprobada).**  
  * *Ajuste UX (Empty State Uniforme):* En proyectos en "Borrador", se ocultan las métricas y se renderiza un contenedor neutro con el texto: *"Proyecto en Borrador: Línea Base No Configurada. Las métricas de conciliación se habilitarán automáticamente al aprobar la primera cotización comercial"*, incorporando un botón hacia el Motor de Cotizaciones.  
* **UX Smell 2: Registros anulados invisibles o desorientadores.**  
  * *Ajuste UX (Popover de Auditoría Trail):* La tabla incluye un toggle *"Mostrar registros anulados"*. Las filas anuladas se muestran tachadas con opacidad reducida e integran un icono de escudo informativo. Al pasar el cursor o presionar el icono, un popover muestra explícitamente: usuario responsable (annulled\_by\_user\_id), fecha/hora exacta (annulled\_at) y motivo textual de la anulación (annulment\_reason).  
* **UX Smell 3: Ocultamiento de botones en formularios modales largos.**  
  * *Ajuste UX (Sticky Modal Footer):* Los modales de ingesta rápida fijan su pie de contenedor (Sticky Footer) con los botones "Guardar" y "Cancelar", garantizando visibilidad permanente en visores de 904 px de altura.

### **2.2 Motor de Cotización Dinámica, Enmiendas y Acciones Comerciales (P2)**

#### **A. Estructura de Layout y Componentes UI (Planos Adaptativos)**

Formulario multipestaña de navegación asistida acoplado a un panel de simulación financiera.

* **Pestaña 1: Configuración General:** Selectores de Cliente y Proyecto (con modal de creación rápida), fechas (start\_date, end\_date), toggle de fines de semana (work\_weekends) e input de margen (margin\_applied).  
* **Pestaña 2: Estimación de Mano de Obra:** Agregador dinámico (*Repeater*) por filas. Selector de Rol (labor\_role\_id), Empleado opcional (employee\_id), marcador libre (worker\_name\_placeholder), y horas regulares/extras estimadas.  
* **Pestaña 3: Estimación de Materiales:** Agregador libre (*Repeater*) con Concepto (texto libre), Cantidad Estimada y Costo Unitario Estimado (decimal libre y editable para cualquier rol con permisos de creación).  
* **Bloque de Totales y Simulación Financiera:** Muestra en vivo: Horas Comprometidas (total\_hours), Costo Directo ($CD$), Overhead Absorbido ($OH$), Costo de Equilibrio ($CE$) y Precio Final de Venta ($PV$).  
* **Botonera de Acciones Comerciales (Cabecera Superior):**  
  * *Clonar Cotización (HU-19):* Habilitado en Borrador, Rechazada o Cancelada. Duplicate la propuesta a un nuevo borrador, resetea snapshots y aplica en caliente las tarifas $C\_{ch}$ y $T\_{oh}$ activas hoy.  
  * *Cancelar Cotización (CA-14.1):* Habilitado en Borrador o Enviada. Abre un modal de confirmación y conmuta el estado a "Cancelada", pasando la interfaz a modo solo lectura.  
  * *Generar Enmienda (HU-20):* Habilitado en cotizaciones Aprobadas con amendment\_level \< 2.  
  * *Exportar PDF (HU-24A/B/C):* Abre el modal de selección de variantes de documento.

**Adaptación Responsive por Dispositivo:**

* **Panorámica Comprimida (2316 × 904 px):** El layout se colapsa a una columna. Los repeaters apilan sus campos verticalmente y el panel de totales muta hacia una barra inferior fija (*Sticky Bottom Bar*) que expone $PV$, $CD$ y horas acumuladas.  
* **Cuadrada de Alta Densidad (2176 × 1812 px):** Layout asimétrico de **8 / 4 columnas**. Las 8 columnas izquierdas alojan las pestañas y las 4 columnas derechas sostienen un panel lateral pegajoso (*Sticky Sidebar*) que acompaña el scroll.

#### **B. Comportamiento Dinámico y Reactividad**

* **Navegación Asistida Condicional:** Pestañas 2 y 3 permanecen deshabilitadas hasta seleccionar un Proyecto válido en la Pestaña 1\.  
* **Cómputo en Caliente:** Al modificar horas o materiales, se recalculan instantáneamente los subtotales de fila y los indicadores $CD$, $OH$, $CE$ y $PV$ sin latencias perceptibles.  
* **Alerta de Quiebra de Margen:** Si el margen configurado genera un $PV$ con utilidad menor al 10% sobre $CE$, el bloque de totales se tiñe de carmesí y despliega un texto de advertencia.

#### **C. Auditoría de UX Smells y Ajustes de Requerimientos**

* **UX Smell 1: Pérdida de progreso por cierres accidentales o caídas de red.**  
  * *Ajuste UX (Auto-guardado Silencioso):* Los cambios en repeaters se persisten automáticamente en el almacenamiento local del navegador. Al reabrir tras un corte, se ofrece restaurar la sesión.  
* **UX Smell 2: Desorientación en validaciones fallidas en pestañas ocultas.**  
  * *Ajuste UX (Checklist de Progreso e Indicador de Error):* Las pestañas muestran un icono verde al completarse. Si la validación falla al guardar, se inyecta un badge rojo con el número de errores en la pestaña afectada y el foco se desplaza automáticamente al primer campo inválido.  
* **UX Smell 3: Exposición de secretos comerciales en reuniones con clientes.**  
  * *Ajuste UX (Modo Cliente \- Enmascaramiento de Costos):* Botón flotante *"Modo Cliente"* que aplica un desenfoque (*blur*) visual sobre salarios base, $C\_{ch}$ y $T\_{oh}$, mostrando únicamente conceptos, cantidades y $PV$.

### **2.3 Bitácora de Ejecución de Campo \- Log Hub (P3)**

#### **A. Estructura de Layout y Componentes UI**

Vista de administración tabular masiva contextualizada en el proyecto seleccionado.

* **Barra Superior de Telemetría:** Expone *Total Recibido*, *Total Gastado* y *Balance de Caja de la Obra*.  
* **Tablas Segmentadas en 3 Pestañas:**  
  1. *Pestaña 1 (Jornadas de Trabajo):* Log de project\_labor\_logs (Fecha, Empleado, Rol, Horas Reg/Extra, Tarifa Real, Subtotal).  
  2. *Pestaña 2 (Compras de Materiales):* Log de project\_material\_purchases (Fecha, Concepto, Categoría: Presupuestado/Excedente, Tienda, Pago, Comprador, Cantidad, Precio Unitario, Subtotal).  
  3. *Pestaña 3 (Anticipos y Depósitos):* Log de project\_deposits (Fecha, Monto, Método, Referencia).

**Adaptación Responsive por Dispositivo:**

* **Panorámica Comprimida (2316 × 904 px):** Reduce el padding de celdas (py-1). La columna de acciones de auditoría (Anular Log) se fija a la derecha (*Fixed Right Column*) para no perderse en scroll lateral.  
* **Cuadrada de Alta Densidad (2176 × 1812 px):** Encabezados pegajosos (*Sticky Header*) y tabla expandida para exponer hasta 30 registros sin paginación.

#### **B. Comportamiento Dinámico y Reactividad**

* **Clasificación Automática de Insumos:** Seleccionar un material estimado clasifica la compra como "Presupuestado". Dejarlo vacío activa un badge naranja de "Excedente / No Presupuestado".  
* **Pre-carga de Costo por Hora:** Seleccionar un rol pre-carga su $C\_{ch}$ en el input de tarifa real, manteniendo su edición abierta para ajustes de campo.

#### **C. Auditoría de UX Smells y Ajustes de Requerimientos**

* **UX Smell 1: Justificaciones vacías en anulaciones.**  
  * *Ajuste UX (Validación de Logitud Mínima):* El botón del modal de anulación permanece bloqueado hasta que annulment\_reason sume al menos 15 caracteres válidos.  
* **UX Smell 2: Datos de anulación invisibles en la tabla.**  
  * *Ajuste UX (Popover de Audit Trail):* Las filas con is\_annulled \= true se muestran atenuadas y tachadas. Incluyen un icono de escudo que al ser presionado despliega el Popover con usuario, fecha/hora y motivo de anulación.  
* **UX Smell 3: Trazabilidad comparativa de mano de obra inaccesible.**  
  * *Ajuste UX (Micro-lectura Comparativa de Tarifa):* Gracias a la clave foránea quote\_labor\_assignment\_id, si la tarifa real pagada supera el snapshot congelado (hourly\_rate\_at\_estimation), se muestra bajo el monto una flecha roja con el valor original (ej: *"▲ $25.00 \[Presupuesto: $23.00\]"*).  
* **UX Smell 4: Pestañas vacías sin guía.**  
  * *Ajuste UX (Empty States Tabulares):* Mensajes contextuales con bordes punteados guían al usuario para efectuar la primera ingesta.

### **2.4 Panel de Control de Parámetros Globales \- Back-Office (P4)**

#### **A. Estructura de Layout y Componentes UI**

Organizado en un *Grid* de **12 columnas** divididas en módulos.

* **Tarjetas Superiores:** *Overhead Mensual Activo*, *Capacidad Laboral Promedio* y *Tasa de Overhead Resultante ($T\_{oh}$)*.  
* **Cuerpo (6 / 6 Columnas):**  
  * *Izquierda:* Tabla de Gastos Fijos (fixed\_expenses) solo con toggles de activación (cero DELETE) y Roster de Personal (employees).  
  * *Derecha:* Matriz de Roles (labor\_roles) con cálculo de $C\_{ch}$ y Formulario de Multiplicadores (global\_settings).

**Adaptación Responsive por Dispositivo:**

* **Panorámica Comprimida (2316 × 904 px):** Layout en 3 bloques horizontales (4 / 4 / 4\) con desplazamiento interno independiente por tarjeta (max-h-\[50vh\] overflow-y-auto).  
* **Cuadrada de Alta Densidad (2176 × 1812 px):** Layout de 2 grandes columnas (6 / 6\) sin restricciones de scroll.

#### **B. Comportamiento Dinámico y Reactividad**

* **Cómputo en Caliente de $C\_{ch}$ y $T\_{oh}$:** Modificar salarios o cargas sociales actualiza de inmediato $C\_{ch}$. Encender/apagar gastos fijos recalcula instantáneamente $T\_{oh}$ en las tarjetas superiores.  
* **Filtro de Teclado Local:** Descarta caracteres alfabéticos o signos negativos en inputs financieros.

#### **C. Auditoría de UX Smells y Ajustes de Requerimientos**

* **UX Smell 1: Opacidad en el cálculo de horas hábiles (Yasumi).**  
  * *Ajuste UX (Flyout Popover de Feriados USA):* Un icono de calendario junto a la Capacidad Laboral despliega un panel flotante que detalla los feriados oficiales de EE. UU. restados offline para el año en curso (ej: *Memorial Day, Juneteenth, Thanksgiving*).  
* **UX Smell 2: Temor a alterar la contabilidad histórica.**  
  * *Ajuste UX (Helper Text de Seguridad):* Un aviso permanente aclara que los cambios en este panel solo aplican a nuevos borradores, manteniendo inmutables los snapshots de cotizaciones aprobadas.  
* **UX Smell 3: Catálogo inicial desierto.**  
  * *Ajuste UX (Empty State Instructivo):* Si no hay gastos o roles, una alerta superior instruye sobre los pasos mínimos para inicializar la tasa $T\_{oh}$.

### **2.5 Gestor de Contenedores de Proyectos y Clientes (P5)**

#### **A. Estructura de Layout y Componentes UI**

Expediente maestro del proyecto organizado en **4 / 8 columnas**.

* **Cabecera:** Breadcrumbs (Clientes $\\rightarrow$ Proyecto) y acciones para "Finalizar" o "Cancelar" obra.  
* **Zona Izquierda (4 Columnas):** Ficha de contacto del Cliente (clients), Badge de estado de obra y widget del *Balance de Caja de la Obra*.  
* **Zona Derecha (8 Columnas):** Matriz tabular de la genealogía de cotizaciones (quotes).

**Adaptación Responsive por Dispositivo:**

* **Panorámica Comprimida (2316 × 904 px):** Layout de 3 / 9 columnas con densidad tabular alta.  
* **Cuadrada de Alta Densidad (2176 × 1812 px):** Layout de 4 / 8 columnas con micro-indicadores financieros ($CD$, $OH$, Utilidad) embebidos en cada fila de la tabla.

#### **B. Comportamiento Dinámico y Reactividad**

* **Árbol Jerárquico de Enmiendas:** Las cotizaciones con parent\_quote\_id aplican sangría visual y muestran el prefijo gráfico └─ Enmienda X.  
* **Sustitución de Baseline:** Aprobar una enmienda marca la nueva fila con borde verde y el badge "LÍNEA BASE ACTIVA", pasando la anterior a "Cerrada por Enmienda".

#### **C. Auditoría de UX Smells y Ajustes de Requerimientos**

* **UX Smell 1: Clientes o proyectos nuevos sin historial.**  
  * *Ajuste UX (Empty State de Expediente):* Si no hay proyectos o cotizaciones, se despliegan ilustraciones atenuadas con instrucciones para crear el primer registro.  
* **UX Smell 2: Riesgo de editar cotizaciones inactivas.**  
  * *Ajuste UX (Botonera Dinámica por Estado):* Cotizaciones en "Borrador" muestran el icono de lápiz ("Editar"); registros "Aprobados", "Enviados" o "Cerrados" muestran un icono de ojo ("Ver Detalles / Solo Lectura").

## **2.6 Catálogo Maestro y Gestor de Clientes (ClientResource \- S1)**

### **A. Estructura de Layout y Componentes UI**

Se implementa mediante un Resource nativo de Filament v4 (Tables\\Table) optimizado para la gestión rápida de las cuentas comerciales de la empresa.

* **Header de Gestión:** Título *"Directorio Maestro de Clientes"* con botón de acción primario \+ Nuevo Cliente que abre un formulario modal / slide-over de alta rápida.  
* **Matriz Tabular de Clientes:**  
  * **Nombre / Razón Social (name):** Columna principal en negrita con buscador integrado.  
  * **Correo Electrónico (email):** Columna de texto con botón interactivo de copia al portapapeles.  
  * **Teléfono (phone):** Columna con formato telefónico estandarizado.  
  * **Proyectos Asociados:** Insignia (*Badge*) numérica interactiva que indica la cantidad de obras vinculadas (ej. 3 Proyectos).  
  * **Acciones por Fila:** Botón de lápiz (Editar Cliente) y botón contextual \+ Crear Proyecto.

### **B. Comportamiento Dinámico y Reactividad**

* **Modal de Alta en Caliente:** El subformulario solicita únicamente name (obligatorio), email (opcional/único) y phone (opcional). Al guardar, el registro se inyecta asíncronamente en la tabla sin recargar la página.  
* **Disparo Directo de Obras:** Accionar \+ Crear Proyecto desde la fila de un cliente abre de forma inmediata el modal de inicialización de contenedor (projects), pre-seleccionando al cliente de forma inalterable para acelerar el flujo operativo.

### **C. Auditoría de UX Smells y Ajustes de Requerimientos**

* **UX Smell 1: Navegación fraccionada para iniciar una cotización.** Si el administrador registra un cliente nuevo, se ve obligado a cerrar la pantalla, ir al menú de Proyectos, crear el proyecto y luego ir al Motor de Cotizaciones, acumulando clics innecesarios.  
  * *Ajuste UX (Acción en Cascada):* Al guardar con éxito un nuevo cliente en el modal, el Toast de notificación ofrecerá un botón secundario de acción inmediata: *"¿Desea crear el primer proyecto para este cliente ahora?"*, transportando al usuario al modal de obras en un solo clic.

### 2.7 Índice General de Contenedores de Proyectos (ProjectResource \- S2)

### **A. Estructura de Layout y Componentes UI**

Vista de administración global de obras configurada con pestañas de filtrado por estado (Tables\\Concerns\\WithTableTabs).

* **Segmentación por Estado (project\_statuses):** Pestañas superiores que conmutan la vista entre: *Todos*, *Borrador*, *En Ejecución*, *Finalizados* y *Cancelados*.  
* **Matriz Tabular de Proyectos:**  
  * **Título del Proyecto (title):** Nombre comercial de la obra con buscador integrado.  
  * **Cliente (client\_id):** Enlace directo a la ficha del cliente en el catálogo maestro.  
  * **Línea Base Activa:** Muestra el código de la cotización aprobada (ej. COT-2026-004) o la etiqueta *"Sin Cotización Aprobada"* en proyectos en Borrador.  
  * **Estado Operativo:** *Badge* cromático unificado (Verde: *En Ejecución*, Ámbar: *Borrador*, Gris: *Finalizado*, Rojo: *Cancelado*).  
  * **Acción Principal Dinámica:** Cambia según el estado del proyecto.

### **B. Comportamiento Dinámico y Reactividad**

* **Enrutamiento Inteligente por Estado:** Si el usuario hace clic en un proyecto en estado *Borrador*, el sistema lo redirige al **Motor de Cotización (P2)** para formular o aprobar la propuesta base. Si el proyecto está *En Ejecución*, la acción principal lo dirige al **Expediente del Proyecto (P5)** o al **Dashboard (P1)**.

### **C. Auditoría de UX Smells y Ajustes de Requerimientos**

* **UX Smell 1: Proyectos en Borrador sin salida clara.** Iniciar un proyecto sin cotización deja el contenedor en un estado flotante que puede confundir al estimador.  
  * *Ajuste UX (Gatillo de Cotización Directo):* Para todo proyecto en estado *Borrador*, la celda de acciones mostrará de forma prominente un botón azul etiquetado como Formular Cotización, reduciendo la fricción para fijar la línea base.

## **2.8 Catálogo de Roles de Trabajo y Tarifa Técnica (LaborRoleResource)**

### **A. Estructura de Layout y Componentes UI**

Interfaz de gestión de puestos operativos (labor\_roles) configurada mediante el patrón de administración de registros de Filament (ManageRecords).

* **Tabla de Densidad de Tarifas:**  
  * **Nombre del Rol (name):** Título único de la posición (ej. *Pintor Principal*, *Instalador Flooring*).  
  * **Salario Base por Hora (base\_salary):** Campo numérico en formato de moneda ($).  
  * **Carga Social / Impuestos (social\_load\_pct):** Porcentaje de cargas patronales (ej. 15.00%).  
  * **Costo Cargado Calculado ($C\_{ch}$):** Campo destacado en fondo azul/índigo con tipografía monoespaciada (hourly\_cost) que expone el costo real por hora para la empresa.  
  * **Disponibilidad (is\_active):** Interruptor visual (*Toggle*) de activación lógica.

### **B. Comportamiento Dinámico y Reactividad**

* **Calculadora Reactiva de $C\_{ch}$ en Formulario (HU-02 / RN-07):** Al crear o editar un rol en el modal, la modificación del *Salario Base* o de la *Carga Social* ejecuta en vivo la ecuación $C\_{ch} \= \\text{Salario Base} \\times (1 \+ \\frac{\\text{\\% Carga Social}}{100})$, actualizando el campo bloqueado de $C\_{ch}$ en tiempo real sin recargar la pantalla.  
* **Gatillo de Evento al Guardar:** Guardar un rol actualiza la tarifa base para **futuros borradores**, pero preserva inmutables los snapshots (hourly\_rate\_at\_estimation) de las cotizaciones ya aprobadas (**RNF-3**).

### **C. Auditoría de UX Smells y Ajustes de Requerimientos**

* **UX Smell 1: Temor a corromper cotizaciones pasadas al actualizar sueldos.**  
  * *Ajuste UX (Helper Text de Inmutabilidad):* El modal de edición de roles incluye un texto permanente: *"Nota de seguridad: Cambiar el salario o la carga social recalculará la tarifa $C\_{ch}$ para nuevas cotizaciones y bitácoras. Los presupuestos aprobados no sufrirán modificaciones."*

## **2.9 Catálogo de Gastos Fijos y Absorción (FixedExpenseResource)**

### **A. Estructura de Layout y Componentes UI**

Vista de administración de costos indirectos de la empresa (fixed\_expenses).

* **Tabla de Costos Fijos Mensuales:**  
  * **Concepto (concept):** Nombre del gasto (ej. *Renta de Bodega*, *Seguros Liability*, *ERP Software*).  
  * **Monto Mensual (amount):** Valor en dólares con formato monetario alineado.  
  * **Estado Activo (is\_active):** Interruptor (*Toggle*) para encender o apagar la absorción de este gasto.  
  * **Restricción de Interfaz:** Ausencia total de botones de borrado (DeleteAction deshabilitado) en cumplimiento de la **RN-06**.

### **B. Comportamiento Dinámico y Reactividad**

* **Recálculo Silencioso de Tasa de Overhead ($T\_{oh}$):** Alterar el monto o conmutar el estado de un gasto fijo dispara un observador en el servidor que suma los gastos activos ($\\sum \\text{amount}$ donde $\\text{is\\\_active} \= \\text{true}$), los divide entre la capacidad de horas calculada offline por Yasumi y actualiza la $T\_{oh}$ global en global\_settings automáticamente.

### **C. Auditoría de UX Smells y Ajustes de Requerimientos**

* **UX Smell 1: Filas inactivas confundidas con costos vigentes.**  
  * *Ajuste UX (Atenuación Lógica):* Apagar el *Toggle* de un gasto aplica una transición inmediata que reduce la opacidad de la fila al 50% y le añade la etiqueta *"Inactivo / No Suma a Overhead"*, manteniendo la visibilidad para auditoría sin ensuciar la lectura contable.

## **2.10 Catálogo de Personal / Roster de Operarios (EmployeeResource)**

### **A. Estructura de Layout y Componentes UI**

Gestión del listado de trabajadores de campo (employees).

* **Tabla de Roster:**  
  * **Nombre Completo (name):** Identificador único del colaborador.  
  * **Estado Operativo (is\_active):** Interruptor (*Toggle*) para definir si el trabajador está disponible para asignaciones.  
  * **Métricas de Participación:** Indicador de solo lectura con el número de jornadas registradas en el proyecto activo.

### **B. Comportamiento Dinámico y Reactividad**

* **Alta Ultra-Rápida Multirol (HU-05):** El modal de alta solicita **exclusivamente el nombre del trabajador**, sin pedir roles ni sueldos fijos, respetando la flexibilidad de asignación técnica diaria en el campo.  
* **Control de Disponibilidad (CA-05.2):** Marcar a un empleado como is\_active \= false lo remueve de forma reactiva e inmediata de los desplegables de selección del *Quick Action Hub* y de las bitácoras del *Log Hub*, previniendo asignaciones a personal dado de baja.

### **C. Auditoría de UX Smells y Ajustes de Requerimientos**

* **UX Smell 1: Incertidumbre al inactivar operarios con historia en la obra.**  
  * *Ajuste UX (Tooltip de Protección):* Al apagar el *Toggle* de un operario, un microtexto aclara: *"El trabajador dejará de aparecer en listas de selección, pero sus jornadas y horas registradas en proyectos pasados se conservarán intactas."*

## **3\. Patrones de Comportamiento Global del Ecosistema (Design System)**

### **3.1 Estrategia Unificada de Validaciones y Mensajería**

* **Toasts:** Avisos efímeros en la esquina superior derecha para operaciones exitosas (3 segundos).  
* **Inline Errors:** Texto rojo bajo el input y borde carmesí en el campo afectado.  
* **Banners Superiores:** Cintillos ámbar en la cabecera para bloqueos de reglas globales.

### **3.2 Políticas de Confirmación e Inmutabilidad Financiera**

* **Inserción Ordinaria:** Guardado directo con notificación Toast.  
* **Acciones Críticas (Doble Factor):** Modales de pantalla completa para Aprobaciones y Anulaciones. Las anulaciones exigen una justificación de mínimo 15 caracteres para habilitar el botón ejecutor.

### **3.3 Matriz Global de Permisos Visuales por Token Granular**

Evaluación directa de los tokens del AppPermission de Spatie:

| Componente / Acción de Interfaz | Token Spatie Requerido | Comportamiento con Permiso Activo | Comportamiento con Permiso Inactivo |
| :---- | :---- | :---- | :---- |
| **Panel Back-Office de Costos** | manage:settings | Menú visible; inputs editables. | Menú oculto; acceso bloqueado (403). |
| **Botón "Aprobar Cotización"** | approve:quotes | Visible en la barra superior de P2. | Oculto de la interfaz. |
| **Botón "Generar Enmienda"** | create:enmiendas | Active en propuestas aprobadas. | Deshabilitado con icono de candado. |
| **Formulario de Cotizaciones** | create:quotes | Creación y edición de borradores. | Vista restringida a modo lectura. |
| **Centro de Ingesta Rápida** | write:logs | Botones de registro masivo activos. | Ocultos de la barra lateral. |
| **Cierre Técnico de Obra** | close:projects | Botones "Finalizar/Cancelar" activos. | Ocultos de la cabecera de P5. |

### **3.4 Lenguaje Visual de Estados (Guía de Badges y Colores)**

* **Verde:** Proyectos *"En Ejecución"*, Cotizaciones *"Aprobadas"*, Materiales *"Presupuestado"*.  
* **Ámbar:** Cotizaciones *"Borrador"*, Materiales *"Excedente / No Presupuestado"*.  
* **Azul:** Cotizaciones *"Enviadas"*.  
* **Carmesí:** Proyectos/Cotizaciones *"Canceladas"*, registros is\_annulled \= true, déficit de caja.  
* **Gris:** Proyectos *"Finalizados"*, Cotizaciones *"Cerradas por Enmienda"*, Personal *"Inactivo"*.

### **3.5 Escalabilidad de Tablas de Alta Densidad**

* Paginación asíncrona fija a 10 registros en vistas incrustadas y 25 en Resources nativos.  
* Carga incremental diferida (*Lazy Loading*) en pestañas y persistencia de filtros en la sesión del usuario.

### **3.6 Estándar de Accesibilidad y Usabilidad Empresarial**

* Anillo de foco visual en navegación por teclado (focus:ring-2 focus:ring-primary-500).  
* Contraste mínimo de 4.5:1 en métricas y lecturas comparativas.  
* Iconografía semántica obligatoriamente acompañada de texto o tooltips descriptivos.

### **3.7 Blueprint del Modal de Exportación PDF con Variantes (HU-24A/B/C)**

Invocado desde la barra superior del Motor de Cotizaciones (P2) mediante la acción *"Exportar PDF"*:

* **Selector de Variante de Documento (Select):**  
  1. *Propuesta Comercial (Cliente):* Censura salarios base, $C\_{ch}$, precios unitarios de insumos y $T\_{oh}$. Expone únicamente conceptos, cantidades y el **Precio Final de Venta ($PV$)**.  
  2. *Orden de Trabajo / Lista de Campo:* Muestra conceptos, cantidades y horas de esfuerzo. Censura el 100% de los valores monetarios.  
  3. *Reporte Interno Consolidado:* Muestra la simulación financiera completa ($CD$, $OH$, $CE$, margen y $PV$) sin restricciones.  
* **Toggle "Emitir y Bloquear Propuesta" (HU-24C):** Al activarse antes de descargar, conmuta el estado de "Borrador" a "Enviada" de manera transaccional, bloqueando el formulario en modo solo lectura.  
* **Responsive Target (2316 × 904 px):** El modal fija su pie de contenedor (*Sticky Footer*) para mantener el botón "Descargar PDF" inmóvil durante el scroll.

### **3.8 Estrategia de Recuperación ante Errores y Caídas de Red**

* **Offline Guard:** La pérdida de conexión dispara un banner carmesí superior: *"Conexión perdida. Sus datos se preservan localmente. No cierre esta ventana"*, bloqueando temporalmente los botones de envío.  
* **Timeout / Error 500:** Un diálogo de recuperación notifica: *"El servidor no respondió a tiempo. Sus datos siguen intactos en el formulario"*, evitando la pérdida de información rellenada.

### **3.9 Control de Concurrencia y Datos Desactualizados**

* **Detección de Conflictos:** Si un usuario mantiene abierta una cotización o el Log Hub y otro administrador aprueba una enmienda o ingresa un gasto en segundo plano, el sistema identifica la desincronización.  
* **Banner de Concurrencia:** Se despliega una alerta naranja superior: *"La información fue modificada recientemente por otro usuario"* con un botón de *"Actualizar Vista"* para refusionar métricas sin sobrescribir data histórica.

### **3.10 Nota de Escalabilidad y Crecimiento Futuro**

* **Soporte Multi-moneda:** Los bloques analíticos reservan espacio para incluir selectores de divisas (USD, COP, EUR) en etapas posteriores.  
* **Multi-sucursal:** Los layouts de Back-Office (P4) y Gestor de Proyectos (P5) reservan la cabecera para albergar filtros por región o centro de costos.

