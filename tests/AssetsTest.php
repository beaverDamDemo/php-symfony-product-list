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

    public function testProductsJsonUsesNewIzdelkiFolderPaths(): void
    {
        $jsonPath = dirname(__DIR__) . '/data/products.json';
        $data = json_decode((string) file_get_contents($jsonPath), true);

        self::assertIsArray($data);
        self::assertCount(5, $data);

        foreach ($data as $product) {
            self::assertStringContainsString('/public/izdelki/', (string) ($product['image'] ?? ''));
            self::assertStringEndsWith('.jpg', (string) ($product['image'] ?? ''));
        }
    }
}
