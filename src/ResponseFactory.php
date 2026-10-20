<?php

declare(strict_types=1);

namespace Sabroan\Http\Message;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @api
 */
final class ResponseFactory implements ResponseFactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @psalm-pure
     */
    #[\Override]
    public function createResponse(
        int $code = 200,
        string $reasonPhrase = '',
    ): ResponseInterface {
        // @phpstan-ignore possiblyImpure.methodCall
        return new Response()
            ->withStatus($code, $reasonPhrase);
    }
}
