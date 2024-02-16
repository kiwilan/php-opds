<?php

use Kiwilan\Opds\Engine\Paginate\OpdsPaging;
use Kiwilan\Opds\Opds;
use Kiwilan\XmlReader\XmlReader;

it('can use paging information for xml', function () {
    $feeds = manyFeeds();
    $total = count($feeds);
    $feeds = array_slice(manyFeeds(), 33, 32);
    $page = 2;

    $opds = Opds::make(getConfig())
        ->url('http://localhost:8080/opds?u=2')
        ->feeds($feeds)
        ->paging(new OpdsPaging(
            currentPage: $page,
            totalItems: $total,
            firstUrl: 'http://localhost:8080/opds?f=1',
            lastUrl: 'http://localhost:8080/opds?l=42',
            previousUrl: 'http://localhost:8080/opds?p=1',
            nextUrl: 'http://localhost:8080/opds?n=3',
        ))
        ->get();

    $links = XmlReader::make($opds->getResponse()->getContents())
        ->find('link', strict: false);
    $entries = XmlReader::make($opds->getResponse()->getContents())
        ->find('entry');

    $pagination = [];
    foreach ($links as $item) {
        $pagination[$item['@attributes']['rel']] = $item;
    }

    expect(count($pagination))->toBe(8);
    expect($pagination['self']['@attributes']['href'])->toBe('http://localhost:8080/opds?u=2');
    expect($pagination['first']['@attributes']['href'])->toBe('http://localhost:8080/opds?f=1');
    expect($pagination['last']['@attributes']['href'])->toBe('http://localhost:8080/opds?l=42');
    expect($pagination['next']['@attributes']['href'])->toBe('http://localhost:8080/opds?n=3');
    expect($pagination['previous']['@attributes']['href'])->toBe('http://localhost:8080/opds?p=1');

    expect($entries)->toBeArray();
    expect(count($entries))->toBe(32);
});

it('can use paging information for json', function () {
    $feeds = manyFeeds();
    $total = count($feeds);
    $feeds = array_slice(manyFeeds(), 33, 32);
    $page = 2;

    $opds = Opds::make(getConfig()->forceJson())
        ->url('http://localhost:8080/opds?u=2')
        ->feeds($feeds)
        ->paging(new OpdsPaging(
            currentPage: $page,
            totalItems: $total,
            firstUrl: 'http://localhost:8080/opds?f=1',
            lastUrl: 'http://localhost:8080/opds?l=42',
            previousUrl: 'http://localhost:8080/opds?p=1',
            nextUrl: 'http://localhost:8080/opds?n=3',
        ))
        ->get();

    $response = $opds->getResponse()->toArray();

    $pagination = [];
    foreach ($response['links'] as $item) {
        $pagination[$item['rel']] = $item;
    }

    expect(count($pagination))->toBe(5);
    expect($pagination['self']['href'])->toBe('http://localhost:8080/opds?u=2');
    expect($pagination['first']['href'])->toBe('http://localhost:8080/opds?f=1');
    expect($pagination['last']['href'])->toBe('http://localhost:8080/opds?l=42');
    expect($pagination['next']['href'])->toBe('http://localhost:8080/opds?n=3');
    expect($pagination['previous']['href'])->toBe('http://localhost:8080/opds?p=1');

    expect($response['publications'])->toBeArray();
    expect(count($response['publications']))->toBe(32);
});
