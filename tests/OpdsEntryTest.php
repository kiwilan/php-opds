<?php

use Kiwilan\Opds\Entries\OpdsEntry;
use Kiwilan\Opds\Entries\OpdsEntryBook;
use Kiwilan\Opds\Entries\OpdsEntryBookAuthor;

it('is OpdsEntry', function (OpdsEntry $entry) {
    expect($entry)->toBeInstanceOf(OpdsEntry::class);
    expect($entry->id())->toBe('authors');
    expect($entry->title())->toBe('Authors');
    expect($entry->route())->toBe('http://localhost:8000/opds/authors');
    expect($entry->summary())->toBe('Authors, 1 available');
    expect($entry->media())->toBe('https://user-images.githubusercontent.com/48261459/201463225-0a5a084e-df15-4b11-b1d2-40fafd3555cf.svg');
    expect($entry->updated())->toBeInstanceOf(DateTime::class);
    expect($entry->toArray())->toBeArray();
})->with('entries');

it('is OpdsEntryBook', function (OpdsEntryBook $entry) {
    expect($entry)->toBeInstanceOf(OpdsEntryBook::class);
    expect($entry->title())->toBe('The Clan of the Cave Bear');
    expect($entry->route())->toBe('http://localhost:8000/opds/books/the-clan-of-the-cave-bear-epub-en');
    expect($entry->summary())->toBeString();
    expect($entry->media())->toBe('https://user-images.githubusercontent.com/48261459/201463225-0a5a084e-df15-4b11-b1d2-40fafd3555cf.svg');
    expect($entry->updated())->toBeInstanceOf(DateTime::class);
    expect($entry->download())->toBe('http://localhost:8000/api/download/books/the-clan-of-the-cave-bear-epub-en');
    expect($entry->mediaThumbnail())->toBe('https://user-images.githubusercontent.com/48261459/201463225-0a5a084e-df15-4b11-b1d2-40fafd3555cf.svg');
    expect($entry->categories())->toBeArray();
    expect($entry->categories())->toBe(['category']);
    expect($entry->authors())->toBeArray();
    expect($entry->authors())->each()->toBeInstanceOf(OpdsEntryBookAuthor::class);
    expect($entry->published())->toBeInstanceOf(DateTime::class);
    expect($entry->volume())->toBe(1);
    expect($entry->serie())->toBe('Earth\'s Children');
    expect($entry->language())->toBe('English');
    expect($entry->toArray())->toBeArray();
})->with('entries-books');
