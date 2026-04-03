<?php

declare(strict_types=1);

if (!class_exists('ProjectTestCaseBase')) {
    if (class_exists('PHPUnit\\Framework\\TestCase')) {
        class_alias('PHPUnit\\Framework\\TestCase', 'ProjectTestCaseBase');
    } else {
        abstract class ProjectTestCaseBase
        {
            protected static function assertSame(mixed $expected, mixed $actual, string $message = ''): void {}
            protected static function assertCount(int $expectedCount, mixed $haystack, string $message = ''): void {}
            protected static function assertIsArray(mixed $actual, string $message = ''): void {}
            protected static function assertStringContainsString(string $needle, string $haystack, string $message = ''): void {}
            protected static function assertStringEndsWith(string $suffix, string $string, string $message = ''): void {}
            protected static function assertFileExists(string $filename, string $message = ''): void {}
            protected function expectException(string $exception): void {}
        }
    }
}
