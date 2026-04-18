# POS - Product Search 

## General features
- Search products manually.
- Search by category
- Products displayed in cards format.
- Button to add products to the sale.

## Input
- Product code or name
- Product / category search switch

## View
- An expanded page of the product search in @docs/specs/Shoppy_Sales/sale.md.
- The products display becomes multi-line grid with 30 products per page.

## Rules
- If a product searched doesn't exist, show a message error.
- If a product is low stock, show yellow-warning alert below the price.
- If a product has no available stock, show a red-warning alert below the price.
- When a product is added, navigate to the sale page and show a product added alert.

## Edge cases
- A product doesn't have enough stock: Don't proceed with the sale, show a danger-type modal to ask to poceed anyways.

## Output
- Product search page to add products to the sale.