<?php

declare(strict_types=1);

namespace Sabroan\Http\Message\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use Sabroan\Http\Message\RequestFactory;

#[CoversClass(RequestFactory::class)]
final class RequestFactoryTest extends TestCase
{
    private function createFactory(): RequestFactoryInterface
    {
        return new RequestFactory();
    }

    #[DataProvider('provideMethods')]
    public function testCreateRequestSetsMethod(string $method): void
    {
        $factory = $this->createFactory();
        $request = $factory->createRequest($method, 'https://example.com');

        self::assertInstanceOf(RequestInterface::class, $request);
        self::assertSame($method, $request->getMethod());
    }

    /**
     * @return array<array{string}>
     */
    public static function provideMethods(): array
    {
        return [
            'GET' => ['GET'],
            'POST' => ['POST'],
            'PUT' => ['PUT'],
            'DELETE' => ['DELETE'],
            'PATCH' => ['PATCH'],
            'HEAD' => ['HEAD'],
            'OPTIONS' => ['OPTIONS'],
            'Custom' => ['CUSTOM_METHOD'],
            'Lowercase' => ['get'],
        ];
    }

    #[DataProvider('provideUriStrings')]
    public function testCreateRequestWithUriAsString(string $uriString): void
    {
        $factory = $this->createFactory();
        $request = $factory->createRequest('GET', $uriString);

        self::assertInstanceOf(RequestInterface::class, $request);
        self::assertInstanceOf(UriInterface::class, $request->getUri());
        self::assertSame($uriString, (string) $request->getUri());
    }

    /**
     * @return array<array{string}>
     */
    public static function provideUriStrings(): array
    {
        return [
            'empty' => [''],
            'absolute HTTP' => ['http://example.com'],
            'absolute HTTPS' => ['https://example.com/path?query=value#fragment'],
            'path only' => ['/path/to/resource'],
            'full authority' => ['https://user:pass@example.com:8080/path?key=val#hash'],
        ];
    }

    public function testCreateRequestWithUriInterface(): void
    {
        $uriStub = self::createStub(UriInterface::class);

        $factory = $this->createFactory();
        $request = $factory->createRequest('POST', $uriStub);

        self::assertInstanceOf(RequestInterface::class, $request);
        self::assertSame($uriStub, $request->getUri());
    }

    #[DataProvider('provideInvalidUris')]
    public function testCreateRequestThrowsExceptionForInvalidUri(mixed $invalidUri): void
    {
        self::expectException(\Throwable::class);

        $factory = $this->createFactory();
        // @phpstan-ignore argument.type (Intentionally testing invalid values.)
        $factory->createRequest('GET', $invalidUri);
    }

    /**
     * @return array<array{mixed}>
     */
    public static function provideInvalidUris(): array
    {
        return [
            'integer' => [123],
            'float' => [45.67],
            'boolean' => [true],
            'array' => [['https://example.com']],
            'object non-uri' => [new \stdClass()],
            'null' => [null],
        ];
    }
}
