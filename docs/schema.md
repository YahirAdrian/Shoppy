## Database Schema — Shoppy

> **Seeder convention:** All AI-generated seed data must be in Spanish.



### `business_settings`
Singleton row holding the store's global configuration.

| Column | Type | Notes |
|---|---|---|
| `id` | bigint | PK |
| `business_name` | varchar | |
| `logo` | varchar | file path/URL |
| `address` | varchar | |
| `phone` | varchar | |
| `email` | varchar | |
| `currency_symbol` | varchar | |
| `low_stock` | mediumint | default threshold for low-stock alerts |
| `receipt_header` | text | |
| `receipt_footer` | text | |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

---

### `users`
Stores both admin and seller accounts.

| Column | Type | Notes |
|---|---|---|
| `id` | bigint | PK |
| `name` | varchar | |
| `email` | varchar | |
| `role` | enum | `admin`, `seller` |
| `is_active` | boolean | soft-disable accounts |
| `remember_token` | varchar | Laravel auth |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

---

### `categories`
Product groupings.

| Column | Type | Notes |
|---|---|---|
| `id` | bigint | PK |
| `name` | varchar | |
| `description` | text | |
| `is_active` | boolean | |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

---

### `products`
Inventory items for sale.

| Column | Type | Notes |
|---|---|---|
| `id` | bigint | PK |
| `category_id` | bigint | FK → `categories.id` |
| `name` | varchar | |
| `sku` | varchar | unique |
| `barcode` | varchar | unique |
| `description` | text | |
| `cost_price` | decimal | |
| `selling_price` | decimal | |
| `stock` | decimal | current quantity on hand |
| `low_stock_alert` | decimal | nullable; per-product override |
| `unit` | varchar | e.g. `pcs`, `kg` |
| `image` | varchar | file path/URL |
| `is_active` | boolean | |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

---

### `sale`
Header record for each transaction.

| Column | Type | Notes |
|---|---|---|
| `id` | bigint | PK |
| `user_id` | bigint | FK → `users.id` (seller) |
| `customer_name` | varchar | nullable |
| `subtotal` | decimal | before discount |
| `discount_amount` | decimal | |
| `payment_method` | enum | e.g. `cash`, `card` |
| `amount_tendered` | decimal | cash given by customer |
| `change_given` | decimal | |
| `note` | text | |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

---

### `sale_item`
Line items belonging to a sale.

| Column | Type | Notes |
|---|---|---|
| `id` | bigint | PK |
| `sale_id` | bigint | FK → `sale.id` |
| `product_id` | bigint | FK → `products.id` |
| `product_name` | varchar | snapshot at time of sale |
| `unit_price` | decimal | snapshot at time of sale |
| `quantity` | decimal | |
| `discount_amount` | decimal | per-line discount |
| `subtotal` | decimal | `unit_price × quantity − discount` |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

---

### `stock_movement`
Audit log of every stock change (sales, manual adjustments, returns).

| Column | Type | Notes |
|---|---|---|
| `id` | bigint | PK |
| `user_id` | bigint | nullable FK → `users.id` |
| `product_id` | bigint | FK → `products.id` |
| `action` | enum | e.g. `sale`, `adjustment`, `return` |
| `quantity` | int | positive = added, negative = removed |
| `note` | text | nullable |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

---

**Relationships summary**

- `products` → `categories` (many-to-one)
- `sale` → `users` (many-to-one, seller who rang the sale)
- `sale_item` → `sale` (many-to-one)
- `sale_item` → `products` (many-to-one)
- `stock_movement` → `products` (many-to-one)
- `stock_movement` → `users` (many-to-one, nullable)
