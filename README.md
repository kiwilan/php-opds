# PHP OPDS

![Banner with woman with eReader picture in background and PHP OPDS title](https://raw.githubusercontent.com/kiwilan/php-opds/main/docs/banner.jpg)

[![php][php-version-src]][php-version-href]
[![version][version-src]][version-href]
[![downloads][downloads-src]][downloads-href]
[![license][license-src]][license-href]
[![tests][tests-src]][tests-href]
[![codecov][codecov-src]][codecov-href]

PHP package to create [OPDS feed](https://opds.io/) (Open Publication Distribution System) for eBooks.

> **Warning**
>
> This package is not ready for production.

| Version | Supported | Latest | Draft |       Date        |  Planned  | Format |
| :-----: | :-------: | :----: | :---: | :---------------: | :-------: | :----: |
|   1.2   |    ✅     |   ✅   |       | November 11, 2018 | Supported |  XML   |
|   2.0   |    ❌     |        |  ✅   |                   |    ✅     |  JSON  |

## Requirements

-   PHP >= 8.1

## About

OPDS is like RSS feeds but adapted for eBooks, it's a standard to share eBooks between libraries, bookstores, publishers, and readers. Developed by [Hadrien Gardeur](https://github.com/HadrienGardeur) and [Leonard Richardson](https://github.com/leonardr).

This package has been created to be used with [bookshelves-project/bookshelves](https://github.com/bookshelves-project/bookshelves), an open source eBook web app.

> The Open Publication Distribution System (OPDS) catalog format is a syndication format for electronic publications based on Atom and HTTP. OPDS catalogs enable the aggregation, distribution, discovery, and acquisition of electronic publications. OPDS catalogs use existing or emergent open standards and conventions, with a priority on simplicity.
>
> The Open Publication Distribution System specification is prepared by an informal grouping of partners, combining Internet Archive, O'Reilly Media, Feedbooks, OLPC, and others.
>
> From [Wikipedia](https://en.wikipedia.org/wiki/Open_Publication_Distribution_System)

### Resources

-   [opds.io](https://opds.io/): OPDS official website
-   [thorium-reader](https://github.com/edrlab/thorium-reader): test OPDS feed with Thorium Reader
-   OPDS feeds examples (these projects don't use `kiwilan/php-opds`)
    -   [gallica.bnf.fr](https://gallica.bnf.fr/opds): Gallica (French National Library)
    -   [cops-demo.slucas.fr](https://cops-demo.slucas.fr/feed.php): COPS (OPDS PHP Server)

## Installation

You can install the package via composer:

```bash
composer require kiwilan/php-opds
```

## Usage

### Response

You can use the `Opds::make()` method to create an OPDS response, default response is XML with OPDS version 1.2.

> **Note**
>
> You can use the `OpdsVersionEnum` to set the OPDS version statically or use query parameter `version` to set it dynamically. You could change this query into `OpdsConfig::class`.
>
> -   Version `1.2` can be set with `?version=1.2`
> -   Version `2.0` can be set with `?version=2.0`

```php
<?php

use Kiwilan\Opds\Opds;
use Kiwilan\Opds\OpdsConfig;
use Kiwilan\Opds\OpdsVersionEnum;

class OpdsController
{
  public function index()
  {
    return Opds::make(
      config: new OpdsConfig(),
      feeds: [], // OpdsEntry[]|OpdsEntryBook[]
      title: 'My feed',
      url: 'https://example.com/opds', // Can be null to be set automatically
      version: OpdsVersionEnum::v1_2, // OPDS version
      asString: false, // Output as string
      isSearch: false, // Is search feed
    );
  }
}
```

OPDS config can be set with `OpdsConfig::class`:

```php
<?php

use Kiwilan\Opds\OpdsConfig;

new OpdsConfig(
  name: 'My OPDS Catalog',
  author: 'John Doe',
  authorUrl: 'https://example.com',
  iconUrl: 'https://example.com/icon.png',
  startUrl: 'https://example.com/opds',
  searchUrl: 'https://example.com/opds/search',
  searchQuery: 'q',
  versionQuery: 'version',
  updated: new DateTime(),
  usePagination: true,
  maxItemsPerPage: 32,
);
```

### Basic usage

Example of a simple OPDS feed into controller (like Laravel).

```php
<?php

use Kiwilan\Opds\Opds;
use Kiwilan\Opds\OpdsConfig;
use Kiwilan\Opds\Entries\OpdsEntry;
use Kiwilan\Opds\Entries\OpdsEntryBook;
use Kiwilan\Opds\Entries\OpdsEntryBookAuthor;

class OpdsController
{
  public function index()
  {
    $opds = Opds::make(
      config: new OpdsConfig(
        name: 'My OPDS Catalog',
        author: 'John Doe',
        authorUrl: 'https://example.com',
        startUrl: 'https://example.com/opds',
        searchUrl: 'https://example.com/opds/search',
        updated: new DateTime(),
      ),
      feeds: [
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

    return $opds->response();
  }

  public function books()
  {
    $opds = Opds::make(
      config: new OpdsConfig(
        name: 'My OPDS Catalog',
        author: 'John Doe',
        authorUrl: 'https://example.com',
        startUrl: 'https://example.com/opds',
        searchUrl: 'https://example.com/opds/search',
        updated: new DateTime(),
      ),
      feeds: [
        new OpdsEntryBook(
          id: 'the-clan-of-the-cave-bear-epub-en',
          title: 'The Clan of the Cave Bear',
          route: 'http://localhost:8000/opds/books/the-clan-of-the-cave-bear-epub-en',
          summary: 'The Clan of the Cave Bear is an epic work of prehistoric fiction by Jean M. Auel.',
          content: 'The Clan of the Cave Bear is an epic work of prehistoric fiction by Jean M. Auel about prehistoric times. It is the first book in the Earth\'s Children book series which speculates on the possibilities of interactions between Neanderthal and modern Cro-Magnon humans.',
          media: 'https://user-images.githubusercontent.com/48261459/201463225-0a5a084e-df15-4b11-b1d2-40fafd3555cf.svg',
          updated: new DateTime(),
          download: 'http://localhost:8000/api/download/books/the-clan-of-the-cave-bear-epub-en',
          mediaThumbnail: 'https://user-images.githubusercontent.com/48261459/201463225-0a5a084e-df15-4b11-b1d2-40fafd3555cf.svg',
          categories: ['category'],
          authors: [
              new OpdsEntryBookAuthor(
                  name: 'Jean M. Auel',
                  uri: 'http://localhost:8000/opds/authors/jean-m-auel',
              ),
          ],
          published: new DateTime(),
          volume: 1,
          serie: 'Earth\'s Children',
          language: 'English',
        ),
      ],
    );

    return $opds->response();
  }
}
```

### Advanced usage

-   [With Laravel application](docs/real-world-application.md)

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

[<img src="https://user-images.githubusercontent.com/48261459/201463225-0a5a084e-df15-4b11-b1d2-40fafd3555cf.svg" height="120rem" width="100%" />](https://github.com/kiwilan)

[version-src]: https://img.shields.io/packagist/v/kiwilan/php-opds.svg?style=flat-square&colorA=18181B&colorB=777BB4
[version-href]: https://packagist.org/packages/kiwilan/php-opds
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
