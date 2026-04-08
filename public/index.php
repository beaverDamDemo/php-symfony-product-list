<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/config/bootstrap.php';

use App\Container;
use App\Kernel;
use Symfony\Component\HttpFoundation\Request;

$request = Request::createFromGlobals();
$kernel = new Kernel(new Container());
$response = $kernel->handle($request);

$response->send();
