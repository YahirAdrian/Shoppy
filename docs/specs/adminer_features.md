# Shoppy Adminer features

This document describes the features included in the application for the administration page and admin roles.

Each feature listed here in a second-level heading belongs to the pages shown in the navigation section of the admnin page. Third-level headings correspond to sections that belong to those pages.

These features described here are included in their corresponding admin page UI.


## Dashboard
The dashboard shows the summary of the business, sales stats, and pending actions.

### Dashboard blocks: Sales, products & stock status.

These three blocks are included in a dashboard grid card.

- **Sales**: Shows the number of daily/weekly/monthly sales and list the three most recents ones. The sale period is modified through a select component.
- **Products**: Shows the three most sold products, and three low stock products. Layout is divided in two columns.
- **Pending actions**: List all pending actions (if any) such as seller issued reports, low stock or scheduled pending task from the admin.

### Stats

- A line chart showing the monthly sales.
- A pie chart showing the total earnings of each product category.

### Tasks
This section is a ToDo list of actions created by the Admin. Each task in the task lists described should have their CRUD functionality.

- **Task creation**: A button to create tasks. Then a form is displayed to input task name, date, repetition (each X days, daily, weekly, monthly, every X of month), action that links to a.
- **Pending task list**: A list of pending tasks. 
- **Upcoming task list**: A list of upcoming tasks. 
- **Scheduled task list**: A list of scheduled tasks. 

## Sales

This page display the sales in a table format. The table displays all the sale info.

Sales table should:
- Display sales in chronological order: Recent to latest.
- Show 30 sales per page.
- Include a button to view sales detail in a modal.

## Inventory

This page shows all products and categories with CRUD functionality. A tab-based UI is used for this page to switch between products and categories.

### Products
Products are displayed in a table or grid-sectioned layout based on user preferences (grid-sectioned layout as default).

- **Grid-based layout**: Product grouped and displayed by category sections. Products in each section are displayed in cards with the product image as the card header. Then, the product info is listed below as a key-value format. The update and delete buttons are found in a floating button at the top-right corner as a kebab menu. Only 20 products per page are listed.Categories would not be split up if they have more than 20 products.

- **Table layout**: Products are displayed in a table. No product image is displayed. All product info is listed in the table. A preview button is located ad the end of the table row, then a kebab menu is added to show show the delete and update buttons.

### Categories

Categories are displayed in a grid card format. Every card shows the number of products they have with their corresponding CRUD actions.

## Reports

Admin can generate their reports in this page. A report previsualization is generated in a table format in this page to be printed afterwards.

Reports can be genarated following these settings and filters:
- **Period**: Daily, weekly, monthly, yearly, custom dates.
- **Products**: A specifc list of products to be included in the report (Default: All).
- **Category**: Products of a specific category (Default: All)

## Users

The user aministration panel to add additonal admin users and sellers.


## Business

Pagina de configuracion del negocio. Permite editar los ajustes globales que afectan toda la aplicacion. La tabla `business_settings` contiene una unica fila (singleton). Solo se permiten acciones de editar y guardar (no crear ni eliminar).

Full spec: @docs/specs/business_settings.md

### Informacion del negocio
- **Nombre del negocio** (obligatorio): Nombre que se muestra en tickets y encabezados.
- **Logo**: Carga de imagen con vista previa y opcion de eliminar.
- **Direccion, Telefono, Correo electronico**: Datos de contacto opcionales.

### Configuracion de moneda e inventario
- **Simbolo de moneda** (obligatorio): Simbolo usado en toda la aplicacion para precios (e.g. "$", "Q").
- **Umbral de stock bajo** (obligatorio): Valor global por defecto para alertas de stock bajo. Los productos sin valor propio usan este umbral.

### Ticket de venta
- **Encabezado del ticket**: Texto que aparece en la parte superior del ticket de venta.
- **Pie del ticket**: Texto que aparece en la parte inferior del ticket de venta.

## Tasks

A ToDo list of the admin tasks 

