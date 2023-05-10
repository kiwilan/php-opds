<?php

namespace Kiwilan\Opds\Modules;

use Kiwilan\Opds\Converters\OpdsXmlConverter;
use Kiwilan\Opds\Opds;

/**
 * OPDS 1.2 Module
 *
 * @docs https://specs.opds.io/opds-1.2
 */
class Opds1Dot2Module extends OpdsModule
{
    public static function make(Opds $opds): string
    {
        $self = new self($opds);

        return OpdsXmlConverter::make($self->opds);
    }
}
