# Gemini Project Context: Laravel 13 Modular API

This project is a modern, modular Laravel 13 application designed for high-quality API development. It follows strict architectural patterns and utilizes the latest PHP and Laravel features.

## Project Overview

- **Framework:** Laravel 13
- **PHP Version:** ^8.3 (8.4 recommended)
- **Architecture:** Modular (`app/Modules`)
- **Main Technologies:**
    - **Testing:** Pest PHP 4
    - **API Documentation:** Scribe
    - **Code Style:** Laravel Pint
    - **Frontend:** Tailwind CSS v4 (Vite)
    - **Database:** Eloquent ORM, Spatie Laravel Query Builder
    - **Tools:** Laravel Boost (MCP)

## Modular Structure

The application is divided into modules located in `app/Modules`. Each module is self-contained and typically includes:
- `Controllers/`: API controllers.
- `Models/`: Eloquent models with attribute-based configuration.
- `Providers/`: Service providers for module registration and dependency injection.
- `Repositories/`: Data access layer with `Contracts/` for interfaces.
- `Requests/`: Form Requests for validation.
- `Resources/`: Eloquent API Resources (extending `BaseResource`).
- `Routes/`: Module-specific API routes.
- `Services/`: Business logic layer.

### Core Module
Contains shared base classes and traits, such as `BaseResource` for consistent JSON responses and `ApiResponse` trait.

## Development Conventions

### PHP Standards
- **Strict Typing:** Every PHP file must start with `declare(strict_types=1);`.
- **Class Modifiers:** Use `final` for classes that shouldn't be extended (e.g., ServiceProviders, FormRequests).
- **Type Hinting:** Always provide explicit parameter and return type hints.
- **Constructor Promotion:** Use PHP 8 constructor property promotion.
- **Attributes:** Use PHP attributes for model configuration (e.g., `#[Fillable]`, `#[Hidden]`).

### Laravel Patterns
- **Validation:** Always use Form Request classes. Prefer array-based rules.
- **API Responses:** Always use Eloquent Resources. Extend `App\Modules\Core\Resources\BaseResource` for a standardized `{success: true, data: [...]}` envelope.
- **Repositories:** Use the Repository pattern for data access. Bind interfaces to implementations in module Service Providers.
- **Enums:** Use Backed Enums for statuses and types.
- **Models:** Use `HasFactory` and define proper PHPDoc `@property` blocks for IDE support.

### Testing
- Use **Pest PHP** for all tests.
- Feature tests should extend `Tests\TestCase` and use `RefreshDatabase`.
- Place tests in `tests/Feature/Modules/{ModuleName}`.

## Building and Running

### Key Commands
- **Setup:** `composer run setup`
- **Development Server:** `npm run dev` (starts Laravel server, Vite, and queue)
- **Run Tests:** `php artisan test`
- **Format Code:** `vendor/bin/pint --format agent`
- **Generate API Docs:** `php artisan scribe:generate`
- **Static Analysis:** `vendor/bin/phpstan`

## Tooling & Skills

- **Laravel Boost:** Use the provided MCP tools for database inspection, documentation search, and Artisan commands.
- **Skills Activation:**
    - Activate `pest-testing` for any test-related work.
    - Activate `tailwindcss-development` for UI/styling tasks.

## Deployment & Environments
- Served via **Laravel Herd** at `https://laravel13.test`.
- Uses `.env` for configuration. Never use `env()` outside of config files.
