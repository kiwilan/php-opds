<?php

namespace Kiwilan\Opds\Modules;

use Kiwilan\Opds\Converters\OpdsXmlConverter;
use Kiwilan\Opds\Opds;
use Kiwilan\Opds\OpdsResponse;

/**
 * OPDS 1.2 Module
 *
 * @docs https://specs.opds.io/opds-1.2
 */
class OpdsVersionOneDotTwoModule
{
    protected function __construct(
        public Opds $opds,
    ) {
    }

    public static function response(Opds $opds): OpdsResponse
    {
        $self = new OpdsVersionOneDotTwoModule($opds);
        $xml = OpdsXmlConverter::make($self->opds->app, $self->opds->entries, $self->opds->title);

        return OpdsResponse::xml($xml);
    }
}
