<?php

namespace Kiwilan\Opds\Modules;

use Kiwilan\Opds\OpdsEngine;
use Kiwilan\Opds\Responses\OpdsXmlResponse;
use Kiwilan\Opds\Xml\OpdsXmlConverter;

/**
 * OPDS 1.2 Module
 *
 * @docs https://specs.opds.io/opds-1.2
 */
class OpdsVersionOneDotTwoModule
{
    protected function __construct(
        public OpdsEngine $opds,
    ) {
    }

    public static function response(OpdsEngine $opds): OpdsXmlResponse
    {
        $self = new OpdsVersionOneDotTwoModule($opds);
        $xml = OpdsXmlConverter::make($self->opds->app, $self->opds->entries, $self->opds->title);

        return OpdsXmlResponse::make($xml);
    }
}
