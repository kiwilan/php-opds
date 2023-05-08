<?php

namespace Kiwilan\Opds\Modules;

use Kiwilan\Opds\Opds;
use Kiwilan\Opds\OpdsResponse;

class OpdsNotSupportedModule
{
    protected function __construct(
        public Opds $opds,
    ) {
    }

    public static function response(Opds $opds): OpdsResponse
    {
        $self = new OpdsNotSupportedModule($opds);

        return $self->responseNotSupported();
    }

    private function responseNotSupported(): OpdsResponse
    {
        return OpdsResponse::json([
            'message' => "Version {$this->opds->version} is not supported.",
        ], 400);
    }
}
