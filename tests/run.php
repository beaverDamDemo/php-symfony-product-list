<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

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

$runTest('route / maps to home with renderHomePage controller', static function () use ($assert): void {
    $matcher = new UrlMatcher(buildRoutes(), new RequestContext('/'));
    $params = $matcher->match('/');
    $assert(($params['_route'] ?? null) === 'home', 'Route / should match home');
    $assert(($params['_controller'] ?? null) === 'renderHomePage', 'Route / should use renderHomePage controller');
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
    $home = renderHomePage();
    $homeHtml = (string) $home->getContent();
    $assert(str_contains($homeHtml, 'class="logo-row row-inner"'), 'Home should include shared logo row');
    $assert(str_contains($homeHtml, 'aria-label="Glavna navigacija"'), 'Home should include shared nav');
    $assert(str_contains($homeHtml, '/public/logo.png'), 'Home should include logo image path');
});

$runTest('products page contains grid, accordion and izdelki image path', static function () use ($assert): void {
    $products = renderProductsPage();
    $productsHtml = (string) $products->getContent();
    $assert(str_contains($productsHtml, 'class="products-grid"'), 'Products should include products-grid layout');
    $assert(str_contains($productsHtml, 'class="product-desc-accordion"'), 'Products should include mobile accordion markup');
    $assert(str_contains($productsHtml, '+ VEČ O IZDELKU 1'), 'Products should include accordion label for product 1');
    $assert(str_contains($productsHtml, '/public/izdelki/izdelek-1.jpg'), 'Products should use new izdelki image folder paths');
});

$runTest('invalid product detail shows not found message and back link', static function () use ($assert): void {
    $invalidDetail = renderProductDetailPage('999');
    $invalidDetailHtml = (string) $invalidDetail->getContent();
    $assert(str_contains($invalidDetailHtml, 'Izdelek ni bil najden'), 'Invalid product detail should show not found message');
    $assert(str_contains($invalidDetailHtml, 'Nazaj na seznam'), 'Invalid product detail should include back-to-list link');
});

$runTest('404 page returns status 404 with correct content and button style', static function () use ($assert): void {
    $notFound = renderNotFoundPage();
    $notFoundHtml = (string) $notFound->getContent();
    $assert($notFound->getStatusCode() === 404, '404 page should return status code 404');
    $assert(str_contains($notFoundHtml, 'Stran ni bila najdena'), '404 page should show page-not-found heading');
    $assert(str_contains($notFoundHtml, 'class="detail-back-btn"'), '404 page should use shared button style');
});

$runTest('products.json has 5 entries with valid izdelki image paths', static function () use ($assert): void {
    $jsonPath = dirname(__DIR__) . '/data/products.json';
    $data = json_decode((string) file_get_contents($jsonPath), true);
    $assert(is_array($data), 'products.json should decode to an array');
    $assert(count($data) === 5, 'products.json should contain exactly 5 products');
    foreach ($data as $product) {
        $assert(str_contains((string) ($product['image'] ?? ''), '/public/izdelki/'), 'Each product image should use /public/izdelki/ path');
        $assert(str_ends_with((string) ($product['image'] ?? ''), '.jpg'), 'Each product image should end with .jpg');
    }
});

$runTest('required public assets exist', static function () use ($assert): void {
    $root = dirname(__DIR__);
    $assets = [
        $root . '/public/logo.png',
        $root . '/public/kabi-test.png',
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
