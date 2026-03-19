# Shoppy — Architecture Reference

## Middleware

### RoleMiddleware (`App\Http\Middleware\RoleMiddleware`)

**Alias:** `role`
**Registration:** `bootstrap/app.php` via `$middleware->alias(['role' => RoleMiddleware::class])`

#### Purpose
Enforces role-based access control on protected route groups. Every request to `/admin/*` or `/pos/*` passes through this middleware.

#### Parameters
Accepts a single string parameter — the required role (`admin` or `seller`).

```
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(...)
Route::middleware(['auth', 'role:seller'])->prefix('pos')->group(...)
```

#### Resolution Logic

| User state         | Required role | Action                          |
|--------------------|---------------|---------------------------------|
| Unauthenticated    | any           | Redirect → `/login`             |
| `admin`            | `admin`       | Allow through                   |
| `admin`            | `seller`      | Redirect → `/admin/dashboard`   |
| `seller`           | `seller`      | Allow through                   |
| `seller`           | `admin`       | Redirect → `/pos`               |

#### Design Notes
- The `auth` middleware is always stacked **before** `role` — by the time `RoleMiddleware` runs, unauthenticated requests are already caught. The unauthenticated guard inside `RoleMiddleware` is a defensive fallback for cases where the middleware is applied without `auth`.
- Role values are stored as plain strings on the `users.role` column (`admin` | `seller`).
- No permission granularity beyond role — Shoppy intentionally keeps authorization simple.

---

## Route Groups

| Prefix    | Middleware              | Audience       |
|-----------|-------------------------|----------------|
| `/admin`  | `auth`, `role:admin`    | Admin users    |
| `/pos`    | `auth`, `role:seller`   | Seller users   |
| `/login`  | `guest`                 | Unauthenticated |
