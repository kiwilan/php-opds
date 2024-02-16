<?php

namespace Kiwilan\Opds\Engine\Paginate;

use Kiwilan\Opds\Engine\OpdsEngine;
use Kiwilan\Opds\Entries\OpdsEntryBook;
use Kiwilan\Opds\Entries\OpdsEntryNavigation;
use Kiwilan\Opds\Enums\OpdsOutputEnum;
use Kiwilan\Opds\Opds;

/**
 * Handle pagination for OPDS.
 */
class OpdsPaginator extends OpdsPaginate
{
    protected function __construct(
        protected bool $usePagination = false,
        protected bool $useAutoPagination = false,
        protected int $size = 0,
        protected int $startPage = 0,
        protected int $firstPage = 0,
        protected int $lastPage = 0,
    ) {
        parent::__construct();
    }

    /**
     * Create an instance of OpdsPagination.
     */
    public static function make(OpdsEngine $engine): self
    {
        $self = new self(
            usePagination: $engine->getOpds()->getConfig()->isUsePagination(),
            useAutoPagination: $engine->getOpds()->getConfig()->isUseAutoPagination(),
        );
        $self->parseUrl($engine);

        return $self;
    }

    public function usePagination(): bool
    {
        return $this->usePagination;
    }

    public function useAutoPagination(): bool
    {
        return $this->useAutoPagination;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getStartPage(): int
    {
        return $this->startPage;
    }

    public function getFirstPage(): int
    {
        return $this->firstPage;
    }

    public function getLastPage(): int
    {
        return $this->lastPage;
    }

    public function setStartPage(int $startPage): self
    {
        $this->startPage = $startPage;

        return $this;
    }

    public function setFirstPage(int $firstPage): self
    {
        $this->firstPage = $firstPage;

        return $this;
    }

    public function setLastPage(int $lastPage): self
    {
        $this->lastPage = $lastPage;

        return $this;
    }

    /**
     * Handle pagination.
     *
     * @param  array<string, mixed>  $content
     * @param  OpdsEntryNavigation[]|OpdsEntryBook[]  $feeds
     */
    public function paginate(array &$content, array &$feeds): self
    {
        if (! $this->usePagination && ! $this->useAutoPagination) {
            return $this;
        }

        if (count($feeds) < $this->perPage) {
            return $this;
        }

        $this->totalItems = count($feeds);
        $this->startPage = intval($this->query['startRecord'] ?? 0);
        $this->size = intval(ceil($this->totalItems / $this->perPage));
        $this->firstPage = $this->startPage;
        $this->lastPage = ($this->perPage * $this->size) - $this->perPage;

        if ($this->output === OpdsOutputEnum::json) {
            $this->startPage = $this->currentPage === 1 ? 0 : ($this->currentPage - 1) * $this->perPage;
            $this->json($content);
        }

        if ($this->output === OpdsOutputEnum::xml) {
            $this->xml($content);
        }

        $feeds = array_slice($feeds, $this->startPage, $this->perPage);

        return $this;
    }

    protected function route(?string $route, array $params = []): ?string
    {
        $query = [];
        $currentQuery = $this->query;
        $versionQuery = $currentQuery[$this->versionQuery] ?? null;

        if ($versionQuery) {
            $query = [$this->versionQuery => $versionQuery];
        }

        if (! empty($params)) {
            $query = array_merge($query, $params);
        }

        if (empty($query)) {
            return $route;
        }

        return $route.'?'.http_build_query($query);
    }

    /**
     * Handle JSON pagination.
     */
    private function json(array &$content): void
    {
        $content['metadata'] = [
            ...$content['metadata'],
            'numberOfItems' => $this->totalItems,
            'itemsPerPage' => $this->perPage,
            'currentPage' => $this->currentPage,
        ];

        $content['links'] = [
            OpdsEngine::addJsonLink(
                rel: 'self',
                href: $this->route($this->url),
            ),
        ];

        if ($this->currentPage !== 1) {
            $content['links'][] = OpdsEngine::addJsonLink(
                rel: 'first',
                href: $this->route($this->url, [$this->paginationQuery => 1]),
            );

            $content['links'][] = OpdsEngine::addJsonLink(
                rel: 'previous',
                href: $this->route($this->url, [$this->paginationQuery => $this->currentPage - 1]),
            );
        }

        if ($this->currentPage !== $this->size) {
            $content['links'][] = OpdsEngine::addJsonLink(
                rel: 'next',
                href: $this->route($this->url, [$this->paginationQuery => $this->currentPage + 1]),
            );

            $content['links'][] = OpdsEngine::addJsonLink(
                rel: 'last',
                href: $this->route($this->url, [$this->paginationQuery => $this->size]),
            );
        }
    }

    /**
     * Handle XML pagination.
     */
    private function xml(array &$content): void
    {
        $startRecord = $this->startPage + $this->perPage;
        $queryStartRecord = intval($this->query['startRecord'] ?? 0);
        $this->currentPage = intval(ceil($startRecord / $this->perPage));

        $previousUrl = $this->url.'?'.http_build_query([
            'q' => $this->query['q'] ?? null,
            'startRecord' => $startRecord - ($this->perPage * 2),
            'maximumRecords' => $this->perPage,
        ]);
        $nextUrl = $this->url.'?'.http_build_query([
            'q' => $this->query['q'] ?? null,
            'startRecord' => $startRecord,
            'maximumRecords' => $this->perPage,
        ]);
        $firstUrl = $this->url.'?'.http_build_query([
            'q' => $this->query['q'] ?? null,
            'startRecord' => 0,
            'maximumRecords' => $this->perPage,
        ]);
        $lastUrl = $this->url.'?'.http_build_query([
            'q' => $this->query['q'] ?? null,
            'startRecord' => $this->lastPage,
            'maximumRecords' => $this->perPage,
        ]);

        if ($queryStartRecord !== 0) {
            $content['__custom:link:5'] = OpdsEngine::addXmlLink(
                href: $previousUrl,
                rel: 'previous',
                title: 'Previous page'
            );
        }

        if ($queryStartRecord !== $this->lastPage) {
            $content['__custom:link:6'] = OpdsEngine::addXmlLink(
                href: $nextUrl,
                rel: 'next',
                title: 'Next page'
            );
        }

        if ($queryStartRecord !== 0) {
            $content['__custom:link:7'] = OpdsEngine::addXmlLink(
                href: $firstUrl,
                rel: 'first',
                title: 'First page'
            );
        }

        if ($queryStartRecord !== $this->lastPage) {
            $content['__custom:link:8'] = OpdsEngine::addXmlLink(
                href: $lastUrl,
                rel: 'last',
                title: 'Last page'
            );
        }

        $content['opensearch:totalResults'] = $this->totalItems;
        $content['opensearch:itemsPerPage'] = $this->perPage;
        $content['opensearch:startIndex'] = $this->startPage === 0 ? 1 : $this->startPage;
    }
}
