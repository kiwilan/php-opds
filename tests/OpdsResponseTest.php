<?php

use Kiwilan\Opds\Opds;

it('can reject with JSON if version is not supported', function () {
    $opds = Opds::response(
        version: '2.0',
        asString: true,
    );

    expect($opds)->toBeString();
    expect(json_decode($opds))->toBeObject();
});
