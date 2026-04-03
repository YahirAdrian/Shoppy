# Phase 4: Inventory (Products & Categories)

## Context
The admin shell (layout, sidebar, dashboard) is complete. The inventory page is the first core business feature. Database migrations and seeders already exist for categories (6) and products (35). No models or controllers exist yet. The sidebar already references `admin.inventory.index`.

---

## Implementation Steps

### Step 1 — Models
Create 3 models (no service classes needed — CRUD is straightforward):

| File | Key details |
|------|-------------|
| `app/Models/Category.php` | fillable: name, description, is_active. Has `products()` hasMany |
| `app/Models/Product.php` | fillable: all product columns. BelongsTo `category()`. Casts for decimals and boolean |
| `app/Models/StockMovement.php` | fillable: user_id, product_id, action, quantity, note. BelongsTo product & user |
| `app/Models/BusinessSetting.php` | fillable: all columns. Used to fetch `currency_symbol` |

### Step 2 — Routes & Controllers
**Routes** (add to admin group in `routes/web.php`):
```
GET    /inventario                          → inventory.index
POST   /inventario/productos                → products.store
PUT    /inventario/productos/{product}      → products.update
DELETE /inventario/productos/{product}      → products.destroy
POST   /inventario/productos/{product}/ajuste → products.adjust-stock
POST   /inventario/categorias               → categories.store
PUT    /inventario/categorias/{category}    → categories.update
DELETE /inventario/categorias/{category}    → categories.destroy
```

**Controllers:**
- `app/Http/Controllers/Admin/ProductController.php` — `index()`, `store()`, `update()`, `destroy()`, `adjustStock()`
- `app/Http/Controllers/Admin/CategoryController.php` — `store()`, `update()`, `destroy()`

The `index()` method serves the main inventory page for both tabs. It loads products grouped by category + categories with product count + currency symbol.

### Step 3 — Views
```
resources/views/admin/inventory/
├── index.blade.php              ← Main page: header, tabs, layout toggle, includes partials + modals
├── products-grid.blade.php      ← Products in card grid grouped by category
├── products-table.blade.php     ← Products in table layout
├── categories.blade.php         ← Category cards grid
├── product-form-modal.blade.php ← Shared create/edit product modal
├── category-form-modal.blade.php ← Shared create/edit category modal
└── stock-adjustment-modal.blade.php ← Stock adjustment modal
```

**UI structure (`index.blade.php`):**
- `<x-layouts.admin>` wrapper
- Page header: "Inventario" title + subtitle
- Alpine.js `x-data` managing: `tab` (products/categories), `layout` (grid/table from localStorage)
- Tab bar: two styled buttons (active = `primary-600` bg)
- Products tab: layout toggle icons + conditional `x-show` for grid/table partials
- Categories tab: category grid + "Agregar categoría" button

**Grid layout:** Products grouped by category sections. Cards with placeholder image area, key-value info (SKU, precio, stock, unidad), kebab menu for edit/delete. Low stock badge in red/orange.

**Table layout:** Standard table with columns (Nombre, SKU, Categoría, Precio Venta, Stock, Unidad, Estado). Kebab menu per row.

**Category cards:** Grid of cards showing name, description, product count badge, edit/delete kebab menu.

**Modals (Alpine.js):** All modals use `x-show` + `x-transition`. Forms submit via standard POST/PUT (not AJAX). Delete uses `@method('DELETE')`. Product form includes image upload with preview. Stock adjustment shows current stock + quantity input + note textarea.

### Step 4 — Image Storage
- Products stored via `$request->file('image')->store('products', 'public')`
- Run `php artisan storage:link` if not already done
- Display with `asset('storage/' . $product->image)` or placeholder

### Step 5 — Polish
- Flash messages for CRUD success/error
- Spanish validation messages
- Low stock visual indicators
- Empty state for no products/categories
- `?tab=categories` URL param for redirects after category CRUD

---

## Build Order
1. Models → 2. Routes + Controllers (index only) → 3. Main page shell with tabs → 4. Products grid → 5. Products table → 6. Categories tab → 7. Category CRUD → 8. Product CRUD → 9. Stock adjustment → 10. Polish

## Verification
- Navigate to `/admin/inventario` — see products in grid view grouped by category
- Toggle to table view — preference persists on reload
- Switch to categories tab — see 6 category cards with product counts
- Create/edit/delete a category — flash message, correct tab on redirect
- Create a product with image — appears in correct category section
- Edit/delete a product — works from both grid and table views
- Adjust stock — movement recorded, stock updated
- Check currency symbol comes from `business_settings`, not hardcoded
