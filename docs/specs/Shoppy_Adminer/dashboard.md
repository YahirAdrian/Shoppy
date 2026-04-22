# Shoppy Adminer — Dashboard Spec

## Overview

The dashboard is the landing page for admins after login. It shows a live summary of the business: sales activity, product health, seller status, and pending tasks. All data is server-rendered and passed to views; chart data is passed as JSON via `@js()`. No inline JS in Blade — chart initialization lives in `resources/js/admin/dashboard.js`.

---

## Layout

Three sections stacked vertically:

1. **Summary Cards** — 3-column grid (Sales, Products, Sellers)
2. **Stats** — Line chart (left, 2/3 width) + Doughnut chart (right, 1/3 width)
3. **Actions** — Two-column grid (Pending tasks, Upcoming tasks)

---

## Section 1 — Summary Cards

### Card 1: Ventas

- **Header**: "Ventas" label + period `<select>` (Hoy / Semanal / Mensual)
- **Body**:
  - Large number: count of sales in selected period
  - Sub-text: total revenue in selected period (e.g. `$12,480.00 en ingresos`)
  - Divider
  - "Ventas recientes" sub-heading
  - List of 3 most recent sales (sale ID + total amount)
- **Period toggle**: Handled client-side with Alpine.js. All three period values (daily/weekly/monthly) are pre-computed server-side and passed as a JSON object. The select switches Alpine's `period` variable, no AJAX needed.
- **Data sources**:
  - Sale count: `Sale::whereDate/whereBetween('created_at', ...)` for each period
  - Revenue: `SUM(subtotal - discount_amount)` per period
  - Recent sales: `Sale::latest()->limit(3)` — show sale `id` and computed `total`

### Card 2: Productos

Two-column layout inside the card:

- **Left column — Más vendidos**: Top 3 products by total quantity sold (all-time). Source: `SaleItem::groupBy('product_name')->sum('quantity')` ordered descending.
- **Right column — Stock bajo**: Top 3 products closest to or below their low stock threshold. Source: Products where `stock <= COALESCE(low_stock_alert, global_threshold)`, ordered by stock ascending, limit 3. Show product name + current stock value.

### Card 3: Vendedores (replaces Pendientes)

- **Header**: "Vendedores"
- **Body**:
  - List of all active sellers (`users` where `role = 'seller'` and `is_active = true`)
  - Per seller row: name + count of sales made today
  - If the seller has an active POS session (`pos_sessions` where `status = 'active'`), show a badge "En turno" and a **Terminar turno** button
  - The **Terminar turno** button opens a confirmation dialog (Alpine.js modal) asking the admin to confirm before ending the session
  - Ending the session calls a POST route that sets the session `status = 'finished'` and `finished_at = now()` — only allowed if `current_cash = 0` (guard identical to POS end-session logic)
  - If no sellers exist or none are active, show an empty state: "Sin vendedores activos"
- **Data sources**:
  - Sellers: `User::where('role', 'seller')->where('is_active', true)->get()`
  - Today's sales per seller: `Sale::whereDate('created_at', today())->where('user_id', $seller->id)->count()`
  - Active session: `PosSession::where('user_id', $seller->id)->where('status', 'active')->first()`

---

## Section 2 — Stats

### Chart 1: Ventas diarias (últimos 30 días) — Line chart

- **Width**: 2/3 of stats row (lg:col-span-2)
- **X axis**: Last 30 days, labeled as short dates (e.g. "21 Abr", "22 Abr", …)
- **Y axis**: Revenue in currency (formatted with currency symbol from `business_settings`)
- **Dataset**: One line only — daily revenue. Label: "Ventas"
- **Color**: `primary-600` (purple)
- **Data source**: `Sale::selectRaw('DATE(created_at) as day, SUM(subtotal - discount_amount) as revenue')->whereBetween('created_at', [now()->subDays(29)->startOfDay(), now()->endOfDay()])->groupBy('day')->orderBy('day')->get()`. Days with no sales default to 0.
- **Note**: Gastos and Ganancia lines are excluded — cost price is not snapshotted in sale history.

### Chart 2: Ingresos por categoría — Doughnut chart

- **Width**: 1/3 of stats row
- **Data**: Revenue grouped by product category (all-time or current year — implementation choice)
- **Labels**: Category names
- **Data source**: `SaleItem::join('products', 'sale_items.product_id', 'products.id')->join('categories', 'products.category_id', 'categories.id')->selectRaw('categories.name as category, SUM(sale_items.subtotal) as revenue')->groupBy('categories.name')->orderByDesc('revenue')->get()`
- **Note**: Uses current product→category assignment. If a product was recategorized, historical items reflect the current category.

---

## Section 3 — Actions (Tasks)

### Panel 1: Tareas pendientes

- Lists tasks where `is_completed = false` and `due_date <= today()`, ordered by `due_date` ascending, limit 5.
- Each row: checkbox (decorative, links to `/admin/tareas`), task name, due date, overdue highlight (red text if overdue).
- Footer link: "Ver todas las tareas →" → `/admin/tareas`

### Panel 2: Próximas tareas

- Lists tasks where `is_completed = false` and `due_date > today()`, ordered by `due_date` ascending, limit 5.
- Each row: task name, due date, repeat type badge (if recurring).
- Footer link: "Ver todas las tareas →" → `/admin/tareas`

---

## Controller

`App\Http\Controllers\Admin\DashboardController@index`

Computes and passes to the view:

| Variable | Type | Description |
|---|---|---|
| `$salesStats` | array | `['daily' => ['count', 'revenue'], 'weekly' => [...], 'monthly' => [...]]` |
| `$recentSales` | Collection | Last 3 sales (id, total) |
| `$topProducts` | Collection | Top 3 products by qty sold |
| `$lowStockProducts` | Collection | Up to 3 low-stock products (name, stock) |
| `$sellers` | Collection | Active sellers with today's sale count + active session |
| `$dailySalesChart` | array | `[labels => [...], data => [...]]` for last 30 days |
| `$categoryRevenueChart` | array | `[labels => [...], data => [...]]` for doughnut |
| `$pendingTasks` | Collection | Up to 5 pending/overdue tasks |
| `$upcomingTasks` | Collection | Up to 5 upcoming tasks |
| `$currency` | string | From `business_settings.currency_symbol` |

---

## Routes

| Method | URI | Controller | Notes |
|---|---|---|---|
| GET | `/admin/dashboard` | `DashboardController@index` | Existing route, change from closure |
| POST | `/admin/dashboard/end-session/{session}` | `DashboardController@endSession` | Admin ends a seller's POS session |

---

## File Structure

Views split into partials under `resources/views/admin/dashboard/`:

- `summary-cards.blade.php` — 3-card grid
- `stats.blade.php` — both charts
- `tasks.blade.php` — pending + upcoming tasks panels

JS: `resources/js/admin/dashboard.js` — Chart.js initialization (no inline scripts in Blade). Exposed on `window` if Alpine interaction is needed.

---

## Constraints & Rules

- Never hardcode currency symbol — always use `$currency` from `business_settings`
- All UI text in Spanish
- No inline JS in Blade files — chart setup goes in `resources/js/admin/dashboard.js`
- Alpine.js period toggle in summary card uses pre-computed server data, no AJAX
- Ending a seller session requires `current_cash = 0`; return an error message in the dialog if not
