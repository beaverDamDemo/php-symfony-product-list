<?php

declare(strict_types=1);

$projectDir = dirname(__DIR__);
$envFile = is_file($projectDir . '/.env')
    ? $projectDir . '/.env'
    : $projectDir . '/.env.example';

if (is_file($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    foreach ($lines as $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || str_starts_with($trimmed, '#')) {
            continue;
        }

        [$name, $value] = array_pad(explode('=', $trimmed, 2), 2, '');
        $name = trim($name);
        $value = trim($value);

        if ($name === '' || array_key_exists($name, $_ENV) || getenv($name) !== false) {
            continue;
        }

        $value = trim($value, "\"'");
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
        putenv($name . '=' . $value);
    }
}
