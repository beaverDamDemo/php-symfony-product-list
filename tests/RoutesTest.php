<?php

declare(strict_types=1);

require_once __DIR__ . '/phpunit_fallback.php';

use App\HomeController;
use App\ProductController;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

final class RoutesTest extends ProjectTestCaseBase
{
    public function testHomeRouteMatches(): void
    {
        $matcher = new UrlMatcher(buildRoutes(), new RequestContext('/'));
        $params = $matcher->match('/');

        self::assertSame('home', $params['_route']);
        self::assertSame([HomeController::class, 'index'], $params['_controller']);
    }

    public function testProductsRoutesMatch(): void
    {
        $matcher = new UrlMatcher(buildRoutes(), new RequestContext('/'));

        self::assertSame('products', $matcher->match('/products')['_route']);
        self::assertSame('products_sl', $matcher->match('/izdelki')['_route']);
    }

    public function testProductDetailRouteMatchesNumericId(): void
    {
        $matcher = new UrlMatcher(buildRoutes(), new RequestContext('/'));
        $params = $matcher->match('/izdelek/3');

        self::assertSame('product_detail', $params['_route']);
        self::assertSame('3', (string) $params['id']);
    }

    public function testProductDetailRouteRejectsNonNumericId(): void
    {
        $matcher = new UrlMatcher(buildRoutes(), new RequestContext('/'));

        $this->expectException(\Throwable::class);
        $matcher->match('/izdelek/abc');
    }
}
