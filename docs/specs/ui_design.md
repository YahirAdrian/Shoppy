# UI design specifications

- **Color scheme**: Teal (purple) + Orange (accent) — defined in `resources/css/app.css` under `@theme`
- Use `primary-*` classes for main UI elements (buttons, links, focus rings, active states)
- Use `accent-*` classes for highlights, badges, CTAs, and secondary actions
- Use `dark-*` classes for backgrounds and headings (dark blue)
- Never hardcode hex colors in Blade — always use the Tailwind `primary-*` / `accent-*` scale
- Preferred base shades: `primary-600` for buttons, `accent-500` for accent elements