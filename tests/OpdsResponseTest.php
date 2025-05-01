<?php

use Kiwilan\Opds\Engine\OpdsJsonEngine;
use Kiwilan\Opds\Engine\OpdsXmlEngine;
use Kiwilan\Opds\Enums\OpdsOutputEnum;
use Kiwilan\Opds\Opds;
use Kiwilan\Opds\OpdsConfig;
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

it('can use response with json', function () {
    $opds = Opds::make(getConfig(true))
        ->get();
    $response = $opds->getResponse();

    expect($response->toArray())->toBeArray();
});

it('can send xml response', function () { // @phpstan-ignore-line
    $opds = Opds::make();
    $engine = OpdsXmlEngine::make($opds);

    $response = OpdsResponse::make($engine, $opds->getOutput(), 200);
    $response->setContents(exampleXml());

    $response->send();

    expect($opds)->toBeInstanceOf(Opds::class);
})->expectOutputString(exampleXml());

it('can send json response', function () { // @phpstan-ignore-line
    $opds = Opds::make(new OpdsConfig(forceJson: true));
    $engine = OpdsJsonEngine::make($opds);

    $response = OpdsResponse::make($engine, $opds->getOutput(), 200);
    $response->setContents(exampleJson());

    $json = $response->send();

    expect($opds)->toBeInstanceOf(Opds::class);
    expect($json)->toContain('{"metadata"');
    expect($json)->toBeString();
})->expectOutputString(exampleJson());

it('can use response method', function () {
    $xml = Opds::make()->send();
    $xml = str_replace("\n", '', $xml);

    expect($xml)->toContain('<?xml version="1.0" encoding="UTF-8"');
    expect($xml)->toBeString();
});

it('can failed on getJson', function () {
    $response = OpdsResponse::make(exampleXml(), OpdsOutputEnum::xml, 200);

    expect($response->isJson())->toBeFalse();
    expect(fn () => $response->getJson())->toThrow(\Exception::class);
});

it('can use force exit', function () {
    $html = '<!DOCTYPE html>';
    $config = new OpdsConfig;
    $config->forceExit();
    $opds = Opds::make($config);

    $engine = OpdsXmlEngine::make($opds);
    $engine->setContents(['html' => $html]);

    expect($engine->getContents())->toBe(['html' => $html]);

    $response = $opds->getResponse();
    expect($response->isUseForceExit())->toBeTrue();

    $opds = Opds::make();

    $engine = OpdsXmlEngine::make($opds);
    $engine->setContents(['html' => $html]);

    expect($engine->getContents())->toBe(['html' => $html]);

    $response = $opds->getResponse();
    expect($response->isUseForceExit())->toBeFalse();
});
