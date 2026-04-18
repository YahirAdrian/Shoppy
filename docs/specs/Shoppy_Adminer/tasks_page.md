# Tasks — Especificación

## Contexto
Página dedicada para la gestión de tareas del administrador. Funciona como una lista de pendientes (ToDo) con soporte para tareas recurrentes.

## Estructura de datos

### Tabla `tasks`

| Campo           | Tipo                                  | Requerido | Notas                                                        |
|-----------------|---------------------------------------|-----------|--------------------------------------------------------------|
| id              | bigint (PK)                           | Sí        | Auto-incremental                                             |
| name            | string                                | Sí        | Nombre de la tarea                                           |
| due_date        | date                                  | No        | Fecha límite                                                 |
| is_completed    | boolean (default: false)              | Sí        | Estado de la tarea                                           |
| completed_at    | timestamp                             | No        | Fecha en que se completó                                     |
| repeat_type     | enum: none, daily, weekly, monthly    | Sí        | Tipo de repetición (default: none)                           |
| repeat_interval | integer                               | No        | Cada cuántos días/semanas/meses se repite (default: 1)      |
| next_due_date   | date                                  | No        | Próxima fecha de vencimiento para tareas recurrentes         |
| created_at      | timestamp                             | Sí        | Timestamp de creación                                        |
| updated_at      | timestamp                             | Sí        | Timestamp de actualización                                   |

## Lógica de repetición

Cuando una tarea recurrente se marca como completada:
1. La tarea actual se marca como completada (`is_completed = true`, `completed_at = now`).
2. Se crea automáticamente una nueva tarea con los mismos datos y la siguiente fecha calculada:
   - **Diario**: `due_date + repeat_interval días`
   - **Semanal**: `due_date + repeat_interval semanas`
   - **Mensual**: `due_date + repeat_interval meses`
3. Si la tarea original no tenía `due_date`, la nueva tarea usa la fecha actual como base para calcular la siguiente.

## Diseño de página

### Encabezado
- Título: "Tareas"
- Botón "Nueva tarea"

### Secciones

La página se divide en dos secciones:

1. **Pendientes** — Tareas con `is_completed = false`, ordenadas por `due_date` ascendente (las más próximas primero), luego por `created_at`.
2. **Completadas** — Tareas con `is_completed = true`, ordenadas por `completed_at` descendente (las más recientes primero).

### Tarjeta de tarea
Cada tarea se muestra como una fila/card con:
- Checkbox para marcar como completada/pendiente
- Nombre de la tarea
- Fecha límite (si tiene), con indicador visual si está vencida (texto rojo)
- Badge de repetición si es recurrente (ej. "Cada 2 semanas")
- Menú kebab → Editar, Eliminar

### Modal de crear / editar tarea
Campos del formulario:

- **Nombre** — texto, obligatorio
- **Fecha límite** — date picker, opcional
- **Repetición** — select: `Sin repetición` / `Diario` / `Semanal` / `Mensual`
- **Cada** — número, visible solo si repetición ≠ Sin repetición (default: 1, min: 1)

## Pasos de implementación

### 1. Migración
Crear tabla `tasks` con los campos descritos.

### 2. Modelo
`App\Models\Task` con `$fillable` y métodos helper para la lógica de repetición.

### 3. Rutas
Rutas bajo `admin/tareas` con nombre `admin.tasks.*`:
- `GET /admin/tareas` → `index`
- `POST /admin/tareas` → `store`
- `PUT /admin/tareas/{task}` → `update`
- `PATCH /admin/tareas/{task}/toggle` → `toggle` (completar/desmarcar)
- `DELETE /admin/tareas/{task}` → `destroy`

### 4. Controlador
`App\Http\Controllers\Admin\TaskController` con métodos: `index`, `store`, `update`, `toggle`, `destroy`.

### 5. Vista
`resources/views/admin/tasks/index.blade.php`:
- Secciones de pendientes y completadas
- Modal para crear/editar
- Mensajes flash

## Verificación
- [ ] Crear tarea simple (sin repetición)
- [ ] Crear tarea con fecha límite
- [ ] Crear tarea recurrente
- [ ] Completar tarea simple
- [ ] Completar tarea recurrente genera nueva tarea con fecha calculada
- [ ] Desmarcar tarea completada (vuelve a pendiente)
- [ ] Editar tarea
- [ ] Eliminar tarea
- [ ] Tareas vencidas muestran indicador visual
- [ ] Mensajes de validación en español
