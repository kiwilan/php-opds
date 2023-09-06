# Advanced usage

## Laravel

> **Note**
>
> This example use Laravel but you could use `kiwilan/php-opds` with any PHP framework.

You could create a file like `MyOpds.php` to store all your OPDS configuration.

-   `config()` is the OPDS config configuration
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
use Kiwilan\Opds\OpdsConfig;
use Kiwilan\Opds\Entries\OpdsEntryNavigation;
use Kiwilan\Opds\Entries\OpdsEntryBook;
use Kiwilan\Opds\Entries\OpdsEntryBookAuthor;

class MyOpds
{
    public static function config(): OpdsConfig
    {
        return new OpdsConfig(
            name: config('app.name'),
            author: 'Bookshelves',
            authorUrl: config('app.url'),
            startUrl: route('opds.index'),
            searchUrl: route('opds.search'),
            updated: Book::orderBy('updated_at', 'desc')->first()->updated_at,
        );
    }

    /**
     * @return array<OpdsEntryNavigation>
     */
    public static function home(): array
    {
        $authors = self::cache('opds.authors', fn () => Author::all());
        $series = self::cache('opds.series', fn () => Serie::all());

        return [
            new OpdsEntryNavigation(
                id: 'authors',
                title: 'Authors',
                route: route('opds.authors.index'),
                summary: "Authors, {$authors->count()} available",
                media: asset('vendor/images/opds/authors.png'),
                updated: Author::orderBy('updated_at', 'desc')->first()->updated_at,
            ),
            new OpdsEntryNavigation(
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

use App\Opds\MyOpds;
use App\Engines\SearchEngine;
use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Kiwilan\Opds\OpdsConfig;
use Kiwilan\Opds\Entries\OpdsEntryNavigation;
use Kiwilan\Opds\Entries\OpdsEntryBook;
use Kiwilan\Opds\Entries\OpdsEntryBookAuthor;

class IndexController extends Controller
{
    public function index()
    {
        return Opds::make(MyOpds::config())
            ->feeds(MyOpds::home())
            ->send()
        ;
    }

    public function searchResults(Request $request)
    {
        $query = $request->input('q');
        $results = // use your search engine here

        $feeds = [];

        foreach ($results as $result) {
            /** @var Book $result */
            $feeds[] = MyOpds::bookToEntry($result);
        }

        return Opds::make(MyOpds::config())
            ->title("Search for {$query}")
            ->isSearch()
            ->feeds($feeds)
            ->send()
        ;
    }
}
```

You could create book OPDS page.

```php
<?php

namespace App\Http\Controllers\Opds;

use App\Opds\MyOpds;
use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Book;
use Kiwilan\Opds\OpdsConfig;
use Kiwilan\Opds\Entries\OpdsEntryNavigation;
use Kiwilan\Opds\Entries\OpdsEntryBook;
use Kiwilan\Opds\Entries\OpdsEntryBookAuthor;

class BookController extends Controller
{
    public function show(string $author_slug, string $book_slug)
    {
        $author = Author::whereSlug($author_slug)->firstOrFail();
        $book = Book::whereAuthorMainId($author->id)
            ->whereSlug($book_slug)
            ->firstOrFail()
        ;

        return Opds::make(MyOpds::config())
            ->title("Book {$book->title}")
            ->feeds(MyOpds::bookToEntry($book))
            ->send()
        ;
    }
}
```
