<?php

namespace Kiwilan\Opds\Modules;

use Kiwilan\Opds\Opds;
use Kiwilan\Opds\OpdsResponse;

class OpdsNotSupportedModule
{
    protected function __construct(
        protected Opds $opds,
    ) {
    }

    public static function response(Opds $opds): OpdsResponse|string
    {
        $self = new OpdsNotSupportedModule($opds);

        return $self->responseNotSupported();
    }

    private function responseNotSupported(): OpdsResponse|string
    {
        return OpdsResponse::json([
            'message' => "Version {$this->opds->version()} is not supported.",
        ], 400, $this->opds->asString());
    }
}
