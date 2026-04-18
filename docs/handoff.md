# Shoppy — Session Handoff

## Current State

Phases 1–9 are complete. Phase 4 (Inventory) was the last feature **browser QA'd**.

**What's working (Phases 1–3):**
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

**What's built but needs browser QA (Phase 4 — Inventory):**
- Full inventory page at `/admin/inventario` with tab-based UI (Products / Categories)
- Products: grid layout (cards grouped by category) + table layout with localStorage toggle
- Product CRUD via Alpine.js modals with image upload, Spanish validation messages
- Category CRUD via Alpine.js modals with product count badges
- Stock adjustment modal with live preview of new stock, creates StockMovement records
- Low stock badges, flash messages, empty states
- Currency symbol pulled from `business_settings`, never hardcoded
- Storage symlink created (`php artisan storage:link`)

**Phase 5 — Sales (built):**
- Sales table at `/admin/ventas` — paginated (30/page), chronological order
- Sale detail modal with full breakdown

**Phase 6 — Reports (built):**
- Report generator at `/admin/reportes` with filter controls (period, product, category)
- Table-format preview and print-friendly layout

**Phase 8 — Business Settings (built):**
- Settings form at `/admin/negocio` with three sections: business info (name, logo, address, phone, email), currency & inventory (currency symbol, low stock threshold), receipt text (header/footer)
- Logo upload with preview/remove, Spanish validation, flash messages

**Phase 7 — Users (built):**
- Users table at `/admin/usuarios` with role/status badges, kebab menu (edit, activate/deactivate)
- Create/edit user modal with role select and password management
- Admin self-protection: can't deactivate self or change own role
- Inactive users blocked at login with error message

**Phase 9 — Tasks (built):**
- Tasks page at `/admin/tareas` with pending/completed sections
- Task CRUD with due dates and recurring tasks (daily, weekly, monthly with custom intervals)
- Completing a recurring task auto-creates the next occurrence
- Overdue tasks highlighted in red

**What's not done yet:**
- Phase 10 — Shoppy Sales (POS Mode)
- Phase 11 — Polish & QA
- Dashboard still uses fictional data — not wired to real queries

---

## Where We Are

**Current phase:** Shoppy Sales — Phase 3 (Sale Creation Backend)

**Shoppy Sales — Phase 2 complete (Sale Page UI):**
- `app/Http/Controllers/Pos/PosApiController.php` — `searchProducts()` returns active products filtered by barcode (exact) or name (LIKE), limit 20, JSON shape `{products: [{id, name, barcode, selling_price, stock, unit, category, category_id, image}]}`
- `resources/views/pos/sale.blade.php` — full Alpine.js page: search bar (0.8s debounce, auto-add single result, scrollable cards for multiple), cart table with editable quantity/discount, live totals, localStorage persistence under key `pos_cart`, reset + confirm buttons
- `resources/views/pos/partials/payment-modal.blade.php` — method selector (cash only active), tendered input, live change calc, collapsible optional fields (customer, note)
- `resources/views/pos/partials/stock-warning-modal.blade.php` — red-styled modal showing insufficient-stock items, "continue anyway" flow
- `submitSale()` intentionally stubbed (alert) — Phase 3 wires it to `POST /pos/api/sales`

**Immediate next tasks:**
1. Phase 3 — Sale creation backend (`storeSale()` with DB::transaction, stock decrement, StockMovement rows)
2. Phase 4 — Receipt printing
3. Phases 5–7 — Search page, status page, tests
4. Adminer Phase 10 — Polish & QA

---

## Session Log — 2026-04-04 (Phase 7 — Users & Phase 9 — Tasks)

**Phase 7 — Users CRUD:**
- `app/Models/User.php` — added `role`, `is_active` to `$fillable`
- `app/Http/Controllers/Admin/UserController.php` — `index()`, `store()`, `update()`, `toggleActive()` with self-protection rules
- `resources/views/admin/users/index.blade.php` — Table with role/status badges, kebab menus with fixed positioning
- `resources/views/admin/users/user-form-modal.blade.php` — Create/edit modal
- `app/Http/Controllers/Auth/LoginController.php` — Added `is_active` check after authentication
- Routes: 4 routes under `/admin/usuarios`
- Spec: `docs/specs/users.md`

