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

| Version | Supported |       Date        | Format | Query param |
| :-----: | :-------: | :---------------: | :----: | :---------: |
|   1.2   |    ✅     | November 11, 2018 |  XML   |  `?v=1.2`   |
|   2.0   |    ✅     |       Draft       |  JSON  |  `?v=2.0`   |

All old versions: 0.9, 1.0 and 1.1 have a fallback to OPDS 1.2.

## Requirements

-   `php` v8.1 minimum

## About

OPDS is like RSS feeds but adapted for eBooks, it's a standard to share eBooks between libraries, bookstores, publishers, and readers. Developed by [Hadrien Gardeur](https://github.com/HadrienGardeur) and [Leonard Richardson](https://github.com/leonardr).

This package has been created to be used with [`bookshelves-project/bookshelves`](https://github.com/bookshelves-project/bookshelves), an open source eBook web app.

> [!NOTE]
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

-   [ ] OPDS 1.2: support advanced acquisition feeds
-   [ ] OPDS 2.0: support `Facets`, `Groups`, advanced `belongsTo`
-   [ ] Add [OPDS Page Streaming Extension](https://github.com/anansi-project/opds-pse) from `anansi-project`

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
use Kiwilan\Opds\Opds;

$opds = Opds::make()
  ->title('My feed')
  ->feeds([...])
  ->get()
;

$opds->getConfig(); // OpdsConfig - Configuration used to create OPDS feed set into `make()` method
$opds->getUrl(); // string|null - Current URL, generated automatically but can be overrided with `url()` method
$opds->getTitle(); // string - Title of OPDS feed set with `title()` method
$opds->getVersion(); // OpdsVersionEnum - OPDS version used, determined by query parameter `v` or `OpdsConfig::class` method `forceJson()`
$opds->getQueryVersion(); // OpdsVersionEnum|null - Name of query parameter used to set OPDS version, default is `v`
$opds->getUrlParts(); // array - URL parts, determined from `url`
$opds->getQuery(); // array - Query parameters, determined from `url`
$opds->getFeeds(); // array - Feeds set with `feeds()` method
$opds->checkIfSearch(); // bool, default is false, set to true if `isSearch()` method is used
```

And about engine and response:

```php
use Kiwilan\Opds\Opds;

$opds = Opds::make()
  ->title('My feed')
  ->feeds([...])
  ->get()
;

$opds->getEngine(); // OpdsEngine|null - Engine used to create OPDS feed, determined by OPDS version, can be `OpdsXmlEngine::class` or `OpdsJsonEngine::class`
$opds->getOutput(); // OpdsOutputEnum|null - Output of response, useful for debug
$opds->getPaginator(); // OpdsPaginator|OpdsPaginate|null - Paginator used to paginate feeds, if you use `paginate()` method
$opds->getResponse(); // OpdsResponse|null - Response of OPDS feed, will use `OpdsEngine` to create a response
```

### OPDS Version

You can use query parameter `version` to set it dynamically. You could change this query into `OpdsConfig::class`.

-   Version `1.2` can be set with `?v=1.2`
-   Version `2.0` can be set with `?v=2.0`

> [!WARNING]
>
> If you set `v` query parameter to `1.2` with `OpdsConfig::class` method `forceJson()`, query param will be ignored.

### OPDS Engine

Engine will convert your feeds to OPDS, depending of OPDS version.

-   [OPDS 1.2](https://specs.opds.io/opds-1.2) will use `OpdsXmlEngine::class`
-   [OPDS 2.0](https://drafts.opds.io/opds-2.0) will use `OpdsJsonEngine::class`

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

### OPDS Response

To build OPDS feed, you have to `get()` method. It will return an instance of `Opds` with `OpdsEngine`, `OpdsResponse` and paginator filled.

```php
use Kiwilan\Opds\Opds;

$opds = Opds::make()
  ->title('My feed')
  ->feeds([...])
  ->get() // `Opds` to fill `OpdsEngine`, `OpdsResponse` and paginator
;
```

To get response, you can use `getResponse()` method from `Opds::class`.

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
```

#### Send response

> [!NOTE]
>
> This method is totally optional, you can send response to browser by yourself.

You can send response to browser by yourself from `OpdsResponse` to get status code, headers and contents or use `send()` method available into `Opds` and `OpdsResponse`.

-   You can use `send()` from `Opds` or `OpdsResponse` to send response to browser (exactly the same)
-   You don't have to call `get()` method before `send()` method, `send()` will call `get()` automatically

```php
use Kiwilan\Opds\Opds;

Opds::make()
  ->title('My feed')
  ->feeds([...])
  ->send(); // XML or JSON response
;
```

You can call `get()` method before `send()` method if you want to get `OpdsResponse` instance.

```php
use Kiwilan\Opds\Opds;

$opds = Opds::make()
  ->title('My feed')
  ->feeds([...])
  ->get()
;

// do something with `OpdsResponse` instance

$opds->send(); // XML or JSON response
```

To get response

```php
use Kiwilan\Opds\Opds;

$opds = Opds::make()
  ->title('My feed')
  ->feeds([...])
  ->get();

$response = $opds->getResponse(); // OpdsResponse
$response->send(); // XML or JSON response
```

> [!NOTE]
>
> You can use `exit` parameter from `send()` method to stop script after sending response.

### OPDS Config

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
  versionQuery: 'v', // query parameter for version
  paginationQuery: 'page', // query parameter for pagination
  updated: new DateTime(), // Last update of OPDS feed
  maxItemsPerPage: 16, // Max items per page, default is 16
  forceJson: false, // To force JSON response as OPDS 2.0, default is false
);
```

> [!NOTE]
>
> You can override `OpdsConfig` with setter methods.

#### OPDS Pagination

You can use pagination from `Opds` with `paginate()` method, it will generate pagination based on `maxItemsPerPage` property from `OpdsConfig::class`.

-   If you not set any parameter, it will generate pagination
-   If you set `OpdsPaginate` object, it will generate pagination based on it

```php
use Kiwilan\Opds\Opds;

$opds = Opds::make()
  ->title('My feed')
  ->feeds([...])
  ->paginate() // will generate pagination
  ->get();

$opds->getPaginator(); // OpdsPaginator
```

You can use `OpdsPaginate::class` to handle manual pagination

```php
use Kiwilan\Opds\Opds;

$opds = Opds::make(getConfig())
  ->title('My feed')
  ->url('http://localhost:8080/opds?u=2')
  ->feeds([...])
  ->paginate(new OpdsPaginate(
    currentPage: $page,
    totalItems: $total,
    firstUrl: 'http://localhost:8080/opds?f=1',
    lastUrl: 'http://localhost:8080/opds?l=42',
    previousUrl: 'http://localhost:8080/opds?p=1',
    nextUrl: 'http://localhost:8080/opds?n=3',
  )) // will generate pagination based on `OpdsPaginate` object
  ->get();

$opds->getPaginator(); // OpdsPaginate
```

### OPDS entry

#### Navigation

You can create a navigation entry with `OpdsEntryNavigation::class`:

```php
use Kiwilan\Opds\Entries\OpdsEntryNavigation;

$entry = new OpdsEntryNavigation(
  id: 'authors',
  title: 'Authors',
  route: 'http://mylibrary.com/opds/authors',
  summary: 'Authors, 1 available',
  media: 'https://user-images.githubusercontent.com/48261459/201463225-0a5a084e-df15-4b11-b1d2-40fafd3555cf.svg',
  updated: new DateTime(),
  properties: [
    'numberOfItems' => 1,
  ], // to include extra properties (like numberOfItems for facets)
  relation: 'current', // to specify the relation to use (instead of `current`)
);
```

> [!TIP]
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

> [!WARNING]
>
> Some properties can be used only into OPDS 2.0, see [OPDS 2.0 specification](https://drafts.opds.io/opds-2.0.html#book).

```php
use Kiwilan\Opds\Entries\OpdsEntryBook;
use Kiwilan\Opds\Entries\OpdsEntryBookAuthor;

$entry = new OpdsEntryBook(
  id: 'the-clan-of-the-cave-bear-epub-en',
  title: 'The Clan of the Cave Bear',
  route: 'http://mylibrary.com/opds/books/the-clan-of-the-cave-bear-epub-en',
  summary: 'The Clan of the Cave Bear is an epic work of prehistoric fiction by Jean M. Auel.',
  content: 'The Clan of the Cave Bear is an epic work of prehistoric fiction by Jean M. Auel about prehistoric times. It is the first book in the Earth\'s Children book series which speculates on the possibilities of interactions between Neanderthal and modern Cro-Magnon humans.',
  media: 'https://user-images.githubusercontent.com/48261459/201463225-0a5a084e-df15-4b11-b1d2-40fafd3555cf.svg',
  updated: new DateTime(),
  download: 'http://mylibrary.com/api/download/books/the-clan-of-the-cave-bear-epub-en',
  mediaThumbnail: 'https://user-images.githubusercontent.com/48261459/201463225-0a5a084e-df15-4b11-b1d2-40fafd3555cf.svg',
  categories: ['category'],
  authors: [
    new OpdsEntryBookAuthor(
      name: 'Jean M. Auel',
      uri: 'http://mylibrary.com/opds/authors/jean-m-auel',
    ),
  ],
  published: new DateTime(),
  volume: 1,
  serie: 'Earth\'s Children',
  language: 'English',
  identifier: 'urn:isbn:9780553381672', // to specify the actual identifier to use (instead of `urn:isbn:...`)
  translator: 'translator',
  publisher: 'publisher',
);
```

> [!TIP]
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

> [!TIP]
>
> I advice [Meilisearch](https://www.meilisearch.com/) for search engine, it's a powerful and easy to use search engine.

Here an example:

```php
use Kiwilan\Opds\Opds;
use Kiwilan\Opds\Entries\OpdsEntryBook;

$query = // get query from URL, `q` or `query` param
$feeds = [];

if ($query) {
    $results = []; // use your search engine here

    foreach ($results as $result) {
      $feeds[] = new OpdsEntryBook(
        title: $result->title,
        // ...
      );
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

-   [OPDS creators](https://opds.io/): for OPDS specifications
-   [`ewilan-riviere`](https://github.com/ewilan-riviere): author
-   [Contributors](https://github.com/kiwilan/php-opds/graphs/contributors)
-   [`spatie/array-to-xml`](https://github.com/spatie/array-to-xml): to convert array to XML
-   [`spatie/package-skeleton-php`](https://github.com/spatie/package-skeleton-php): skeleton for PHP package

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[<img src="https://user-images.githubusercontent.com/48261459/201463225-0a5a084e-df15-4b11-b1d2-40fafd3555cf.svg" height="120rem" width="100%" />](https://github.com/kiwilan)

[version-src]: https://img.shields.io/packagist/v/kiwilan/php-opds.svg?style=flat&colorA=18181B&colorB=777BB4
[version-href]: https://packagist.org/packages/kiwilan/php-opds
[php-version-src]: https://img.shields.io/static/v1?style=flat&label=PHP&message=v8.1&color=777BB4&logo=php&logoColor=ffffff&labelColor=18181b
[php-version-href]: https://www.php.net/
[downloads-src]: https://img.shields.io/packagist/dt/kiwilan/php-opds.svg?style=flat&colorA=18181B&colorB=777BB4
[downloads-href]: https://packagist.org/packages/kiwilan/php-opds
[license-src]: https://img.shields.io/github/license/kiwilan/php-opds.svg?style=flat&colorA=18181B&colorB=777BB4
[license-href]: https://github.com/kiwilan/php-opds/blob/main/README.md
[tests-src]: https://img.shields.io/github/actions/workflow/status/kiwilan/php-opds/run-tests.yml?branch=main&label=tests&style=flat&colorA=18181B
[tests-href]: https://packagist.org/packages/kiwilan/php-opds
[codecov-src]: https://img.shields.io/codecov/c/gh/kiwilan/php-opds/main?style=flat&colorA=18181B&colorB=777BB4
[codecov-href]: https://codecov.io/gh/kiwilan/php-opds
