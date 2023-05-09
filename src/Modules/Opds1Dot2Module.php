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
class Opds1Dot2Module
{
    protected function __construct(
        protected Opds $opds,
    ) {
    }

    public static function response(Opds $opds): OpdsResponse|string
    {
        $self = new Opds1Dot2Module($opds);
        $xml = OpdsXmlConverter::make($self->opds);

        return OpdsResponse::xml($xml, $self->opds->asString());
    }
}
