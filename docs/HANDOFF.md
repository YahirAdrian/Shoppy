# Shoppy — Session Handoff

## Current State

Phase 1 is complete. The project has a working Laravel install, a fully migrated database, and seeded data. The app is not yet runnable in the browser — no routes, views, or auth exist yet.

**What's working:**
- Fresh Laravel 11 install with `.env` configured for MySQL (via XAMPP)
- TailwindCSS and Laravel Breeze installed
- MySQL database `shoppy` is live and connected
- All 9 migrations ran successfully — every table is created and correct
- Database fully seeded: admin user, 2 sellers, business settings, 6 categories, 35 products

**What's not done yet:**
- Everything from Phase 2 onward (auth, routes, UI)

---

## Where We Are

**Current phase:** Phase 2 — Auth & Roles (not started)

**Immediate next task:** Build auth:
1. Login page UI
2. `role:admin` and `role:seller` middleware
3. Route protection for `/admin` and `/pos` groups
4. Post-login redirect — admin → `/admin/dashboard`, seller → `/pos`
5. Logout wired up

---

## Seeded Data
**Provisional user data only for development mode**
| Table | Rows | Notes |
|---|---|---|
| `users` | 3 | `shoppyadminer` (admin), `maría@shoppy.local` (seller), `carlos@shoppy.local` (seller). All passwords: `1234` |
| `business_settings` | 1 | "La Tiendita", currency `$`, low stock threshold `5` |
| `categories` | 6 | Bebidas, Snacks, Lácteos, Panadería, Limpieza, Abarrotes |
| `products` | 35 | Spread across all 6 categories, all with SKU, barcode, cost/selling price, stock |

---

## Key Decisions Made

**Schema design**
- `users` table extended from Breeze defaults — added `role` enum (`admin`, `seller`) and `is_active` boolean directly in the original migration
- `sale` → `sales`, `sale_item` → `sale_items` (Laravel plural conventions)
- `products.stock` is `decimal` not `int` to support fractional units (e.g. kg)
- `products.low_stock_alert` is nullable — falls back to the global `business_settings.low_stock` threshold when null
- `sale_items` snapshots `product_name` and `unit_price` at time of sale — so historical receipts are accurate even if the product is later edited

**Foreign key behaviour**
- `sale_items.sale_id` → `cascadeOnDelete` (line items are meaningless without their sale)
- `stock_movements.user_id` → `nullOnDelete` (audit trail must survive user deletion)
- All other FKs → `restrictOnDelete` (prevent silent data loss)

---

## Watch Out For

- **Always use `/opt/lampp/bin/php artisan`**, not `php artisan` — the system PHP will fail with `could not find driver`
- **`business_settings` is a singleton** — seeders and any settings form must assume exactly one row; never insert a second one
- **Currency symbol must never be hardcoded** — always read from `business_settings.currency_symbol`
- **`role` and `is_active` live on the `users` table** — Breeze's default scaffolding does not know about these; any generated auth views or redirects will need to be customised to handle role-based routing
- **No `status` column on `sales` yet** — void/refund (Phase 5) will require adding a `status` enum (`completed`, `voided`, `refunded`); worth adding as an early migration before Phase 5 work begins
- **`mbstring` and `pdo_mysql` extensions** must be enabled in `/etc/php/8.3/cli/php.ini` — both were missing on this machine and had to be installed via `apt`
