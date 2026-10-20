<?php

declare(strict_types=1);

namespace Sabroan\Http\Message\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Sabroan\Http\Message\StreamFactory;

#[CoversClass(StreamFactory::class)]
final class StreamFactoryTest extends TestCase
{
    private function createFactory(): StreamFactoryInterface
    {
        return new StreamFactory();
    }

    #[DataProvider('provideStreamContent')]
    public function testCreateStream(string $content): void
    {
        $factory = $this->createFactory();
        $stream = $factory->createStream($content);

        self::assertInstanceOf(StreamInterface::class, $stream);
        self::assertSame($content, (string) $stream);
    }

    /**
     * @return array<array{string}>
     */
    public static function provideStreamContent(): array
    {
        return [
            'empty string' => [''],
            'simple text' => ['Hello, World!'],
            'multiline text' => ["Line 1\nLine 2\nLine 3"],
            'binary content' => ["\x00\x01\x02\x03\xFF"],
        ];
    }

    #[DataProvider('provideFileModes')]
    public function testCreateStreamFromFile(string $mode, string $expectedContent): void
    {
        $filename = tempnam(sys_get_temp_dir(), 'psr17_test_');

        if (false === $filename) {
            throw new \RuntimeException("Unabe to create file");
        }

        file_put_contents($filename, 'file content test');

        $factory = $this->createFactory();
        $stream = $factory->createStreamFromFile($filename, $mode);

        self::assertInstanceOf(StreamInterface::class, $stream);
        self::assertSame($expectedContent, (string) $stream);

        $stream->close();
        unlink($filename);
    }

    /**
     * @return array<array{string}>
     */
    public static function provideFileModes(): array
    {
        return [
            'read mode' => ['r', 'file content test'],
            'read write mode' => ['r+', 'file content test'],
            'write read mode' => ['w+', ''],
            'append read mode' => ['a+', 'file content test'],
            'binary read mode' => ['rb', 'file content test'],
        ];
    }

    public function testCreateStreamFromFileThrowsExceptionForNonExistentFile(): void
    {
        self::expectException(\Throwable::class);

        $factory = $this->createFactory();
        $factory->createStreamFromFile('/invalid/path/to/non_existent_file_' . uniqid('', true) . '.txt');
    }

    #[DataProvider('provideInvalidModes')]
    public function testCreateStreamFromFileThrowsExceptionForInvalidMode(string $mode): void
    {
        $filename = tempnam(sys_get_temp_dir(), 'psr17_test_');

        if (false === $filename) {
            throw new \RuntimeException("Unabe to create file");
        }

        self::expectException(\Throwable::class);

        try {
            $factory = $this->createFactory();
            $factory->createStreamFromFile($filename, $mode);
        } finally {
            unlink($filename);
        }
    }

    /**
     * @return array<array{string}>
     */
    public static function provideInvalidModes(): array
    {
        return [
            'empty mode' => [''],
            'invalid character' => ['z'],
            'numeric mode' => ['1'],
        ];
    }

    public function testCreateStreamFromResource(): void
    {
        $resource = fopen('php://temp', 'r+');

        if (false === $resource) {
            throw new \RuntimeException("Unabe to create stream");
        }

        fwrite($resource, 'stream from resource');
        rewind($resource);

        $factory = $this->createFactory();
        $stream = $factory->createStreamFromResource($resource);

        self::assertInstanceOf(StreamInterface::class, $stream);
        self::assertSame('stream from resource', (string) $stream);
    }

    #[DataProvider('provideInvalidResources')]
    public function testCreateStreamFromResourceThrowsExceptionForInvalidResource(mixed $invalidResource): void
    {
        self::expectException(\Throwable::class);

        $factory = $this->createFactory();
        // @phpstan-ignore argument.type (Intentionally testing invalid values.)
        $factory->createStreamFromResource($invalidResource);
    }

    /**
     * @return array<array{mixed}>
     */
    public static function provideInvalidResources(): array
    {
        return [
            'integer' => [123],
            'float' => [45.67],
            'string' => ['not_a_resource'],
            'boolean' => [true],
            'array' => [[1, 2, 3]],
            'object' => [new \stdClass()],
            'null' => [null],
        ];
    }
}
