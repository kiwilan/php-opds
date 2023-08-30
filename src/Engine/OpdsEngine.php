<?php

namespace Kiwilan\Opds\Engine;

use Kiwilan\Opds\Entries\OpdsEntryBook;
use Kiwilan\Opds\Entries\OpdsEntryNavigation;
use Kiwilan\Opds\Opds;

abstract class OpdsEngine
{
    protected function __construct(
        protected Opds $opds,
        protected array $xml = [],
        protected ?string $response = null,
    ) {
    }

    /**
     * Create an instance of the converter.
     */
    abstract public static function make(Opds $opds): self;

    /**
     * Build feed page.
     */
    abstract public function feed(): self;

    /**
     * Build search page.
     */
    abstract public function search(): self;

    /**
     * Add entry to feed.
     */
    protected function addEntry(OpdsEntryNavigation|OpdsEntryBook $entry): array
    {
        if ($entry instanceof OpdsEntryBook) {
            return $this->addBookEntry($entry);
        }

        return $this->addNavigationEntry($entry);
    }

    /**
     * Add navigation entry to feed.
     */
    abstract public function addNavigationEntry(OpdsEntryNavigation $entry): array;

    /**
     * Add book entry to feed.
     */
    abstract public function addBookEntry(OpdsEntryBook $entry): array;

    /**
     * Get OPDS instance.
     */
    public function getOpds(): Opds
    {
        return $this->opds;
    }

    /**
     * Get XML array.
     */
    public function getXml(): array
    {
        return $this->xml;
    }

    /**
     * Get XML response.
     */
    public function getResponse(): string
    {
        return $this->response;
    }

    /**
     * Get current URL.
     */
    public static function getCurrentUrl(): string
    {
        $http = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        return "{$http}://{$host}{$uri}";
    }

    protected function addXmlNode(string $value = null, ?array $attributes = []): array
    {
        $node = [];

        if ($value) {
            $node['_value'] = $value;
        }

        if ($attributes) {
            $node['_attributes'] = $attributes;
        }

        return $node;
    }

    protected function addXmlLink(
        string $href,
        string $title = null,
        string $rel = null,
        string $type = 'application/atom+xml;profile=opds-catalog;kind=acquisition',
    ): array {
        return [
            '_attributes' => [
                'rel' => $rel,
                'href' => $href,
                'type' => $type,
                'title' => $title,
            ],
        ];
    }

    protected function route(string $route): string
    {
        $query = $this->opds->getQuery();

        if (! $query) {
            return $route;
        }

        return $route.'?'.http_build_query($query);
    }

    protected function handleJsonPagination(): array
    {
        return [
            'metadata' => [
                'title' => 'Paginated feed',
                'numberOfItems' => 5678,
                'itemsPerPage' => 50,
                'currentPage' => 2,
            ],
            'links' => [
                ['rel' => 'self', 'href' => '/?page=2', 'type' => 'application/opds+json'],
                ['rel' => ['first', 'previous'], 'href' => '/?page=1', 'type' => 'application/opds+json'],
                ['rel' => 'next', 'href' => '/?page=3', 'type' => 'application/opds+json'],
                ['rel' => 'last', 'href' => '/?page=114', 'type' => 'application/opds+json'],
            ],
        ];
    }

    protected function handleXmlPagination(array &$xml)
    {
        $feeds = $this->opds->getFeeds();
        $paginate = $this->opds->getConfig()->isUsePagination();
        $perPage = $this->opds->getConfig()->getMaxItemsPerPage();
        $page = 1;

        if (! $paginate) {
            return $xml;
        }

        if (count($feeds) < $perPage) {
            return $xml;
        }

        $current = OpdsEngine::getCurrentUrl();

        if (str_contains($current, '?')) {
            $current = explode('?', $current)[0];
        }

        $queryStartRecord = $this->opds->getQuery()['startRecord'] ?? 0;
        $queryStartRecord = intval($queryStartRecord);

        $count = count($feeds);
        $pageNumbers = intval(ceil($count / $perPage));
        $start = $this->opds->getQuery()['startRecord'] ?? $page - 1;
        $feeds = array_slice($feeds, $start, $perPage);

        $first = $this->opds->getQuery()['startRecord'] ?? 0;
        $last = ($perPage * $pageNumbers) - $perPage;

        $startRecord = $start + $perPage;

        $previousUrl = $current.'?'.http_build_query([
            'q' => $this->opds->getQuery()['q'] ?? null,
            'startRecord' => '-'.$startRecord,
            'maximumRecords' => $perPage,
        ]);
        $nextUrl = $current.'?'.http_build_query([
            'q' => $this->opds->getQuery()['q'] ?? null,
            'startRecord' => $startRecord,
            'maximumRecords' => $perPage,
        ]);
        $firstUrl = $current.'?'.http_build_query([
            'q' => $this->opds->getQuery()['q'] ?? null,
            'startRecord' => 0,
            'maximumRecords' => $perPage,
        ]);
        $lastUrl = $current.'?'.http_build_query([
            'q' => $this->opds->getQuery()['q'] ?? null,
            'startRecord' => $last,
            'maximumRecords' => $perPage,
        ]);

        if ($queryStartRecord !== 0) {
            $xml['__custom:link:4'] = $this->addXmlLink(href: $previousUrl, rel: 'previous', title: 'Previous page');
        }

        if ($queryStartRecord !== $last) {
            $xml['__custom:link:5'] = $this->addXmlLink(href: $nextUrl, rel: 'next', title: 'Next page');
        }

        if ($queryStartRecord !== 0) {
            $xml['__custom:link:6'] = $this->addXmlLink(href: $firstUrl, rel: 'first', title: 'First page');
        }

        if ($queryStartRecord !== $last) {
            $xml['__custom:link:7'] = $this->addXmlLink(href: $lastUrl, rel: 'last', title: 'Last page');
        }

        $xml['opensearch:totalResults'] = count($this->opds->getFeeds());
        $xml['opensearch:itemsPerPage'] = $perPage;
        $xml['opensearch:startIndex'] = $startRecord === 0 ? 1 : $start;

        return $xml;
    }
}
