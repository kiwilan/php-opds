<?php

use Kiwilan\Opds\Engine\OpdsXmlEngine;
use Kiwilan\Opds\Entries\OpdsEntryNavigation;
use Kiwilan\Opds\Opds;
use Kiwilan\Opds\OpdsConfig;
use Kiwilan\XmlReader\XmlReader;

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
        ->feeds(feeds())
        ->get();

    $response = $opds->getResponse();
    expect($response->getContent())->toBeString();
});

it('can search', function () {
    $opds = Opds::make()
        ->feeds(feeds()[0])
        ->get();

    $response = $opds->getResponse();
    expect($response->getContent())->toBeString();
});

it('can get opds from engine', function () {
    $opds = Opds::make(getConfig());
    $xml = OpdsXmlEngine::make($opds);

    expect($xml->getOpds())->toBeInstanceOf(Opds::class);
    expect($xml->getContent())->toBeArray();
});

it('can use search', function () {
    $opds = Opds::make()
        ->isSearch()
        ->get();

    $response = $opds->getResponse();
    expect($response->getContent())->toBeString();
});

it('can use search query', function () {
    $opds = Opds::make()
        ->isSearch()
        ->url('http://localhost:8000/opds?q=the')
        ->get();

    $response = $opds->getResponse();
    expect($response->getContent())->toBeString();
});

it('can use navigation feeds', function () {
    $opds = Opds::make()
        ->feeds(navigationEntries())
        ->get();

    expect($opds)->toBeInstanceOf(Opds::class);
    expect($opds->getEngine()->getContent())->toBeArray();
});
