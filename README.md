# PHP OPDS

## NOT READY FOR PRODUCTION

[![php][php-version-src]][php-version-href]
[![version][version-src]][version-href]
[![downloads][downloads-src]][downloads-href]
[![license][license-src]][license-href]

[![tests][tests-src]][tests-href]
[![codecov][codecov-src]][codecov-href]

PHP package to create [OPDS feed](https://opds.io/) (Open Publication Distribution System) for eBooks.

## Requirements

-   PHP >= 8.1

## About

OPDS is like RSS feeds but adapted for eBooks, it's a standard to share eBooks between libraries, bookstores, publishers, and readers. Developed by Hadrien Gardeur and Leonard Richardson.

> The Open Publication Distribution System (OPDS) catalog format is a syndication format for electronic publications based on Atom and HTTP. OPDS catalogs enable the aggregation, distribution, discovery, and acquisition of electronic publications. OPDS catalogs use existing or emergent open standards and conventions, with a priority on simplicity.
>
> The Open Publication Distribution System specification is prepared by an informal grouping of partners, combining Internet Archive, O'Reilly Media, Feedbooks, OLPC, and others.
>
> From [Wikipedia](https://en.wikipedia.org/wiki/Open_Publication_Distribution_System)

### Resources

-   [opds.io](https://opds.io/): OPDS official website

## Features

-   [x] OPDS 1.2

## Installation

You can install the package via composer:

```bash
composer require kiwilan/php-opds
```

## Usage

Example of a simple OPDS feed into controller (like Laravel).

```php
use Kiwilan\Opds\Opds;

class OpdsController
{
  public function index()
  {
    return OpdsEngine::response(
      app: new OpdsApp(
        name: 'My OPDS Catalog',
        author: 'John Doe',
        authorUrl: 'https://example.com',
        startUrl: 'https://example.com/opds',
        searchUrl: 'https://example.com/opds/search',
        updated: new DateTime(),
      ),
      entries: [
        new OpdsEntry(
          id: 'authors',
          title: 'Authors',
          route: 'http://localhost:8000/opds/authors',
          summary: 'Authors, 1 available',
          media: 'https://user-images.githubusercontent.com/48261459/201463225-0a5a084e-df15-4b11-b1d2-40fafd3555cf.svg',
          updated: new DateTime(),
        ),
        new OpdsEntry(
          id: 'series',
          title: 'Series',
          route: 'http://localhost:8000/opds/series',
          summary: 'Series, 1 available',
          media: 'https://user-images.githubusercontent.com/48261459/201463225-0a5a084e-df15-4b11-b1d2-40fafd3555cf.svg',
          updated: new DateTime(),
        ),
      ],
    );
  }
}
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

-   [Ewilan Rivière](https://github.com/ewilan-riviere)
-   [spatie/array-to-xml](https://github.com/spatie/array-to-xml)
-   [spatie/package-skeleton-php](https://github.com/spatie/package-skeleton-php)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[version-src]: https://img.shields.io/packagist/v/kiwilan/php-opds.svg?style=flat-square&colorA=18181B&colorB=777BB4
[version-href]: https://packagist.org/packages/kiwilan/steward-laravel
[php-version-src]: https://img.shields.io/static/v1?style=flat-square&label=PHP&message=v8.1&color=777BB4&logo=php&logoColor=ffffff&labelColor=18181b
[php-version-href]: https://www.php.net/
[downloads-src]: https://img.shields.io/packagist/dt/kiwilan/php-opds.svg?style=flat-square&colorA=18181B&colorB=777BB4
[downloads-href]: https://packagist.org/packages/kiwilan/php-opds
[license-src]: https://img.shields.io/github/license/kiwilan/php-opds.svg?style=flat-square&colorA=18181B&colorB=777BB4
[license-href]: https://github.com/kiwilan/php-opds/blob/main/README.md
[tests-src]: https://img.shields.io/github/actions/workflow/status/kiwilan/php-opds/run-tests.yml?branch=main&label=tests&style=flat-square&colorA=18181B
[tests-href]: https://packagist.org/packages/kiwilan/php-opds
[codecov-src]: https://codecov.io/gh/kiwilan/php-opds/branch/main/graph/badge.svg?token=UFISWRY4QW
[codecov-href]: https://codecov.io/gh/kiwilan/php-opds
