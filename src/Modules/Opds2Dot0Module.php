<?php

namespace Kiwilan\Opds\Modules;

use Kiwilan\Opds\Converters\OpdsConverter;
use Kiwilan\Opds\Converters\OpdsJsonConverter;
use Kiwilan\Opds\Opds;

/**
 * OPDS 2.0 Module
 *
 * @docs https://drafts.opds.io/opds-2.0
 */
class Opds2Dot0Module extends OpdsModule
{
    public static function make(Opds $opds): OpdsConverter
    {
        $self = new self($opds);

        return OpdsJsonConverter::make($self->opds);
    }
}
