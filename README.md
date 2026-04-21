# CVConnectMX

CVConnectMX es una plataforma web para conectar perfiles laborales entre **candidatos**, **agencias** y **contratistas**.  
Está construida con **Laravel 13**, **Livewire 4**, **Flux UI**, **Fortify** (autenticación) y **Filament** (panel administrativo).

## ¿Qué hace el proyecto?

- Permite registro e inicio de sesión con distintos tipos de cuenta:
  - `candidate`
  - `agency`
  - `contractor`
- Maneja autenticación completa con Fortify:
  - login / registro
  - recuperación de contraseña
  - verificación de email
  - autenticación de dos factores (2FA)
- Incluye un panel Filament en `/agency` con recurso de candidatos (listado, detalle, edición y creación).
- Incluye vistas base para dashboard, documentos y notificaciones.

## ¿Cómo funciona?

### 1) Registro de usuarios por tipo

En el formulario de registro se solicita el tipo de cuenta (`user_type`).  
Al crear un usuario:

1. Se valida `name`, `last_name`, `email`, `password` y `user_type`.
2. Se crea el usuario en la tabla `users`.
3. Según el tipo de cuenta, se crea automáticamente su relación:
   - candidato → `candidates`
   - agencia → `agencies`
   - contratista → `contractors`

> El rol `admin` no está disponible en el registro público.

### 2) Modelo de datos principal

- `users`: información de autenticación y tipo de usuario.
- `candidates`: perfil extendido de candidato (teléfono, edad, sexo, dirección, scoring).
- `agencies`: perfil de agencia ligado al usuario.
- `contractors`: perfil de contratista ligado al usuario.

Relaciones:
- `User hasOne Candidate`
- `User hasOne Agency`
- `User hasOne Contractor`

### 3) Panel Filament

El panel está configurado con ruta base:

- `https://tu-dominio/agency`

Actualmente incluye el recurso **Candidates**, donde puedes:
- ver listado de candidatos
- buscar por campos principales
- ver detalle
- editar
- crear nuevos registros

## Stack tecnológico

- PHP 8.4
- Laravel 13
- Livewire 4
- Flux UI 2
- Laravel Fortify
- Filament 5
- Tailwind CSS 4 + Vite
- Pest (tests)

## Instalación local

1. Clona el repositorio.
2. Instala dependencias PHP:

```bash
composer install
```

3. Crea archivo de entorno:

```bash
cp .env.example .env
```

4. Genera llave de aplicación:

```bash
php artisan key:generate
```

5. Ejecuta migraciones:

```bash
php artisan migrate
```

6. (Opcional) Carga datos de ejemplo:

```bash
php artisan db:seed
```

7. Instala dependencias frontend:

```bash
npm install
```

8. Levanta entorno de desarrollo:

```bash
composer run dev
```

## Comandos útiles

- Ejecutar pruebas:

```bash
php artisan test --compact
```

- Ejecutar formatter/lint de PHP (Pint):

```bash
vendor/bin/pint --dirty --format agent
```

- Build de frontend:

```bash
npm run build
```

## Datos de prueba

El `UserSeeder` crea:
- 1 usuario admin
- 5 candidatos
- 5 agencias
- 5 contratistas

Además, define un admin fijo para pruebas locales:
- **email:** `cesar@unach.mx`
- **password:** `123456789`

## Estado actual

CVConnectMX tiene la base de autenticación, tipos de usuario y panel inicial de gestión de candidatos.  
Las secciones de documentos y notificaciones están listas para extenderse con lógica de negocio adicional.
