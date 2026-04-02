<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/layout.php';
require_once __DIR__ . '/../src/home.php';
require_once __DIR__ . '/../src/products.php';
require_once __DIR__ . '/../src/not_found.php';
require_once __DIR__ . '/../src/routes.php';

use Symfony\Component\HttpFoundation\Request;

// -- Routes ------------------------------------------------------------------

$routes = buildRoutes();

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