**Phase 9 — Tasks:**
- `database/migrations/2026_04_04_213646_create_tasks_table.php` — tasks table with repeat fields
- `app/Models/Task.php` — `isRecurring()`, `isOverdue()`, `calculateNextDueDate()` helpers, `repeat_interval` cast to integer
- `app/Http/Controllers/Admin/TaskController.php` — full CRUD + toggle with recurring task auto-creation
- `resources/views/admin/tasks/index.blade.php` — Pending/completed sections with task cards
- `resources/views/admin/tasks/task-form-modal.blade.php` — Create/edit modal with repeat controls
- Routes: 5 routes under `/admin/tareas`
- Spec: `docs/specs/tasks_page.md`

**Bug fixes:**
- Added global `[x-cloak] { display: none !important; }` to `resources/css/app.css` to prevent modal flash on page load (affected Inventory and all pages with modals)

---

## Session Log — 2026-04-03 (Phase 8 — Business Settings)

**Controller created (1):**
- `app/Http/Controllers/Admin/BusinessSettingController.php` — `edit()`, `update()` with logo upload/removal, Spanish validation messages

**Routes added** — 2 routes under `/admin/negocio` in `routes/web.php` (`GET` edit, `PUT` update)

**Views created (1):**
- `resources/views/admin/business/edit.blade.php` — Settings form with 3 card sections (business info, currency & inventory, receipt text), logo preview with Alpine.js, flash messages

**Docs:**
- `docs/specs/business_settings.md` — Full spec with data structure, page layout, implementation steps
- `docs/specs/adminer_features.md` — Business section fleshed out with field descriptions
- `docs/roadmap.md` — Phase 8 marked complete

---

## Session Log — 2026-04-02 (Phase 4 — Inventory)

**Models created (4):**
- `app/Models/Category.php` — `products()` hasMany, `is_active` cast
- `app/Models/Product.php` — `category()` belongsTo, `stockMovements()` hasMany, `isLowStock()` helper, decimal casts
- `app/Models/StockMovement.php` — `product()` and `user()` belongsTo
- `app/Models/BusinessSetting.php` — fillable for all settings columns

**Controllers created (2):**
- `app/Http/Controllers/Admin/ProductController.php` — `index()`, `store()`, `update()`, `destroy()`, `adjustStock()`
- `app/Http/Controllers/Admin/CategoryController.php` — `store()`, `update()`, `destroy()`

**Routes added** — 9 routes under `/admin/inventario` in `routes/web.php`

**Views created (7):**
- `resources/views/admin/inventory/index.blade.php` — Main page with Alpine.js tabs, layout toggle, flash messages, delete confirmation modal
- `resources/views/admin/inventory/products-grid.blade.php` — Cards grouped by category with kebab menus, low stock badges, image placeholders
- `resources/views/admin/inventory/products-table.blade.php` — Full table with all product columns and kebab menus
- `resources/views/admin/inventory/categories.blade.php` — Grid cards with product count and CRUD actions
- `resources/views/admin/inventory/product-form-modal.blade.php` — Create/edit product modal with image upload
- `resources/views/admin/inventory/category-form-modal.blade.php` — Create/edit category modal
- `resources/views/admin/inventory/stock-adjustment-modal.blade.php` — Stock adjustment with live new-stock preview

**Docs:**
- `docs/specs/inventory.md` — Full implementation plan saved

**Infrastructure:**
- `php artisan storage:link` executed for product image uploads
- `app/Http/Controllers/Admin/` directory created for admin-namespaced controllers

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
- **Eloquent models created so far**: `User`, `Category`, `Product`, `StockMovement`, `BusinessSetting`, `Sale`, `SaleItem`, `Task`
- **Inventory page not yet QA'd in browser** — all code is written but needs visual review and end-to-end CRUD testing
- **File uploads** require `storage:link` (already run) and the `public` disk configured — product images in `storage/app/public/products/`, business logos in `storage/app/public/logos/`
