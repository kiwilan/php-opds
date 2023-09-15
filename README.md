# PHP OPDS

![Banner with woman with eReader picture in background and PHP OPDS title](https://raw.githubusercontent.com/kiwilan/php-opds/main/docs/banner.jpg)

[![php][php-version-src]][php-version-href]
[![version][version-src]][version-href]
[![downloads][downloads-src]][downloads-href]
[![license][license-src]][license-href]
[![tests][tests-src]][tests-href]
[![codecov][codecov-src]][codecov-href]

PHP package to create [OPDS feed](https://opds.io/) (Open Publication Distribution System) for eBooks.

-   **Demo**: <https://bookshelves.ink/opds> from [`bookshelves-project/bookshelves`](https://github.com/bookshelves-project/bookshelves)

| Version | Supported |       Date        | Format |  Query param   |
| :-----: | :-------: | :---------------: | :----: | :------------: |
|   1.2   |    ✅     | November 11, 2018 |  XML   | `?version=1.2` |
|   2.0   |    ✅     |       Draft       |  JSON  | `?version=2.0` |

All old versions: 0.9, 1.0 and 1.1 have a fallback to OPDS 1.2.

## Requirements

-   PHP >= 8.1

## About

OPDS is like RSS feeds but adapted for eBooks, it's a standard to share eBooks between libraries, bookstores, publishers, and readers. Developed by [Hadrien Gardeur](https://github.com/HadrienGardeur) and [Leonard Richardson](https://github.com/leonardr).

This package has been created to be used with [`bookshelves-project/bookshelves`](https://github.com/bookshelves-project/bookshelves), an open source eBook web app.

> The Open Publication Distribution System (OPDS) catalog format is a syndication format for electronic publications based on Atom and HTTP. OPDS catalogs enable the aggregation, distribution, discovery, and acquisition of electronic publications. OPDS catalogs use existing or emergent open standards and conventions, with a priority on simplicity.
>
> The Open Publication Distribution System specification is prepared by an informal grouping of partners, combining Internet Archive, O'Reilly Media, Feedbooks, OLPC, and others.
>
> From [Wikipedia](https://en.wikipedia.org/wiki/Open_Publication_Distribution_System)

Some resources about OPDS and eBooks:

-   [opds.io](https://opds.io/): OPDS official website
-   OPDS feeds examples
    -   [bookshelves.ink](https://bookshelves.ink/opds): Bookshelves (eBook web app, which use `kiwilan/php-opds`)
    -   [gallica.bnf.fr](https://gallica.bnf.fr/opds): Gallica (French National Library)
    -   [cops-demo.slucas.fr](https://cops-demo.slucas.fr/feed.php): COPS (OPDS PHP Server)
    -   [feedbooks.com](https://catalog.feedbooks.com/catalog/public_domain.atom): Feedbooks
-   [`kiwilan/php-ebook`](https://github.com/kiwilan/php-ebook): PHP package to handle eBook
-   [`koreader/koreader`](https://github.com/koreader/koreader): eBook reader for Android, iOS, Kindle, Kobo, Linux, macOS, Windows, and more. If your eReader can't use OPDS feeds, you can install KOReader on it
-   [`edrlab/thorium-reader`](https://github.com/edrlab/thorium-reader): A cross platform desktop reading app, based on the Readium Desktop toolkit. You can use it to use OPDS feeds and read eBooks

## Features

-   ⚛️ Generate OPDS XML and JSON feed (navigation feeds and acquisition feeds)
-   👌 Support OPDS 1.2 and 2.0
-   🔖 With pagination option
-   🔍 Search page included, but NOT search engine
-   🌐 Option to handle response to browser as XML or JSON

### Roadmap

-   OPDS 1.2: support advanced acquisition feeds
-   OPDS 2.0: support `Facets`, `Groups`, advanced `belongsTo`
-   Add [OPDS Page Streaming Extension](- https://github.com/anansi-project/opds-pse) from `anansi-project`

## Installation

You can install the package via composer:

```bash
composer require kiwilan/php-opds
```

## Usage

You have to use `Opds::make()` method to create an OPDS instance, the only param is `config` to set OPDS config, totally optional. Default response is XML with OPDS version 1.2, you can force JSON response with `OpdsConfig::class` method `forceJson()` to use only OPDS 2.0. With `get()` method, you can get full instance of `Opds` with `OpdsEngine` and `OpdsResponse`.

```php
use Kiwilan\Opds\Opds;
use Kiwilan\Opds\OpdsConfig;

$opds = Opds::make(new OpdsConfig()) // OpdsConfig::class, optional
  ->title('My feed')
  ->feeds([...]) // OpdsEntryNavigation[]|OpdsEntryBook[]|OpdsEntryNavigation|OpdsEntryBook
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
$opds->getPaginator(); // Paginator|null - Paginator used to paginate feeds, determined by `OpdsConfig::class` method `usePagination()` or `useAutoPagination()`
$opds->getResponse(); // OpdsResponse|null - Response of OPDS feed, will use `OpdsEngine` to create a response
```

### Version

You can use query parameter `version` to set it dynamically. You could change this query into `OpdsConfig::class`.

-   Version `1.2` can be set with `?version=1.2`
-   Version `2.0` can be set with `?version=2.0`

> **Warning**
>
> If you set `version` query parameter to `1.2` with `OpdsConfig::class` method `forceJson()`, it will be ignored.

### Engine

Engine will convert your feeds to OPDS, depending of OPDS version.

-   OPDS 1.2 will use `OpdsXmlEngine::class`
-   OPDS 2.0 will use `OpdsJsonEngine::class`

You can get engine used with `getEngine()` method from `Opds::class`. Property `contents` contains array of feeds, `OpdsEngine` allow conversion into XML or JSON with `__toString()` method, the output depends of OPDS version.

```php
use Kiwilan\Opds\Opds;

$opds = Opds::make()
  ->title('My feed')
  ->feeds([...])
  ->get()
;

$engine = $opds->getEngine(); // OpdsEngine
$contents = $engine->getContents(); // array
$output = $engine->__toString(); // string
```

### Response

You can use `get()` method and after that, use `send()` method to send response to browser.

```php
use Kiwilan\Opds\Opds;

$opds = Opds::make()
  ->title('My feed')
  ->feeds([...])
  ->get()
;

$opds->send(); // XML or JSON response, stop script
```

You can send directly response to browser:

> **Warning**
>
> If you send response to browser, you can't use any method after that.

```php
use Kiwilan\Opds\Opds;

Opds::make()
  ->title('My feed')
  ->feeds([...])
  ->send(); // XML or JSON response, stop script
```

To get only instance of `OpdsResponse`, you can use `getResponse()` method from `Opds::class`. You can use this response to get status code, headers and contents, you can send it to browser by yourself or use `send()` method.

```php
use Kiwilan\Opds\Opds;

$opds = Opds::make()
  ->title('My feed')
  ->feeds([...])
  ->get()
;

$response = $opds->getResponse(); // OpdsResponse

$response->getStatus(); // int - Status code of response
$response->isJson(); // bool - If response is JSON
$response->isXml(); // bool - If response is XML
$response->getHeaders(); // array - Headers of response
$response->getContents(); // string - Contents of response

$response->send(); // Send response to browser, stop script
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
  versionQuery: 'version', // query parameter for version
  paginationQuery: 'page', // query parameter for pagination
  updated: new DateTime(), // Last update of OPDS feed
  usePagination: false, // To enable pagination, default is false
  useAutoPagination: false, // To enable auto pagination, default is false, if `usePagination` is true, this option will be ignored
  maxItemsPerPage: 16, // Max items per page, default is 16
  forceJson: false, // To force JSON response as OPDS 2.0, default is false
);
```

> **Note**
>
> You can override `OpdsConfig` with setter methods.

#### Pagination

You can use pagination with `OpdsConfig::class` method `usePagination()` or `useAutoPagination()`.

-   `usePagination()` will paginate feeds based on `maxItemsPerPage` property
-   `useAutoPagination()` will paginate only `OpdsEntryBook` feeds if exceed `maxItemsPerPage` property
    -   Useful if you have a lot of navigations feeds, e.g. 1000 authors, you don't want to paginate this feed

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
  properties: [
    'numberOfItems' => 1,
  ], // to include extra properties (like numberOfItems for facets)
  relation: 'current', // to specify the relation to use (instead of `current`)
);
```

> **Note**
>
> You can override `OpdsEntryNavigation` with setter methods.

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
use Kiwilan\Opds\Entries\OpdsEntryBookAuthor;

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
  isbn: '9780553381672', // deprecated, use `identifier` instead
  identifier: 'urn:isbn:9780553381672', // to specify the actual identifier to use (instead of `urn:isbn:...`)
  translator: 'translator',
  publisher: 'publisher',
);
```

> **Note**
>
> You can override `OpdsEntryBook` with setter methods.

And you can add this entry to OPDS feed with `feeds()` method:

```php
$opds = Opds::make()
  ->feeds([$entry])
  ->get();
```

### Search

This package do NOT implements any search engine, you can use your own search engine and use `Opds::class` to create OPDS feed.

**Query parameters used for search are statically defined into specifications**:

-   `q` param is used by OPDS 1.2
-   `query` param is used by OPDS 2.0

> **Note**
>
> I advice [Meilisearch](https://www.meilisearch.com/) for search engine, it's a powerful and easy to use search engine.

Here an example:

```php
use Kiwilan\Opds\Opds;
use Kiwilan\Opds\Entries\OpdsEntryBook;

$query = // get query from URL, `q` or `query` param
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

-   [`ewilan-riviere`](https://github.com/ewilan-riviere): Author
-   [`spatie/array-to-xml`](https://github.com/spatie/array-to-xml): to convert array to XML
-   [`spatie/package-skeleton-php`](https://github.com/spatie/package-skeleton-php): skeleton for PHP package
-   [Contributors](https://github.com/kiwilan/php-opds/graphs/contributors)

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
