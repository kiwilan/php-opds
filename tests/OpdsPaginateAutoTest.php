<?php

use Kiwilan\Opds\Engine\Paginate\OpdsPaginator;
use Kiwilan\Opds\Enums\OpdsOutputEnum;
use Kiwilan\Opds\Opds;
use Kiwilan\XmlReader\XmlReader;

it('can use pagination', function () {
    $opds = Opds::make(getConfig())
        ->feeds(manyFeeds())
        ->paginate()
        ->get();

    $xml = XmlReader::make($opds->getResponse()->getContents())
        ->find('entry');

    expect($xml)->toBeArray();
    expect(count($xml))->toBe(32);

    $opds = Opds::make(getConfig())
        ->url('http://localhost:8000/opds?startRecord=32')
        ->feeds(manyFeeds())
        ->paginate()
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
    $opds = Opds::make(getConfig())
        ->feeds(manyFeeds(10))
        ->paginate()
        ->get();

    $xml = XmlReader::make($opds->getResponse()->getContents())
        ->find('entry');

    expect($xml)->toBeArray();
    expect(count($xml))->toBe(10);
});

it('can use paginator', function () {
    $opds = Opds::make(getConfig())
        ->feeds(manyFeeds())
        ->paginate()
        ->get();

    expect($opds->getPaginator())->toBeInstanceOf(OpdsPaginator::class);

    $paginator = $opds->getPaginator();
    expect($paginator->getOutput())->toBe(OpdsOutputEnum::xml);
    expect($paginator->getUrl())->toBe('http://localhost/');
    expect($paginator->getQuery())->toBeArray();
    expect($paginator->getPerPage())->toBe(32);
    expect($paginator->getCurrentPage())->toBe(1);
    expect($paginator->getTotalItems())->toBe(100);
    expect($paginator->getStartPage())->toBe(0);
    expect($paginator->getSize())->toBe(4);
    expect($paginator->getFirstPage())->toBe(0);
    expect($paginator->getLastPage())->toBe(96);

    $paginator->setStartPage(0);
    $paginator->setFirstPage(0);
    $paginator->setLastPage(96);

    expect($paginator->getStartPage())->toBe(0);
    expect($paginator->getFirstPage())->toBe(0);
    expect($paginator->getLastPage())->toBe(96);
});

it('can use json pagination', function () {
    $opds = Opds::make(getConfig()->forceJson())
        ->feeds(manyFeeds())
        ->paginate()
        ->get();

    $response = json_decode($opds->getResponse()->getContents(), true);

    expect($response['publications'])->toBeArray();
    expect(count($response['publications']))->toBe(32);

    $opds = Opds::make(getConfig()->forceJson())
        ->url('http://localhost:8000/opds?page=2')
        ->feeds(manyFeeds())
        ->paginate()
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
    $opds = Opds::make(getConfig())
        ->feeds(manyFeeds())
        ->paginate()
        ->get();

    $xml = XmlReader::make($opds->getResponse()->getContents())->find('entry');

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

it('can use OpdsPagination', function () {
    $opds = Opds::make(getConfig()->forceJson())
        ->feeds(manyFeeds())
        ->paginate()
        ->get();

    $paginate = $opds->getPaginator();

    expect($paginate->getOutput())->toBe(OpdsOutputEnum::json);
    expect($paginate->getUrl())->toBe('http://localhost/');
    expect($paginate->getFullUrl())->toBe('http://localhost/');
    expect($paginate->getQuery())->toBeArray();

    $paginate->setCurrentPage(2);
    $paginate->setTotalItems(100);
    $paginate->setOutput(OpdsOutputEnum::xml);
    $paginate->setUrl('http://localhost:8000/opds');
    $paginate->setFullUrl('http://localhost:8000/opds');
    $paginate->setQuery(['page' => 2]);
});
