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

### Phase 3 — Shoppy Adminer Core
- [ ] **Admin layout** — Shared blade layout with sidebar nav for all admin pages
- [ ] **Dashboard page** — Placeholder page with key stat cards (sales today, low stock, etc.)
- [ ] **Business settings page** — Form to update business name, logo, currency, tax, receipt note
- [ ] **Categories CRUD** — List, create, edit, delete categories
- [ ] **Products CRUD** — List, create, edit, delete products with category assignment
- [ ] **Manual stock adjustment** — Form to add/remove stock with a reason note
- [ ] **Users CRUD** — Admin can create, edit, deactivate seller accounts

---

### Phase 4 — Shoppy Sales (POS Mode)
- [ ] **POS layout** — Clean, touch-friendly blade layout for the seller terminal
- [ ] **POS terminal page** — Product grid + cart panel side by side
- [ ] **Add to cart** — Select products, adjust quantities in the cart
- [ ] **Checkout & charge** — Enter amount tendered, calculate change, confirm sale
- [ ] **Save sale to DB** — Persist sale, sale items, and stock movement records on checkout
- [ ] **Receipt screen** — Post-sale summary screen with print option

---

### Phase 5 — Sales History & Receipts
- [ ] **Admin sales list** — Paginated table of all transactions with filters
- [ ] **Sale detail page** — Full breakdown of a single sale
- [ ] **Void / refund actions** — Mark a sale as voided or refunded, reverse stock
- [ ] **Seller history page** — POS-side view of the seller's own past sales
- [ ] **Printable receipt** — Clean print-only blade view for receipts

---

### Phase 6 — Reports
- [ ] **Reports hub** — Landing page with links to each report type
- [ ] **Sales report** — Total sales filtered by date range
- [ ] **Inventory report** — Current stock levels across all products
- [ ] **Low stock report** — Products at or below their alert threshold
- [ ] **Expenses report** — Expenses summary filtered by date range
- [ ] **CSV export** — Export sales report as a downloadable CSV

---

### Phase 7 — Expenses
- [ ] **Expenses CRUD** — List, create, edit, delete expense entries

---

### Phase 8 — Polish & QA
- [ ] **Form validation** — Server-side validation with user-friendly error messages on all forms
- [ ] **Flash messages** — Success/error feedback after every action
- [ ] **Low stock alerts** — Visual warning on dashboard and product list for low stock items
- [ ] **Empty states** — Friendly UI for empty tables and no-results scenarios
- [ ] **Responsive check** — Verify admin UI on tablet, POS UI on tablet/touch screen
- [ ] **Final QA pass** — Walk through every route and feature end-to-end