<?php

use Kiwilan\Opds\Opds;

it('can use paging information for json', function () {
    $feeds = manyFeeds();
    $total = count($feeds);
    $feeds = array_slice(manyFeeds(), 33, 32);
    $page = 2;
    $opds = Opds::make(getConfig()->forceJson())
        // current app url
        ->url('http://localhost:8080/opds?u=2')
        // pre-paginated feed
        ->feeds($feeds)
        // paging information with some links
        ->paging(page: $page, total: $total, first: 'http://localhost:8080/opds?f=1', last: 'http://localhost:8080/opds?l=42', previous: 'http://localhost:8080/opds?p=1', next: 'http://localhost:8080/opds?n=3')
        ->get();

    $response = json_decode($opds->getResponse()->getContents(), true);

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
