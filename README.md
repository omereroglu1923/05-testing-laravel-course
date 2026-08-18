# 05 Testing Laravel Course

## Why this project

The practice project for LaravelDaily's ["Testing in Laravel 13 For Beginners"](https://laraveldaily.com) course (26 lessons) — a small Products CRUD app (web + API) built specifically to be thoroughly tested, not to ship features. Every mocking primitive, authorization rule, and edge case in the app exists because a lesson needed something to test against.

## What's inside

- Products CRUD on the web (Blade views), with an `Api\ProductController` exposing `index`/`store` only
- Role-based authorization via an `is_admin` flag and an `IsAdminMiddleware` guarding create/edit/update/delete
- A `CurrencyService` that converts product prices to EUR through an external HTTP call, shown on the product page
- Sanctum token authentication on the API, required for product creation
- Mail (`ProductCreatedMail`), Notification (`ProductDeletedNotification`), Queue (`SyncProductToExternalCatalog` job), and Storage (product image upload) side effects wired into the product lifecycle
- The `show` page, added through a red-green-refactor TDD cycle

## Concepts learned / practiced

- Feature testing with Pest: `actingAs`, form requests, validation errors, redirects, database assertions (`assertDatabaseHas`, `assertModelMissing`, `assertDatabaseEmpty`)
- Faking Laravel's I/O boundaries in tests — `Mail::fake()`, `Notification::fake()`, `Queue::fake()`, `Storage::fake()`, `Http::fake()` — to assert side effects without hitting real services
- API testing with `Sanctum::actingAs()` and JSON assertions (`assertJson`, `assertCreated`, `assertUnprocessable`, `assertUnauthorized`)
- Datasets (`tests/Datasets/Products.php`) to run the same test against multiple inputs
- Pest architecture tests (`arch()`) to enforce conventions like "no `dd`/`dump` in the codebase" and "models extend Eloquent"
- TDD: writing a failing test for the product `show` route first, then implementing it, then refactoring
- Catching a false-positive `assertSee` by switching to `assertViewHas` for value-based assertions
- **AI-assisted test auditing**: ran the `laraveldaily-testing-audit` skill against the codebase, which surfaced 2 High severity findings — undefined routes left exposed by `apiResource()` and a missing auth requirement on the API's product creation endpoint — both fixed and covered by regression tests (`api product show route does not exist`, `api product store requires authentication`)

## Tech stack

- PHP 8.5, Laravel 13
- Laravel Breeze (Blade) for authentication scaffolding
- Laravel Sanctum 4 for API token authentication
- Pest 5 + PHPUnit 13 for testing (feature, architecture, datasets)
- Laravel Boost for AI-assisted development

## Setup

```bash
git clone https://github.com/omereroglu1923/05-testing-laravel-course.git
cd 05-testing-laravel-course
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
php artisan serve
```

Run the test suite:

```bash
php artisan test --compact
```

Current state: 59 tests, 186 assertions, all passing.

## Part of a larger roadmap

This project is the fifth stop in a full-stack learning path: [`01-weather-cli-app`](https://github.com/omereroglu1923/weather-cli-app) → [`02-chirper`](https://github.com/omereroglu1923/chirper) → [`03-blog-crm`](https://github.com/omereroglu1923/03-blog-crm) → [`04-laravel-api-course`](https://github.com/omereroglu1923/04-laravel-api-course) → **`05-testing-laravel-course`**.
