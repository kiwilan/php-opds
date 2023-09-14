<?php

use Kiwilan\Opds\Engine\OpdsPaginator;
use Kiwilan\Opds\Enums\OpdsOutputEnum;
use Kiwilan\Opds\Opds;
use Kiwilan\XmlReader\XmlReader;

it('can use pagination', function () {
    $opds = Opds::make(getConfig()->usePagination())
        ->feeds(manyFeeds())
        ->get();

    $xml = XmlReader::make($opds->getResponse()->getContents())
        ->find('entry');

    expect($xml)->toBeArray();
    expect(count($xml))->toBe(32);

    $opds = Opds::make(getConfig()->usePagination())
        ->url('http://localhost:8000/opds?startRecord=32')
        ->feeds(manyFeeds())
        ->get();

    $xml = XmlReader::make($opds->getResponse()->getContents())
        ->find('entry');
    $first = $xml[0];

    $pagination = [];
    $links = XmlReader::make($opds->getResponse()->getContents())
        ->find('link', strict: false);

    foreach ($links as $link) {
        $attrs = XmlReader::parseAttributes($link);
        if (str_contains($attrs['href'], 'maximumRecords')) {
            $pagination[$attrs['rel']] = $attrs;
        }
    }

    expect(count($pagination))->toBe(4);
    expect($pagination['first']['href'])->toBe('http://localhost:8000/opds?startRecord=0&maximumRecords=32');
    expect($pagination['last']['href'])->toBe('http://localhost:8000/opds?startRecord=96&maximumRecords=32');
    expect($pagination['next']['href'])->toBe('http://localhost:8000/opds?startRecord=64&maximumRecords=32');
    expect($pagination['previous']['href'])->toBe('http://localhost:8000/opds?startRecord=0&maximumRecords=32');

    expect($xml)->toBeArray();
    expect(count($xml))->toBe(32);
    expect($first['id'])->toBe('32');
});

it('can use pagination under limit', function () {
    $opds = Opds::make(getConfig()->usePagination())
        ->feeds(manyFeeds(10))
        ->get();

    $xml = XmlReader::make($opds->getResponse()->getContents())
        ->find('entry');

    expect($xml)->toBeArray();
    expect(count($xml))->toBe(10);
});

it('can use paginator', function () {
    $opds = Opds::make(getConfig()->usePagination())
        ->feeds(manyFeeds())
        ->get();

    expect($opds->getPaginator())->toBeInstanceOf(OpdsPaginator::class);

    $paginator = $opds->getPaginator();
    expect($paginator->getOutput())->toBe(OpdsOutputEnum::xml);
    expect($paginator->getUrl())->toBe('http://localhost/');
    expect($paginator->getQuery())->toBeArray();
    expect($paginator->usePagination())->toBeTrue();
    expect($paginator->useAutoPagination())->toBeFalse();
    expect($paginator->getPerPage())->toBe(32);
    expect($paginator->getPage())->toBe(1);
    expect($paginator->getTotal())->toBe(100);
    expect($paginator->getStart())->toBe(0);
    expect($paginator->getSize())->toBe(4);
    expect($paginator->getFirst())->toBe(0);
    expect($paginator->getLast())->toBe(96);
});

it('can use json pagination', function () {
    $opds = Opds::make(getConfig()->usePagination()->forceJson())
        ->feeds(manyFeeds())
        ->get();

    $response = json_decode($opds->getResponse()->getContents(), true);

    expect($response['publications'])->toBeArray();
    expect(count($response['publications']))->toBe(32);

    $opds = Opds::make(getConfig()->usePagination()->forceJson())
        ->url('http://localhost:8000/opds?page=2')
        ->feeds(manyFeeds())
        ->get();

    $response = json_decode($opds->getResponse()->getContents(), true);

    $pagination = [];
    foreach ($response['links'] as $item) {
        $pagination[$item['rel']] = $item;
    }

    expect(count($pagination))->toBe(5);
    expect($pagination['first']['href'])->toBe('http://localhost:8000/opds?page=1');
    expect($pagination['last']['href'])->toBe('http://localhost:8000/opds?page=4');
    expect($pagination['next']['href'])->toBe('http://localhost:8000/opds?page=3');
    expect($pagination['previous']['href'])->toBe('http://localhost:8000/opds?page=1');

    expect($response['publications'])->toBeArray();
    expect(count($response['publications']))->toBe(32);
});

it('can use auto pagination', function () {
    $opds = Opds::make(getConfig()->useAutoPagination())
        ->feeds(manyFeeds())
        ->get();

    $xml = XmlReader::make($opds->getResponse()->getContents())->find('entry');

    expect($opds->getConfig()->isUsePagination())->toBeFalse();
    expect($opds->getConfig()->isUseAutoPagination())->toBeTrue();
    expect($xml)->toBeArray();
    expect(count($xml))->toBe(32);
});

it('can skip json pagination', function () {
    $opds = Opds::make(getConfig()->forceJson())
        ->feeds(manyFeeds())
        ->get();

    $response = json_decode($opds->getResponse()->getContents(), true);

    expect(count($response['publications']))->toBe(100);

    $opds = Opds::make(getConfig()->forceJson())
        ->url('http://localhost:8000/opds?page=2')
        ->feeds(manyFeeds())
        ->get();

    $response = json_decode($opds->getResponse()->getContents(), true);

    expect(count($response['publications']))->toBe(100);
});
