<?php

declare(strict_types=1);

namespace App;

use Symfony\Component\HttpFoundation\Response;

final class ProductController
{
    public function __construct(private ?ProductPageService $productPageService = null)
    {
        $this->productPageService ??= new ProductPageService();
    }

    public function index(): Response
    {
        $products = $this->productPageService->loadProducts();

        return renderLayout('Izdelki', 'products', $this->productPageService->renderProductsContent($products));
    }

    public function detail(string $id): Response
    {
        $productId = (int) $id;
        $product = $this->productPageService->loadProductById($productId);

        if (!is_array($product)) {
            return renderLayout('404 – Izdelek ni najden', 'products', $this->productPageService->renderMissingProductContent());
        }

        return renderLayout(
            htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'),
            'products',
            $this->productPageService->renderProductDetailContent($product, $productId)
        );
    }
}
