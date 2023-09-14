<?php

use Kiwilan\Opds\Engine\OpdsPaginator;
use Kiwilan\Opds\Engine\OpdsXmlEngine;
use Kiwilan\Opds\Enums\OpdsOutputEnum;
use Kiwilan\Opds\Enums\OpdsVersionEnum;
use Kiwilan\Opds\Opds;
use Kiwilan\Opds\OpdsConfig;
use Kiwilan\Opds\OpdsResponse;
use Kiwilan\XmlReader\XmlReader;

it('is string', function () {
    $opds = Opds::make()
        ->get();

    $response = $opds->getResponse();
    expect($response->getContents())->toBeString();
});

it('is valid xml', function () {
    $opds = Opds::make()
        ->get();

    $response = $opds->getResponse();
    expect(isValidXml($response->getContents()))->toBeTrue();
});

it('can be parsed', function () {
    $opds = Opds::make()
        ->get();

    $response = $opds->getResponse();
    $xml = XmlReader::make($response->getContents())->toArray();
    expect($xml)->toBeArray();
});

it('can use opds properties', function () {
    $opds = Opds::make()
        ->title('feed');

    expect($opds->getTitle())->toBe('feed');
    expect($opds->getUrl())->toBe('http://localhost/');
    expect($opds->getVersion())->toBe(OpdsVersionEnum::v1Dot2);
    expect($opds->getFeeds())->toBeArray();
    expect($opds->getEngine())->toBeInstanceOf(OpdsXmlEngine::class);
    expect($opds->getOutput())->toBe(OpdsOutputEnum::xml);
    expect($opds->getResponse())->toBeInstanceOf(OpdsResponse::class);
    expect($opds->getUrlParts())->toBeArray();
    expect($opds->getPaginator())->toBeNull();
});

it('can use opds paginator', function () {
    $config = (new OpdsConfig())->usePagination()
        ->setVersionQuery('v')
        ->setPaginationQuery('pagination');
    $opds = Opds::make($config)
        ->title('feed')
        ->get();

    expect($opds->getPaginator())->toBeInstanceOf(OpdsPaginator::class);
});

it('can use output', function () {
    $opds = Opds::make()
        ->title('feed');

    expect($opds->getOutput())->toBe(OpdsOutputEnum::xml);
});

it('can use response', function () {
    $opds = Opds::make()
        ->title('feed');

    expect($opds->getResponse())->toBeInstanceOf(OpdsResponse::class);
});

it('will throw exception with unspported version', function () {
    $opds = Opds::make()
        ->title('feed');

    expect(fn () => $opds->url('http://localhost:8000/opds?version=0.8'))->toThrow(Exception::class);
    expect(fn () => $opds->url('http://localhost:8000/opds?version=0.8'))->toThrow('OPDS version 0.8 is not supported.');
});
