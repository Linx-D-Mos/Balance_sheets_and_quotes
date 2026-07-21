## **Mapa de Épicas: MVP Sistema Transaccional de Gestión de Costos y Cotizaciones**

## **ÉPICA 1: PARAMETRIZACIÓN GLOBAL DE COSTOS OPERATIVOS (BACK-OFFICE)**

**Descripción:** Como Administrador de la empresa (Super Admin), quiero contar con un panel unificado de Back-Office en Filament para gestionar los costos fijos (Overhead), definir los roles laborales con sus respectivas cargas fiscales y calcular automáticamente la capacidad laboral mensual de la compañía utilizando un calendario local offline de días festivos de EE. UU. (USA). Esto me permitirá establecer una Tasa de Overhead ($T\_{oh}$) y costos de mano de obra estables, precisos y matemáticamente auditables antes de procesar cualquier simulación o cotización comercial.

**RF-1.1 (Administración de Gastos Fijos):** El sistema debe proveer una interfaz para registrar, actualizar y suspender (desactivar) los gastos fijos mensuales de la empresa (renta, suscripciones, seguros).

**RF-1.2 (Suma Dinámica de Overhead):** El sistema debe recalcular automáticamente el costo total mensual de gastos fijos sumando únicamente los registros que se encuentren marcados con estado activo.

**RF-1.3 (Restricción de Eliminación de Gastos):** El sistema debe denegar la eliminación física de registros de gastos fijos para preservar las auditorías históricas del negocio.

**RF-1.4 (Definición Reactiva de Roles):** El sistema debe proveer un formulario para configurar el catálogo de perfiles de trabajo (pintor, carpintero, preparador) calculando automáticamente su costo cargado por hora ($C\_{ch}$) en pantalla a partir del salario base y el factor de carga social patronal.

**RF-1.5 (Cálculo Offline de Capacidad de Trabajo):** El sistema debe deducir automáticamente y sin necesidad de internet las horas hábiles laborables promedio de la empresa, restando fines de semana y feriados federales de EE. UU. (USA) del año en curso.

**RF-1.6 (Establecimiento de Tasa de Overhead \- $T\_{oh}$):** El sistema debe calcular y guardar automáticamente la Tasa de Overhead por Hora global dividiendo la suma de gastos fijos mensuales activos entre la capacidad promedio mensual de horas laborables.

**RF-1.7 (Panel de Control de Costos Operativos):** El sistema debe consolidar en una pantalla única de solo lectura los indicadores métricos globales de la tasa de overhead, horas de capacidad y total de gastos de la empresa.

**RF-1.8 (Administración Roster de Personal):** El sistema debe permitir la creación de empleados en el sistema limitándose únicamente a capturar su nombre para admitir la naturaleza multirol en el campo de trabajo.

**RF-1.9 (Control de Disponibilidad de Trabajadores):** El sistema debe permitir pausar el estado de un trabajador para excluirlo de asignaciones operativas en futuras cotizaciones o registros reales de horas.

## **Épica 2: MOTOR DE COTIZACIÓN DINÁMICA**

**Descripción:** Como Administrador de la empresa de remodelación y pintura (Super Admin), quiero un motor de cotización centralizado que me permita estructurar propuestas comerciales ágilmente mediante pestañas organizadas, estimando de forma independiente la mano de obra (con horas regulares, extras y número de empleados/cargo) y los materiales directos (pinturas, yeso, herramientas específicas). El sistema debe calcular en tiempo real el costo directo total ($CD$), la porción de Overhead que el proyecto absorberá basándose en el esfuerzo real estimado, y sugerir un precio de venta final libre de pérdidas que, una vez aprobado, congele de forma inmutable la contabilidad de la cotización para protegerla de futuras fluctuaciones de precios o salarios. 

**RF-2.1 (Vinculación Obligatoria a Proyecto):** El sistema debe impedir la creación de cualquier cotización que no esté asociada a un contenedor de proyecto válido.

**RF-2.2 (Creación Rápida de Proyectos en Caliente):** El formulario de creación de cotizaciones debe integrar un botón modal para dar de alta un proyecto y un cliente rápidamente sin salir de la pantalla.

**RF-2.3 (Historial del Contenedor de Proyectos):** El sistema debe listar cronológicamente todas las propuestas comerciales (borradores, enviadas, enmiendas) asociadas a un proyecto dentro de su vista de detalle.

**RF-2.4 (Estructura de Estimación Organizada):** El formulario de cotización debe organizar sus campos de carga de datos en tres secciones claras: Datos Generales, Mano de Obra y Materiales.

**RF-2.5 (Estimación Atómica de Mano de Obra):** El sistema debe proveer un agregador dinámico de personal en el que se puedan simular las horas regulares y extras estimadas de cada rol de trabajo, heredando el costo cargado ($C\_{ch}$) vigente del catálogo global.

**RF-2.6 (Estimación Flexible de Materiales):** El sistema debe permitir la carga libre de materiales e insumos necesarios para la obra capturando concepto de texto, cantidad estimada y costo unitario estimado.

