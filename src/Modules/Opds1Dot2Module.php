<?php

namespace Kiwilan\Opds\Modules;

use Kiwilan\Opds\Engine\OpdsEngine;
use Kiwilan\Opds\Engine\OpdsXmlEngine;
use Kiwilan\Opds\Opds;

/**
 * OPDS 1.2 Module
 *
 * @docs https://specs.opds.io/opds-1.2
 */
class Opds1Dot2Module extends OpdsModule
{
    public static function make(Opds $opds): OpdsEngine
    {
        $self = new self($opds);

        return OpdsXmlEngine::make($self->opds);
    }
}
