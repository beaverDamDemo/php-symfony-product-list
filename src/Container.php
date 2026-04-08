<?php

declare(strict_types=1);

namespace App;

use PDO;

final class Container
{
    private ?PDO $pdo = null;
    private ?ProductRepository $productRepository = null;
    private ?ProductPageService $productPageService = null;
    private ?ProductController $productController = null;
    private ?HomeController $homeController = null;
    private ?NotFoundController $notFoundController = null;
    private ?RouteProvider $routeProvider = null;

    public function getProductController(): ProductController
    {
        if ($this->productController === null) {
            $this->productController = new ProductController($this->getProductPageService());
        }

        return $this->productController;
    }

    public function getHomeController(): HomeController
    {
        if ($this->homeController === null) {
            $this->homeController = new HomeController();
        }

        return $this->homeController;
    }

    public function getNotFoundController(): NotFoundController
    {
        if ($this->notFoundController === null) {
            $this->notFoundController = new NotFoundController();
        }

        return $this->notFoundController;
    }

    public function getRouteProvider(): RouteProvider
    {
        if ($this->routeProvider === null) {
            $this->routeProvider = new RouteProvider();
        }

        return $this->routeProvider;
    }

    public function getProductPageService(): ProductPageService
    {
        if ($this->productPageService === null) {
            $repository = hasDatabaseConfig() ? $this->getProductRepository() : null;
            $this->productPageService = new ProductPageService($repository);
        }

        return $this->productPageService;
    }

    private function getProductRepository(): ProductRepository
    {
        if ($this->productRepository === null) {
            $this->productRepository = new ProductRepository($this->getPdo());
        }

        return $this->productRepository;
    }

    private function getPdo(): PDO
    {
        if ($this->pdo === null) {
            $this->pdo = getDatabaseConnection();
        }

        return $this->pdo;
    }
}