**RF-2.7 (Simulación Financiera en Caliente):** El sistema debe actualizar automáticamente en el pie del formulario de cotización el costo directo, el overhead absorbido, el costo de equilibrio y el precio de venta sugerido utilizando la fórmula de margen de ganancia real sobre precio.

**RF-2.8 (Alerta de Seguridad en Precios):** El sistema debe alertar visualmente al administrador si introduce manualmente un margen que reduzca el precio de venta sugerido por debajo del 10% de utilidad sobre el costo de equilibrio.

**Épica 3: PERSISTENCIA HISTÓRICA, CICLO DE VIDA E INMUTABILIDAD**   
**Descripción:** Garantizar la inmutabilidad de los datos financieros aprobados o enviados y proveer un mecanismo controlado para realizar ajustes de alcance (Enmiendas) y exportación de propuestas profesionales en formato PDF, protegiendo el margen histórico de la empresa ante desviaciones u optimizaciones durante la ejecución.    
**RF-3.1 (Clonación Rápida de Propuestas):** El sistema debe permitir la duplicación de propuestas en estado Borrador, Rechazada o Cancelada a un nuevo borrador, descartando tarifas congeladas y aplicando los costos globales de mano de obra y overhead vigentes el día de hoy.  
**RF-3.2 (Gatillo de Enmiendas):** El sistema debe habilitar la acción de generar enmiendas únicamente sobre cotizaciones que se encuentren en estado "Aprobada".  
**RF-3.3 (Límite Jerárquico de Enmiendas):** El sistema debe verificar que no se creen más de dos niveles de enmiendas vinculadas a un proyecto en la base de datos (Original $\\rightarrow$ Enmienda 1 $\\rightarrow$ Enmienda 2).  
**RF-3.4 (Estructura Dinámica de Borrador de Enmienda):** Al crearse la enmienda en estado borrador, el sistema debe duplicar todo el presupuesto del padre y permitir al administrador añadir, eliminar o reajustar libremente el roster de pintores y materiales directos con tarifas vigentes hoy.  
**RF-3.5 (Sustitución de Línea Base Activa):** Al aprobarse una enmienda, el sistema debe cambiar el estado del padre a "Cerrada por Enmienda" y definir este nuevo registro como el único baseline activo del proyecto para calcular las desviaciones de gastos.  
**RF-3.6 (Pestaña de Historial de Cambios):** El sistema debe renderizar de forma visual un árbol cronológico con los montos y estados de todas las cotizaciones involucradas en el proyecto.  
**RF-3.7 (Exportación Propuesta PDF):** El sistema debe generar un documento PDF limpio y descargable de la cotización que visualice de forma consolidada el personal y materiales, ocultando estrictamente sueldos base y costos cargados internos de la empresa.  
**RF-3.8 (Transición Automática por Descarga):** Al descargarse con éxito el PDF de la cotización, el sistema debe cambiar automáticamente su estado de "Borrador" a "Enviada".

**Épica 4: Balance y Conciliación de Gastos Reales**   
**Descripción:** Proveer al administrador de un motor transaccional para registrar la mano de obra real trabajada, las compras de materiales ejecutadas y los depósitos recibidos por el cliente durante la ejecución de la obra, permitiendo contrastar de forma agregada las desviaciones de costos y evaluar la utilidad neta real obtenida.   
**RF-4.1 (Bitácora de Horas Reales por Colaborador):** El sistema debe proveer una bitácora para registrar jornadas de horas regulares y extras trabajadas de forma individual por empleado asociando el rol desempeñado en ese periodo.  
**RF-4.2 (Cálculo del Costo de Nómina Ejecutado):** El sistema debe calcular en caliente el costo de cada jornada real de trabajo basándose en las horas laboradas, el costo real negociado de la hora y el multiplicador de horas extras de la empresa.  
**RF-4.3 (Bitácora de Compras de Materiales):** El sistema debe proveer una bitácora de tickets de compra de insumos capturando concepto, costo, tienda, método de pago y comprador.  
**RF-4.4 (Control de Excedentes de Compra):** El sistema debe permitir asociar opcionalmente la compra real de un material a un insumo estimado de la cotización aprobada activa. Si se deja libre, el sistema lo catalogará automáticamente como "Gasto Excedente No Presupuestado".  
**RF-4.5 (Registro Contable de Anticipos):** El sistema debe proveer un registro para capturar los abonos de dinero entregados por el cliente a lo largo de la obra.  
**RF-4.6 (Cálculo del Balance de Caja de la Obra):** El sistema debe calcular y mostrar de forma prominente la liquidez operativa restante del proyecto, restando el total de mano de obra y materiales reales pagados al total de abonos recibidos.  
**RF-4.7 (Dashboard Analítico "Estimado vs. Real"):** El sistema debe desplegar un panel interactivo que compare los costos directos, el overhead absorbido y la utilidad neta en dólares de la cotización activa aprobada frente a la suma total de gastos reales.  
**RF-4.8 (Alertas de Pérdida de Margen):** El sistema debe alertar en color rojo si la rentabilidad real calculada cae por debajo del margen pactado originalmente en el contrato por una diferencia mayor al 5%.  
