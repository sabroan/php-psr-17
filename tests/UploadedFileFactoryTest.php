<?php

declare(strict_types=1);

namespace Sabroan\Http\Message\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UploadedFileInterface;
use Sabroan\Http\Message\UploadedFileFactory;

#[CoversClass(UploadedFileFactory::class)]
final class UploadedFileFactoryTest extends TestCase
{
    private function createFactory(): UploadedFileFactoryInterface
    {
        return new UploadedFileFactory();
    }

    public function testCreateUploadedFileWithDefaultParameters(): void
    {
        $stream = self::createStub(StreamInterface::class);
        $stream->method('getSize')->willReturn(1024);

        $factory = $this->createFactory();
        $uploadedFile = $factory->createUploadedFile($stream);

        self::assertInstanceOf(UploadedFileInterface::class, $uploadedFile);
        self::assertSame($stream, $uploadedFile->getStream());
        self::assertSame(1024, $uploadedFile->getSize());
        self::assertSame(\UPLOAD_ERR_OK, $uploadedFile->getError());
        self::assertNull($uploadedFile->getClientFilename());
        self::assertNull($uploadedFile->getClientMediaType());
    }

    #[DataProvider('provideUploadedFileCases')]
    public function testCreateUploadedFileWithExplicitParameters(
        ?int $size,
        int $error,
        ?string $clientFilename,
        ?string $clientMediaType,
    ): void {
        $stream = self::createStub(StreamInterface::class);

        $factory = $this->createFactory();
        $uploadedFile = $factory->createUploadedFile(
            $stream,
            $size,
            $error,
            $clientFilename,
            $clientMediaType,
        );

        self::assertInstanceOf(UploadedFileInterface::class, $uploadedFile);
        self::assertSame($size, $uploadedFile->getSize());
        self::assertSame($error, $uploadedFile->getError());
        self::assertSame($clientFilename, $uploadedFile->getClientFilename());
        self::assertSame($clientMediaType, $uploadedFile->getClientMediaType());

        if ($error === \UPLOAD_ERR_OK) {
            self::assertSame($stream, $uploadedFile->getStream());
        }
    }

    /**
     * @return array<array{null|int|string}>
     */
    public static function provideUploadedFileCases(): array
    {
        return [
            'explicit size and custom media info' => [
                2048,
                \UPLOAD_ERR_OK,
                'document.pdf',
                'application/pdf',
            ],
            'upload error status code' => [
                0,
                \UPLOAD_ERR_INI_SIZE,
                'large_file.zip',
                'application/zip',
            ],
            'null filename and media type' => [
                512,
                \UPLOAD_ERR_OK,
                null,
                null,
            ],
            'empty filename and octet stream media type' => [
                128,
                \UPLOAD_ERR_PARTIAL,
                '',
                'application/octet-stream',
            ],
        ];
    }

    #[DataProvider('provideInvalidErrorCodes')]
    public function testCreateUploadedFileThrowsExceptionWithInvalidErrorCode(int $errorCode): void
    {
        $stream = self::createStub(StreamInterface::class);

        self::expectException(\InvalidArgumentException::class);

        $factory = $this->createFactory();
        $factory->createUploadedFile($stream, null, $errorCode);
    }

    /**
     * @return array<array{int}>
     */
    public static function provideInvalidErrorCodes(): array
    {
        return [
            'negative error code' => [-1],
            'undefined high error code' => [999],
        ];
    }
}
