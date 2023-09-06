<?php

namespace Kiwilan\Opds\Engine;

class OpdsNamespaces
{
    /**
     * OPDS 1.2 Namespaces
     *
     * @var array<string, string>
     */
    const VERSION_1_2 = [
        'xmlns:app' => 'http://www.w3.org/2007/app',
        'xmlns:opds' => 'http://opds-spec.org/2010/catalog',
        'xmlns:opensearch' => 'http://a9.com/-/spec/opensearch/1.1/',
        'xmlns:odl' => 'http://opds-spec.org/odl',
        'xmlns:dcterms' => 'http://purl.org/dc/terms/',
        'xmlns' => 'http://www.w3.org/2005/Atom',
        'xmlns:thr' => 'http://purl.org/syndication/thread/1.0',
        'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
    ];

    /**
     * OPDS 1.2 Search Namespaces
     *
     * @var array<string, string>
     */
    const VERSION_1_2_SEARCH = [
        'xmlns' => 'http://a9.com/-/spec/opensearch/1.1/',
    ];
}
