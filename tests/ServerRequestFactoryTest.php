<?php

declare(strict_types=1);

namespace Sabroan\Http\Message\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Sabroan\Http\Message\ServerRequestFactory;

#[CoversClass(ServerRequestFactory::class)]
final class ServerRequestFactoryTest extends TestCase
{
    private function createFactory(): ServerRequestFactoryInterface
    {
        return new ServerRequestFactory();
    }

    #[DataProvider('provideMethods')]
    public function testCreateServerRequestWithMethod(string $method): void
    {
        $factory = $this->createFactory();
        $request = $factory->createServerRequest($method, 'https://example.com');

        self::assertInstanceOf(ServerRequestInterface::class, $request);
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
            'Custom' => ['CUSTOM'],
            'Lowercase' => ['get'],
        ];
    }

    #[DataProvider('provideUriStrings')]
    public function testCreateServerRequestWithUriAsString(string $uriString): void
    {
        $factory = $this->createFactory();
        $request = $factory->createServerRequest('GET', $uriString);

        self::assertInstanceOf(ServerRequestInterface::class, $request);
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

    public function testCreateServerRequestWithUriInterfaceStub(): void
    {
        $uriStub = self::createStub(UriInterface::class);

        $factory = $this->createFactory();
        $request = $factory->createServerRequest('POST', $uriStub);

        self::assertInstanceOf(ServerRequestInterface::class, $request);
        self::assertSame($uriStub, $request->getUri());
    }

    /**
     * @param array<string, mixed> $serverParams
     */
    #[DataProvider('provideServerParams')]
    public function testCreateServerRequestWithServerParams(array $serverParams): void
    {
        $factory = $this->createFactory();
        $request = $factory->createServerRequest('GET', 'https://example.com', $serverParams);

        self::assertInstanceOf(ServerRequestInterface::class, $request);
        self::assertSame($serverParams, $request->getServerParams());
    }

    /**
     * @return array<array{mixed}>
     */
    public static function provideServerParams(): array
    {
        return [
            'default empty' => [[]],
            'standard SAPI params' => [[
                'SERVER_PROTOCOL' => 'HTTP/1.1',
                'REQUEST_METHOD' => 'POST',
                'SCRIPT_NAME' => '/index.php',
                'REQUEST_URI' => '/path?query=1',
                'QUERY_STRING' => 'query=1',
                'HTTP_HOST' => 'localhost',
                'HTTP_USER_AGENT' => 'PHPUnit',
            ]],
            'custom array structures' => [[
                'CUSTOM_PARAM' => ['nested' => 'value'],
            ]],
        ];
    }

    #[DataProvider('provideInvalidUris')]
    public function testCreateServerRequestThrowsExceptionForInvalidUri(mixed $invalidUri): void
    {
        self::expectException(\Throwable::class);

        $factory = $this->createFactory();
        // @phpstan-ignore argument.type (Intentionally testing invalid values.)
        $factory->createServerRequest('GET', $invalidUri);
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
