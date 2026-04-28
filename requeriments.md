# Arquitectura y Guía de Desarrollo para CVConnectMX

**Objetivo:** Desarrollar la plataforma completa de CVConnectMX utilizando el ecosistema de Laravel (Livewire, Tailwind CSS). El sistema se divide en tres portales distintos con tecnologías específicas para cada uno.

## 1. Modificación del Flujo de Registro (Authentication)

Se requiere personalizar el registro por defecto de Laravel para soportar múltiples tipos de usuario.

- **Implementación:** Crear una vista de registro con pestañas (Tabs) o selección de tipo de cuenta: "Soy Candidato" vs "Soy Empresa".
- **Lógica de Backend:** \* Al registrar un **Candidato**, se debe crear el registro en la tabla `users` (asignando el `role_id` correspondiente) y automáticamente generar su perfil vacío en la tabla `candidates`.
    - Al registrar una **Empresa**, se crea el `user` (con su respectivo `role_id`) y su perfil en la tabla `companies`.
- **Redirección:** Dependiendo del rol del usuario autenticado, el sistema debe redirigirlo a su portal correspondiente post-login.

---

## 2. Portal 1: Portal del Candidato

**Tecnología:** Laravel Livewire + Flux UI + Tailwind CSS.
**Propósito:** Interfaz de usuario final orientada a la experiencia del aplicante. Debe sentirse como una aplicación web moderna (B2C).

**Módulos a desarrollar:**

- **Dashboard del Candidato:** Resumen de sus postulaciones recientes (`applications`) y sugerencias de vacantes.
- **Gestión de Perfil (Mi CV):** Vistas construidas con componentes de Flux UI para gestionar:
    - Información personal (`candidates`).
    - Experiencia Laboral (`work_experiencies`).
    - Educación (`educations`).
    - Habilidades (`skills`).
- **Buscador de Vacantes:** Interfaz para explorar la tabla `vacancies` (con filtros) y aplicar a las ofertas.
- **Mis Postulaciones:** Historial y estado de las postulaciones realizadas.

---

## 3. Portal 2: Portal de Empresas (Reclutadores)

**Tecnología:** FilamentPHP (Panel de Empresa).
**Propósito:** Panel administrativo ágil para que las empresas gestionen su reclutamiento (B2B).

**Módulos a desarrollar (Filament Resources):**

- **Perfil de Empresa:** Gestión de la información de la empresa (`companies`).
- **Gestión de Vacantes (`VacancyResource`):** CRUD completo para la tabla `vacancies` (crear, publicar, cerrar ofertas).
- **Gestión de Postulaciones (`ApplicationResource`):** \* Kanban board o tabla para revisar a los candidatos que aplicaron a sus vacantes.
    - Poder cambiar el estado de la aplicación (`status`) y agregar notas internas (`internal_notes`).
- **Visualización de CVs:** Interfaz para ver los perfiles de los candidatos. **Importante:** Cada vez que una empresa vea un CV, se debe registrar la acción en la tabla `cv_accesses`.

---

## 4. Portal 3: Portal de Super Administración (System Admin)

**Tecnología:** FilamentPHP (Panel Admin).
**Propósito:** Control total del sistema, seguridad, roles y auditoría.

**Módulos a desarrollar (Filament Resources):**

- **Gestión de Usuarios y Accesos:**
    - `UserResource`, `RoleResource`, `PermissionResource`.
- **Directorio General:**
    - Visualización (Read-Only o con permisos de edición superior) de todos los `candidates` y `companies` registrados.
- **Panel de Logs y Auditoría (Requerimiento Crítico):**
    - `AuditLogResource`: Interfaz para leer la tabla `audit_logs` (cambios en entidades).
    - `BackupLogResource`: Monitoreo del estado de los respaldos (`backup_logs`).
    - `LoginAttemptResource`: Visualización de los intentos de acceso (`login_attempts`), destacando los fallidos para detectar anomalías.
- **Gestión de Incidentes y Seguridad:**
    - `IncidentResource`: Panel para gestionar y resolver incidencias (`incidents` e `incident_actions`).
    - `SystemAlertResource`: Visualización de la tabla `system_alerts`.
- **Capacitación Interna:** Gestión de `trainings` y `user_trainings`.

## Instrucciones de Ejecución para Antigravity:

1.  Generar los Modelos de Eloquent asegurando que todas las relaciones (`hasMany`, `belongsTo`, `hasOne`) estén correctamente mapeadas según el archivo `.sql` proporcionado.
2.  Adecuar los Providers de Filament para crear dos paneles separados (Ej: `CompanyPanelProvider` y `AdminPanelProvider`) con paths distintos (ej. `/company` y `/admin`).
3.  Asegurar que los controladores/componentes de Flux UI para el candidato estén protegidos por middleware validando el rol de candidato.
4.  Implementar los Observers necesarios para alimentar automáticamente la tabla `audit_logs` cuando ocurran eventos importantes (creación, edición, borrado) en modelos clave.
