# PHP Symfony Product List

Simple PHP 8 application using Symfony Routing and HttpFoundation components.

The app includes:

- shared site layout (logos + navigation + responsive shell)
- product list page with mobile accordion behavior
- product detail page
- custom 404 page
- JSON-backed product data

## Stack

- PHP 8
- symfony/routing
- symfony/http-foundation
- Apache + .htaccess rewriting

## Project Structure

- public/index.php: front controller and request dispatch
- src/layout.php: shared HTML shell and global CSS
- src/home.php: home page content
- src/products.php: product list/detail rendering and data loading
- src/not_found.php: 404 page rendering
- src/routes.php: centralized route definitions
- data/products.json: product data source
- tests/run.php: lightweight test runner (no external test framework required)
- tests/\*.php: PHPUnit test files prepared for full PHPUnit usage

## Routes

- /: home
- /products: product list (EN alias)
- /izdelki: product list (primary)
- /izdelek/{id}: product detail (id must be numeric)

## Local Run (XAMPP)

This project is currently used under:

- C:/xampp/htdocs/php-symfony-product-list

Routing is handled through root and public .htaccess files.

## Tests

### Default Tests (currently working)

Run:

```powershell
composer test
```

This executes tests/run.php and validates:

- route matching
- page rendering basics
- 404 behavior
- required asset presence

### PHPUnit Tests (prepared, may need environment fix)

Run:

```powershell
composer test:phpunit
```

If vendor/bin/phpunit is missing, install dev dependencies first:

```powershell
composer require --dev phpunit/phpunit:^10.5
```

If Composer fails with SSL certificate issues, configure CA certs for CLI PHP and retry.

## Notes

- Product images are expected in public/izdelki/ as izdelek-1.jpg through izdelek-5.jpg.
- Shared header, logos, and navigation are rendered once in src/layout.php and reused across all pages.
