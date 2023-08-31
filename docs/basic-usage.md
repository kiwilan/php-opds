# Basic usage

Example of a simple OPDS feed into controller (like Laravel).

```php
<?php

use Kiwilan\Opds\Opds;
use Kiwilan\Opds\OpdsConfig;
use Kiwilan\Opds\Entries\OpdsEntryNavigation;
use Kiwilan\Opds\Entries\OpdsEntryBook;
use Kiwilan\Opds\Entries\OpdsEntryBookAuthor;

class OpdsController
{
  public function index()
  {
    $opds = Opds::make($this->config())
      ->feeds([
        new OpdsEntryNavigation(
          id: 'authors',
          title: 'Authors',
          route: 'http://localhost:8000/opds/authors',
          summary: 'Authors, 1 available',
          media: 'https://user-images.githubusercontent.com/48261459/201463225-0a5a084e-df15-4b11-b1d2-40fafd3555cf.svg',
          updated: new DateTime(),
        ),
        new OpdsEntryNavigation(
          id: 'series',
          title: 'Series',
          route: 'http://localhost:8000/opds/series',
          summary: 'Series, 1 available',
          media: 'https://user-images.githubusercontent.com/48261459/201463225-0a5a084e-df15-4b11-b1d2-40fafd3555cf.svg',
          updated: new DateTime(),
        ),
      ])
    );

    return $opds->response();
  }

  public function books()
  {
    $opds = Opds::make($this->config())
      ->feeds([
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
      ]);

    return $opds->response();
  }

  private function config(): OpdsConfig
  {
    return new OpdsConfig(
      name: 'My OPDS Catalog',
      author: 'John Doe',
      authorUrl: 'https://example.com',
      startUrl: 'https://example.com/opds',
      searchUrl: 'https://example.com/opds/search',
      updated: new DateTime(),
    );
  }
}
```
