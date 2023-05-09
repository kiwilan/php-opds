<?php

use Kiwilan\Opds\Entries\OpdsEntry;
use Kiwilan\Opds\Entries\OpdsEntryBook;
use Kiwilan\Opds\Entries\OpdsEntryBookAuthor;

dataset('entries', [
    new OpdsEntry(
        id: 'authors',
        title: 'Authors',
        route: 'http://localhost:8000/opds/authors',
        summary: 'Authors, 1 available',
        media: 'https://user-images.githubusercontent.com/48261459/201463225-0a5a084e-df15-4b11-b1d2-40fafd3555cf.svg',
        updated: new DateTime(),
    ),
]);

dataset('entries-books', [
    new OpdsEntryBook(
        id: 'the-clan-of-the-cave-bear-1-epub-en',
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
    new OpdsEntryBook(
        id: 'the-clan-of-the-cave-bear-2-epub-en',
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
    new OpdsEntryBook(
        id: 'the-clan-of-the-cave-bear-3-epub-en',
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
