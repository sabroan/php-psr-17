<?php

declare(strict_types=1);

namespace Sabroan\Http\Message\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Sabroan\Http\Message\UriFactory;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

#[CoversClass(UriFactory::class)]
final class UriFactoryTest extends TestCase
{
    private function createFactory(): UriFactoryInterface
    {
        return new UriFactory();
    }

    public function testCreateUriDefaultParameter(): void
    {
        $factory = $this->createFactory();
        $uri = $factory->createUri();

        self::assertInstanceOf(UriInterface::class, $uri);
        self::assertSame('', (string) $uri);
    }

    public function testCreateUriReturnsNewInstanceOnEveryCall(): void
    {
        $factory = $this->createFactory();
        $uri1 = $factory->createUri('https://example.com');
        $uri2 = $factory->createUri('https://example.com');

        self::assertNotSame($uri1, $uri2);
    }

    #[DataProvider('validUriProvider')]
    public function testCreateUriWithValidString(string $inputUri): void
    {
        $factory = $this->createFactory();
        $uri = $factory->createUri($inputUri);

        self::assertInstanceOf(UriInterface::class, $uri);
        self::assertSame($inputUri, (string) $uri);
    }

    /**
     * @return array<array{string}>
     */
    public static function validUriProvider(): array
    {
        return [
            'empty string' => [''],
            'path only' => ['/path/to/resource'],
            'relative path' => ['relative/path'],
            'scheme and host' => ['https://example.com'],
            'protocol relative' => ['//example.com/path'],
            'query only' => ['?query=value'],
            'fragment only' => ['#fragment'],
            'full uri' => ['http://user:pass@example.com:8080/path?query=value#fragment'],
            'urn' => ['urn:isbn:0451450523'],
            'ipv4 address' => ['http://127.0.0.1:8080'],
            'ipv6 address' => ['http://[::1]:8080'],
        ];
    }
}
