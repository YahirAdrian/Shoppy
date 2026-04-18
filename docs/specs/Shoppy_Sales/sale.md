# POS - Sale

## General features
- Users can add products to a sale. 
- Products added are listed in a table. Total and subtotal are displayed below the table. 
- Register sale button and payment Modal.
- Receipt printing

## Input
- Product barcode || product name (required, string)
- Quantity (required, default: 1.00, decimal)
- List of products added to the table (products array[product, quantity])


## View
- The prouct search or barcode input is on the top of the page. Once a value is input, the results display in the screen.
- Below the table, the product preview is shown if there's more than one products searched. Product preview contains: Image, name, price, add button. Preview list is a one-line card list with an overflow-x-scroll display.
- Sale products table display the product's name,category, barcode, quantity (editable), subtotal, discount, total, remove item button. The table has a total row at the end of all products.
- Reset sale button
- Confirm sale button triggers a payment modal
- Payment modal includes: Payment (cash, card, other [only cash enabled]), quantity input, costumer's name (optional), note (optional) change calculation. Optional fields are hidden and expandable with a show more button.

## Rules
- If a product searched doesn't exist, show a message error.
- Add some delay of 0.8 seconds after entering values before displaying the products in the results display.
- If there's only one product in the product search, add it to the sale automatically.
- Increase the product quantity if a product is repeated in the sale.
- When a sale is completed, update the stock of products
- Store temporarily sale data in localstorage. Remove data when sale is completed.

## Edge cases
- A product doesn't have enough stock: Don't proceed with the sale, show a danger-type modal to ask to poceed anyways.

## Output
- A sale is created
- Receipt is printed