# PSR-17 HTTP Factory Implementation

A lightweight, implementation of the [PSR-17 (HTTP Factories)](https://www.php-fig.org/psr/psr-17/).

## Installation

Install via Composer:

```bash
composer require sabroan/psr-17
```

## Usage

This package provides factories for creating [PSR-7](https://www.php-fig.org/psr/psr-7/) HTTP message objects (requests, responses, streams, URIs, and uploaded files).

## Classes
- `Sabroan\Http\Message\RequestFactory`
- `Sabroan\Http\Message\ResponseFactory`
- `Sabroan\Http\Message\ServerRequestFactory`
- `Sabroan\Http\Message\StreamFactory`
- `Sabroan\Http\Message\UploadedFileFactory`
- `Sabroan\Http\Message\UriFactory`

## License

This package is released under the MIT License. See the LICENSE file for details.
