<?php

declare(strict_types=1);

namespace Sabroan\Http\Message;

use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * @api
 */
final class ServerRequestFactory implements ServerRequestFactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @param array<array-key, mixed> $serverParams
     *
     * @psalm-pure
     * @psalm-suppress InvalidReturnStatement, InvalidReturnType, MixedArgument
     */
    #[\Override]
    public function createServerRequest(
        string $method,
        mixed $uri,
        array $serverParams = [],
    ): ServerRequestInterface {
        if (\is_string($uri)) {
            /** @psalm-suppress ImpureMethodCall */
            // @phpstan-ignore possiblyImpure.methodCall
            $uri = new UriFactory()->createUri($uri);
        }

        // @phpstan-ignore possiblyImpure.new
        $request = new ServerRequest($serverParams);

        // @phpstan-ignore possiblyImpure.methodCall
        $request = $request->withMethod($method);

        /** @psalm-suppress ImpureMethodCall */
        // @phpstan-ignore possiblyImpure.methodCall
        $request = $request->withUri($uri);

        return $request;
    }
}
