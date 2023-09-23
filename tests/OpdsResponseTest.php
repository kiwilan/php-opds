<?php

use Kiwilan\Opds\Engine\OpdsXmlEngine;
use Kiwilan\Opds\Opds;
use Kiwilan\Opds\OpdsResponse;

it('can fail on bad content', function () {
    $html = '<!DOCTYPE html>';

    $opds = Opds::make();
    $engine = OpdsXmlEngine::make($opds);
    $engine->setContents(['html' => $html]);

    expect($engine->getContents())->toBe(['html' => $html]);
    expect(fn () => OpdsResponse::make(json_encode($html), $opds->getOutput(), 500))->toThrow(Exception::class);
    expect(fn () => OpdsResponse::make(json_encode($html), $opds->getOutput(), 500))->toThrow('OPDS Response: invalid content');
});

it('can use response', function () {
    $opds = Opds::make()
        ->get();
    $response = $opds->getResponse();

    expect($response->getStatus())->toBe(200);
    expect($response->isJson())->toBeFalse();
    expect($response->isXml())->toBeTrue();
    expect($response->getHeaders())->toBeArray();
    expect($response->getHeaders())->toHaveKey('Access-Control-Allow-Origin');
    expect($response->getHeaders())->toHaveKey('Content-Type');
    expect($response->getContents())->toBeString();

    $response->setHeaders(['Content-Encoding' => 'gzip']);
    expect($response->getHeaders())->toHaveKey('Content-Encoding');
});

it('can send response', function () { // @phpstan-ignore-line
    $opds = Opds::make();
    $engine = OpdsXmlEngine::make($opds);

    $response = OpdsResponse::make($engine, $opds->getOutput(), 200);
    $response->setContents(exampleXml());

    $response->send(mock: true);

    expect($opds)->toBeInstanceOf(Opds::class);
})->expectOutputString(exampleXml());

it('can use response method', function () {
    $opds = Opds::make() // @phpstan-ignore-line
        ->send(mock: true);

    expect($opds)->toBeNull();
});
