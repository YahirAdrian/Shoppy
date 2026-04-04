# Users — Especificación

## Contexto
Página de administración de usuarios. Permite al admin gestionar cuentas de administradores y vendedores. Los usuarios no se eliminan, solo se activan/desactivan para preservar la integridad de datos (ventas asociadas).

## Estructura de datos

La tabla `users` ya existe con los campos necesarios:

| Campo              | Tipo                        | Requerido | Notas                                  |
|--------------------|-----------------------------|-----------|----------------------------------------|
| name               | string                      | Sí        | Nombre del usuario                     |
| email              | string (unique)             | Sí        | Correo electrónico                     |
| password           | string                      | Sí        | Requerido al crear, opcional al editar |
| role               | enum: admin, seller         | Sí        | Rol del usuario                        |
| is_active          | boolean (default: true)     | Sí        | Estado activo/inactivo                 |

## Diseño de página

### Tabla de usuarios
Tabla con las siguientes columnas:

| Columna            | Contenido                                          |
|--------------------|----------------------------------------------------|
| Nombre             | Nombre del usuario                                 |
| Correo             | Correo electrónico                                 |
| Rol                | Badge: `Admin` o `Vendedor`                        |
| Estado             | Badge verde `Activo` o rojo `Inactivo`             |
| Fecha de registro  | `created_at` formateado                            |
| Acciones           | Menú kebab → Editar, Activar/Desactivar            |

- Botón "Nuevo usuario" en la parte superior.
- Sin paginación inicial (pocos usuarios en negocio pequeño).

### Modal de crear / editar usuario
Campos del formulario:

- **Nombre** — texto, obligatorio
- **Correo electrónico** — email, obligatorio, único
- **Contraseña** — obligatorio al crear, opcional al editar (vacío = mantener actual)
- **Confirmar contraseña** — campo de confirmación
- **Rol** — select: `Admin` / `Vendedor`
- **Estado** — toggle: Activo / Inactivo

### Reglas de negocio
- El admin no puede desactivarse a sí mismo.
- El admin no puede cambiar su propio rol.
- Usuarios inactivos no pueden iniciar sesión (validar `is_active` en el flujo de autenticación).

## Pasos de implementación

### 1. Rutas
Rutas resource bajo `admin/usuarios` con nombre `admin.users.*`:
- `GET /admin/usuarios` → `index`
- `POST /admin/usuarios` → `store`
- `PUT /admin/usuarios/{user}` → `update`
- `PATCH /admin/usuarios/{user}/toggle-active` → `toggleActive`

### 2. Controlador
`App\Http\Controllers\Admin\UserController` con métodos:
- `index` — lista todos los usuarios
- `store` — crea usuario con validación
- `update` — actualiza usuario (contraseña opcional)
- `toggleActive` — alterna estado activo/inactivo con protección contra auto-desactivación

### 3. Vista
`resources/views/admin/users/index.blade.php`:
- Tabla de usuarios
- Modal para crear/editar (reutilizado)
- Mensajes flash de éxito/error

### 4. Autenticación
Agregar verificación de `is_active` en el flujo de login para bloquear usuarios inactivos.

## Verificación
- [ ] Listar usuarios en tabla
- [ ] Crear usuario con todos los campos
- [ ] Editar usuario (contraseña opcional)
- [ ] Activar/desactivar usuario
- [ ] No permitir auto-desactivación ni cambio de rol propio
- [ ] Usuarios inactivos no pueden iniciar sesión
- [ ] Mensajes de validación en español
