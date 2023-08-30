<?php

namespace Kiwilan\Opds\Modules;

use Kiwilan\Opds\Engine\OpdsEngine;
use Kiwilan\Opds\Engine\OpdsJsonEngine;
use Kiwilan\Opds\Opds;

/**
 * OPDS 2.0 Module
 *
 * @docs https://drafts.opds.io/opds-2.0
 */
class Opds2Dot0Module extends OpdsModule
{
    public static function make(Opds $opds): OpdsEngine
    {
        $self = new self($opds);

        return OpdsJsonEngine::make($self->opds);
    }
}
