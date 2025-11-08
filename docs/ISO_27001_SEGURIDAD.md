# Declaración de Controles de Seguridad - ISO/IEC 27001
## MASSHA'S INVENTORY - Sistema de Gestión de Activos

### 1. Introducción
Este documento detalla los controles técnicos implementados en el sistema para garantizar la confidencialidad, integridad y disponibilidad de la información, alineados con el estándar ISO 27001.

### 2. Controles de Acceso (Dominio A.9)
* **Gestión de Usuarios:** Cada usuario tiene una cuenta única e intransferible.
* **Autenticación Robusta:** Las contraseñas se almacenan utilizando el algoritmo de hashing **Bcrypt** (estándar de la industria), garantizando que ni siquiera los administradores pueden verlas en texto plano.
* **Control de Acceso Basado en Roles (RBAC):** Se han implementado 4 niveles de acceso diferenciados para cumplir con el principio de "menor privilegio":
    * `admin`: Control total del sistema.
    * `produccion`: Acceso limitado a operaciones diarias (solicitudes).
    * `ventas`: Acceso de solo lectura a inventario.
    * `gerencia`: Acceso a reportes estratégicos.

### 3. Criptografía (Dominio A.10)
* **Protección de Credenciales:** Uso mandatorio de hashing para todas las credenciales de acceso.
* **Tokens de Sesión:** Gestión segura de sesiones mediante cookies encriptadas y tokens CSRF para evitar secuestro de sesiones.

### 4. Seguridad en las Operaciones (Dominio A.12)
* **Protección contra Malware y Ataques Web:**
    * **Protección CSRF:** Todos los formularios incluyen tokens de validación para prevenir ataques de falsificación de solicitudes entre sitios.
    * **Validación de Entradas:** Se sanean y validan estrictamente todos los datos ingresados por los usuarios (ej. cantidades numéricas, fechas válidas) para prevenir inyección de código.
* **Respaldo de Información:** La arquitectura dockerizada facilita la creación de snapshots y backups completos de la base de datos.

### 5. Seguridad en las Comunicaciones (Dominio A.13)
* **Segregación de Redes (Docker):** En el entorno dockerizado, la base de datos se encuentra en una red interna aislada, no accesible directamente desde internet pública, solo a través de la aplicación.

---
*Documento generado como evidencia de cumplimiento normativo para auditoría académica.*