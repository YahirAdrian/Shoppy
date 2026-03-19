# Shoppy — Laravel POS & Inventory System

## Project Overview
- **Shoppy Adminer**: Admin panel (CRUD, reports, settings, user management)
- **Shoppy Sales**: POS terminal for sellers (touch-friendly, fast checkout)
- Target: small local businesses (simple, not over-engineered)

## Tech Stack
- PHP 8.x / Laravel 11
- MySQL
- TailwindCSS (no custom CSS unless necessary)
- Laravel Breeze (auth scaffolding)
- Blade templates (no Vue/React)

## Key Commands
- Dev server: `php artisan serve`
- Migrations: `php artisan migrate`
- Seed: `php artisan db:seed`
- Tests: `php artisan test`

## Architecture Rules
- Admin routes grouped under `/admin` with `role:admin` middleware
- POS routes grouped under `/pos` with `role:seller` middleware
- Controllers stay thin — logic in Service classes if needed

## Style Guidelines
- **Color scheme**: Teal (primary) + Orange (accent) — defined in `resources/css/app.css` under `@theme`
- Use `primary-*` classes for main UI elements (buttons, links, focus rings, active states)
- Use `accent-*` classes for highlights, badges, CTAs, and secondary actions
- Use `dark-*` classes for backgrounds and headings (dark blue)
- Never hardcode hex colors in Blade — always use the Tailwind `primary-*` / `accent-*` scale
- Preferred base shades: `primary-600` for buttons, `accent-500` for accent elements

## Prohibited
- No npm-based JS frameworks (keep it Blade + Alpine.js if needed)
- No over-engineered abstractions — this is a simple store app
- Never hardcode currency symbols — use `business_settings`
- All user interface text must be in Spanish

## Roadmap
@docs/roadmap.md