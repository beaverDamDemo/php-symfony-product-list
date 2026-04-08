<?php

declare(strict_types=1);

namespace App;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

final class Kernel
{
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function handle(Request $request): Response
    {
        $routes = $this->container->getRouteProvider()->createRoutes();
        $context = new RequestContext();
        $context->fromRequest($request);
        $matcher = new UrlMatcher($routes, $context);

        try {
            $pathInfo = rtrim($request->getPathInfo(), '/') ?: '/';
            $parameters = $matcher->match($pathInfo);
            $controller = $this->resolveController($parameters['_controller'] ?? null);

            if (!is_callable($controller)) {
                throw new \RuntimeException('Resolved controller is not callable.');
            }

            $routeParams = array_filter(
                $parameters,
                static fn($key) => $key !== '_controller' && $key !== '_route',
                ARRAY_FILTER_USE_KEY
            );

            return \call_user_func($controller, ...(empty($routeParams) ? [] : array_values($routeParams)));
        } catch (\Throwable) {
            return $this->container->getNotFoundController()->index();
        }
    }

    private function resolveController(mixed $controller): mixed
    {
        if (!is_array($controller) || !isset($controller[0], $controller[1]) || !is_string($controller[0])) {
            return $controller;
        }

        return match ($controller[0]) {
            HomeController::class => [$this->container->getHomeController(), $controller[1]],
            ProductController::class => [$this->container->getProductController(), $controller[1]],
            NotFoundController::class => [$this->container->getNotFoundController(), $controller[1]],
            default => [new $controller[0](), $controller[1]],
        };
    }
}
