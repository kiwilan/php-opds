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

### Response

You can use the `Opds::response()` method to create an OPDS response, default response is XML with OPDS version 1.2.

```php
use Kiwilan\Opds\Opds;

class OpdsController
{
  public function index()
  {
    return Opds::response(
      app: new OpdsApp(),
      entries: [], // OpdsEntry[]|OpdsEntryBook[]
      title: 'My feed',
      url: 'https://example.com/opds', // Can be null to be set automatically
      version: '1.2', // OPDS version
      asString: false, // Output as string
      isSearch: false, // Is search feed
    );
  }
}
```

### Basic usage

Example of a simple OPDS feed into controller (like Laravel).

```php
use Kiwilan\Opds\Opds;
use Kiwilan\Opds\Models\OpdsEntry;
use Kiwilan\Opds\Models\OpdsEntryBook;
use Kiwilan\Opds\Models\OpdsEntryBookAuthor;

class OpdsController
{
  public function index()
  {
    return Opds::response(
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

  public function books()
  {
    return Opds::response(
      app: new OpdsApp(
        name: 'My OPDS Catalog',
        author: 'John Doe',
        authorUrl: 'https://example.com',
        startUrl: 'https://example.com/opds',
        searchUrl: 'https://example.com/opds/search',
        updated: new DateTime(),
      ),
      entries: [
        new OpdsEntryBook(
          id: 'the-clan-of-the-cave-bear-epub-en',
          title: 'The Clan of the Cave Bear',
          route: 'http://localhost:8000/opds/books/the-clan-of-the-cave-bear-epub-en',
          summary: 'The Clan of the Cave Bear is an epic work of prehistoric fiction by Jean M. Auel about prehistoric times. It is the first book in the Earth\'s Children book series which speculates on the possibilities of interactions between Neanderthal and modern Cro-Magnon humans.',
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
  }
}
```

### Real world example

> **Note**
> This example use Laravel but you could use `kiwilan/php-opds` with any PHP framework.

You could create a file like `OpdsConfig.php` to store all your OPDS configuration.

-   `app()` is the OPDS app configuration
-   `home()` is the OPDS home page
-   `bookToEntry()` is a function to convert a book to an OPDS entry

```php
<?php

namespace App\Opds;

use App\Models\Author;
use App\Models\Book;
use App\Models\Serie;
use Closure;
use Illuminate\Support\Facades\Cache;
use Kiwilan\Opds\Models\OpdsApp;
use Kiwilan\Opds\Models\OpdsEntry;
use Kiwilan\Opds\Models\OpdsEntryBook;
use Kiwilan\Opds\Models\OpdsEntryBookAuthor;

class OpdsConfig
{
    public static function app(): OpdsApp
    {
        return new OpdsApp(
            name: config('app.name'),
            author: 'Bookshelves',
            authorUrl: config('app.url'),
            startUrl: route('opds.index'),
            searchUrl: route('opds.search'),
            updated: Book::orderBy('updated_at', 'desc')->first()->updated_at,
        );
    }

    /**
     * @return array<OpdsEntry>
     */
    public static function home(): array
    {
        $authors = self::cache('opds.authors', fn () => Author::all());
        $series = self::cache('opds.series', fn () => Serie::all());

        return [
            new OpdsEntry(
                id: 'authors',
                title: 'Authors',
                route: route('opds.authors.index'),
                summary: "Authors, {$authors->count()} available",
                media: asset('vendor/images/opds/authors.png'),
                updated: Author::orderBy('updated_at', 'desc')->first()->updated_at,
            ),
            new OpdsEntry(
                id: 'series',
                title: 'Series',
                route: route('opds.series.index'),
                summary: "Series, {$series->count()} available",
                media: asset('vendor/images/opds/series.png'),
                updated: Serie::orderBy('updated_at', 'desc')->first()->updated_at,
            ),
        ];
    }

    public static function cache(string $name, Closure $closure): mixed
    {
        if (config('app.env') === 'local') {
            Cache::forget($name);
        }

        $cache = 60 * 60 * 24;

        return Cache::remember($name, $cache, $closure);
    }

    public static function bookToEntry(Book $book): OpdsEntryBook
    {
        $book = $book->load('authors', 'serie', 'tags');
        $series = null;
        $seriesContent = null;

        if ($book->serie) {
            $seriesTitle = $book->serie->title;

            $series = " ({$seriesTitle} vol. {$book->volume})";
            $seriesContent = "<strong>Series {$seriesTitle} {$book->volume}</strong><br>";
        }

        $authors = [];

        foreach ($book->authors as $author) {
            $authors[] = new OpdsEntryBookAuthor(
                name: $author->name,
                uri: route('opds.authors.show', ['author' => $author->slug]),
            );
        }

        return new OpdsEntryBook(
            id: $book->slug,
            title: "{$book->title}{$series}",
            summary: "{$seriesContent}{$book->description}",
            updated: $book->updated_at,
            route: route('opds.books.show', ['author' => $book->meta_author, 'book' => $book->slug]),
            download: route('api.download.book', ['author_slug' => $book->meta_author, 'book_slug' => $book->slug]),
            media: $book->cover_og,
            mediaThumbnail: $book->cover_thumbnail,
            categories: $book->tags->pluck('name')->toArray(),
            authors: $authors,
            published: $book->released_on,
            volume: $book->volume,
            serie: $book->serie?->title,
            language: $book->language?->name,
        );
    }
}
```

And then you can use it into any controller.

```php
<?php

namespace App\Http\Controllers\Opds;

use App\Opds\OpdsConfig;
use App\Engines\SearchEngine;
use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Kiwilan\Opds\Models\OpdsApp;
use Kiwilan\Opds\Models\OpdsEntry;
use Kiwilan\Opds\Models\OpdsEntryBook;
use Kiwilan\Opds\Models\OpdsEntryBookAuthor;

class IndexController extends Controller
{
    public function index()
    {
        return Opds::response(
            app: OpdsConfig::app(),
            entries: OpdsConfig::home(),
        );
    }

    public function search(Request $request)
    {
        $query = $request->input('q');
        $search = SearchEngine::make(q: $query, relevant: false, opds: true, types: ['books']);

        $entries = [];

        foreach ($search->results_opds as $result) {
            /** @var Book $result */
            $entries[] = OpdsConfig::bookToEntry($result);
        }

        return Opds::response(
            app: OpdsConfig::app(),
            entries: $entries,
            title: "Search for {$query}",
            isSearch: true,
        );
    }
}
```

You could create book OPDS page.

```php
<?php

namespace App\Http\Controllers\Opds;

use App\Opds\OpdsConfig;
use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Book;
use Kiwilan\Opds\Models\OpdsApp;
use Kiwilan\Opds\Models\OpdsEntry;
use Kiwilan\Opds\Models\OpdsEntryBook;
use Kiwilan\Opds\Models\OpdsEntryBookAuthor;

class BookController extends Controller
{
    public function show(string $author_slug, string $book_slug)
    {
        $author = Author::whereSlug($author_slug)->firstOrFail();
        $book = Book::whereAuthorMainId($author->id)
            ->whereSlug($book_slug)
            ->firstOrFail()
        ;

        return Opds::response(
            app: OpdsConfig::app(),
            entries: [
                OpdsConfig::bookToEntry($book),
            ],
            title: "Book {$book->title}",
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
