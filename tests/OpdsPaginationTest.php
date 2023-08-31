<?php

use Kiwilan\Opds\Opds;
use Kiwilan\XmlReader\XmlReader;

it('can use pagination', function () {
    $opds = Opds::make(getConfig()->usePagination())
        ->feeds(manyFeeds())
        ->get();

    $xml = XmlReader::make($opds->getResponse()->getContent())
        ->find('entry');

    expect($xml)->toBeArray();
    expect(count($xml))->toBe(32);

    $opds = Opds::make(getConfig()->usePagination())
        ->url('http://localhost:8000/opds?startRecord=32')
        ->feeds(manyFeeds())
        ->get();

    $xml = XmlReader::make($opds->getResponse()->getContent())
        ->find('entry');
    $first = $xml[0];

    expect($xml)->toBeArray();
    expect(count($xml))->toBe(32);
    expect($first['id'])->toBe('32');
});

it('can use pagination under limit', function () {
    $opds = Opds::make(getConfig()->usePagination())
        ->feeds(manyFeeds(10))
        ->get();

    $xml = XmlReader::make($opds->getResponse()->getContent())
        ->find('entry');

    expect($xml)->toBeArray();
    expect(count($xml))->toBe(10);
});
