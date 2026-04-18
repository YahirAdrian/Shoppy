# UI design specifications

## Color scheme

- **Primary**: Purple — defined in `resources/css/app.css` under `@theme`
- **Accent**: Orange / Amber — defined in `resources/css/app.css` under `@theme`
- Use `primary-*` classes for main UI elements (buttons, links, focus rings, active states)
- Use `accent-*` classes for highlights, badges, CTAs, and secondary actions
- Never hardcode hex colors in Blade — always use the Tailwind `primary-*` / `accent-*` scale
- Preferred base shades: `primary-600` for buttons, `accent-500` for accent elements
- Reference palette: `resources/assets/color-palette.png`

## Admin sidebar

- **Background**: `stone-800`
- **Nav link hover**: `primary-400` text
- **Nav link active**: `primary-800` background with white text
- **Logo area**: Shoppy logo (white) with `primary-700` background circle
- **User footer**: Pinned to bottom with avatar, name, role, settings and logout icons

## Admin navigation items

Sidebar nav links (in order):
1. Dashboard
2. Ventas
3. Inventario
4. Reportes
5. Negocio
6. Usuarios
7. Tareas

## Typography

- **Font family**: Libre Franklin (Google Fonts) — defined in `resources/css/app.css` as `--font-sans`
- Loaded via Google Fonts CDN in both layout files (`admin.blade.php`, `guest.blade.php`)
- Weights available: 300 (light), 400 (regular), 500 (medium), 600 (semibold), 700 (bold), 800 (extrabold)

## General

- The app design should be consistent with `resources/assets/Dashboard.png`, which is the admin dashboard page.
- All user interface text must be in Spanish.

## JavaScript

- **No inline JavaScript in Blade components.** Any non-trivial JS (Alpine.js component factories, utility functions, handlers beyond simple one-liner expressions) must live in a dedicated file under `resources/js/` and be imported via `resources/js/app.js`.
- Alpine factories should be exposed on `window` (e.g. `window.posSale = posSale`) and referenced from Blade via `x-data="posSale(...)"`.
- Pass server-rendered values (route URLs, config) to JS as function arguments, never hardcode in the JS file.
- Trivial inline expressions in attributes (`@click="showModal = true"`, `x-show="open"`) are allowed.
