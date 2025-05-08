<?php

use Kiwilan\Opds\Entries\OpdsEntryBook;
use Kiwilan\Opds\Entries\OpdsEntryBookAuthor;
use Kiwilan\Opds\Entries\OpdsEntryImage;
use Kiwilan\Opds\Entries\OpdsEntryNavigation;

it('is OpdsEntryNavigation', function (OpdsEntryNavigation $entry) {
    expect($entry)->toBeInstanceOf(OpdsEntryNavigation::class);
    expect($entry->getId())->toBe('authors');
    expect($entry->getTitle())->toBe('Authors');
    expect($entry->getRoute())->toBe('http://localhost:8000/opds/authors');
    expect($entry->getSummary())->toBe('Authors, 1 available');
    expect($entry->getMedia())->toBe('https://user-images.githubusercontent.com/48261459/201463225-0a5a084e-df15-4b11-b1d2-40fafd3555cf.svg');
    expect($entry->getUpdated())->toBeInstanceOf(DateTime::class);
    expect($entry->getRelation())->toBe('series');
    expect($entry->getProperties())->toBeArray();
    expect($entry->getProperties())->toBe(['properties']);
    expect($entry->toArray())->toBeArray();

    $entry->relation('new relation');
    $entry->properties(['new properties']);
    expect($entry->getRelation())->toBe('new relation');
    expect($entry->getProperties())->toBeArray();
    expect($entry->getProperties())->toBe(['new properties']);
})->with('feeds');

it('is OpdsEntryBook', function (OpdsEntryBook $entry) {
    expect($entry)->toBeInstanceOf(OpdsEntryBook::class);
    expect($entry->getTitle())->toBe('The Clan of the Cave Bear');
    expect($entry->getRoute())->toBe('http://localhost:8000/opds/books/the-clan-of-the-cave-bear-epub-en');
    expect($entry->getSummary())->toBeString();
    expect($entry->getContents())->toBeString();
    expect($entry->getMedia())->toBe('https://user-images.githubusercontent.com/48261459/201463225-0a5a084e-df15-4b11-b1d2-40fafd3555cf.svg');
    expect($entry->getUpdated())->toBeInstanceOf(DateTime::class);
    expect($entry->getDownload())->toBe('http://localhost:8000/api/download/books/the-clan-of-the-cave-bear-epub-en');
    expect($entry->getMediaThumbnail())->toBe('https://user-images.githubusercontent.com/48261459/201463225-0a5a084e-df15-4b11-b1d2-40fafd3555cf.svg');
    expect($entry->getCategories())->toBeArray();
    expect($entry->getCategories())->toBe(['category']);
    expect($entry->getAuthors())->toBeArray();
    expect($entry->getAuthors())->each()->toBeInstanceOf(OpdsEntryBookAuthor::class);
    expect($entry->getPublished())->toBeInstanceOf(DateTime::class);
    expect($entry->getVolume())->toBe(1);
    expect($entry->getSerie())->toBe('Earth\'s Children');
    expect($entry->getLanguage())->toBe('English');
    expect($entry->getIsbn())->toBe('1234567890');
    expect($entry->getTranslator())->toBe('Translator');
    expect($entry->getPublisher())->toBe('Publisher');
    expect($entry->toArray())->toBeArray();

    $entry = $entry->volume(1.2);
    expect($entry->getVolume())->toBe(1.2);
})->with('feeds-books');

it('can use setter', function () {
    $entry = new OpdsEntryBook(
        id: 'the-clan-of-the-cave-bear-epub-1-en',
        title: 'The Clan of the Cave Bear',
        route: 'http://localhost:8000/opds/books/the-clan-of-the-cave-bear-epub-en',
    );

    $entry->id('the-clan-of-the-cave-bear-epub-2-en');
    $entry->title('The Clan of the Cave Bear 2');
    $entry->route('http://localhost:8000/opds/books/the-clan-of-the-cave-bear-epub-en-2');
    $entry->summary('summary');
    $entry->content('content');
    $entry->media('https://user-images.githubusercontent.com/48261459/201463225-0a5a084e-df15-4b11-b1d2-40fafd3555cf.svg');
    $entry->updated(new DateTime);
    $entry->download('http://localhost:8000/api/download/books/the-clan-of-the-cave-bear-epub-en');
    $entry->mediaThumbnail('https://user-images.githubusercontent.com/48261459/201463225-0a5a084e-df15-4b11-b1d2-40fafd3555cf.svg');
    $entry->categories(['category']);
    $entry->authors([new OpdsEntryBookAuthor('author')]);
    $entry->published(new DateTime);
    $entry->volume(1);
    $entry->serie('Earth\'s Children');
    $entry->language('English');
    $entry->isbn('1234567890');
    $entry->identifier('1234567890');
    $entry->translator('Translator');
    $entry->publisher('Publisher');

    expect($entry->getId())->toBe('the-clan-of-the-cave-bear-epub-2-en');
    expect($entry->getTitle())->toBe('The Clan of the Cave Bear 2');
    expect($entry->getRoute())->toBe('http://localhost:8000/opds/books/the-clan-of-the-cave-bear-epub-en-2');
    expect($entry->getSummary())->toBe('summary');
    expect($entry->getContents())->toBe('content');
    expect($entry->getMedia())->toBe('https://user-images.githubusercontent.com/48261459/201463225-0a5a084e-df15-4b11-b1d2-40fafd3555cf.svg');
    expect($entry->getUpdated())->toBeInstanceOf(DateTime::class);
    expect($entry->getDownload())->toBe('http://localhost:8000/api/download/books/the-clan-of-the-cave-bear-epub-en');
    expect($entry->getMediaThumbnail())->toBe('https://user-images.githubusercontent.com/48261459/201463225-0a5a084e-df15-4b11-b1d2-40fafd3555cf.svg');
    expect($entry->getCategories())->toBeArray();
    expect($entry->getCategories())->toBe(['category']);
    expect($entry->getAuthors())->toBeArray();
    expect($entry->getAuthors())->each()->toBeInstanceOf(OpdsEntryBookAuthor::class);
    expect($entry->getPublished())->toBeInstanceOf(DateTime::class);
    expect($entry->getVolume())->toBe(1);
    expect($entry->getSerie())->toBe('Earth\'s Children');
    expect($entry->getLanguage())->toBe('English');
    expect($entry->getIsbn())->toBe('1234567890');
    expect($entry->getIdentifier())->toBe('1234567890');
    expect($entry->getTranslator())->toBe('Translator');
    expect($entry->getPublisher())->toBe('Publisher');
});

it('can use setter for author', function () {
    $entry = new OpdsEntryBookAuthor(
        name: 'Jean M. Auel',
        uri: 'http://localhost:8000/opds/authors/jean-m-auel',
    );

    $entry->name('New author');
    $entry->uri('http://localhost:8000/opds/authors/new-author');

    expect($entry->getName())->toBe('New author');
    expect($entry->getUri())->toBe('http://localhost:8000/opds/authors/new-author');
});

it('can use setter for image', function () {
    $entry = new OpdsEntryImage(
        uri: 'https://raw.githubusercontent.com/kiwilan/php-opds/main/docs/banner.jpg',
    );

    $entry->uri('http://localhost:8000/opds/images/123-cover.jpg');
    $entry->path(__DIR__.'/media/banner.jpg');
    // @todo get type based on path and/or uri
    // @todo get height and width based on path

    expect($entry->getUri())->toBe('http://localhost:8000/opds/images/123-cover.jpg');
    expect($entry->getPath())->toBe(__DIR__.'/media/banner.jpg');
});
