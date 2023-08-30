<?php

use Kiwilan\Opds\Entries\OpdsEntryBook;
use Kiwilan\Opds\Entries\OpdsEntryBookAuthor;
use Kiwilan\Opds\Entries\OpdsEntryNavigation;
use Kiwilan\Opds\Opds;
use Kiwilan\Opds\OpdsConfig;
use Kiwilan\Opds\OpdsVersionEnum;
use Kiwilan\XmlReader\XmlReader;

it('is string', function () {
    $opds = Opds::make()
        ->get();

    $response = $opds->getResponse();
    expect($response->getContent())->toBeString();
});

it('is valid xml', function () {
    $opds = Opds::make()
        ->get();

    $response = $opds->getResponse();
    expect(isValidXml($response->getContent()))->toBeTrue();
});

it('can be parsed', function () {
    $opds = Opds::make()
        ->get();

    $response = $opds->getResponse();
    $xml = XmlReader::make($response->getContent())->toArray();
    expect($xml)->toBeArray();
});

it('can be display feeds', function () {
    $opds = Opds::make()
        ->feeds([
            new OpdsEntryNavigation(
                id: 'authors',
                title: 'Authors',
                route: 'http://localhost:8000/opds/authors',
                summary: 'Authors, 1 available',
                media: 'https://user-images.githubusercontent.com/48261459/201463225-0a5a084e-df15-4b11-b1d2-40fafd3555cf.svg',
                updated: new DateTime(),
            ),
        ])
        ->get();

    $response = $opds->getResponse();
    $xml = XmlReader::make($response->getContent())->toArray();

    expect($xml)->toBeArray();
    expect($response->getContent())->toBeString();
});

it('can be display feeds books', function () {
    $opds = Opds::make(new OpdsConfig(maxItemsPerPage: 1))
        ->feeds([
            new OpdsEntryBook(
                id: 'the-clan-of-the-cave-bear-epub-1-en',
                title: 'The Clan of the Cave Bear',
                route: 'http://localhost:8000/opds/books/the-clan-of-the-cave-bear-epub-en',
                summary: 'summary',
                content: 'content',
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
            new OpdsEntryBook(
                id: 'the-clan-of-the-cave-bear-epub-2-en',
                title: 'The Clan of the Cave Bear',
                route: 'http://localhost:8000/opds/books/the-clan-of-the-cave-bear-epub-en',
                summary: 'summary',
                content: 'content',
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
        ])
        ->get();

    $response = $opds->getResponse();
    expect($response->getContent())->toBeString();
});

it('can search', function () {
    $opds = Opds::make()
        ->feeds([
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
        ])
        ->get();

    $response = $opds->getResponse();
    expect($response->getContent())->toBeString();
});

it('can force OPDS 2.0', function () {
    $opds = Opds::make(getConfig(true))
        ->url('http://localhost:8000/opds')
        ->get();

    expect($opds->getVersion())->toBe(OpdsVersionEnum::v2Dot0);
});

it('can use query for OPDS 2.0', function () {
    $opds = Opds::make(getConfig())
        ->url('http://localhost:8000/opds?v=2.0')
        ->get();

    expect($opds->getVersion())->toBe(OpdsVersionEnum::v2Dot0);
});
