# Shoppy — Laravel POS & Inventory System

## Project Overview
- **Shoppy Adminer**: Admin panel (CRUD, reports, settings, user management)
- **Shoppy Sales**: POS terminal for sellers (touch-friendly, fast checkout)
- Target: small local businesses (simple, not over-engineered)

### Features specification
- **Shoppy Adminer** feature specifications (Dashboard, sales, inventory, reports, business settings, users, finance) are described at @docs/specs/adminer_features.md

- **Shoppy Sales** feature specifications (Create sales, search products, charge sales) are described at @docs/specs/sales_features.md

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
@docs/specs/ui_design.md

## Prohibited
- No npm-based JS frameworks (keep it Blade + Alpine.js if needed)
- No over-engineered abstractions — this is a simple store app
- Never hardcode currency symbols — use `business_settings`
- All user interface text must be in Spanish

## Roadmap
- **Shoppy Adminer**: @docs/adminer_roadmap.md
- **Shoppy Sales**: @docs/sales_roadmap.md

## Tests
Create a test for every feature created or task in the roadmap
- **Shoppy Adminer**: @tests/Feature/Shoppy_Adminer/
- **Shoppy Sales**: @tests/Feature/Shoppy_Sales/

## Handoff
Summarize the status of the project after completing a task or finishing a session at @docs/handoff.md