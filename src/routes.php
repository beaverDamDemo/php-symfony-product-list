<?php

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

function buildRoutes(): RouteCollection
{
    $routes = new RouteCollection();

    $routes->add('home', new Route('/', [
        '_controller' => 'renderHomePage',
    ]));

    $routes->add('products', new Route('/products', [
        '_controller' => 'renderProductsPage',
    ]));

    $routes->add('products_sl', new Route('/izdelki', [
        '_controller' => 'renderProductsPage',
    ]));

    $detailRoute = new Route('/izdelek/{id}', [
        '_controller' => 'renderProductDetailPage',
        'id'          => 1,
    ]);
    $detailRoute->setRequirements(['id' => '\\d+']);
    $routes->add('product_detail', $detailRoute);

    return $routes;
}
