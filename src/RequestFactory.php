<?php

declare(strict_types=1);

namespace Sabroan\Http\Message;

use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * @api
 */
final class RequestFactory implements RequestFactoryInterface
{
    /** {@inheritDoc} */
    #[\Override]
    public function createRequest(
        string $method,
        mixed $uri,
    ): RequestInterface {

        if (false === $uri instanceof UriInterface) {
            $uri = new UriFactory()->createUri($uri);
        }

        return new Request()
            ->withMethod($method)
            ->withUri($uri);
    }
}
