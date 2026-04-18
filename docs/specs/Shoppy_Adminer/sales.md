# Sales Page — Specifications

## Overview

The sales page (`/admin/ventas`) displays all completed sales in a paginated table. It is a read-only view for the admin to review sales history and inspect individual sale details.

Sales are created from the POS terminal (Shoppy Sales) — this page only displays them.

## Database Schema

### `sales` table

| Column           | Type              | Notes                            |
|------------------|-------------------|----------------------------------|
| id               | bigint (PK)       | Auto-increment                   |
| user_id          | foreignId          | Seller who made the sale         |
| customer_name    | string (nullable) | Optional customer name           |
| subtotal         | decimal(10,2)     | Sum of all item subtotals        |
| discount_amount  | decimal(10,2)     | Discount applied to the sale     |
| payment_method   | enum              | `cash` or `card`                 |
| amount_tendered  | decimal(10,2)     | Amount the customer paid         |
| change_given     | decimal(10,2)     | Change returned to the customer  |
| note             | text (nullable)   | Optional sale note               |
| created_at       | timestamp         | Sale date                        |

### `sale_items` table

| Column          | Type           | Notes                                  |
|-----------------|----------------|----------------------------------------|
| id              | bigint (PK)    | Auto-increment                         |
| sale_id         | foreignId       | Parent sale                            |
| product_id      | foreignId       | Product sold                           |
| product_name    | string         | Snapshot of product name at sale time  |
| unit_price      | decimal(10,2)  | Price per unit at sale time            |
| quantity        | decimal(10,2)  | Quantity sold                          |
| discount_amount | decimal(10,2)  | Discount on this item                 |
| subtotal        | decimal(10,2)  | (unit_price * quantity) - discount     |

## Sales Table

### Layout
- Full-width table inside the admin layout
- Page header: "Ventas" title with a subtitle

### Table Columns

| Column          | Source                          | Format                        |
|-----------------|---------------------------------|-------------------------------|
| #               | `sale.id`                       | Numeric                       |
| Fecha           | `sale.created_at`               | `d/m/Y H:i`                  |
| Vendedor        | `sale.user.name`                | Text                          |
| Cliente         | `sale.customer_name`            | Text or "—" if null           |
| Productos       | `sale.items_count`              | Numeric (count of items)      |
| Subtotal        | `sale.subtotal`                 | Currency formatted            |
| Descuento       | `sale.discount_amount`          | Currency formatted            |
| Total           | `subtotal - discount_amount`    | Currency formatted, bold      |
| Método de pago  | `sale.payment_method`           | Badge: "Efectivo" / "Tarjeta" |
| Acciones        | —                               | "Ver detalle" button          |

### Behavior
- Ordered by `created_at` descending (most recent first)
- Paginated at 30 rows per page
- Empty state shown when no sales exist

## Sale Detail Modal

Triggered by the "Ver detalle" button on each table row.

### Header Section
- Sale ID and date
- Seller name
- Customer name (if provided)
- Payment method badge
- Note (if provided)

### Items Table

| Column     | Content                        |
|------------|--------------------------------|
| Producto   | `item.product_name`           |
| Cant.      | `item.quantity`                |
| P. Unitario| `item.unit_price` (currency)  |
| Descuento  | `item.discount_amount` (currency) |
| Subtotal   | `item.subtotal` (currency)    |

### Totals Section
- Subtotal
- Descuento total
- **Total** (bold, larger)
- Monto recibido
- Cambio

## Models

### Sale
- `belongsTo` User (seller)
- `hasMany` SaleItem
- Computed: `total` = `subtotal - discount_amount`

### SaleItem
- `belongsTo` Sale
- `belongsTo` Product

## Route

- `GET /admin/ventas` — `SaleController@index` — name: `admin.sales.index`
