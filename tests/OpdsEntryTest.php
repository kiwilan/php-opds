<?php

use Kiwilan\Opds\Entries\OpdsEntryBook;
use Kiwilan\Opds\Entries\OpdsEntryBookAuthor;
use Kiwilan\Opds\Entries\OpdsEntryNavigation;

it('is OpdsEntryNavigation', function (OpdsEntryNavigation $entry) {
    expect($entry)->toBeInstanceOf(OpdsEntryNavigation::class);
    expect($entry->getId())->toBe('authors');
    expect($entry->getTitle())->toBe('Authors');
    expect($entry->getRoute())->toBe('http://localhost:8000/opds/authors');
    expect($entry->getSummary())->toBe('Authors, 1 available');
    expect($entry->getMedia())->toBe('https://user-images.githubusercontent.com/48261459/201463225-0a5a084e-df15-4b11-b1d2-40fafd3555cf.svg');
    expect($entry->getUpdated())->toBeInstanceOf(DateTime::class);
    expect($entry->toArray())->toBeArray();
})->with('feeds');

it('is OpdsEntryBook', function (OpdsEntryBook $entry) {
    expect($entry)->toBeInstanceOf(OpdsEntryBook::class);
    expect($entry->getTitle())->toBe('The Clan of the Cave Bear');
    expect($entry->getRoute())->toBe('http://localhost:8000/opds/books/the-clan-of-the-cave-bear-epub-en');
    expect($entry->getSummary())->toBeString();
    expect($entry->getContent())->toBeString();
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
})->with('feeds-books');
