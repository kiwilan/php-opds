<?php

use Kiwilan\Opds\Enums\OpdsVersionEnum;
use Kiwilan\Opds\Opds;
use Kiwilan\Opds\OpdsConfig;
use Opis\JsonSchema\Validator;

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
    expect($response->getContents())->toBeString();
});

it('can use search', function () {
    $opds = Opds::make(getConfigV2())
        ->isSearch()
        ->get();

    $response = $opds->getResponse();
    expect($response->getContents())->toBeString();
});

it('can use feeds', function () {
    $opds = Opds::make(getConfigV2())
        ->feeds(manyFeeds())
        ->get();

    expect($opds)->toBeInstanceOf(Opds::class);
    expect($opds->getEngine()->getContents())->toBeArray();
});

it('can use navigation feeds', function () {
    $opds = Opds::make(getConfigV2())
        ->feeds(navigationEntries())
        ->get();

    expect($opds)->toBeInstanceOf(Opds::class);
    expect($opds->getEngine()->getContents())->toBeArray();
});

// https://github.com/opds-community/drafts/tree/master/schema
it('can validate metadata schema', function () {
    $validator = new Validator();

    $opds = Opds::make(getConfigV2())->get();
    $json = json_decode($opds->getResponse()->getContents());
    $json = $json->metadata;

    $validate = $validator->validate(
        $json,
        json_decode(file_get_contents(SCHEMA_FEED_METADATA))
    );

    expect($validate->isValid())->toBeTrue();
});

// it('can validate feed schema', function () {
//     $validator = new Validator();

//     $opds = Opds::make(getConfigV2())
//         ->feeds(manyFeeds())
//         ->get();
//     $json = json_decode($opds->getResponse()->getContents());

//     ray(json_decode(file_get_contents(SCHEMA_FEED)));
//     $validate = $validator->validate(
//         $json,
//         json_decode(file_get_contents(SCHEMA_FEED))
//     );

//     ray($json);
//     ray($validate->error());

//     expect($validate->isValid())->toBeTrue();
// });
