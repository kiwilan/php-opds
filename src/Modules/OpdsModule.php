<?php

namespace Kiwilan\Opds\Modules;

use Kiwilan\Opds\Engine\OpdsEngine;
use Kiwilan\Opds\Opds;

abstract class OpdsModule
{
    protected function __construct(
        protected Opds $opds,
    ) {
    }

    abstract public static function make(Opds $opds): OpdsEngine;

    public function opds(): Opds
    {
        return $this->opds;
    }
}
