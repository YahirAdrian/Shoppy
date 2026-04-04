## Shoppy — Build Roadmap

### Phase 1 — Project Foundation
- [x] **Laravel install** — Fresh Laravel project setup with `.env` configured for MySQL
- [x] **Install dependencies** — TailwindCSS, Laravel Breeze (for auth scaffolding)
- [x] **Database setup** — Create the MySQL database and verify connection
- [x] **Run migrations** — Generate and run all migration files based on the schema
- [x] **Seed database** — Seeders for default admin user and `business_settings` row

---

### Phase 2 — Auth & Roles
- [x] **Login page** — Build the shared login form UI
- [x] **Role middleware** — Create `role:admin` and `role:seller` middleware
- [x] **Route protection** — Apply middleware to `/admin` and `/pos` route groups
- [x] **Post-login redirect** — Admin goes to `/admin/dashboard`, seller goes to `/pos`
- [x] **Logout** — Wire up logout and redirect to `/login`

---

### Phase 3 — Admin Layout & Navigation
- [x] **Admin layout** — Shared Blade layout with sidebar nav (Dashboard, Ventas, Inventario, Reportes, Usuarios, Negocio, Tareas)
- [x] **Sidebar navigation** — Collapsible sidebar with icons, active state highlighting, and mobile responsive menu
- [x] **Dashboard content** — Summary cards (ventas, productos, pendientes), statistics charts (line + doughnut), and actions/tasks sections

---

### Phase 4 — Inventory (Products & Categories)
- [x] **Inventory page with tabs** — Tab-based UI to switch between Products and Categories
- [x] **Products grid layout (default)** — Products grouped by category sections, displayed as cards with image header, product info as key-value, kebab menu for edit/delete. 20 products per page, categories not split across pages
- [x] **Products table layout** — Table view with all product info, preview button, kebab menu for edit/delete
- [x] **Layout toggle** — Switch between grid and table views (persisted preference)
- [x] **Product CRUD** — Create, edit, delete products with category assignment and image upload
- [x] **Manual stock adjustment** — Form to add/remove stock with a reason note
- [x] **Categories grid** — Grid card layout showing category name, product count, and CRUD actions
- [x] **Category CRUD** — Create, edit, delete categories

---

### Phase 5 — Sales
- [x] **Sales table page** — Paginated table (30 per page) in chronological order (recent first) with all sale info
- [x] **Sale detail modal** — Button to view full sale breakdown in a modal

---

### Phase 6 — Reports
- [x] **Report generator** — Page with filter controls: period (daily, weekly, monthly, yearly, custom dates), product selection, category filter
- [x] **Report preview** — Table-format previsualization of the generated report
- [x] **Report print** — Print-friendly layout for the previsualized report

---

### Phase 7 — Users
- [ ] **Users CRUD** — List, create, edit, deactivate admin and seller accounts

---

### Phase 8 — Business Settings
- [x] **Business settings page** — Form to update business name, logo, currency, low stock threshold, receipt header/footer

---

### Phase 9 — Tasks (standalone page)
- [ ] **Tasks page** — Full ToDo list management for admin tasks (mirrors dashboard task section with expanded UI)

---

### Phase 10 — Shoppy Sales (POS Mode)
- [ ] **POS layout** — Clean, touch-friendly Blade layout for the seller terminal
- [ ] **POS terminal page** — Product grid + cart panel side by side
- [ ] **Add to cart** — Select products, adjust quantities in the cart
- [ ] **Checkout & charge** — Enter amount tendered, calculate change, confirm sale
- [ ] **Save sale to DB** — Persist sale, sale items, and stock movement records on checkout
- [ ] **Receipt screen** — Post-sale summary screen with print option

---

### Phase 11 — Polish & QA
- [ ] **Form validation** — Server-side validation with user-friendly error messages on all forms
- [ ] **Flash messages** — Success/error feedback after every action
- [ ] **Low stock alerts** — Visual warning on dashboard and product list for low stock items
- [ ] **Empty states** — Friendly UI for empty tables and no-results scenarios
- [ ] **Responsive check** — Verify admin UI on tablet, POS UI on tablet/touch screen
- [ ] **Final QA pass** — Walk through every route and feature end-to-end