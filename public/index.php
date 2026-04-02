<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/layout.php';
require_once __DIR__ . '/../src/home.php';
require_once __DIR__ . '/../src/products.php';
require_once __DIR__ . '/../src/not_found.php';

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Request;

// -- Routes ------------------------------------------------------------------

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
$detailRoute->setRequirements(['id' => '\d+']);
$routes->add('product_detail', $detailRoute);

// -- Dispatch -----------------------------------------------------------------

$request = Request::createFromGlobals();
$context = new Symfony\Component\Routing\RequestContext();
$context->fromRequest($request);

$matcher = new Symfony\Component\Routing\Matcher\UrlMatcher($routes, $context);

try {
    $parameters  = $matcher->match($request->getPathInfo());
    $controller  = $parameters['_controller'];
    $routeParams = array_filter(
        $parameters,
        static fn($key) => $key !== '_controller' && $key !== '_route',
        ARRAY_FILTER_USE_KEY
    );
    $response = \call_user_func($controller, ...(empty($routeParams) ? [] : array_values($routeParams)));
} catch (\Throwable $e) {
    $response = renderNotFoundPage();
}

$response->send();
