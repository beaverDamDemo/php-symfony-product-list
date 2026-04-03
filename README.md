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

All tests are contained in `tests/run.php` and can be run with a single command.

### Run all tests

From the project root:

```powershell
php tests/run.php
```

Or using the Composer script:

```powershell
composer test
```

### What is tested

| Area           | What is checked                                                                       |
| -------------- | ------------------------------------------------------------------------------------- |
| Routes         | `/`, `/products`, `/izdelki`, `/izdelek/{id}` match correct route name and controller |
| Routes         | Non-numeric `/izdelek/abc` is rejected                                                |
| Home page      | Shared layout markers (logo row, navigation, logo image) are present                  |
| Products page  | Products grid, accordion markup, accordion labels, and image paths rendered           |
| Product detail | Invalid product ID shows not-found message and back link                              |
| 404 page       | Returns HTTP 404 status with correct heading and button style                         |
| products.json  | Contains exactly 5 products, each with a valid `/public/izdelki/*.jpg` image path     |
| Assets         | All required image files exist on disk in `public/` and `public/izdelki/`             |

### Example output

```
Running custom test checks...

● PASS  route / maps to home with renderHomePage controller
● PASS  route /products maps to products
● PASS  route /izdelki maps to products_sl
● PASS  route /izdelek/3 maps to product_detail with id=3
● PASS  route /izdelek/abc rejects non-numeric id
● PASS  home page includes shared layout markers
● PASS  products page contains grid, accordion and izdelki image path
● PASS  invalid product detail shows not found message and back link
● PASS  404 page returns status 404 with correct content and button style
● PASS  products.json has 5 entries with valid izdelki image paths
● PASS  required public assets exist

Summary: 11/11 passed, 0 failed.
```

A failed test prints a red dot and the exact assertion message that failed. Exit code is `0` on success and `1` on any failure.

### PHPUnit (optional)

The files `tests/AssetsTest.php`, `tests/PagesTest.php`, and `tests/RoutesTest.php` are also available as standard PHPUnit classes. To run them with the bundled PHPUnit PHAR:

```powershell
php tools/phpunit.phar -c phpunit.xml
```

To install PHPUnit via Composer instead:

```powershell
composer require --dev phpunit/phpunit:^10.5
php vendor/bin/phpunit -c phpunit.xml
```

## Notes

- Product images are expected in public/izdelki/ as izdelek-1.jpg through izdelek-5.jpg.
- Shared header, logos, and navigation are rendered once in src/layout.php and reused across all pages.
