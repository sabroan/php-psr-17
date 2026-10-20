<?php

declare(strict_types=1);

namespace Sabroan\Http\Message;

use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @api
 */
final class StreamFactory implements StreamFactoryInterface
{
    /** {@inheritDoc} */
    #[\Override]
    public function createStream(string $content = ''): StreamInterface
    {
        $stream = $this->createStreamFromFile('php://temp', 'r+b');

        if ('' !== $content) {
            $stream->write($content);
            $stream->rewind();
        }

        return $stream;
    }

    /** {@inheritDoc} */
    #[\Override]
    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        if (! (bool) \preg_match('/^[rwaXc][bt]?\+?[bt]?$/i', $mode)) {
            throw new \InvalidArgumentException("Invalid mode '{$mode}' provided");
        }

        if ('r' === $mode[0] && !\str_contains($filename, '://') && !\file_exists($filename)) {
            throw new \RuntimeException("File '{$filename}' does not exist");
        }

        if (false === $resource = \fopen($filename, $mode)) {
            throw new \RuntimeException("Unable to open file '{$filename}'");
        }

        return $this->createStreamFromResource($resource);
    }

    /** {@inheritDoc} */
    #[\Override]
    public function createStreamFromResource(mixed $resource): StreamInterface
    {
        return new Stream($resource);
    }
}
