# Repository Guidelines

## Project Structure & Module Organization

This is a Laravel 13 ERP application using Livewire 4 and Filament 5. Domain logic lives in `app/`: Eloquent entities are in `Models/`, interactive screens in `Livewire/`, orchestration in `Services/`, HTTP concerns in `Http/`, and AI integrations in `Ai/`. Blade templates, JavaScript, and Tailwind styles are under `resources/`. Routes are split by business domain in `routes/` (for example, `routes/financeiro.php`), while schema changes, factories, and seed data belong in `database/`. Public images and compiled assets live in `public/`. Keep tests in `tests/Feature` or `tests/Unit`; detailed module documentation belongs in `Documentation/`.

## Build, Test, and Development Commands

- `composer run setup` installs PHP/Node dependencies, creates `.env`, generates the app key, migrates the database, and builds assets.
- `composer run dev` runs Laravel, the queue listener, and Vite HMR concurrently.
- `npm run build` creates production front-end assets.
- `composer test` clears cached configuration and runs the complete test suite.
- `php artisan test --filter=SupplierFormTest` runs a focused test.
- `vendor/bin/pint` formats PHP code to the project standard.

## Coding Style & Naming Conventions

Follow `.editorconfig`: UTF-8, LF endings, four-space indentation (two spaces for YAML), and a final newline. Follow Laravel conventions and PSR-4 autoloading: classes use `PascalCase`, methods and variables use `camelCase`, and database columns use `snake_case`. Name migrations with Laravel's timestamp pattern. Keep domain-specific Livewire components, services, views, and routes grouped consistently. Run Pint before submitting PHP changes.

## Testing Guidelines

Tests use Pest 4 with Laravel's testing plugin and an in-memory SQLite database. Name files after the behavior or unit under test, ending in `Test.php`; place cross-layer and HTTP/UI behavior in `Feature`, and isolated logic in `Unit`. Add regression coverage for bug fixes and cover validation, authorization, and failure paths where applicable. No numeric coverage threshold is configured, but changed behavior should be exercised.

## Commit & Pull Request Guidelines

Recent history generally uses concise, imperative Conventional Commit prefixes such as `feat:`, `fix:`, and `docs:`; scopes are welcome, for example `feat(ai-support): ...`. Keep each commit focused. Pull requests should explain the problem and solution, identify affected modules, link relevant issues, list verification commands, and include screenshots for UI changes. Call out migrations, seeders, queue impacts, or new environment variables explicitly; never commit secrets or populated `.env` files.
