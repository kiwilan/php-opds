<?php

use Kiwilan\Opds\Engine\OpdsXmlEngine;
use Kiwilan\Opds\Opds;
use Kiwilan\Opds\OpdsResponse;

it('can fail on bad content', function () {
    $html = '<!DOCTYPE html>';

    $opds = Opds::make();
    $engine = OpdsXmlEngine::make($opds);
    $engine->setContent([$html]);
    $engine->setResponse($html);

    expect($engine->getContent())->toBe([$html]);
    expect($engine->getResponse())->toBe($html);
    expect(fn () => OpdsResponse::make($engine, 500))->toThrow(Exception::class);
    expect(fn () => OpdsResponse::make($engine, 500))->toThrow('OPDS Response: invalid content');
});

it('can use response', function () {
    $opds = Opds::make()
        ->get();
    $response = $opds->getResponse();

    expect($response->getStatus())->toBe(200);
    expect($response->isJson())->toBeFalse();
    expect($response->isXml())->toBeTrue();
    expect($response->getContent())->toBeString();
});

it('can send response', function () { // @phpstan-ignore-line
    $opds = Opds::make();
    $engine = OpdsXmlEngine::make($opds);
    $engine->setContent([exampleXml()]);
    $engine->setResponse(exampleXml());
    $response = OpdsResponse::make($engine, 200);

    $response->response(send: false);

    expect($opds)->toBeInstanceOf(Opds::class);
})->expectOutputString(exampleXml());

it('can use response method', function () {
    $opds = Opds::make() // @phpstan-ignore-line
        ->response(send: false);

    expect($opds)->toBeNull();
});
