<?php

declare(strict_types=1);

$_SERVER['SCRIPT_NAME'] = '/public/index.php';

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/layout.php';
require_once __DIR__ . '/../src/database.php';
require_once __DIR__ . '/../src/routes.php';
