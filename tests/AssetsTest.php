<?php

declare(strict_types=1);

require_once __DIR__ . '/phpunit_fallback.php';

final class AssetsTest extends ProjectTestCaseBase
{
    public function testRequiredPublicAssetsExist(): void
    {
        $root = dirname(__DIR__);
        $assets = [
            $root . '/public/logo.png',
            $root . '/public/kabi-test.png',
            $root . '/public/izdelki/izdelek-1.jpg',
            $root . '/public/izdelki/izdelek-2.jpg',
            $root . '/public/izdelki/izdelek-3.jpg',
            $root . '/public/izdelki/izdelek-4.jpg',
            $root . '/public/izdelki/izdelek-5.jpg',
        ];

        foreach ($assets as $assetPath) {
            self::assertFileExists($assetPath, 'Missing asset: ' . $assetPath);
        }
    }

    public function testSeedSqlContainsFiveProductsWithValidImagePaths(): void
    {
        $seedPath = dirname(__DIR__) . '/docker/mysql/init/002_seed_products.sql';
        $seedSql = file_get_contents($seedPath);

        self::assertIsString($seedSql);
        self::assertSame(5, substr_count($seedSql, '), \'/public/izdelki/izdelek-'));
        self::assertStringContainsString("(1, 'Brezžične Slušalke NovaSound'", $seedSql);
        self::assertStringContainsString("(5, 'Miška Glide X'", $seedSql);
    }
}
