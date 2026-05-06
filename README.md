# CVConnectMX 🚀

**CVConnectMX** es una plataforma web moderna que conecta a **candidatos**, **agencias** y **empresas** en un ecosistema laboral integrado. Permite a los candidatos gestionar sus perfiles profesionales, a las empresas publicar ofertas de trabajo, y a las agencias intermediar entre ambas partes.

---

## 🎯 ¿Qué hace CVConnectMX?

### Para Candidatos
- ✅ **Perfil profesional completo:** Datos personales, edad, sexo, ubicación y scoring profesional
- ✅ **Gestión de experiencia:** Registro detallado de experiencia laboral con fechas y descripciones
- ✅ **Formación educativa:** Historial de estudios y certificaciones
- ✅ **Habilidades y competencias:** Lista de skills profesionales
- ✅ **Gestión de documentos:** Carga y descarga de CV, certificados y documentos importantes
- ✅ **Aplicaciones a ofertas:** Candidatos pueden postularse a las ofertas de las empresas
- ✅ **Control de privacidad:** Registro de quién accede a su CV y cuándo
- ✅ **Dashboard personal:** Vista personalizada con sus aplicaciones y perfil

### Para Empresas/Contratistas
- ✅ **Creación de ofertas de trabajo:** Publicar vacantes con requisitos y descripción del puesto
- ✅ **Banco de candidatos:** Acceso al directorio de candidatos activos
- ✅ **Vista de perfiles:** Revisar CVs y perfiles completos de candidatos registrados
- ✅ **Gestión de aplicaciones:** Panel para revisar, evaluar y cambiar estado de candidatos que aplican
- ✅ **Registro de accesos:** Historial de qué candidatos consultaron, cuándo y cuántas veces

### Para Administradores
- ✅ **Panel de control centralizado:** Gestión completa de la plataforma
- ✅ **Auditoría y registros:** Historial completo de cambios en el sistema
- ✅ **Monitoreo de seguridad:** Intentos de acceso fallidos, incidentes de seguridad y alertas del sistema
- ✅ **Gestión de usuarios:** Control de roles y permisos granulares
- ✅ **Backup y respaldos:** Monitoreo del estado de copias de seguridad
- ✅ **Gestión de capacitación:** Asignar entrenamientos a usuarios del sistema

### Seguridad y Autenticación
- ✅ **Autenticación segura:** Sistema de login/registro robusto
- ✅ **Recuperación de contraseña:** Proceso seguro de reset
- ✅ **Verificación de email:** Confirmación de identidad
- ✅ **Autenticación de dos factores (2FA):** TOTP para máxima seguridad
- ✅ **Protección contra ataques:** Bloqueo temporal tras múltiples intentos fallidos
- ✅ **Historial de contraseñas:** Prevención de reutilización
- ✅ **Registro de incidentes:** Detección y documentación de anomalías de seguridad

---

## 🏗️ Cómo funciona CVConnectMX

### 1️⃣ Registro de Usuarios

El sistema soporta **tres tipos de cuentas** en el registro público:

1. **Candidato:** Persona buscando oportunidades laborales
2. **Agencia/Empresa:** Organización que publica ofertas o busca talento
3. **Contratista:** Empresa que requiere servicios específicos

**Proceso de registro:**
- El usuario proporciona: nombre, apellido, email, contraseña y tipo de cuenta
- El sistema valida automáticamente los datos
- Se crea la cuenta en la base de datos
- Se genera automáticamente su perfil extendido según el tipo
- El usuario recibe un email de verificación

> ⚠️ El rol `admin` solo se asigna internamente por administradores y no está disponible en registro público.

### 2️⃣ Estructura de Datos

El sistema está organizado en **5 entidades principales:**

| Entidad | Descripción | Relación |
|---------|-----------|----------|
| **Users** | Credenciales de acceso y tipo de usuario | Tabla base de autenticación |
| **Candidates** | Perfil profesional del candidato | 1 Usuario = 1 Candidato |
| **Companies** | Información de la empresa contratante | 1 Usuario = 1 Empresa |
| **Applications** | Postulaciones de candidatos a ofertas | Múltiples por candidato/empresa |
| **CvAccess** | Registro de quién accedió a qué CV | Auditoría de privacidad |

**Datos adicionales capturados:**
- 📚 **Educación:** Instituciones, diplomas, fechas
- 💼 **Experiencia laboral:** Empresas, puestos, duración, responsabilidades
- 🎯 **Habilidades:** Competencias técnicas y blandas
- 📄 **Documentos:** CVs, certificados, documentación
- 🔍 **Auditoría:** Cambios, intentos de acceso, incidentes

### 3️⃣ Paneles de Usuario

La plataforma incluye **tres espacios principales:**

#### 📊 Dashboard del Candidato (`/dashboard`)
- Vista de perfil personal
- Listado de aplicaciones realizadas
- Histórico de accesos a su CV
- Gestión de documentos

