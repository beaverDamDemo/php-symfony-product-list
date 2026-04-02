<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// Create routes
$routes = new RouteCollection();

$routes->add('home', new Route('/', [
    '_controller' => function () {
        return new Response('Home page works!');
    }
]));

$routes->add('products', new Route('/products', [
    '_controller' => [new \App\ProductController(), 'list']
]));

// Handle request
$request = Request::createFromGlobals();
$context = new Symfony\Component\Routing\RequestContext();
$context->fromRequest($request);

$matcher = new Symfony\Component\Routing\Matcher\UrlMatcher($routes, $context);

try {
    $parameters = $matcher->match($request->getPathInfo());
    $controller = $parameters['_controller'];
    $response = $controller();
} catch (Exception $e) {
    $response = new Response('Not Found', 404);
}

$response->send();
