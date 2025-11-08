# Documentación de Proceso Clave - ISO 9001
## MASSHA'S INVENTORY - Sistema de Gestión de Calidad

### Nombre del Proceso: Gestión de Solicitudes de Insumos para Eventos
**Código:** PROC-LOG-001
**Responsable:** Gerencia de Operaciones
**Objetivo:** Asegurar el abastecimiento controlado de insumos para eventos, minimizando mermas y evitando quiebres de stock mediante validaciones automáticas.

---

### 1. Actores Involucrados (Roles)
| Actor | Rol en Sistema | Responsabilidad |
| :--- | :--- | :--- |
| **Solicitante** | Producción (Cocina) | Identificar necesidades y registrar requerimientos. |
| **Aprobador** | Admin / Gerencia | Validar la pertinencia del gasto y autorizar el despacho. |
| **Sistema** | (Automático) | Validar stock en tiempo real, registrar movimientos y actualizar inventario. |

---

### 2. Descripción del Flujo de Trabajo (BPM)

#### Fase 1: Registro del Requerimiento
1.  El **Solicitante** accede al módulo "Solicitudes" y selecciona "Nueva Solicitud".
2.  Ingresa los detalles del evento (Nombre, Fecha, Observaciones).
3.  Selecciona los ítems requeridos y las cantidades necesarias.
4.  **[Punto de Control Automático]**: El Sistema verifica el stock disponible al momento del registro.
    * *Si hay stock suficiente:* Permite continuar normalmente.
    * *Si NO hay stock suficiente:* El sistema emite una alerta (Warning), pero permite guardar la solicitud para gestión de compra futura.
5.  La solicitud queda en estado **PENDIENTE**.

#### Fase 2: Revisión y Aprobación
1.  El **Aprobador** recibe la solicitud pendiente en su dashboard.
2.  Revisa los detalles y pertinencia del pedido.
3.  **Decisión de Gerencia:**
    * **A. RECHAZAR:** Se registra el motivo. El sistema cambia el estado a **RECHAZADA** y fin del proceso.
    * **B. APROBAR:** Se procede a la validación final.

#### Fase 3: Ejecución y Despacho (Automático)
1.  **[Punto de Control Crítico]**: Al hacer clic en "Aprobar", el Sistema realiza una **segunda validación de stock** en tiempo real (para evitar inconsistencias si el stock cambió desde la creación).
    * *Si el stock ya NO es suficiente:* El sistema bloquea la aprobación y muestra error.
2.  Si la validación es exitosa, el Sistema ejecuta automáticamente:
    * Generación de registros de "Salida" (Outgoing Movement) para cada ítem.
    * Descuento físico del inventario.
    * Cambio de estado de la solicitud a **APROBADA**.
    * Registro de fecha y usuario responsable de la aprobación (Trazabilidad).

---

### 3. Indicadores de Calidad (KPIs) Asociados
* % de solicitudes aprobadas con stock insuficiente (debe ser 0% gracias al bloqueo del sistema).
* Tiempo promedio entre creación y aprobación de solicitud.
* Tasa de rechazos por evento.