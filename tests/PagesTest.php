<?php

declare(strict_types=1);

require_once __DIR__ . '/phpunit_fallback.php';

use App\HomeController;
use App\NotFoundController;
use App\ProductController;

final class PagesTest extends ProjectTestCaseBase
{
    public function testHomePageUsesSharedLayout(): void
    {
        $response = (new HomeController())->index();
        $html = $response->getContent();

        self::assertStringContainsString('class="logo-row row-inner"', (string) $html);
        self::assertStringContainsString('aria-label="Glavna navigacija"', (string) $html);
        self::assertStringContainsString('/public/tinified/logo.png', (string) $html);
    }

    public function testProductsPageRendersCardsAndAccordionMarkup(): void
    {
        $response = (new ProductController())->index();
        $html = (string) $response->getContent();

        self::assertStringContainsString('class="products-grid"', $html);
        self::assertStringContainsString('class="product-desc-accordion"', $html);
        self::assertStringContainsString('+ VEČ O IZDELKU 1', $html);
        self::assertStringContainsString('/public/izdelki/izdelek-1.jpg', $html);
    }

    public function testInvalidProductDetailShowsNotFoundMessage(): void
    {
        $response = (new ProductController())->detail('999');
        $html = (string) $response->getContent();

        self::assertStringContainsString('Izdelek ni bil najden', $html);
        self::assertStringContainsString('Nazaj na seznam', $html);
    }

    public function testNotFoundPageReturns404StatusAndSharedButtonStyle(): void
    {
        $response = (new NotFoundController())->index();
        $html = (string) $response->getContent();

        self::assertSame(404, $response->getStatusCode());
        self::assertStringContainsString('Stran ni bila najdena', $html);
        self::assertStringContainsString('detail-back-btn', $html);
    }
}
