# PHP OPDS

![Banner with woman with eReader picture in background and PHP OPDS title](https://raw.githubusercontent.com/kiwilan/php-opds/main/docs/banner.jpg)

[![php][php-version-src]][php-version-href]
[![version][version-src]][version-href]
[![downloads][downloads-src]][downloads-href]
[![license][license-src]][license-href]
[![tests][tests-src]][tests-href]
[![codecov][codecov-src]][codecov-href]

PHP package to create [OPDS feed](https://opds.io/) (Open Publication Distribution System) for eBooks.

| Version | Supported |       Date        | Format |  Query param   |
| :-----: | :-------: | :---------------: | :----: | :------------: |
|   1.2   |    ✅     | November 11, 2018 |  XML   | `?version=1.2` |
|   2.0   |    ✅     |       Draft       |  JSON  | `?version=2.0` |

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

Some resources about OPDS and eBooks:

-   [opds.io](https://opds.io/): OPDS official website
-   [thorium-reader](https://github.com/edrlab/thorium-reader): test OPDS feed with Thorium Reader
-   OPDS feeds examples (these projects don't use `kiwilan/php-opds`)
    -   [gallica.bnf.fr](https://gallica.bnf.fr/opds): Gallica (French National Library)
    -   [cops-demo.slucas.fr](https://cops-demo.slucas.fr/feed.php): COPS (OPDS PHP Server)
-   [kiwilan/php-ebook](https://github.com/kiwilan/php-ebook): PHP package to handle eBook
-   [koreader/koreader](https://github.com/koreader/koreader): eBook reader for Android, iOS, Kindle, Kobo, Linux, macOS, Windows, and more. If your eReader can't use OPDS feeds, you can install KOReader on it.
-   [edrlab/thorium-reader](https://github.com/edrlab/thorium-reader): A cross platform desktop reading app, based on the Readium Desktop toolkit. You can use it to use OPDS feeds and read eBooks.

## Features

-   ⚛️ Generate OPDS XML and JSON feed (navigation feeds and acquisition feeds)
-   👌 Support OPDS 1.2 and 2.0
-   🔖 With pagination option
-   🔍 Search page included, but NOT search engine
-   🌐 Can handle sending response to browser

## Installation

You can install the package via composer:

```bash
composer require kiwilan/php-opds
```

## Usage

### Version

You can use query parameter `version` to set it dynamically. You could change this query into `OpdsConfig::class`.

-   Version `1.2` can be set with `?version=1.2`
-   Version `2.0` can be set with `?version=2.0`

You can use the `Opds::make()` method to create an OPDS instance, default response is XML with OPDS version 1.2, you can force JSON response with `OpdsConfig::class` method `forceJson()`.

```php
use Kiwilan\Opds\Opds;
use Kiwilan\Opds\OpdsConfig;

$opds = Opds::make(new OpdsConfig()) // OpdsConfig::class, optional
  ->title('My feed')
  ->feeds([]) // OpdsEntryNavigation[]|OpdsEntryBook[]|OpdsEntryNavigation|OpdsEntryBook
  ->get()
;
```

You have different informations into `Opds::class`.

Some informations about OPDS instance:

```php
$opds->getConfig(); // OpdsConfig - Configuration used to create OPDS feed set into `make()` method
$opds->getUrl(); // string|null - Current URL, generated automatically but can be overrided with `url()` method
$opds->getTitle(); // string - Title of OPDS feed set with `title()` method
$opds->getVersion(); // OpdsVersionEnum - OPDS version used, determined by query parameter `version` or `OpdsConfig::class` method `forceJson()`
$opds->getQueryVersion(); // OpdsVersionEnum|null - Name of query parameter used to set OPDS version, default is `version`
$opds->getUrlParts(); // array - URL parts, determined from `url`
$opds->getQuery(); // array - Query parameters, determined from `url`
$opds->getFeeds(); // array - Feeds set with `feeds()` method
$opds->checkIfSearch(); // bool, default is false, set to true if `isSearch()` method is used
```

And about engine and response:

```php
$opds->getEngine(); // OpdsEngine|null - Engine used to create OPDS feed, determined by OPDS version, can be `OpdsXmlEngine::class` or `OpdsJsonEngine::class`
$opds->getOutput(); // OpdsOutputEnum|null - Output of response, useful for debug
$opds->getResponse(); // OpdsResponse|null - Response of OPDS feed, will use `OpdsEngine` to create a response
```

### Response

You can send response to browser if you want:

> **Warning**
>
> If you send response to browser, you can't use any method after that.

```php
use Kiwilan\Opds\Opds;

$opds = Opds::make()
  ->title('My feed')
  ->feeds([])
  ->get()
;

return $opds->response(); // XML or JSON response
```

You can send directly response to browser:

```php
use Kiwilan\Opds\Opds;

return Opds::make()
  ->title('My feed')
  ->feeds([])
  ->response();
```

### Config

OPDS config can be set with `OpdsConfig::class`:

```php
<?php

use Kiwilan\Opds\OpdsConfig;

$config = new OpdsConfig(
  name: 'My OPDS Catalog', // Name of OPDS feed
  author: 'John Doe', // Author name
  authorUrl: 'https://example.com', // Author URL
  iconUrl: 'https://example.com/icon.png', // Icon URL
  startUrl: 'https://example.com/opds', // Start URL, will be included in top navigation
  searchUrl: 'https://example.com/opds/search', // Search URL, will be included in top navigation
  searchQuery: 'q', // query parameter for search
  versionQuery: 'version', // query parameter for version
  updated: new DateTime(), // Last update of OPDS feed
  usePagination: false, // To enable pagination, default is false
  maxItemsPerPage: 16, // Max items per page, default is 16
  forceJson: false, // To force JSON response as OPDS 2.0, default is false
);
```

### OPDS entry

#### Navigation

You can create a navigation entry with `OpdsEntryNavigation::class`:

```php
use Kiwilan\Opds\Entries\OpdsEntryNavigation;

$entry = new OpdsEntryNavigation(
  id: 'authors',
  title: 'Authors',
  route: 'http://localhost:8000/opds/authors',
  summary: 'Authors, 1 available',
  media: 'https://user-images.githubusercontent.com/48261459/201463225-0a5a084e-df15-4b11-b1d2-40fafd3555cf.svg',
  updated: new DateTime(),
);
```

And you can add this entry to OPDS feed with `feeds()` method:

```php
use Kiwilan\Opds\Opds;

$opds = Opds::make()
  ->feeds([$entry])
  ->get();
```

#### Book

You can create a book entry with `OpdsEntryBook::class`:

> **Warning**
>
> Some properties can be used only into OPDS 2.0, see [OPDS 2.0 specification](https://drafts.opds.io/opds-2.0.html#book).

```php
use Kiwilan\Opds\Entries\OpdsEntryBook;

$entry = new OpdsEntryBook(
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
  isbn: '9780553381672',
  translator: 'translator',
  publisher: 'publisher',
);
```

And you can add this entry to OPDS feed with `feeds()` method:

```php
$opds = Opds::make()
  ->feeds([$entry])
  ->get();
```

### Search

This package do NOT implements any search engine, you can use your own search engine and use `Opds::class` to create OPDS feed.

Here an example:

```php
use Kiwilan\Opds\Opds;
use Kiwilan\Opds\Entries\OpdsEntryBook;

$query = // get query from URL
$feeds = [];

if ($query) {
    $results = // use your search engine here

    foreach ($results as $result) {
      $feeds[] = new OpdsEntryBook();
    }
}

$opds = Opds::make()
  ->title("Search for {$query}")
  ->isSearch()
  ->feeds($feeds)
  ->get();
```

### More usages

-   [Basic usage](docs/basic-usage.md)
-   [Advanced usage](docs/advanced-usage.md)

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
