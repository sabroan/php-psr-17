<?php

declare(strict_types=1);

namespace Sabroan\Http\Message;

use Psr\Http\Message\UriInterface;
use Psr\Http\Message\UriFactoryInterface;

/**
 * @api
 */
final class UriFactory implements UriFactoryInterface
{
    /** {@inheritDoc} */
    #[\Override]
    public function createUri(string $uri = ''): UriInterface
    {
        $components = \parse_url($uri);

        if (false === $components) {
            throw new \RuntimeException("Unable to parse URI: '{$uri}'");
        }

        return (new Uri())
            ->withScheme($components['scheme'] ?? '')
            ->withHost($components['host'] ?? '')
            ->withPort($components['port'] ?? null)
            ->withUserInfo($components['user'] ?? '', $components['pass'] ?? '')
            ->withPath($components['path'] ?? '')
            ->withQuery($components['query'] ?? '')
            ->withFragment($components['fragment'] ?? '');
    }
}
