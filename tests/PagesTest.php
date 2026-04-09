<?php

declare(strict_types=1);

require_once __DIR__ . '/phpunit_fallback.php';

use App\Container;

final class PagesTest extends ProjectTestCaseBase
{
    public function testHomePageUsesSharedLayout(): void
    {
        $_SERVER['SCRIPT_NAME'] = '/index.php';
        $_SERVER['DOCUMENT_ROOT'] = '/var/www/html/public';

        $container = new Container();
        $response = $container->getHomeController()->index();
        $html = $response->getContent();

        self::assertStringContainsString('class="logo-row row-inner"', (string) $html);
        self::assertStringContainsString('aria-label="Glavna navigacija"', (string) $html);
        self::assertStringContainsString('/tinified/logo.png', (string) $html);
        self::assertStringContainsString('href="/izdelki"', (string) $html);
        self::assertSame(false, str_contains((string) $html, 'href="//izdelki"'));
    }

    public function testProductsPageRendersCardsAndAccordionMarkup(): void
    {
        $container = new Container();
        $response = $container->getProductController()->index();
        $html = (string) $response->getContent();

        self::assertStringContainsString('class="products-grid"', $html);
        self::assertStringContainsString('class="product-desc-accordion"', $html);
        self::assertStringContainsString('+ VEČ O IZDELKU 1', $html);
        self::assertStringContainsString('/izdelki/izdelek-1.jpg', $html);
    }

    public function testInvalidProductDetailShowsNotFoundMessage(): void
    {
        $container = new Container();
        $response = $container->getProductController()->detail('999');
        $html = (string) $response->getContent();

        self::assertStringContainsString('Izdelek ni bil najden', $html);
        self::assertStringContainsString('Nazaj na seznam', $html);
    }

    public function testNotFoundPageReturns404StatusAndSharedButtonStyle(): void
    {
        $container = new Container();
        $response = $container->getNotFoundController()->index();
        $html = (string) $response->getContent();

        self::assertSame(404, $response->getStatusCode());
        self::assertStringContainsString('Stran ni bila najdena', $html);
        self::assertStringContainsString('detail-back-btn', $html);
    }
}
