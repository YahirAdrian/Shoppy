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
- [ ] **Admin layout** — Shared Blade layout with sidebar nav (Dashboard, Ventas, Inventario, Reportes, Usuarios, Negocio, Tareas)
- [ ] **Sidebar navigation** — Collapsible sidebar with icons, active state highlighting, and mobile responsive menu
- [ ] **Top bar** — Business name, logged-in user info, and logout action

---

### Phase 4 — Dashboard
- [ ] **Dashboard blocks** — Grid cards for: sales summary (daily/weekly/monthly via select), top 3 sold + low stock products, pending actions list
- [ ] **Sales stats chart** — Line chart showing monthly sales
- [ ] **Category earnings chart** — Pie chart showing total earnings per product category
- [ ] **Tasks section** — Admin ToDo list with CRUD: task name, date, repetition (daily, weekly, monthly, every X days, every X of month), linked action
- [ ] **Task lists** — Pending, upcoming, and scheduled task lists with status management

---

### Phase 5 — Inventory (Products & Categories)
- [ ] **Inventory page with tabs** — Tab-based UI to switch between Products and Categories
- [ ] **Products grid layout (default)** — Products grouped by category sections, displayed as cards with image header, product info as key-value, kebab menu for edit/delete. 20 products per page, categories not split across pages
- [ ] **Products table layout** — Table view with all product info, preview button, kebab menu for edit/delete
- [ ] **Layout toggle** — Switch between grid and table views (persisted preference)
- [ ] **Product CRUD** — Create, edit, delete products with category assignment and image upload
- [ ] **Manual stock adjustment** — Form to add/remove stock with a reason note
- [ ] **Categories grid** — Grid card layout showing category name, product count, and CRUD actions
- [ ] **Category CRUD** — Create, edit, delete categories

---

### Phase 6 — Sales
- [ ] **Sales table page** — Paginated table (30 per page) in chronological order (recent first) with all sale info
- [ ] **Sale detail modal** — Button to view full sale breakdown in a modal

---

### Phase 7 — Reports
- [ ] **Report generator** — Page with filter controls: period (daily, weekly, monthly, yearly, custom dates), product selection, category filter
- [ ] **Report preview** — Table-format previsualization of the generated report
- [ ] **Report print** — Print-friendly layout for the previsualized report

---

### Phase 8 — Users
- [ ] **Users CRUD** — List, create, edit, deactivate admin and seller accounts

---

### Phase 9 — Business Settings
- [ ] **Business settings page** — Form to update business name, logo, currency, tax rate, receipt note

---

### Phase 10 — Tasks (standalone page)
- [ ] **Tasks page** — Full ToDo list management for admin tasks (mirrors dashboard task section with expanded UI)

---

### Phase 11 — Shoppy Sales (POS Mode)
- [ ] **POS layout** — Clean, touch-friendly Blade layout for the seller terminal
- [ ] **POS terminal page** — Product grid + cart panel side by side
- [ ] **Add to cart** — Select products, adjust quantities in the cart
- [ ] **Checkout & charge** — Enter amount tendered, calculate change, confirm sale
- [ ] **Save sale to DB** — Persist sale, sale items, and stock movement records on checkout
- [ ] **Receipt screen** — Post-sale summary screen with print option

---

### Phase 12 — Polish & QA
- [ ] **Form validation** — Server-side validation with user-friendly error messages on all forms
- [ ] **Flash messages** — Success/error feedback after every action
- [ ] **Low stock alerts** — Visual warning on dashboard and product list for low stock items
- [ ] **Empty states** — Friendly UI for empty tables and no-results scenarios
- [ ] **Responsive check** — Verify admin UI on tablet, POS UI on tablet/touch screen
- [ ] **Final QA pass** — Walk through every route and feature end-to-end