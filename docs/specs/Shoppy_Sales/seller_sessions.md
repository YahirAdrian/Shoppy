# Shoppy Sales — Seller Sessions

## Overview

A **POS session** (`pos_sessions`) represents a seller's work shift at the terminal. It tracks how much cash is in the drawer at any given moment, which sales were made during the shift, and when the shift started and ended.

Sessions are created automatically on login and resumed if an active one already exists. Every sale is linked to the session in which it was made.

---

## Database Schema

### New table: `pos_sessions`

| Column | Type | Default | Notes |
|---|---|---|---|
| `id` | bigint PK | | |
| `seller_id` | FK → `users.id` | | `nullOnDelete` — keep session history if seller is deleted |
| `opening_cash` | `decimal(10,2)` | `0.00` | Cash in the drawer at shift start |
| `current_cash` | `decimal(10,2)` | `0.00` | Live balance: opening + sales − withdrawals |
| `status` | `enum('active','finished')` | `'active'` | |
| `started_at` | `timestamp` | | Set on creation (same as `created_at`) |
| `finished_at` | `timestamp` | `NULL` | Set when session ends |
| `timestamps` | | | `created_at`, `updated_at` |

**Constraint:** A seller may have at most one `active` session at a time. Enforced at the controller level (abort 409 if an active session already exists when attempting to create a new one).

### Change to existing table: `sales`

Add one nullable foreign key column:

| Column | Type | Notes |
|---|---|---|
| `pos_session_id` | FK → `pos_sessions.id`, nullable | `nullOnDelete` — sales survive if session is deleted. Null for sales made before this feature was introduced. |

---

## Session Lifecycle

### 1. Starting a session

Triggered automatically by the `LoginController` after a successful seller login:

- Query `pos_sessions` for any row where `seller_id = auth()->id()` and `status = 'active'`.
- **Active session found → resume:** redirect to `/pos/venta`. No prompt, no new row created.
- **No active session → new shift:** redirect to `/pos/iniciar-turno` (a dedicated page) where the seller enters their `opening_cash`. On form submit, create a new `pos_sessions` row and redirect to `/pos/venta`.

The `/pos/iniciar-turno` route is only accessible when the seller has no active session. If they navigate there while a session is active, redirect to `/pos/venta`.

### 2. During a session

- **Sale created:** `current_cash` increments by `sale.total` for `cash` payment method only. Non-cash payments do not affect the drawer balance. The new `Sale` row is linked via `pos_session_id`.
- **Sale voided (deleted):** `current_cash` decrements by `sale.total` (cash sales only) within the same DB transaction.
- **Withdrawal recorded:** `current_cash` decrements by the withdrawn amount. Each withdrawal is a `PATCH` call to the API; the client no longer tracks withdrawals in memory.

### 3. Ending a session

Triggered by the "Terminar sesión" button on the POS status page (admin operation):

- **Precondition:** `current_cash` must equal `0.00` — all cash (including the opening float) has been physically removed from the drawer and verified. The button is disabled otherwise.
- On confirm: set `status = 'finished'`, set `finished_at = now()`, then log the seller out.

---

## `current_cash` Formula

At any point:

```
current_cash = opening_cash
             + Σ(total of all cash sales in this session)
             − Σ(all withdrawals in this session)
             − Σ(total of all voided cash sales in this session)
```

`current_cash` is updated inside DB transactions whenever a sale is created, voided, or a withdrawal is recorded. It is never recomputed from scratch at query time; the stored value is authoritative.

---

## Business Rules

1. A seller can have at most **one active session** at a time.
2. Sales made without an active session are not possible — `storeSale()` must reject the request if no active session exists (HTTP 409).
3. `opening_cash` is set once at session start and is never modified afterwards.
4. `current_cash` can temporarily go negative (e.g. a cash sale is voided after the cash was already withdrawn). This is allowed — the negative balance flags a discrepancy.
5. Only cash-payment sales affect `current_cash`. Card or other payments are excluded.
6. A session can only be ended when `current_cash == 0.00`.
7. Ending a session is an **admin operation** (requires admin auth, same mechanism as sale deletion).
8. A finished session is read-only — no sales can be linked to it after `finished_at` is set.

---

## New API Endpoints

| Method | URL | Name | Description |
|---|---|---|---|
| `POST` | `/pos/api/sessions` | `pos.api.sessions.store` | Create a new session with `opening_cash` |
| `GET` | `/pos/api/sessions/current` | `pos.api.sessions.current` | Return the active session for the current seller |
| `PATCH` | `/pos/api/sessions/current/withdraw` | `pos.api.sessions.withdraw` | Record a withdrawal; decrements `current_cash` |
| `PATCH` | `/pos/api/sessions/current/end` | `pos.api.sessions.end` | End the active session (admin auth required) |

All endpoints are under the `role:seller` middleware group.

---

## Changes to Existing Endpoints

| Endpoint | Change |
|---|---|
| `POST /pos/api/sales` (`storeSale`) | Look up active session, attach `pos_session_id`, increment `current_cash` for cash sales |
| `DELETE /pos/api/sales/{sale}` (`deleteSale`) | Decrement `current_cash` of the linked session for cash sales |
| `GET /pos/api/status` (via `PosController::status()`) | Source session stats from `pos_sessions` (current_cash, opening_cash, started_at) rather than recomputing from raw Sale queries |

---

## New Page: `/pos/iniciar-turno`

- Only accessible when the seller has **no active session**. If they have one, redirect to `/pos/venta`.
- Simple centered form: one numeric input for `opening_cash` (label: "Efectivo en caja al iniciar"), a "Iniciar turno" submit button.
- On submit: `POST /pos/api/sessions` → redirect to `/pos/venta`.
- Uses the POS layout.

---

## Status Page Changes

The admin operations section of `/pos/estado` replaces the client-side withdrawal tracker with server-persisted data:

- Display `opening_cash`, `current_cash` (live from the session), and `total withdrawn` (computed as `opening_cash + cash_sales_total − current_cash`).
- Withdrawal input calls `PATCH /pos/api/sessions/current/withdraw` instead of updating Alpine state.
- "Terminar sesión" calls `PATCH /pos/api/sessions/current/end` (requires admin auth), then logs out.
- Stats cards (total sales, total sold, avg ticket) are sourced from sales linked to the current session (`pos_session_id = current_session.id`).

---

## Implementation Order

1. Migrations — `create_pos_sessions_table`, `add_pos_session_id_to_sales`
2. `PosSession` model + relationships (`belongsTo User`, `hasMany Sale`)
3. `Sale` model — add `belongsTo PosSession`
4. `/pos/iniciar-turno` route, controller method, and view
5. `LoginController` — post-login redirect logic for session check
6. Middleware or route constraint to guard `/pos/iniciar-turno` access
7. `PosApiController` — new session endpoints (`store`, `current`, `withdraw`, `end`)
8. `PosApiController::storeSale()` — attach session + update `current_cash`
9. `PosApiController::deleteSale()` — reverse `current_cash`
10. `PosController::status()` + status page Alpine updates
11. Tests
