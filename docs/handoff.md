# Shoppy — Session Handoff

## Current State

Phases 1, 2, and 3 are complete. The admin layout with sidebar navigation and a fully designed dashboard (with fictional data) are working in the browser.

**What's working:**
- Fresh Laravel 11 install with `.env` configured for MySQL (via XAMPP)
- TailwindCSS and Laravel Breeze installed
- MySQL database `shoppy` is live and connected
- All 9 migrations ran successfully — every table is created and correct
- Database fully seeded: admin user, 2 sellers, business settings, 6 categories, 35 products
- Login page at `/login` with Spanish validation messages
- Role middleware (`role:admin`, `role:seller`) registered and applied to route groups
- `/admin/*` protected — admin only; `/pos/*` protected — seller only
- Post-login redirect by role; authenticated users forced to `/login` go to their home
- `/` redirects to `/login`
- Logout clears session and returns to `/login`
- Frontend assets built via Vite (`npm run build`)
- **Admin layout** — Shared Blade layout (`components/layouts/admin.blade.php`) with sidebar component (`components/admin/sidebar.blade.php`)
- **Sidebar** — stone-800 background, 7 nav links (Dashboard, Ventas, Inventario, Reportes, Negocio, Usuarios, Tareas) with white SVG icons, active state detection, disabled state for unbuilt routes, user footer with logout
- **Mobile responsive** — Hamburger menu toggle via Alpine.js with overlay
- **Dashboard** — Split into partials (`admin/dashboard/summary-cards.blade.php`, `stats.blade.php`, `tasks.blade.php`) with fictional data: 3 summary cards, line + doughnut charts, pending/upcoming task lists

**What's not done yet:**
- Phase 4 onward (wire dashboard to real data, task CRUD, inventory, POS, reports, etc.)

---

## Where We Are

**Current phase:** Phase 4 — Dashboard (not started)

**Immediate next tasks:**
1. Wire dashboard summary cards to real DB queries
2. Wire charts to real sales/category data
3. Task CRUD (create, edit, delete tasks with repetition support)
4. Task lists with status management (pending, upcoming, scheduled)

---

## Session Log — 2026-04-01 (Phase 3)

**Roadmap restructured** — Rewrote roadmap from 8 phases to 12, mapping each admin nav section from the adminer features spec to its own phase.

**Color palette updated** — Replaced teal primary with purple, refined orange/amber accent in `resources/css/app.css` to match `resources/assets/color-palette.png`. Removed `dark-*` scale.

**Typography** — Changed font from Instrument Sans to Libre Franklin (Google Fonts), loaded in both layout files.

**Dependencies installed via npm** (replaced CDNs):
- `alpinejs` — imported in `app.js`
- `chart.js` — imported in `app.js`, exposed as `window.Chart`

**Icon files** — 9 white SVG icons created in `public/icons/` (dashboard, ventas, inventario, reportes, negocio, usuarios, tareas, logout, menu).

**Specs updated:**
- `docs/specs/ui_design.md` — New color scheme, sidebar specs, nav items (replaced "Financiero" with "Tareas"), typography section
- `docs/roadmap.md` — Phase 3 marked complete

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

**UI/Frontend decisions**
- Admin sidebar extracted as a separate Blade component (`components/admin/sidebar.blade.php`)
- Dashboard split into 3 partials: `summary-cards`, `stats`, `tasks`
- Alpine.js and Chart.js installed via npm, not CDN
- SVG icons stored as individual files in `public/icons/` with `stroke="white"` hardcoded (since `<img>` tags can't inherit `currentColor`)
- Font: Libre Franklin via Google Fonts CDN, defined as `--font-sans` in Tailwind `@theme`

---

## Watch Out For

- **Always use `/opt/lampp/bin/php artisan`**, not `php artisan` — the system PHP will fail with `could not find driver`
- **`business_settings` is a singleton** — seeders and any settings form must assume exactly one row; never insert a second one
- **Currency symbol must never be hardcoded** — always read from `business_settings.currency_symbol`
- **`role` and `is_active` live on the `users` table** — Breeze's default scaffolding does not know about these; any generated auth views or redirects will need to be customised to handle role-based routing
- **No `status` column on `sales` yet** — void/refund (Phase 5) will require adding a `status` enum (`completed`, `voided`, `refunded`); worth adding as an early migration before Phase 5 work begins
- **`mbstring` and `pdo_mysql` extensions** must be enabled in `/etc/php/8.3/cli/php.ini` — both were missing on this machine and had to be installed via `apt`
- **Dashboard data is fictional** — All summary cards, charts, and task lists use hardcoded data; must be wired to real queries in Phase 4
- **No Eloquent models yet** besides `User.php` — Product, Category, Sale, SaleItem, BusinessSettings models need to be created when building CRUD features
