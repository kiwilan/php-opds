<?php

use Kiwilan\Opds\Opds;
use Kiwilan\Opds\OpdsConfig;
use Kiwilan\Opds\OpdsVersionEnum;

function getConfigV2(): OpdsConfig
{
    return getConfig(true);
}

it('can force OPDS 2.0', function () {
    $opds = Opds::make(getConfigV2())
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

it('is string', function () {
    $opds = Opds::make(getConfigV2())
        ->get();

    $response = $opds->getResponse();
    expect($response->getContent())->toBeString();
});

it('can use search', function () {
    $opds = Opds::make(getConfigV2())
        ->isSearch()
        ->get();

    $response = $opds->getResponse();
    expect($response->getContent())->toBeString();
});

it('can use feeds', function () {
    $opds = Opds::make(getConfigV2())
        ->feeds(manyFeeds())
        ->get();

    expect($opds)->toBeInstanceOf(Opds::class);
    expect($opds->getEngine()->getXml())->toBeArray();
});

it('can use navigation feeds', function () {
    $opds = Opds::make(getConfigV2())
        ->feeds(navigationEntries())
        ->get();

    expect($opds)->toBeInstanceOf(Opds::class);
    expect($opds->getEngine()->getXml())->toBeArray();
});
