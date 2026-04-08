<?php

declare(strict_types=1);

use App\RouteProvider;
use Symfony\Component\Routing\RouteCollection;

function buildRoutes(): RouteCollection
{
    return (new RouteProvider())->createRoutes();
}
