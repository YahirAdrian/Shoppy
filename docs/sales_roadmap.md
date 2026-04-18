# Shoppy Sales — Build Roadmap

### Phase 1 — POS Layout & Navigation Shell
- [x] **POS layout** — Blade layout component (`components/layouts/pos.blade.php`) with narrow icon-only sidebar (`w-20`), title "Shoppy Sales - Punto de Venta"
- [x] **POS sidebar** — Icon-only sidebar (`components/pos/sidebar.blade.php`) with user avatar, 3 nav links (Venta, Buscar, Estado), logout button. Stone-800 bg, primary-800 active state
- [x] **SVG icons** — 3 white SVG icons in `public/icons/`: `pos-venta.svg`, `pos-buscar.svg`, `pos-estado.svg`
- [x] **Controllers** — `Pos/PosController.php` (3 view methods: sale, search, status) and `Pos/PosApiController.php` (stub for JSON endpoints)
- [x] **Routes** — Expand POS route group: page routes (`/pos/venta`, `/pos/buscar`, `/pos/estado`), API routes (`/pos/api/products`, `/pos/api/sales`, `/pos/api/admin-auth`)
- [x] **Placeholder views** — `pos/sale.blade.php`, `pos/search.blade.php`, `pos/status.blade.php` using POS layout

---

### Phase 2 — Sale Page (Product Search + Cart)
- [x] **Search bar** — Product barcode/name input with 0.8s debounce, fetches `GET /pos/api/products?q=...`. Auto-adds if 1 result, shows horizontal scrollable cards if multiple, error if none
- [x] **Product search endpoint** — `PosApiController::searchProducts()`: search by name LIKE or exact barcode, active products only, returns JSON (id, name, barcode, selling_price, stock, category, image), limit 20
- [x] **Cart table** — Producto, Cantidad (editable), Categoría, Código, Descuento, Subtotal, Total, Eliminar button. Total row at bottom. All state in Alpine.js
- [x] **localStorage persistence** — Save cart on every change, restore on page init
- [x] **Payment modal** — Payment method (cash only enabled), amount tendered input, live change calculation, optional fields (customer name, note) hidden by default, "Registrar venta" button
- [x] **Stock warning modal** — Danger-style modal when product has insufficient stock, option to proceed anyway
- [x] **Action buttons** — "Reiniciar venta" (clear cart + localStorage), "Confirmar venta" (open payment modal)

---

### Phase 3 — Sale Creation Backend
- [ ] **Store sale endpoint** — `PosApiController::storeSale()` wrapped in `DB::transaction`: validate items/payment, create Sale + SaleItems (snapshot product_name, unit_price), decrement Product.stock, create StockMovement records (action='sale'), return created sale as JSON
- [ ] **Stock validation** — If insufficient stock and `force_low_stock` is false, return 422 with problematic products list

---

### Phase 4 — Receipt Printing
- [ ] **Client-side receipt** — After successful sale POST, build receipt HTML (business name, date, sale #, items table, totals, receipt footer from business_settings), open in new window with `window.print()`

---

### Phase 5 — Product Search Page
- [ ] **Search page UI** — Search bar + product/category toggle, category pills (horizontal scroll), product grid (30/page) with cards showing image, name, price, stock warnings (yellow low, red zero), "Agregar producto" button
- [ ] **Enhanced search endpoint** — Add `?category_id=` and `?page=` params to `searchProducts()`
- [ ] **Add to sale flow** — Save product to localStorage, redirect to `/pos/venta?added=1` with success alert

---

### Phase 6 — POS Status Page
- [ ] **Session stats** — Grid of 4 cards: seller name, total sales count, total sold, average ticket (server-rendered from today's sales)
- [ ] **Sales history table** — Sale #, time, subtotal, discount, payment method, total, note, preview button, delete button (admin-only)
- [ ] **Sale preview modal** — Fetch sale detail via AJAX, show items table
- [ ] **Admin auth** — Padlock button opens modal for admin email + password. 15-min timeout, cleared on tab close / page navigation / `visibilitychange`
- [ ] **Admin operations** — Money withdrawal input, end session button (disabled until all money withdrawn). Delete sale in table (requires admin auth)

---

### Phase 7 — Tests
- [ ] **PosPageAccessTest** — Seller access OK, admin redirected, guest redirected to login
- [ ] **PosProductSearchTest** — Search by name, barcode, active products only
- [ ] **PosSaleCreationTest** — Valid sale, stock decrement, stock movements, insufficient stock handling, change calculation
- [ ] **PosStatusTest** — Stats accuracy, admin auth validation, sale deletion with stock restore