#### 🏢 Panel de Empresa/Agencia (`/company`)
- Publicación de ofertas de trabajo
- Visualización de candidatos
- Gestión de aplicaciones recibidas
- Estadísticas de búsqueda
- Recurso Filament para gestión de candidatos

#### 👨‍💼 Panel de Administrador (`/admin`)
- Control centralizado de todos los usuarios
- Gestión de roles y permisos
- Auditoría completa de cambios del sistema
- Monitoreo de seguridad e incidentes
- Gestión de capacitación del equipo

## 🚀 Stack Tecnológico

CVConnectMX está construida con las **herramientas más modernas** del ecosistema Laravel:

| Herramienta | Versión | Propósito |
|-----------|---------|----------|
| **PHP** | 8.4 | Lenguaje backend |
| **Laravel** | 13 | Framework principal |
| **Livewire** | 4 | Componentes reactivos sin JavaScript |
| **Flux UI** | 2 | Sistema de componentes UI |
| **Filament** | 5 | Panel administrativo |
| **Laravel Fortify** | 1 | Sistema de autenticación avanzada |
| **Tailwind CSS** | 4 | Estilos y diseño responsivo |
| **Vite** | Último | Bundler frontend de alta velocidad |
| **Pest** | 4 | Framework de testing |
| **PHPUnit** | 12 | Testing adicional |

## 📦 Instalación Local

### Requisitos previos
- PHP 8.4+
- Composer
- Node.js y npm
- Base de datos (MySQL, PostgreSQL, SQLite)
- Git

### Pasos de instalación

1. **Clonar el repositorio:**
```bash
git clone https://github.com/tu-usuario/cvconnectmx.git
cd cvconnectmx
```

2. **Instalar dependencias PHP:**
```bash
composer install
```

3. **Configurar variables de entorno:**
```bash
cp .env.example .env
```
Edita `.env` y configura:
- `DB_*` (conexión a base de datos)
- `MAIL_*` (servidor de correo para verificaciones)
- `APP_KEY` (se genera automáticamente)

4. **Generar clave de aplicación:**
```bash
php artisan key:generate
```

5. **Crear base de datos y ejecutar migraciones:**
```bash
php artisan migrate
```

6. **Cargar datos de prueba (opcional):**
```bash
php artisan db:seed
```
Esto crea usuarios de prueba para todas las funcionalidades.

7. **Instalar dependencias frontend:**
```bash
npm install
```

8. **Iniciar servidor de desarrollo:**
```bash
composer run dev
```

La aplicación estará disponible en `http://localhost:8000`

## 🛠️ Comandos Útiles para Desarrollo

### Testing y Calidad de Código

```bash
# Ejecutar todas las pruebas
php artisan test --compact

# Ejecutar pruebas de un archivo específico
php artisan test --filter=NombreDelTest --compact

# Ejecutar formatter de código (Pint)
vendor/bin/pint --dirty --format agent

# Verificar sintaxis sin aplicar cambios
vendor/bin/pint --test --format agent
```

### Gestión de Base de Datos

```bash
# Ver estado de migraciones
php artisan migrate:status

# Revertir última migración
php artisan migrate:rollback

# Resetear toda la base de datos (cuidado en producción)
php artisan migrate:reset

# Resetear + ejecutar todas las migraciones
php artisan migrate:refresh --seed

# Ejecutar seeders específicos
php artisan db:seed --class=UserSeeder
```

### Artisan Útiles

```bash
# Ver todas las rutas
php artisan route:list

# Limpiar caché de la aplicación
php artisan cache:clear

# Limpiar configuración
php artisan config:clear

# Generar caché de configuración
php artisan config:cache
```

### Frontend

```bash
# Build de producción
npm run build

# Desarrollo en tiempo real con HMR
npm run dev

# Lint de código JavaScript
npm run lint
```

---

## 👤 Datos de Prueba

El `UserSeeder` genera automáticamente usuarios para todas las funcionalidades:

| Tipo | Cantidad | Propósito |
|------|----------|----------|
| Admin | 1 | Acceso a panel administrativo |
| Candidatos | 5 | Perfiles de ejemplo |
| Empresas | 5 | Crear ofertas y revisar aplicaciones |
| Contratistas | 5 | Buscar talento |

### Cuenta Admin por defecto (testing)
- **Email:** `cesar@unach.mx`
- **Contraseña:** `123456789`
- **Permisos:** Acceso total a la plataforma

### Generar más datos de prueba
```bash
# Ejecutar seeder específico
php artisan db:seed --class=CandidateSeeder

# Crear datos con factory
php artisan tinker
>>> User::factory(10)->create()
>>> Candidate::factory(10)->create()
```

---

## 📊 Estado Actual de Implementación

### ✅ Funcionalidades Completadas

#### Autenticación y Seguridad
- ✓ Sistema de login/registro con Fortify
- ✓ Verificación de email
- ✓ Recuperación de contraseña
- ✓ Autenticación de dos factores (TOTP)
- ✓ Protección contra ataques (brute force)
- ✓ Historial de contraseñas
- ✓ Soft delete en usuarios

