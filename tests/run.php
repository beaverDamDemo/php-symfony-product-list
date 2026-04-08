<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

use App\HomeController;
use App\NotFoundController;
use App\ProductController;
use App\ProductPageService;
use App\ProductRepository;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

$isTty = true;
if (function_exists('stream_isatty')) {
    $isTty = stream_isatty(STDOUT);
}

$color = static function (string $text, string $code) use ($isTty): string {
    if (!$isTty) {
        return $text;
    }

    return "\033[{$code}m{$text}\033[0m";
};

$greenDot = $color('●', '32');
$redDot = $color('●', '31');
$yellow = static fn(string $text): string => $color($text, '33');
$green = static fn(string $text): string => $color($text, '32');
$red = static fn(string $text): string => $color($text, '31');

$assert = static function (bool $condition, string $message): void {
    if (!$condition) {
        throw new RuntimeException($message);
    }
};

$results = [];

$runTest = static function (string $name, callable $test) use (&$results): void {
    try {
        $test();
        $results[] = ['name' => $name, 'ok' => true, 'error' => ''];
    } catch (Throwable $e) {
        $results[] = ['name' => $name, 'ok' => false, 'error' => $e->getMessage()];
    }
};

$runTest('route / maps to home controller method', static function () use ($assert): void {
    $matcher = new UrlMatcher(buildRoutes(), new RequestContext('/'));
    $params = $matcher->match('/');
    $assert(($params['_route'] ?? null) === 'home', 'Route / should match home');
    $assert(($params['_controller'] ?? null) === [HomeController::class, 'index'], 'Route / should use HomeController::index');
});

$runTest('route /products maps to products', static function () use ($assert): void {
    $matcher = new UrlMatcher(buildRoutes(), new RequestContext('/'));
    $assert(($matcher->match('/products')['_route'] ?? null) === 'products', 'Route /products should match products');
});

$runTest('route /izdelki maps to products_sl', static function () use ($assert): void {
    $matcher = new UrlMatcher(buildRoutes(), new RequestContext('/'));
    $assert(($matcher->match('/izdelki')['_route'] ?? null) === 'products_sl', 'Route /izdelki should match products_sl');
});

$runTest('route /izdelek/3 maps to product_detail with id=3', static function () use ($assert): void {
    $matcher = new UrlMatcher(buildRoutes(), new RequestContext('/'));
    $detail = $matcher->match('/izdelek/3');
    $assert(($detail['_route'] ?? null) === 'product_detail', 'Route /izdelek/3 should match product_detail');
    $assert((string) ($detail['id'] ?? '') === '3', 'Route /izdelek/3 should keep id=3');
});

$runTest('route /izdelek/abc rejects non-numeric id', static function () use ($assert): void {
    $matcher = new UrlMatcher(buildRoutes(), new RequestContext('/'));
    $threw = false;
    try {
        $matcher->match('/izdelek/abc');
    } catch (Throwable $e) {
        $threw = true;
    }
    $assert($threw, 'Route /izdelek/abc should throw an exception');
});

$runTest('home page includes shared layout markers', static function () use ($assert): void {
    $home = (new HomeController())->index();
    $homeHtml = (string) $home->getContent();
    $assert(str_contains($homeHtml, 'class="logo-row row-inner"'), 'Home should include shared logo row');
    $assert(str_contains($homeHtml, 'aria-label="Glavna navigacija"'), 'Home should include shared nav');
    $assert(str_contains($homeHtml, '/public/tinified/logo.png'), 'Home should include logo image path');
});

$runTest('products page contains grid, accordion and izdelki image path', static function () use ($assert): void {
    $productPageService = new ProductPageService();
    $products = [
        [
            'id' => 1,
            'name' => 'Test Izdelek 1',
            'subtitle' => 'Test Subtitle',
            'description' => ['Opis 1'],
            'image' => '/public/izdelki/izdelek-1.jpg',
        ],
    ];
    $productsHtml = $productPageService->renderProductsContent($products);
    $assert(str_contains($productsHtml, 'class="products-grid"'), 'Products should include products-grid layout');
    $assert(str_contains($productsHtml, 'class="product-desc-accordion"'), 'Products should include mobile accordion markup');
    $assert(str_contains($productsHtml, '+ VEČ O IZDELKU 1'), 'Products should include accordion label for product 1');
    $assert(str_contains($productsHtml, '/public/izdelki/izdelek-1.jpg'), 'Products should use new izdelki image folder paths');
});

