<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/layout.php';
require_once __DIR__ . '/../src/database.php';
require_once __DIR__ . '/../src/routes.php';

use App\NotFoundController;
use Symfony\Component\HttpFoundation\Request;

// -- Routes ------------------------------------------------------------------

$routes = buildRoutes();

// -- Dispatch -----------------------------------------------------------------

$request = Request::createFromGlobals();
$context = new Symfony\Component\Routing\RequestContext();
$context->fromRequest($request);

$matcher = new Symfony\Component\Routing\Matcher\UrlMatcher($routes, $context);

try {
    // Strip trailing slash Apache adds via directory redirect (e.g. /izdelki/ → /izdelki)
    $pathInfo = rtrim($request->getPathInfo(), '/') ?: '/';
    $parameters  = $matcher->match($pathInfo);
    $controller  = $parameters['_controller'];
    if (is_array($controller) && isset($controller[0], $controller[1]) && is_string($controller[0])) {
        $controller = [new $controller[0](), $controller[1]];
    }
    $routeParams = array_filter(
        $parameters,
        static fn($key) => $key !== '_controller' && $key !== '_route',
        ARRAY_FILTER_USE_KEY
    );
    $response = \call_user_func($controller, ...(empty($routeParams) ? [] : array_values($routeParams)));
} catch (\Throwable $e) {
    $response = (new NotFoundController())->index();
}

$response->send();
