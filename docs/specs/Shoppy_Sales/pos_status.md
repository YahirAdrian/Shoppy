# POS - POS Status

## General features
- Display session status (Name, total sales, total sold, average ticket)
- Sale history: Show all the sales made
- Admin operations (admin password required): Money withdrawal, end session.

## View
- Session status section, display as a dashboard grid.
- Show all the sales made in a table format: Sale number, time, subtotal, discount, payment method, total, note, preview button, delete button (admin operation).
- Preview modal: Shows the sale details as in @docs/specs/Shoppy_Sales/sale.md products table.
- Delete button triggers a confirmation dialog with a danger style.
- Admin operations are in a separate section at the bottom of the page. A padlock button with the "Admin" text is at the beginning of the section. Admin operations are disabled unless it is unlocked.

## Rules
- Removing sales is only allowed if the admin has authorized it.
- Admin operations are only available while the user is on this page. The operations become unavailable when: The tab is closed, user goes to another page even when pos_status page is in another tab, or time limit is exceeded (15 minutes).
- Show a message error if the user attempts to execute an admin action without authorization.
- Session can only be ended when all money is withdrawn.

