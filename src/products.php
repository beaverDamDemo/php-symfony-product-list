<?php

use Symfony\Component\HttpFoundation\Response;

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/ProductRepository.php';

function loadProductsFromDatabase(): array
{
    if (!hasDatabaseConfig()) {
        return [];
    }

    $repository = new ProductRepository(getDatabaseConnection());
    return $repository->findAll();
}

function loadProducts(): array
{
    return loadProductsFromDatabase();
}

function loadProductByIdFromDatabase(int $id): ?array
{
    if (!hasDatabaseConfig()) {
        return null;
    }

    $repository = new ProductRepository(getDatabaseConnection());
    return $repository->findById($id);
}

function renderProductsContent(array $products): string
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
    $basePath = getBasePath();
    foreach ($products as $index => $product) {
        $productId = (int) ($product['id'] ?? ($index + 1));
        $rawImage = $product['image'] !== ''
            ? $basePath . $product['image']
            : $basePath . '/public/izdelek-' . $productId . '.png';
        $imageSrc = htmlspecialchars($rawImage, ENT_QUOTES, 'UTF-8');
        $name     = htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8');
        $subtitle = htmlspecialchars($product['subtitle'], ENT_QUOTES, 'UTF-8');
        $descHtml = '';
        foreach ($product['description'] as $para) {
            $descHtml .= '<p class="product-description">' . htmlspecialchars($para, ENT_QUOTES, 'UTF-8') . '</p>';
        }

        $detailHref = htmlspecialchars($basePath . '/public/izdelek/' . $productId, ENT_QUOTES, 'UTF-8');

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

function renderProductDetailContent(array $product, int $serialNumber): string
{
    $basePath = getBasePath();
    $rawImage = $product['image'] !== ''
        ? $basePath . $product['image']
        : $basePath . '/public/izdelek-' . $serialNumber . '.png';

    $imageSrc = htmlspecialchars($rawImage, ENT_QUOTES, 'UTF-8');
    $name     = htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8');
    $subtitle = htmlspecialchars($product['subtitle'], ENT_QUOTES, 'UTF-8');
    $backHref = htmlspecialchars($basePath . '/public/izdelki', ENT_QUOTES, 'UTF-8');

    $descHtml = '';
    foreach ($product['description'] as $para) {
        $descHtml .= '<p class="detail-description">' . htmlspecialchars($para, ENT_QUOTES, 'UTF-8') . '</p>';
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

function renderProductsPage(): Response
{
    $products = loadProducts();
    return renderLayout('Izdelki', 'products', renderProductsContent($products));
}

function renderProductDetailPage(string $id): Response
{
    $id = (int) $id;
    $product = loadProductByIdFromDatabase($id);

    if (!is_array($product)) {
        return renderLayout('404 – Izdelek ni najden', 'products', '
            <section style="text-align:center; padding: 48px 0;">
                <h1>Izdelek ni bil najden</h1>
                <a href="' . htmlspecialchars(getBasePath() . '/public/izdelki', ENT_QUOTES, 'UTF-8') . '"
                   style="color:#5ea1e1; font-weight:700;">← Nazaj na seznam</a>
            </section>
        ');
    }

    return renderLayout(
        htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'),
        'products',
        renderProductDetailContent($product, $id)
    );
}
