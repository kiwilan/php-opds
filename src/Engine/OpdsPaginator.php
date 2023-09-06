<?php

namespace Kiwilan\Opds\Engine;

use Kiwilan\Opds\Entries\OpdsEntryBook;
use Kiwilan\Opds\Entries\OpdsEntryNavigation;
use Kiwilan\Opds\Enums\OpdsOutputEnum;
use Kiwilan\Opds\Opds;

/**
 * Handle pagination for OPDS.
 */
class OpdsPaginator
{
    protected function __construct(
        protected OpdsOutputEnum $output,
        protected string $versionQuery,
        protected string $url,
        protected array $query = [],
        protected bool $usePagination = false,
        protected int $perPage = 0,
        protected int $page = 1,

        protected int $total = 0,
        protected int $start = 0,
        protected int $size = 0,
        protected int $first = 0,
        protected int $last = 0,
    ) {
    }

    /**
     * Create an instance of OpdsPagination.
     */
    public static function make(OpdsEngine $engine): self
    {
        $url = $engine->getOpds()->getUrl();

        if (str_contains($url, '?')) {
            $url = explode('?', $url)[0];
        }

        $output = $engine->getOpds()->getOutput();
        $query = $engine->getOpds()->getQuery();
        $page = $query['page'] ?? 1;

        return new self(
            output: $output,
            versionQuery: $engine->getOpds()->getConfig()->getVersionQuery(),
            url: $url,
            query: $query,
            usePagination: $engine->getOpds()->getConfig()->isUsePagination(),
            perPage: $engine->getOpds()->getConfig()->getMaxItemsPerPage(),
            page: $page,
        );
    }

    public function getOutput(): OpdsOutputEnum
    {
        return $this->output;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getQuery(): array
    {
        return $this->query;
    }

    public function usePagination(): bool
    {
        return $this->usePagination;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getStart(): int
    {
        return $this->start;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getFirst(): int
    {
        return $this->first;
    }

    public function getLast(): int
    {
        return $this->last;
    }

    /**
     * Handle pagination.
     *
     * @param  array<string, mixed>  $content
     * @param  OpdsEntryNavigation[]|OpdsEntryBook[]  $feeds
     */
    public function paginate(array &$content, array &$feeds): self
    {
        if (! $this->usePagination) {
            return $this;
        }

        if (count($feeds) < $this->perPage) {
            return $this;
        }

        $this->total = count($feeds);
        $this->start = intval($this->query['startRecord'] ?? 0);
        $this->size = intval(ceil($this->total / $this->perPage));
        $this->first = $this->start;
        $this->last = ($this->perPage * $this->size) - $this->perPage;

        if ($this->output === OpdsOutputEnum::json) {
            $this->start = $this->page === 1 ? 0 : ($this->page - 1) * $this->perPage;
            $this->json($content);
        }

        if ($this->output === OpdsOutputEnum::xml) {
            $this->xml($content);
        }

        $feeds = array_slice($feeds, $this->start, $this->perPage);

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
            'numberOfItems' => $this->total,
            'itemsPerPage' => $this->perPage,
            'currentPage' => $this->page,
        ];

        $content['links'] = [
            OpdsEngine::addJsonLink(
                rel: 'self',
                href: $this->route($this->url),
            ),
        ];

        if ($this->page !== 1) {
            $content['links'][] = OpdsEngine::addJsonLink(
                rel: 'first',
                href: $this->route($this->url, ['page' => 1]),
            );

            $content['links'][] = OpdsEngine::addJsonLink(
                rel: 'previous',
                href: $this->route($this->url, ['page' => $this->page - 1]),
            );
        }

        if ($this->page !== $this->size) {
            $content['links'][] = OpdsEngine::addJsonLink(
                rel: 'next',
                href: $this->route($this->url, ['page' => $this->page + 1]),
            );

            $content['links'][] = OpdsEngine::addJsonLink(
                rel: 'last',
                href: $this->route($this->url, ['page' => $this->size]),
            );
        }
    }

    /**
     * Handle XML pagination.
     */
    private function xml(array &$content): void
    {
        $startRecord = $this->start + $this->perPage;
        $queryStartRecord = intval($this->query['startRecord'] ?? 0);
        $this->page = intval(ceil($startRecord / $this->perPage));

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
            'startRecord' => $this->last,
            'maximumRecords' => $this->perPage,
        ]);

        if ($queryStartRecord !== 0) {
            $content['__custom:link:5'] = OpdsEngine::addXmlLink(
                href: $previousUrl,
                rel: 'previous',
                title: 'Previous page'
            );
        }

        if ($queryStartRecord !== $this->last) {
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

        if ($queryStartRecord !== $this->last) {
            $content['__custom:link:8'] = OpdsEngine::addXmlLink(
                href: $lastUrl,
                rel: 'last',
                title: 'Last page'
            );
        }

        $content['opensearch:totalResults'] = $this->total;
        $content['opensearch:itemsPerPage'] = $this->perPage;
        $content['opensearch:startIndex'] = $this->start === 0 ? 1 : $this->start;
    }
}
