<?php

declare(strict_types=1);

namespace Sabroan\Http\Message\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Sabroan\Http\Message\ResponseFactory;

#[CoversClass(ResponseFactory::class)]
final class ResponseFactoryTest extends TestCase
{
    private function createFactory(): ResponseFactoryInterface
    {
        return new ResponseFactory();
    }

    public function testCreateResponseDefaultParameters(): void
    {
        $factory = $this->createFactory();
        $response = $factory->createResponse();

        self::assertInstanceOf(ResponseInterface::class, $response);
        self::assertSame(200, $response->getStatusCode());
        self::assertSame('OK', $response->getReasonPhrase());
    }

    #[DataProvider('provideStatusCodes')]
    public function testCreateResponseWithStatusCode(int $code): void
    {
        $factory = $this->createFactory();
        $response = $factory->createResponse($code);

        self::assertInstanceOf(ResponseInterface::class, $response);
        self::assertSame($code, $response->getStatusCode());
    }

    /**
     * @return array<array{int}>
     */
    public static function provideStatusCodes(): array
    {
        return [
            '100 Continue' => [100],
            '200 OK' => [200],
            '201 Created' => [201],
            '301 Moved Permanently' => [301],
            '400 Bad Request' => [400],
            '404 Not Found' => [404],
            '500 Internal Server Error' => [500],
            '599 Upper Limit' => [599],
        ];
    }

    #[DataProvider('provideReasonPhrases')]
    public function testCreateResponseWithReasonPhrase(int $code, string $reasonPhrase, string $expectedPhrase): void
    {
        $factory = $this->createFactory();
        $response = $factory->createResponse($code, $reasonPhrase);

        self::assertInstanceOf(ResponseInterface::class, $response);
        self::assertSame($code, $response->getStatusCode());
        self::assertSame($expectedPhrase, $response->getReasonPhrase());
    }

    /**
     * @return array<array{int|string}>
     */
    public static function provideReasonPhrases(): array
    {
        return [
            'standard phrase' => [200, 'OK', 'OK'],
            'custom phrase' => [200, 'Custom Reason Phrase', 'Custom Reason Phrase'],
            'empty phrase defaults to standard' => [200, '', 'OK'],
            'custom error phrase' => [404, 'Resource Missing', 'Resource Missing'],
            'unassigned status code empty phrase' => [499, '', ''],
        ];
    }

    #[DataProvider('provideInvalidStatusCodes')]
    public function testCreateResponseThrowsExceptionForInvalidStatusCode(int $code): void
    {
        self::expectException(\Throwable::class);

        $factory = $this->createFactory();
        $factory->createResponse($code);
    }

    /**
     * @return array<array{int}>
     */
    public static function provideInvalidStatusCodes(): array
    {
        return [
            'below lower limit' => [99],
            'above upper limit' => [600],
            'negative status code' => [-1],
        ];
    }
}
