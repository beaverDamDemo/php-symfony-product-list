<?php

declare(strict_types=1);

function getEnvValue(string $key, ?string $default = null): ?string
{
    $value = getenv($key);
    if ($value === false || $value === '') {
        return $default;
    }

    return $value;
}

function hasDatabaseConfig(): bool
{
    return getEnvValue('DB_HOST') !== null
        && getEnvValue('DB_NAME') !== null
        && getEnvValue('DB_USER') !== null;
}

function getDatabaseConnection(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = getEnvValue('DB_HOST', '127.0.0.1');
    $port = getEnvValue('DB_PORT', '3306');
    $dbName = getEnvValue('DB_NAME', 'product_list');
    $user = getEnvValue('DB_USER', 'app');
    $password = getEnvValue('DB_PASSWORD', 'app');

    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $dbName);

    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}
