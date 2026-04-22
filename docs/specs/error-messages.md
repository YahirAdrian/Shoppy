# Error Pages — Shoppy

Custom error pages for application-level failures. Both are standalone Blade templates — they must NOT use `<x-layouts.admin>` or `<x-layouts.pos>` since those layouts query `business_settings` and would crash if the DB is unavailable.

## Shared design

- Use `@vite(['resources/css/app.css'])` for styling — reads `public/build/manifest.json` from disk, no DB required
- Use `public/shoppy-logo-white.svg` as icon inside a `primary-700` circle (consistent with sidebar logo fallback)
- Stone-50 background, centered card layout
- All text in Spanish

## 503 — Database connection error

**File:** `resources/views/errors/db-connection.blade.php`

**When shown:** `PDOException` or `QueryException` containing a connection error code (`2002`, `Connection refused`, `Access denied`) intercepted in `bootstrap/app.php` `withExceptions()`.

**HTTP status:** 503 Service Unavailable

**Behavior for JSON requests** (POS API): return `{"message": "No se pudo conectar a la base de datos."}` with status 503 instead of HTML.

### Visual layout

- Shoppy logo circle (primary-700) centered at top
- Heading: **"Error de conexión"**
- Body: "No se pudo conectar a la base de datos. Intenta de nuevo en un momento."
- Sub-text (small, muted): "Si el problema persiste, contacta al administrador del sistema."
- Button: **"Reintentar"** — calls `window.location.reload()` via inline `onclick` (trivial expression, allowed by JS guidelines)

### Exception handler (bootstrap/app.php)

Intercept these exception types:
- `Illuminate\Database\QueryException` — wraps all PDO errors
- `PDOException` — thrown directly before Eloquent wraps it (e.g. during session startup)

Check message for connection-related strings: `2002`, `Connection refused`, `php_network_getaddresses`, `Access denied for user`.

## 404 — Page not found

**File:** `resources/views/errors/404.blade.php`

**When shown:** Automatically by Laravel when a `404` / `ModelNotFoundException` / `NotFoundHttpException` is raised. No custom exception handler needed — Laravel picks up `resources/views/errors/404.blade.php` automatically.

**HTTP status:** 404 Not Found

### Visual layout

- Same Shoppy logo circle as 503 page
- Large muted "404" numeral (text-8xl, stone-300)
- Heading: **"Página no encontrada"**
- Body: "La página que buscas no existe o fue movida."
- Link button: **"Volver al inicio"** → href `/` (redirects to `/login` for guests, or dashboard/POS for authenticated users via existing redirect middleware)