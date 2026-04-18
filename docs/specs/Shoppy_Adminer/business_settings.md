# Phase 8: Business Settings

## Context
The admin panel core pages (dashboard, inventory, sales, reports) are complete. The sidebar already references `admin.business.edit`. The `business_settings` table is a singleton row seeded by `BusinessSettingsSeeder`. This page lets the admin personalize the application for their business.

Only edit/update actions are needed — no create or delete (the row is seeded on install).

---

## Data Structure

All fields map to the existing `business_settings` table:

| Field | Type | Required | Notes |
|-------|------|----------|-------|
| `business_name` | string (max 255) | Yes | Nombre del negocio, shown in receipts and app header |
| `logo` | image file | No | Logo del negocio. Stored in `storage/logos/`. Displayed in receipts and sidebar |
| `address` | string (max 255) | No | Direccion del negocio, printed on receipts |
| `phone` | string (max 50) | No | Telefono de contacto |
| `email` | string (max 255) | No | Correo electronico de contacto |
| `currency_symbol` | string (max 10) | Yes | Simbolo de moneda (e.g. "$", "Q"). Used across all price displays |
| `low_stock` | integer (min 1) | Yes | Umbral global de stock bajo. Products without a per-product override use this value for low-stock alerts. Default: 5 |
| `receipt_header` | text | No | Texto que aparece en la parte superior del ticket de venta |
| `receipt_footer` | text | No | Texto que aparece en la parte inferior del ticket de venta |

---

## Page Layout

Single form page organized in three card sections:

### Seccion 1 — Informacion del negocio
- **Nombre del negocio**: text input (required)
- **Logo**: image upload with thumbnail preview of current logo. Includes option to remove existing logo
- **Direccion**: text input
- **Telefono**: text input
- **Correo electronico**: email input

### Seccion 2 — Configuracion de moneda e inventario
- **Simbolo de moneda**: text input (required). The symbol used throughout the app for all prices
- **Umbral de stock bajo**: number input (required, min 1). Global default for low-stock alerts across all products that don't have a per-product override

### Seccion 3 — Ticket de venta
- **Encabezado del ticket**: multiline textarea. Printed at the top of sale receipts
- **Pie del ticket**: multiline textarea. Printed at the bottom of sale receipts

### Submit
Single "Guardar cambios" button. On success, redirects back to the same page with a success flash message.

---

## Implementation Steps

### Step 1 — Routes
Add to admin group in `routes/web.php`:
```
GET  /negocio  → BusinessSettingController@edit   (name: business.edit)
PUT  /negocio  → BusinessSettingController@update  (name: business.update)
```

### Step 2 — Controller
`app/Http/Controllers/Admin/BusinessSettingController.php` with two methods:

- **edit()** — Load the singleton row, return the form view
- **update()** — Validate all fields (Spanish messages), handle logo upload/removal, save, redirect with success flash

Logo handling follows the same pattern as `ProductController`:
- New upload: delete old file, store new in `logos/` subdirectory
- Remove checkbox: delete file, set field to null
- No change: leave as-is

### Step 3 — View
```
resources/views/admin/business/
└── edit.blade.php   ← Settings form with three card sections
```

Uses `<x-layouts.admin>` wrapper, flash messages pattern from inventory page, standard form field styling, and Alpine.js for logo preview.

---

## Verification
1. Navigate to `/admin/negocio` — page loads with current settings populated
2. Edit fields and submit — redirects back with success flash, values persisted
3. Upload a new logo — thumbnail appears, file stored in `storage/app/public/logos/`
4. Check "Eliminar logo" and submit — logo removed
5. Submit with invalid data (empty business_name) — validation errors in Spanish
6. Sidebar "Negocio" link is active/highlighted on this page
