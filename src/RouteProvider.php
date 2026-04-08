<?php

declare(strict_types=1);

namespace App;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

final class RouteProvider
{
    public function createRoutes(): RouteCollection
    {
        $routes = new RouteCollection();

        $routes->add('home', new Route('/', [
            '_controller' => [HomeController::class, 'index'],
        ]));

        $routes->add('products', new Route('/products', [
            '_controller' => [ProductController::class, 'index'],
        ]));

        $routes->add('products_sl', new Route('/izdelki', [
            '_controller' => [ProductController::class, 'index'],
        ]));

        $detailRoute = new Route('/izdelek/{id}', [
            '_controller' => [ProductController::class, 'detail'],
            'id' => 1,
        ]);
        $detailRoute->setRequirements(['id' => '\\d+']);
        $routes->add('product_detail', $detailRoute);

        return $routes;
    }
}
