<?php

namespace Kiwilan\Opds\Modules;

use Kiwilan\Opds\OpdsEngine;
use Kiwilan\Opds\Responses\OpdsJsonResponse;

class OpdsNotSupportedModule
{
    protected function __construct(
        public OpdsEngine $opds,
    ) {
    }

    public static function response(OpdsEngine $opds): OpdsJsonResponse
    {
        $self = new OpdsNotSupportedModule($opds);

        return $self->responseNotSupported();
    }

    private function responseNotSupported(): OpdsJsonResponse
    {
        return OpdsJsonResponse::make([
            'message' => "Version {$this->opds->version} is not supported.",
        ], 400);
    }
}
