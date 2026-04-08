<?php

declare(strict_types=1);

namespace App;

final class ProductPageService
{
    public function __construct(private ?ProductRepository $productRepository = null) {}

    public function loadProducts(): array
    {
        if ($this->productRepository === null) {
            return [];
        }

        return $this->productRepository->findAll();
    }

    public function loadProductById(int $id): ?array
    {
        if ($this->productRepository === null) {
            return null;
        }

        return $this->productRepository->findById($id);
    }

    public function renderProductsContent(array $products): string
    {
        if ($products === []) {
            return '
                <section class="placeholder-card">
                    <h1>Izdelki</h1>
                    <p>Podatki o izdelkih niso na voljo.</p>
                </section>
            ';
        }

        $cards = '';
        foreach ($products as $index => $product) {
            $productId = (int) ($product['id'] ?? ($index + 1));
            $rawImage = $product['image'] !== ''
                ? \assetUrl((string) $product['image'])
                : \assetUrl('/izdelek-' . $productId . '.png');
            $imageSrc = htmlspecialchars($rawImage, ENT_QUOTES, 'UTF-8');
            $name = htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8');
            $subtitle = htmlspecialchars($product['subtitle'], ENT_QUOTES, 'UTF-8');
            $descHtml = '';
            foreach ($product['description'] as $paragraph) {
                $descHtml .= '<p class="product-description">' . htmlspecialchars($paragraph, ENT_QUOTES, 'UTF-8') . '</p>';
            }

            $detailHref = htmlspecialchars(\routeUrl('/izdelek/' . $productId), ENT_QUOTES, 'UTF-8');

            $cards .= '
                <article class="product-card">
                    <img class="product-image" src="' . $imageSrc . '" alt="Izdelek ' . $productId . '">
                    <div class="product-body">
                        <h2 class="product-name">' . $name . '</h2>
                        <h3 class="product-subtitle">' . $subtitle . '</h3>
                        <div class="product-desc-full">' . $descHtml . '</div>
                        <details class="product-desc-accordion">
                            <summary class="product-desc-summary">Opis izdelka</summary>
                            ' . $descHtml . '
                        </details>
                        <a class="product-more-btn" href="' . $detailHref . '">+ VEČ O IZDELKU ' . $productId . '</a>
                    </div>
                </article>
            ';
        }

        return '
            <section class="products-wrap">
                <div class="products-grid">' . $cards . '</div>
            </section>
        ';
    }

    public function renderProductDetailContent(array $product, int $serialNumber): string
    {
        $rawImage = $product['image'] !== ''
            ? \assetUrl((string) $product['image'])
            : \assetUrl('/izdelek-' . $serialNumber . '.png');

        $imageSrc = htmlspecialchars($rawImage, ENT_QUOTES, 'UTF-8');
        $name = htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8');
        $subtitle = htmlspecialchars($product['subtitle'], ENT_QUOTES, 'UTF-8');
        $backHref = htmlspecialchars(\routeUrl('/izdelki'), ENT_QUOTES, 'UTF-8');

        $descHtml = '';
        foreach ($product['description'] as $paragraph) {
            $descHtml .= '<p class="detail-description">' . htmlspecialchars($paragraph, ENT_QUOTES, 'UTF-8') . '</p>';
        }

        return '
            <div class="detail-wrap">
                <img class="detail-image" src="' . $imageSrc . '" alt="' . $name . '">
                <div class="detail-body">
                    <h1 class="detail-name">' . $name . '</h1>
                    <h2 class="detail-subtitle">' . $subtitle . '</h2>
                    ' . $descHtml . '
                    <a class="detail-back-btn" href="' . $backHref . '">&#8592; NAZAJ NA SEZNAM</a>
                </div>
            </div>
        ';
    }

    public function renderMissingProductContent(): string
    {
        return '
            <section class="message-panel message-panel--centered">
                <h1 class="message-panel-title">Izdelek ni bil najden</h1>
                     <a href="' . htmlspecialchars(\routeUrl('/izdelki'), ENT_QUOTES, 'UTF-8') . '"
                   class="message-panel-link">← Nazaj na seznam</a>
            </section>
        ';
    }
}