#### Gestión de Perfiles
- ✓ Perfiles de candidatos con datos completos
- ✓ Perfiles de empresas/agencias
- ✓ Carga de documentos (CV, certificados)
- ✓ Gestión de experiencia laboral
- ✓ Registro de educación
- ✓ Gestión de habilidades/skills

#### Funcionalidades Laborales
- ✓ Postulación de candidatos a ofertas
- ✓ Registro de acceso a CVs (auditoría de privacidad)
- ✓ Panel para empresas/agencias
- ✓ Gestión de aplicaciones

#### Paneles Administrativos
- ✓ Panel Filament para gestión de candidatos
- ✓ Gestión de permisos y roles
- ✓ Sistema de auditoría (audit logs)
- ✓ Monitoreo de intentos de login
- ✓ Gestión de incidentes
- ✓ Alertas del sistema
- ✓ Registro de respaldos

#### Infraestructura Técnica
- ✓ Migraciones de base de datos
- ✓ Modelos Eloquent con relaciones
- ✓ Validación robusta
- ✓ Autorización con policies
- ✓ Seeders para datos de prueba
- ✓ Tests con Pest
- ✓ Logging y monitoreo de salud

### 🚀 Próximas Mejoras Planeadas

- 📋 Extensión del panel de empresa con más recursos
- 📧 Notificaciones por email integradas
- 📱 App móvil complementaria
- 🤖 Búsqueda inteligente de candidatos
- 📊 Reportes y estadísticas avanzadas
- 🔗 Integraciones con redes sociales
- 💬 Sistema de mensajería entre usuarios

---

## 📁 Estructura del Proyecto

```
cvconnectmx/
├── app/
│   ├── Actions/          # Acciones de Fortify (registro, login, 2FA)
│   ├── Filament/         # Paneles administrativos
│   ├── Http/             # Controladores y middleware
│   ├── Livewire/         # Componentes Livewire y Flux
│   ├── Models/           # Modelos Eloquent
│   ├── Notifications/    # Notificaciones por email
│   ├── Observers/        # Listeners de eventos
│   └── Providers/        # Service providers
│
├── database/
│   ├── factories/        # Factory para crear datos de prueba
│   ├── migrations/       # Migraciones de base de datos
│   └── seeders/          # Seeders para datos iniciales
│
├── resources/
│   ├── css/              # Estilos Tailwind
│   ├── js/               # JavaScript del frontend
│   └── views/            # Vistas Blade
│
├── routes/
│   └── web.php           # Rutas principales
│
├── tests/
│   ├── Feature/          # Tests de funcionalidad
│   └── Unit/             # Tests unitarios
│
└── config/               # Archivos de configuración
```

---

## 🔒 Seguridad

CVConnectMX implementa **múltiples capas de seguridad:**

- ✅ **CSRF Protection:** Token CSRF en todos los formularios
- ✅ **SQL Injection Prevention:** Uso de Eloquent ORM
- ✅ **XSS Protection:** Escapado automático de salida
- ✅ **Rate Limiting:** Límites de intentos de login
- ✅ **Hashing de Contraseñas:** BCrypt
- ✅ **Soft Deletes:** Datos nunca se eliminan permanentemente
- ✅ **Auditoría:** Cada cambio queda registrado

### Reportar Vulnerabilidades

Si encuentras una vulnerabilidad de seguridad, **NO** la publiques públicamente. 
Envía un email detallado a: `security@cvconnectmx.dev`

---

## 🤝 Contribución

Las contribuciones son bienvenidas. Para contribuir:

1. **Fork** el repositorio
2. **Crea una rama** para tu feature (`git checkout -b feature/AmazingFeature`)
3. **Commit** tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. **Push** a la rama (`git push origin feature/AmazingFeature`)
5. **Abre un Pull Request**

### Estándares de Código
- Sigue PSR-12
- Ejecuta `vendor/bin/pint` antes de hacer commit
- Escribe tests para nuevas funcionalidades
- Actualiza el README si es necesario

---

## 📄 Licencia

CVConnectMX está bajo la licencia **MIT**. Ver archivo [LICENSE](LICENSE) para más detalles.

---

## 📞 Soporte y Contacto

- 📧 **Email:** info@cvconnectmx.mx
- 🐛 **Reportar bugs:** [Issues](https://github.com/tu-usuario/cvconnectmx/issues)
- 💬 **Discussiones:** [Discussions](https://github.com/tu-usuario/cvconnectmx/discussions)
- 🌐 **Sitio web:** https://cvconnectmx.mx

---

## 📚 Recursos Adicionales

- [Documentación de Laravel](https://laravel.com/docs)
- [Documentación de Livewire](https://livewire.laravel.com)
- [Documentación de Filament](https://filamentphp.com/docs)
- [Documentación de Flux UI](https://flux.laravel.com)
- [Laravel Fortify](https://github.com/laravel/fortify)

---

<div align="center">

**Hecho con ❤️ para conectar talento con oportunidades**

© 2025 CVConnectMX. Todos los derechos reservados.

</div>
