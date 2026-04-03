<?php

declare(strict_types=1);

$_SERVER['SCRIPT_NAME'] = '/public/index.php';

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/layout.php';
require_once __DIR__ . '/../src/home.php';
require_once __DIR__ . '/../src/products.php';
require_once __DIR__ . '/../src/not_found.php';
require_once __DIR__ . '/../src/routes.php';
