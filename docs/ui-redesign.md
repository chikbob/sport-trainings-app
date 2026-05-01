# UI Redesign Notes

## What changed

The application UI was aligned around one shared design system for:

- public pages;
- user profile area;
- coach workspace;
- admin workspace.

Changes were intentionally small and layered on top of the existing Laravel + Inertia + Vue structure.

## Shared layout components

- `resources/js/Layouts/AppLayout.vue` - public and authenticated user shell.
- `resources/js/Layouts/AdminLayout.vue` - admin shell built on shared workspace layout.
- `resources/js/Layouts/CoachLayout.vue` - coach shell.
- `resources/js/Layouts/WorkspaceLayout.vue` - common sidebar/topbar workspace foundation.

## Shared UI components

Added reusable UI primitives in `resources/js/Components`:

- `PageHeader.vue`
- `StatCard.vue`
- `AppCard.vue`
- `AppButton.vue`
- `AppInput.vue`
- `EmptyState.vue`
- `StatusBadge.vue`

## Styling

Global tokens and shared surface/button/form/table styles live in:

- `resources/css/app.css`

Notable updates:

- Tailwind CSS is now actually loaded via `@import "tailwindcss";`
- common colors, spacing, radii, cards and form fields were centralized
- shared workspace shell added for coach/admin
- public header/footer were rebuilt into the same visual language as the internal areas

## Bug fixes included

- fixed auth redirect route mismatch: `login.page` instead of missing `login`
- fixed `RouteServiceProvider::HOME` from missing `/home` to `/`
- fixed broken coach dashboard registration update route
- fixed admin user form so `phone` is saved
- fixed admin coach creation so required `user_id` is provided
- fixed Docker Vite port mapping so it can be overridden with `VITE_PORT`
- removed stray debug logging from frontend flow
- removed dead footer links to missing public pages

## Small functional improvement

Added shared `StatusBadge` usage across public, user, coach and admin pages for:

- training states: `planned`, `active`, `completed`, `cancelled`
- registration states
- role badges in admin lists

Also added shared empty states for list pages.

## How to run

### Local frontend

```bash
npm install
npm run build
```

### Docker

If port `5173` is busy, start with another Vite port:

```bash
VITE_PORT=5174 docker compose up -d --build
docker compose ps
docker compose logs
```

## Validation commands

Run after changes:

```bash
npm run build
php artisan test
php artisan route:list
php artisan view:clear
php artisan config:clear
vendor/bin/pint
```

If PHP is not available on the host, run Laravel commands inside Docker:

```bash
docker compose exec app php artisan test
docker compose exec app php artisan route:list
docker compose exec app php artisan view:clear
docker compose exec app php artisan config:clear
```

## Coverage

Unified visually:

- home page
- login / registration
- sports list / detail
- training calendar / detail
- user profile
- coach dashboard / trainings / registrations / edit
- admin dashboard
- admin users / coaches / sports / trainings / registrations lists and forms