$runTest('invalid product detail shows not found message and back link', static function () use ($assert): void {
    $invalidDetail = (new ProductController())->detail('999');
    $invalidDetailHtml = (string) $invalidDetail->getContent();
    $assert(str_contains($invalidDetailHtml, 'Izdelek ni bil najden'), 'Invalid product detail should show not found message');
    $assert(str_contains($invalidDetailHtml, 'Nazaj na seznam'), 'Invalid product detail should include back-to-list link');
});

$runTest('repository findById returns only the requested product', static function () use ($assert): void {
    $pdo = new PDO('sqlite::memory:');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec('CREATE TABLE products (id INTEGER PRIMARY KEY, name TEXT, subtitle TEXT, description TEXT, image TEXT)');

    $insert = $pdo->prepare('INSERT INTO products (id, name, subtitle, description, image) VALUES (:id, :name, :subtitle, :description, :image)');
    $insert->execute([
        'id' => 1,
        'name' => 'Izdelek 1',
        'subtitle' => 'S1',
        'description' => json_encode(['Opis 1A', 'Opis 1B'], JSON_THROW_ON_ERROR),
        'image' => '/public/izdelki/izdelek-1.jpg',
    ]);
    $insert->execute([
        'id' => 2,
        'name' => 'Izdelek 2',
        'subtitle' => 'S2',
        'description' => json_encode(['Opis 2A'], JSON_THROW_ON_ERROR),
        'image' => '/public/izdelki/izdelek-2.jpg',
    ]);

    $repository = new ProductRepository($pdo);
    $product = $repository->findById(2);

    $assert(is_array($product), 'findById should return a product array when id exists');
    $assert((int) ($product['id'] ?? 0) === 2, 'findById should return product with requested id');
    $assert((string) ($product['name'] ?? '') === 'Izdelek 2', 'findById should return the correct product name');
    $assert(($product['description'] ?? []) === ['Opis 2A'], 'findById should parse description JSON into array');

    $missing = $repository->findById(999);
    $assert($missing === null, 'findById should return null for missing id');
});

$runTest('404 page returns status 404 with correct content and button style', static function () use ($assert): void {
    $notFound = (new NotFoundController())->index();
    $notFoundHtml = (string) $notFound->getContent();
    $assert($notFound->getStatusCode() === 404, '404 page should return status code 404');
    $assert(str_contains($notFoundHtml, 'Stran ni bila najdena'), '404 page should show page-not-found heading');
    $assert(str_contains($notFoundHtml, 'detail-back-btn'), '404 page should use shared button style');
});

$runTest('seed SQL contains 5 products with valid image paths', static function () use ($assert): void {
    $seedPath = dirname(__DIR__) . '/docker/mysql/init/002_seed_products.sql';
    $seedSql = file_get_contents($seedPath);

    $assert(is_string($seedSql), 'Seed SQL file should be readable');
    $assert(substr_count($seedSql, '), \'/public/izdelki/izdelek-') === 5, 'Seed SQL should contain exactly 5 image entries in /public/izdelki/');
    $assert(str_contains($seedSql, "(1, 'Brezžične Slušalke NovaSound'"), 'Seed SQL should include product id 1');
    $assert(str_contains($seedSql, "(5, 'Miška Glide X'"), 'Seed SQL should include product id 5');
});

$runTest('required public assets exist', static function () use ($assert): void {
    $root = dirname(__DIR__);
    $assets = [
        $root . '/public/tinified/logo.png',
        $root . '/public/tinified/kabi-test.png',
        $root . '/public/izdelki/izdelek-1.jpg',
        $root . '/public/izdelki/izdelek-2.jpg',
        $root . '/public/izdelki/izdelek-3.jpg',
        $root . '/public/izdelki/izdelek-4.jpg',
        $root . '/public/izdelki/izdelek-5.jpg',
    ];

    foreach ($assets as $assetPath) {
        $assert(is_file($assetPath), 'Missing asset: ' . $assetPath);
    }
});

echo $yellow("\nRunning custom test checks...\n\n");

$failedCount = 0;
foreach ($results as $result) {
    if ($result['ok']) {
        echo "{$greenDot} PASS  {$result['name']}\n";
        continue;
    }

    $failedCount++;
    echo "{$redDot} FAIL  {$result['name']}\n";
    echo '       ' . $red($result['error']) . "\n";
}

$totalCount = count($results);
$passedCount = $totalCount - $failedCount;

echo "\n";
if ($failedCount === 0) {
    echo $green("Summary: {$passedCount}/{$totalCount} passed, 0 failed.\n");
    exit(0);
}

echo $red("Summary: {$passedCount}/{$totalCount} passed, {$failedCount} failed.\n");
exit(1);
