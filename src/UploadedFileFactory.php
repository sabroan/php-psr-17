<?php

declare(strict_types=1);

namespace Sabroan\Http\Message;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UploadedFileInterface;

/**
 * @api
 */
final class UploadedFileFactory implements UploadedFileFactoryInterface
{
    /** {@inheritDoc} */
    #[\Override]
    public function createUploadedFile(
        StreamInterface $stream,
        ?int $size = null,
        int $error = \UPLOAD_ERR_OK,
        ?string $clientFilename = null,
        ?string $clientMediaType = null,
    ): UploadedFileInterface {
        return new UploadedFile(
            source: $stream,
            name: $clientFilename,
            type: $clientMediaType,
            size: (int) ($size ?? $stream->getSize()),
            error: $error,
        );
    }
}
